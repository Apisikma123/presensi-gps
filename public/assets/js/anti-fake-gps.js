/**
 * Anti-Fake GPS & Mock Location Detector (Sharpened Version)
 * Designed for Progressive Web Apps (PWA) & Mobile Browsers
 */
(function (global) {
    'use strict';

    const AntiFakeGPS = {
        samples: [],
        maxSamples: 8,
        motionEventsCount: 0,
        motionDetected: false,
        lastPosition: null,
        initialized: false,

        init() {
            if (this.initialized) return;
            this.initialized = true;

            // Monitor handheld physical motion
            if (typeof window !== 'undefined' && window.DeviceMotionEvent) {
                window.addEventListener('devicemotion', (e) => {
                    const acc = e.accelerationIncludingGravity || e.acceleration;
                    if (acc && (Math.abs(acc.x || 0) > 0.05 || Math.abs(acc.y || 0) > 0.05 || Math.abs(acc.z || 0) > 0.05)) {
                        this.motionDetected = true;
                        this.motionEventsCount++;
                    }
                }, { passive: true });
            }
        },

        recordSample(position) {
            if (!position || !position.coords) return;
            const coords = position.coords;
            const now = Date.now();

            this.lastPosition = position;
            this.samples.push({
                lat: coords.latitude,
                lng: coords.longitude,
                accuracy: coords.accuracy,
                altitude: coords.altitude,
                altitudeAccuracy: coords.altitudeAccuracy,
                speed: coords.speed,
                heading: coords.heading,
                time: now
            });

            if (this.samples.length > this.maxSamples) {
                this.samples.shift();
            }
        },

        /**
         * Analyze GPS data for Mock Location patterns
         * @param {GeolocationPosition} currentPosition
         * @returns {{ isMock: boolean, score: number, reasons: string[], accuracy: number }}
         */
        analyze(currentPosition) {
            this.init();
            if (currentPosition) {
                this.recordSample(currentPosition);
            }

            const position = currentPosition || this.lastPosition;
            if (!position || !position.coords) {
                return { isMock: false, score: 0, reasons: [], accuracy: null };
            }

            const coords = position.coords;
            const reasons = [];
            let score = 0;

            // 1. Check Automation / Headless / DevTools Mock Sensor
            if (navigator.webdriver) {
                score += 80;
                reasons.push('Browser terdeteksi menggunakan DevTools / Otomasi GPS Mocking');
            }

            // 2. Check Accuracy Anomaly
            const acc = coords.accuracy;
            if (typeof acc === 'number') {
                if (acc <= 0) {
                    score += 70;
                    reasons.push('Akurasi GPS tidak valid (<= 0 meter)');
                } else if (acc > 0 && (Number.isInteger(acc) || (acc % 1 === 0))) {
                    // Certain mock apps force rigid integer accuracy like exactly 1, 2, 5
                    score += 15;
                }
            }

            // 3. Check Altitude & Speed absence (low weight to avoid false positive for indoor users)
            if (coords.altitude === null && coords.altitudeAccuracy === null) {
                if (coords.speed === null || coords.speed === 0) {
                    score += 10;
                }
            }

            // 4. Jitter Test (Across multiple samples if available)
            if (this.samples.length >= 4) {
                let allIdentical = true;
                const first = this.samples[0];
                for (let i = 1; i < this.samples.length; i++) {
                    const s = this.samples[i];
                    if (s.lat !== first.lat || s.lng !== first.lng) {
                        allIdentical = false;
                        break;
                    }
                }
                if (allIdentical && (first.altitude === null || first.altitude === 0) && (first.accuracy % 1 === 0)) {
                    score += 35;
                    reasons.push('Koordinat satelit GPS statis / tidak memiliki fluktuasi alami');
                }
            }

            // 5. Teleportation / Velocity Check (localStorage vs current position)
            try {
                const lastLocStr = localStorage.getItem('_gps_last_teleport_check');
                const now = Date.now();
                if (lastLocStr) {
                    const lastLoc = JSON.parse(lastLocStr);
                    const timeDiffHours = (now - lastLoc.time) / (1000 * 60 * 60);
                    
                    if (timeDiffHours > 0.001 && timeDiffHours < 6) {
                        const dKm = this.calculateDistanceKm(lastLoc.lat, lastLoc.lng, coords.latitude, coords.longitude);
                        const speedKmh = dKm / timeDiffHours;

                        // If moved > 30km at an impossible speed (> 600 km/h)
                        if (dKm > 30 && speedKmh > 600) {
                            score += 80;
                            reasons.push(`Perpindahan lokasi ekstrem (${Math.round(dKm)} km dalam ${Math.round(timeDiffHours * 60)} menit, kec: ${Math.round(speedKmh)} km/jam)`);
                        }
                    }
                }

                // Update last ping in storage
                localStorage.setItem('_gps_last_teleport_check', JSON.stringify({
                    lat: coords.latitude,
                    lng: coords.longitude,
                    time: now
                }));
            } catch (e) {
                console.warn('[AntiFakeGPS] Teleportation storage error:', e);
            }

            // A threshold of >= 60 ensures no false positives for legitimate indoor mobile users
            const isMock = score >= 60;

            return {
                isMock: isMock,
                score: score,
                reasons: reasons,
                accuracy: coords.accuracy
            };
        },

        calculateDistanceKm(lat1, lon1, lat2, lon2) {
            const R = 6371; // Earth radius in KM
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }
    };

    global.AntiFakeGPS = AntiFakeGPS;
    if (typeof document !== 'undefined') {
        AntiFakeGPS.init();
    }
})(typeof window !== 'undefined' ? window : this);
