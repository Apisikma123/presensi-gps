(function () {
    const formcreateKaryawan = document.querySelector('#formcreateKaryawan');
    // Form validation for Add / Edit karyawan
    if (formcreateKaryawan) {
        const fv = FormValidation.formValidation(formcreateKaryawan, {
            fields: {
                nama_karyawan: {
                    validators: {
                        notEmpty: {
                            message: 'Nama Karyawan Harus Diisi'
                        },
                    }
                },
                jenis_kelamin: {
                    validators: {
                        notEmpty: {
                            message: 'Jenis Kelamin Harus Dipilih'
                        },
                    }
                },
                kode_cabang: {
                    validators: {
                        notEmpty: {
                            message: 'Kantor Cabang Harus Dipilih'
                        },
                    }
                },
                kode_dept: {
                    validators: {
                        notEmpty: {
                            message: 'Departemen Harus Dipilih'
                        },
                    }
                },
                kode_jabatan: {
                    validators: {
                        notEmpty: {
                            message: 'Jabatan Harus Dipilih'
                        },
                    }
                },
                tanggal_masuk: {
                    validators: {
                        notEmpty: {
                            message: 'Tanggal Masuk Harus Diisi'
                        },
                    }
                },
                status_karyawan: {
                    validators: {
                        notEmpty: {
                            message: 'Status Karyawan Harus Dipilih'
                        },
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
    }
})();
