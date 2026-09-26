<form action="{{ route('karyawan.update', Crypt::encrypt($karyawan->nik)) }}" id="formcreateKaryawan" class="no-ajax-modal" data-custom-ajax="true" method="POST" enctype="multipart/form-data" novalidate>
    @csrf
    @method('PUT')

    <!-- SECTION 1: DATA IDENTITAS PRIBADI -->
    <div class="form-section-header mb-3">
        <div class="d-flex align-items-center gap-2 pb-2 border-bottom">
            <div class="rounded p-1 bg-label-primary text-primary">
                <i class="ti ti-user-check fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">1. Data Identitas Pribadi</h6>
                <small class="text-muted">Informasi kependudukan dan kontak pribadi tenaga kerja</small>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-user" label="Nama Lengkap Karyawan *" name="nama_karyawan" value="{{ $karyawan->nama_karyawan }}" placeholder="Masukkan nama lengkap sesuai KTP" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-barcode" label="NIK / ID Karyawan" name="nik_show" value="{{ $karyawan->nik_show ?? $karyawan->nik }}" placeholder="ID / NIK Karyawan" optional="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-id" label="Nomor KTP / NIK Kependudukan" name="no_ktp" value="{{ $karyawan->no_ktp }}" placeholder="16 digit nomor induk kependudukan" optional="true" />
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label mb-1.5" for="jenis_kelamin">
                    <span>Jenis Kelamin <span class="text-danger fw-bold">*</span></span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-gender-intergender"></i></span>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L" {{ $karyawan->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki - Laki</option>
                        <option value="P" {{ $karyawan->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-phone" label="No. Handphone / WhatsApp *" name="no_hp" value="{{ $karyawan->no_hp }}" placeholder="Contoh: 08123456789" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-mail" label="Email Pribadi" name="personal_email" value="{{ $karyawan->personal_email }}" placeholder="email.pribadi@gmail.com" optional="true" type="email" />
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label mb-1.5">Agama</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-pray"></i></span>
                    <select name="religion" class="form-select">
                        <option value="">-- Pilih Agama --</option>
                        @foreach(['Islam', 'Kristen Protestan', 'Katolik', 'Hindu', 'Buddha', 'Khonghucu', 'Lainnya'] as $rel)
                            <option value="{{ $rel }}" {{ ($karyawan->religion ?? '') == $rel ? 'selected' : '' }}>{{ $rel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label mb-1.5">Kewarganegaraan</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-flag"></i></span>
                    <select name="nationality" class="form-select">
                        <option value="WNI" {{ ($karyawan->nationality ?? 'WNI') == 'WNI' ? 'selected' : '' }}>WNI (Warga Negara Indonesia)</option>
                        <option value="WNA" {{ ($karyawan->nationality ?? '') == 'WNA' ? 'selected' : '' }}>WNA (Warga Negara Asing)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-12">
            <x-textarea-label label="Alamat Domisili Karyawan" name="alamat" icon="ti ti-map-pin" :value="$karyawan->alamat" placeholder="Alamat lengkap tempat tinggal saat ini" rows="2" />
        </div>
    </div>

    <!-- SECTION 2: PENUGASAN, STRUKTUR ORGANISASI & SHIFT -->
    <div class="form-section-header mb-3 mt-4">
        <div class="d-flex align-items-center gap-2 pb-2 border-bottom">
            <div class="rounded p-1 bg-label-success text-success">
                <i class="ti ti-briefcase fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">2. Penugasan & Struktur Organisasi</h6>
                <small class="text-muted">Penempatan cabang, divisi, atasan langsung, dan status keaktifan</small>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6 col-12">
            <x-select-label label="Kantor Cabang / Lokasi *" name="kode_cabang" :data="$cabang" key="kode_cabang" textShow="nama_cabang" selected="{{ $karyawan->kode_cabang }}" placeholder="Pilih Cabang" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Departemen *" name="kode_dept" :data="$departemen" selected="{{ $karyawan->kode_dept }}" key="kode_dept" textShow="nama_dept" placeholder="Pilih Departemen" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label mb-1.5">Divisi / Regu Kerja (Opsional)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-users-group"></i></span>
                    <select name="kode_divisi" class="form-select">
                        <option value="">-- Tanpa Divisi Khusus --</option>
                        @foreach ($divisi as $div)
                            <option value="{{ $div->kode_divisi }}" {{ ($karyawan->kode_divisi ?? '') == $div->kode_divisi ? 'selected' : '' }}>
                                {{ $div->nama_divisi }} ({{ $div->kode_divisi }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Jabatan *" name="kode_jabatan" :data="$jabatan" selected="{{ $karyawan->kode_jabatan }}" key="kode_jabatan" textShow="nama_jabatan" placeholder="Pilih Jabatan" upperCase="true" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-stairs" label="Grade / Level Jabatan" name="grade_level" :value="$karyawan->grade_level" placeholder="Contoh: Staff, Supervisor, Officer, Manajerial" optional="true" />
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label mb-1.5">Atasan Langsung (Direct Supervisor)</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-user-star"></i></span>
                    <select name="direct_supervisor_nik" class="form-select">
                        <option value="">-- Tanpa Atasan / Top Level --</option>
                        @foreach ($supervisors as $spv)
                            <option value="{{ $spv->nik }}" {{ ($karyawan->direct_supervisor_nik ?? '') == $spv->nik ? 'selected' : '' }}>
                                {{ $spv->nama_karyawan }} ({{ $spv->nik_show ?: $spv->nik }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label mb-1.5">Status Hubungan Kerja *</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-file-certificate"></i></span>
                    <select name="employment_type" class="form-select" required>
                        @foreach(['PKWT' => 'PKWT (Kontrak Waktu Tertentu)', 'PKWTT' => 'PKWTT (Karyawan Tetap)', 'PROBATION' => 'Probation / Masa Percobaan', 'INTERNSHIP' => 'Internship / Magang', 'DAILY' => 'Harian Lepas / Freelance'] as $empKey => $empLabel)
                            <option value="{{ $empKey }}" {{ ($karyawan->employment_type ?? 'PKWT') == $empKey ? 'selected' : '' }}>{{ $empLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <div class="form-group mb-3">
                <label style="font-weight: 600" class="form-label mb-1.5" for="status_aktif_karyawan">
                    <span>Status Keaktifan <span class="text-danger fw-bold">*</span></span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-activity"></i></span>
                    <select name="status_aktif_karyawan" id="status_aktif_karyawan" class="form-select" required>
                        <option value="1" {{ $karyawan->status_aktif_karyawan == '1' ? 'selected' : '' }}>Aktif Bekerja</option>
                        <option value="0" {{ $karyawan->status_aktif_karyawan === '0' ? 'selected' : '' }}>Non Aktif / Resign</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-12">
            <x-select-label label="Shift Kerja Default *" name="kode_jam_kerja" :data="$jamkerja" key="kode_jam_kerja" textShow="nama_jam_kerja" selected="{{ $karyawan->kode_jam_kerja }}" placeholder="Pilih Shift Kerja" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-calendar" datepicker="flatpickr-date" label="Tanggal Bergabung / Masuk *" name="tanggal_masuk" value="{{ $karyawan->tanggal_masuk }}" placeholder="Pilih tanggal bergabung" required="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-mail-forward" label="Email Kantor (SSO / Akun)" name="company_email" :value="$karyawan->company_email" placeholder="nama@perusahaan.co.id" optional="true" type="email" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-id" label="RFID Card UID" name="rfid_uid" :value="$karyawan->rfid_uid" placeholder="Tap RFID Card untuk input UID" optional="true" />
        </div>
    </div>

    <!-- SECTION 3: FINANSIAL, PAJAK & BPJS -->
    <div class="form-section-header mb-3 mt-4">
        <div class="d-flex align-items-center gap-2 pb-2 border-bottom">
            <div class="rounded p-1 bg-label-info text-info">
                <i class="ti ti-building-bank fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">3. Finansial, Payroll, Pajak & BPJS</h6>
                <small class="text-muted">Data rekening gaji, nomor NPWP, dan kepesertaan BPJS</small>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-4 col-12">
            <x-input-with-icon-label icon="ti ti-building-bank" label="Nama Bank Transfer Gaji" name="nama_bank" :value="$karyawan->nama_bank" placeholder="Contoh: BCA, Mandiri, BRI" optional="true" />
        </div>
        <div class="col-md-4 col-12">
            <x-input-with-icon-label icon="ti ti-credit-card" label="Nomor Rekening Bank" name="no_rekening" :value="$karyawan->no_rekening" placeholder="Nomor rekening transfer" optional="true" />
        </div>
        <div class="col-md-4 col-12">
            <x-input-with-icon-label icon="ti ti-user" label="Nama Pemilik Rekening" name="nama_rekening" :value="$karyawan->nama_rekening" placeholder="Nama sesuai buku tabungan" optional="true" />
        </div>
        <div class="col-md-4 col-12">
            <x-input-with-icon-label icon="ti ti-receipt-tax" label="Nomor NPWP Karyawan" name="npwp_number" :value="$karyawan->npwp_number" placeholder="15/16 digit NPWP" optional="true" />
        </div>
        <div class="col-md-4 col-12">
            <x-input-with-icon-label icon="ti ti-heart-handshake" label="No. BPJS Kesehatan" name="bpjs_kesehatan_number" :value="$karyawan->bpjs_kesehatan_number" placeholder="Nomor kartu BPJS Kesehatan" optional="true" />
        </div>
        <div class="col-md-4 col-12">
            <x-input-with-icon-label icon="ti ti-shield-check" label="No. BPJS Ketenagakerjaan" name="bpjs_ketenagakerjaan_number" :value="$karyawan->bpjs_ketenagakerjaan_number" placeholder="Nomor KPJ BPJS TK" optional="true" />
        </div>
    </div>

    <!-- SECTION 4: KONTAK DARURAT & FOTO PROFIL -->
    <div class="form-section-header mb-3 mt-4">
        <div class="d-flex align-items-center gap-2 pb-2 border-bottom">
            <div class="rounded p-1 bg-label-warning text-warning">
                <i class="ti ti-phone-call fs-5"></i>
            </div>
            <div>
                <h6 class="mb-0 fw-bold">4. Kontak Darurat, Password & Foto Profil</h6>
                <small class="text-muted">Kontak darurat dan update password/foto karyawan</small>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-3">
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-user-exclamation" label="Nama Kontak Darurat" name="kontak_darurat" :value="$karyawan->kontak_darurat" placeholder="Nama orang tua, pasangan, saudara" optional="true" />
        </div>
        <div class="col-md-6 col-12">
            <x-input-with-icon-label icon="ti ti-heart" label="Hubungan Kontak Darurat" name="hubungan_kontak_darurat" :value="$karyawan->hubungan_kontak_darurat" placeholder="Contoh: Istri, Suami, Ayah, Ibu, Kakak" optional="true" />
        </div>
        <div class="col-12">
            <div class="form-group mb-2">
                <label style="font-weight: 600" class="form-label d-flex align-items-center justify-content-between mb-1.5" for="karyawan_password_edit">
                    <span>Password Baru</span>
                    <span class="badge bg-label-secondary text-muted fw-normal" style="font-size: 11px;">Kosongkan jika tidak ingin mengubah</span>
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="ti ti-key"></i></span>
                    <input type="password" class="form-control" id="karyawan_password_edit" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" autocomplete="new-password">
                    <span class="input-group-text cursor-pointer" id="togglePasswordEditBtn" style="cursor: pointer;" title="Lihat Password">
                        <i class="ti ti-eye" id="togglePasswordEditIcon"></i>
                    </span>
                </div>
                <div class="form-text mt-1 text-muted" style="font-size: 11.5px;">
                    <i class="ti ti-info-circle text-primary me-1"></i>Username login: <strong>{{ $karyawan->nik }}</strong>
                </div>
            </div>
        </div>
        <div class="col-12">
            <x-input-file name="foto" label="Update Foto Profil Karyawan" :value="$karyawan->foto" helper="Format: JPG, PNG, WEBP (Maks. 2MB)" />
        </div>
    </div>

    <!-- BUTTON ACTIONS -->
    <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
            Batal
        </button>
        <button class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4" type="submit" id="btnSimpanKaryawan">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Perubahan</span>
        </button>
    </div>
</form>

<script src="{{ asset('assets/js/jquery.mask.min.js') }}"></script>
<script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>

<script>
    $(function () {
        $(".flatpickr-date").flatpickr({
            dateFormat: "Y-m-d",
            allowInput: true
        });

        $('#togglePasswordEditBtn').on('click', function () {
            const input = $('#karyawan_password_edit');
            const icon = $('#togglePasswordEditIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('ti-eye').addClass('ti-eye-off');
            } else {
                input.attr('type', 'password');
                icon.removeClass('ti-eye-off').addClass('ti-eye');
            }
        });
    });
</script>
