(function () {
    // Helper parse time HH:mm or HH:mm:ss to total minutes
    function parseTimeToMinutes(val) {
        if (!val) return null;
        var clean = String(val).trim();
        var parts = clean.split(':');
        if (parts.length < 2) return null;
        var h = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10);
        if (isNaN(h) || isNaN(m) || h < 0 || h > 23 || m < 0 || m > 59) return null;
        return h * 60 + m;
    }

    // Fungsi Auto Calculate Total Jam yang akurat, tidak bisa minus, dan otomatis deteksi lintas hari/istirahat
    function setupAutoTotalJam(form, fvInstance) {
        if (!form) return;

        var jamMasukInput = form.querySelector('[name="jam_masuk"]');
        var jamPulangInput = form.querySelector('[name="jam_pulang"]');
        var istirahatSelect = form.querySelector('[name="istirahat"]');
        var jamAwalIstInput = form.querySelector('[name="jam_awal_istirahat"]');
        var jamAkhirIstInput = form.querySelector('[name="jam_akhir_istirahat"]');
        var lintasHariSelect = form.querySelector('[name="lintashari"]');
        var totalJamInput = form.querySelector('[name="total_jam"]');

        if (!jamMasukInput || !jamPulangInput || !totalJamInput) return;

        function calculate() {
            var masukMin = parseTimeToMinutes(jamMasukInput.value);
            var pulangMin = parseTimeToMinutes(jamPulangInput.value);

            if (masukMin === null || pulangMin === null) {
                return;
            }

            var isLintasHari = false;

            // Logika Deteksi Shift:
            // 1. Jika jam pulang < jam masuk (contoh Masuk 22:00, Pulang 06:00 atau Masuk 13:15, Pulang 08:00), shift PASTI lintas hari
            if (pulangMin < masukMin) {
                isLintasHari = true;
                if (lintasHariSelect && lintasHariSelect.value !== '1') {
                    lintasHariSelect.value = '1';
                    var sectionLH = form.querySelector('#sectionLintasHari');
                    if (sectionLH) {
                        $(sectionLH).slideDown();
                    }
                }
            } else if (pulangMin > masukMin) {
                // 2. Jika jam pulang > jam masuk (contoh Masuk 00:00, Pulang 08:00 atau Masuk 08:00, Pulang 17:00), shift adalah reguler 1 hari yang sama
                isLintasHari = false;
                if (lintasHariSelect && lintasHariSelect.value !== '0') {
                    lintasHariSelect.value = '0';
                    var sectionLH = form.querySelector('#sectionLintasHari');
                    if (sectionLH) {
                        $(sectionLH).slideUp();
                        var batasInput = form.querySelector('#batas_presensi_pulang');
                        if (batasInput) batasInput.value = '';
                    }
                }
            } else {
                // 3. Masuk dan Pulang sama persis (misal 08:00 ke 08:00 -> shift 24 jam)
                isLintasHari = true;
            }

            var diffMinutes = 0;
            if (isLintasHari) {
                diffMinutes = (24 * 60 - masukMin) + pulangMin;
            } else {
                diffMinutes = pulangMin - masukMin;
            }

            // Validasi agar selisih tidak negatif
            if (diffMinutes <= 0) {
                diffMinutes = (24 * 60 - masukMin) + pulangMin;
            }

            // Kurangi jam istirahat jika opsi istirahat dipilih '1'
            if (istirahatSelect && istirahatSelect.value === '1' && jamAwalIstInput && jamAkhirIstInput) {
                var istAwalMin = parseTimeToMinutes(jamAwalIstInput.value);
                var istAkhirMin = parseTimeToMinutes(jamAkhirIstInput.value);

                if (istAwalMin !== null && istAkhirMin !== null) {
                    var istMinutes = 0;
                    if (istAkhirMin >= istAwalMin) {
                        istMinutes = istAkhirMin - istAwalMin;
                    } else {
                        istMinutes = (24 * 60 - istAwalMin) + istAkhirMin;
                    }

                    if (istMinutes > 0 && istMinutes < diffMinutes) {
                        diffMinutes -= istMinutes;
                    }
                }
            }

            // Konversi menit ke jam wajar (pembulatan menit ke jam, minimal 1, maksimal 24)
            var totalJam = Math.round(diffMinutes / 60);
            if (totalJam < 1) totalJam = 1;
            if (totalJam > 24) totalJam = 24;

            totalJamInput.value = totalJam;

            if (fvInstance && typeof fvInstance.revalidateField === 'function') {
                try {
                    fvInstance.revalidateField('total_jam');
                } catch(e) {}
            }
        }

        // Listener lengkap untuk native DOM dan jQuery
        var watchInputs = [jamMasukInput, jamPulangInput, jamAwalIstInput, jamAkhirIstInput];
        watchInputs.forEach(function (el) {
            if (!el) return;
            el.addEventListener('change', calculate);
            el.addEventListener('input', calculate);
            el.addEventListener('blur', calculate);
            $(el).on('change input blur', calculate);

            // Hook jika flatpickr sudah aktif
            if (el._flatpickr && el._flatpickr.config && Array.isArray(el._flatpickr.config.onChange)) {
                el._flatpickr.config.onChange.push(calculate);
            }
        });

        if (istirahatSelect) {
            istirahatSelect.addEventListener('change', calculate);
            $(istirahatSelect).on('change', calculate);
        }

        if (lintasHariSelect) {
            lintasHariSelect.addEventListener('change', calculate);
            $(lintasHariSelect).on('change', calculate);
        }

        // Sinkronisasi nilai langsung saat load
        calculate();
    }

    // Helper safely init FormValidation
    function initValidation(form, isCreate) {
        if (typeof FormValidation === 'undefined') return null;

        try {
            var fields = {
                nama_jam_kerja: {
                    validators: {
                        notEmpty: { message: 'Nama Jam Kerja Harus Diisi !' },
                        stringLength: { min: 1, max: 50, message: 'Nama Jam Kerja maksimal 50 karakter' }
                    }
                },
                jam_masuk: {
                    validators: {
                        notEmpty: { message: 'Jam Masuk Harus Diisi !' },
                        regexp: { regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/, message: 'Format Jam Masuk harus hh:mm' }
                    }
                },
                jam_pulang: {
                    validators: {
                        notEmpty: { message: 'Jam Pulang Harus Diisi !' },
                        regexp: { regexp: /^(0[0-9]|1[0-9]|2[0-3]):([0-5][0-9])$/, message: 'Format Jam Pulang harus hh:mm' }
                    }
                },
                total_jam: {
                    validators: {
                        notEmpty: { message: 'Total Jam Harus Diisi' },
                        integer: { message: 'Total Jam harus berupa angka' },
                        between: { min: 1, max: 24, message: 'Total Jam harus antara 1 sampai 24 jam' }
                    }
                },
                lintashari: {
                    validators: {
                        notEmpty: { message: 'Lintas Hari Harus Diisi' }
                    }
                },
                keterangan: {
                    validators: {
                        stringLength: { max: 255, message: 'Keterangan maksimal 255 karakter' }
                    }
                }
            };

            if (isCreate) {
                fields.kode_jam_kerja = {
                    validators: {
                        notEmpty: { message: 'Kode Jam Kerja Harus Diisi !' },
                        stringLength: { min: 1, max: 4, message: 'Kode Jam Kerja maksimal 4 karakter' },
                        regexp: { regexp: /^[A-Z0-9]+$/, message: 'Kode Jam Kerja hanya boleh huruf kapital dan angka' }
                    }
                };
            }

            return FormValidation.formValidation(form, {
                fields: fields,
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
                init: function(instance) {
                    instance.on('plugins.message.placed', function (e) {
                        if (e.element.parentElement.classList.contains('input-group')) {
                            e.element.parentElement.insertAdjacentElement('afterend', e.messageElement);
                        }
                    });
                }
            });
        } catch (e) {
            console.warn('FormValidation failed to initialize:', e);
            return null;
        }
    }

    function initForm(formSelector, isCreate) {
        var form = document.querySelector(formSelector);
        if (!form) return;

        // FormValidation (safe wrapper)
        var fv = initValidation(form, isCreate);

        // Auto uppercase untuk kode_jam_kerja
        var kodeInput = form.querySelector('[name="kode_jam_kerja"]');
        if (kodeInput && !kodeInput.readOnly) {
            kodeInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
            });
        }

        // Batasi nama_jam_kerja
        var namaInput = form.querySelector('[name="nama_jam_kerja"]');
        if (namaInput) {
            namaInput.addEventListener('input', function(e) {
                if (e.target.value.length > 50) {
                    e.target.value = e.target.value.substring(0, 50);
                }
            });
        }

        // Setup Auto Calculate Total Jam - selalu dieksekusi tanpa bergantung pada library eksternal
        setupAutoTotalJam(form, fv);
    }

    // Inisialisasi form create & edit
    initForm('#formcreateJamKerja', true);
    initForm('#formeditJamKerja', false);

    // Expose ke global window agar dapat dipanggil kembali kapan saja
    window.setupAutoTotalJam = setupAutoTotalJam;
})();
