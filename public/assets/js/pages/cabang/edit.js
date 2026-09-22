(function () {
    const formeditCabang = document.querySelector('#formeditCabang');
    // Form validation for Add new record
    if (formeditCabang && typeof FormValidation !== 'undefined' && FormValidation.formValidation) {
        try {
            const fv = FormValidation.formValidation(formeditCabang, {
            fields: {
                kode_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Kode Cabang Harus Diisi'
                        },
                        stringLength: {
                            max: 3,
                            min: 3,
                            message: 'Kode Cabang Harus 3 Karakter'
                        },


                    }
                },
                nama_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Cabang Harus Diisi'
                        },
                        stringLength: {
                            min: 1,
                            max: 50,
                            message: 'Nama Cabang maksimal 50 karakter'
                        }
                    }
                },
                alamat_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Alamat Cabang Harus Diisi'
                        },
                        stringLength: {
                            min: 1,
                            max: 100,
                            message: 'Alamat Cabang maksimal 100 karakter'
                        }
                    }
                },
                telepon_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Telepon Cabang Harus Diisi'
                        },
                        stringLength: {
                            min: 1,
                            max: 13,
                            message: 'Telepon Cabang maksimal 13 karakter'
                        },
                        regexp: {
                            regexp: /^[0-9]+$/,
                            message: 'Telepon Cabang hanya boleh angka'
                        }
                    }
                },
                lokasi_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Lokasi Cabang Harus Diisi'
                        }
                    }
                },
                radius_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Radius Cabang Harus Diisi'
                        },
                        integer: {
                            message: 'Radius Cabang harus berupa angka'
                        },
                        between: {
                            min: 1,
                            max: 9999,
                            message: 'Radius Cabang harus antara 1 sampai 9999 meter'
                        }
                    }
                },

                kode_pt: {
                    validators: {
                        notEmpty: {
                            message: 'Kode PT Harus Diisi'
                        },
                        stringLength: {
                            max: 3,
                            min: 3,
                            message: 'Kode PT Harus 3 Karakter'
                        },


                    }
                },

                nama_pt: {
                    validators: {
                        notEmpty: {
                            message: 'Nama PT Harus Diisi'
                        }
                    }
                },


                kode_regional: {
                    validators: {
                        notEmpty: {
                            message: 'Regional Harus Dipilih'
                        }
                    }
                },


                urutan: {
                    validators: {
                        notEmpty: {
                            message: 'Urutan Harus Diisi'
                        }
                    }
                },


            },
            plugins: {
                trigger: new FormValidation.plugins.Trigger(),
                bootstrap5: new FormValidation.plugins.Bootstrap5({
                    eleValidClass: '',
                    rowSelector: '.mb-3'
                }),
                submitButton: new FormValidation.plugins.SubmitButton(),

                defaultSubmit: new FormValidation.plugins.DefaultSubmit(),
                autoFocus: new FormValidation.plugins.AutoFocus()
            },
            init: instance => {
                instance.on('plugins.message.placed', function (e) {
                    if (e.element.parentElement.classList.contains('input-group')) {
                        e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
                    }
                });
            }
        });
        } catch(e) {
            console.warn('FormValidation initialization skipped:', e);
        }
    }

    if (formeditCabang) {
        // Validasi panjang real-time
        const namaCabangInput = formeditCabang.querySelector('[name="nama_cabang"]');
        if (namaCabangInput) {
            namaCabangInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 50) {
                    e.target.value = value.substring(0, 50);
                }
            });
        }

        const alamatCabangInput = formeditCabang.querySelector('[name="alamat_cabang"]');
        if (alamatCabangInput) {
            alamatCabangInput.addEventListener('input', function(e) {
                const value = e.target.value;
                if (value.length > 100) {
                    e.target.value = value.substring(0, 100);
                }
            });
        }

        const teleponCabangInput = formeditCabang.querySelector('[name="telepon_cabang"]');
        if (teleponCabangInput) {
            teleponCabangInput.addEventListener('input', function(e) {
                const value = e.target.value.replace(/[^0-9]/g, '');
                if (value.length > 13) {
                    e.target.value = value.substring(0, 13);
                } else {
                    e.target.value = value;
                }
            });
        }

        // Initialize Leaflet Map
        const mapElement = formeditCabang ? (formeditCabang.querySelector('#map') || document.getElementById('map')) : document.getElementById('map');

        function initMap() {
            if (!mapElement || typeof L === 'undefined') return;

            let map, marker, circle;
            const lokasiInput = formeditCabang.querySelector('[name="lokasi_cabang"]');
            const radiusInput = formeditCabang.querySelector('[name="radius_cabang"]');

            // Fix Leaflet default icon path
            delete L.Icon.Default.prototype._getIconUrl;
            L.Icon.Default.mergeOptions({
                iconRetinaUrl: '/assets/vendor/libs/leaflet/images/marker-icon-2x.png',
                iconUrl: '/assets/vendor/libs/leaflet/images/marker-icon.png',
                shadowUrl: '/assets/vendor/libs/leaflet/images/marker-shadow.png',
            });

            // Clean previous instance if exists to prevent "Map container is already initialized"
            if (window._cabangEditMap) {
                try {
                    window._cabangEditMap.remove();
                } catch(e) {}
                window._cabangEditMap = null;
            }
            if (mapElement._leaflet_id) {
                mapElement.innerHTML = '';
                delete mapElement._leaflet_id;
            }

            // Default location
            let defaultLat = -6.229746;
            let defaultLng = 106.807493;
            let defaultZoom = 15;

            // Parse existing location if available
            if (lokasiInput && lokasiInput.value) {
                const coords = lokasiInput.value.split(',');
                if (coords.length === 2 && !isNaN(parseFloat(coords[0])) && !isNaN(parseFloat(coords[1]))) {
                    defaultLat = parseFloat(coords[0].trim());
                    defaultLng = parseFloat(coords[1].trim());
                }
            }

            // Initialize map directly on element
            map = L.map(mapElement).setView([defaultLat, defaultLng], defaultZoom);
            window._cabangEditMap = map;

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 19
            }).addTo(map);

            // Invalidate size on modal events, observer, and timeouts to avoid blank/grey tiles
            var refreshMap = function() {
                if (map) {
                    map.invalidateSize();
                }
            };

            // ResizeObserver to immediately catch when modal becomes visible
            if ('ResizeObserver' in window && mapElement) {
                var ro = new ResizeObserver(function(entries) {
                    for (var i = 0; i < entries.length; i++) {
                        if (entries[i].contentRect.width > 20 && entries[i].contentRect.height > 20) {
                            refreshMap();
                        }
                    }
                });
                ro.observe(mapElement);
            }

            [50, 150, 300, 600, 1000].forEach(function(delay) {
                setTimeout(refreshMap, delay);
            });
            $('#mdleditCabang, #modal, .modal').on('shown.bs.modal', refreshMap);
            $(window).on('resize', refreshMap);

            // Function to update location input and marker
            function updateLocation(lat, lng) {
                const locationString = lat.toFixed(6) + ',' + lng.toFixed(6);
                if (lokasiInput) {
                    lokasiInput.value = locationString;
                }

                // Remove existing marker and circle
                if (marker) {
                    map.removeLayer(marker);
                }
                if (circle) {
                    map.removeLayer(circle);
                }

                // Add new marker
                marker = L.marker([lat, lng], {
                    draggable: true
                }).addTo(map);

                // Update circle if radius is set
                const radius = radiusInput ? parseInt(radiusInput.value) || 30 : 30;
                if (radius > 0) {
                    circle = L.circle([lat, lng], {
                        color: '#1E4D3E',
                        fillColor: '#1E4D3E',
                        fillOpacity: 0.15,
                        radius: radius
                    }).addTo(map);
                }

                // Update marker position when dragged
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    updateLocation(position.lat, position.lng);
                });

                // Show popup with coordinates
                marker.bindPopup(`
                    <b>Lokasi Cabang</b><br>
                    Latitude: ${lat.toFixed(6)}<br>
                    Longitude: ${lng.toFixed(6)}<br>
                    <small>Drag marker untuk memindahkan lokasi</small>
                `).openPopup();
            }

            // Initialize marker and circle
            updateLocation(defaultLat, defaultLng);

            // Click on map to set location
            map.on('click', function(e) {
                updateLocation(e.latlng.lat, e.latlng.lng);
            });

            // Update circle when radius changes
            if (radiusInput) {
                radiusInput.addEventListener('input', function() {
                    const radius = parseInt(this.value) || 0;
                    if (marker && radius > 0) {
                        const position = marker.getLatLng();
                        if (circle) {
                            map.removeLayer(circle);
                        }
                        circle = L.circle([position.lat, position.lng], {
                            color: '#1E4D3E',
                            fillColor: '#1E4D3E',
                            fillOpacity: 0.15,
                            radius: radius
                        }).addTo(map);
                    }
                });
            }

            // Search location function
            const searchInput = formeditCabang.querySelector('#searchLocation') || document.getElementById('searchLocation');
            const searchButton = formeditCabang.querySelector('#btnSearchLocation') || document.getElementById('btnSearchLocation');

            function searchLocation() {
                const query = searchInput.value.trim();
                if (!query) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Peringatan',
                        text: 'Silakan masukkan nama lokasi yang ingin dicari'
                    });
                    return;
                }

                // Show loading
                if (searchButton) {
                    const originalText = searchButton.innerHTML;
                    searchButton.disabled = true;
                    searchButton.innerHTML = '<i class="ti ti-loader me-1"></i> Mencari...';

                    // Use Nominatim for geocoding
                    fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.length > 0) {
                                const result = data[0];
                                const lat = parseFloat(result.lat);
                                const lon = parseFloat(result.lon);
                                
                                // Update map view and location
                                map.setView([lat, lon], 15);
                                updateLocation(lat, lon);
                                
                                // Update search input with found location
                                searchInput.value = result.display_name;
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Lokasi Ditemukan',
                                    text: result.display_name,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Lokasi Tidak Ditemukan',
                                    text: 'Silakan coba dengan kata kunci lain'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat mencari lokasi'
                            });
                        })
                        .finally(() => {
                            searchButton.disabled = false;
                            searchButton.innerHTML = originalText;
                        });
                }
            }

            // Search button click
            if (searchButton) {
                searchButton.addEventListener('click', searchLocation);
            }

            // Search on Enter key
            if (searchInput) {
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        searchLocation();
                    }
                });
            }
        }

        if (mapElement) {
            if (typeof L === 'undefined') {
                if (!document.querySelector('link[href*="leaflet.css"]')) {
                    const link = document.createElement('link');
                    link.rel = 'stylesheet';
                    link.href = '/assets/vendor/libs/leaflet/leaflet.css';
                    document.head.appendChild(link);
                }
                const script = document.createElement('script');
                script.src = '/assets/vendor/libs/leaflet/leaflet.js';
                script.onload = initMap;
                document.head.appendChild(script);
            } else {
                initMap();
            }
        }
    }
})();
