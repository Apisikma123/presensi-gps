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

            // 1. Check Native Android Mock Provider flag (if passed by native wrapper/PWA)
            if (coords.isMock === true || coords.mocked === true) {
                score += 100;
                reasons.push('Terdeteksi flag Mock Location aktif dari sistem operasi perangkat');
            }

            // 2. Extreme Impossible Teleportation (> 200km at > 900 km/h)
            try {
                const lastLocStr = localStorage.getItem('_gps_last_teleport_check');
                const now = Date.now();
                if (lastLocStr) {
                    const lastLoc = JSON.parse(lastLocStr);
                    const timeDiffHours = (now - lastLoc.time) / (1000 * 60 * 60);
                    
                    if (timeDiffHours > 0.005 && timeDiffHours < 4) {
                        const dKm = this.calculateDistanceKm(lastLoc.lat, lastLoc.lng, coords.latitude, coords.longitude);
                        const speedKmh = dKm / timeDiffHours;

                        if (dKm > 200 && speedKmh > 900) {
                            score += 100;
                            reasons.push(`Perpindahan lokasi ekstrem (${Math.round(dKm)} km dalam ${Math.round(timeDiffHours * 60)} menit)`);
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

            // Only mark as mock if there is hard proof (score >= 100)
            const isMock = score >= 100;

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
