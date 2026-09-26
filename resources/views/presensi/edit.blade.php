<!-- Employee Identity Card (Brew & Beam Minimalist Architecture) -->
<div class="card mb-3 shadow-none" style="background: #FAF9F8; border: 1px solid rgba(60, 42, 33, 0.08); border-radius: 14px;">
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                    style="width: 44px; height: 44px; background: rgba(var(--bs-primary-rgb), 0.1); color: var(--theme-color-1, #3C2A21); font-size: 15px; border: 1.5px solid rgba(var(--bs-primary-rgb), 0.2);">
                    {{ strtoupper(substr($karyawan->nama_karyawan, 0, 2)) }}
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">{{ $karyawan->nama_karyawan }}</h6>
                    <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                        <span class="font-mono text-muted" style="font-size: 11.5px;">
                            <i class="ti ti-id me-0.5"></i>{{ $karyawan->nik_show ?? $karyawan->nik }}
                        </span>
                        <span class="badge" style="background: #E2E8F0; color: #334155; font-size: 10.5px; font-weight: 600;">
                            <i class="ti ti-building me-0.5"></i>{{ $karyawan->kode_dept }}
                        </span>
                        <span class="badge" style="background: #E2E8F0; color: #334155; font-size: 10.5px; font-weight: 600;">
                            <i class="ti ti-map-pin me-0.5"></i>{{ $karyawan->kode_cabang }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-end">
                <span class="badge font-mono" style="background: rgba(var(--bs-primary-rgb), 0.08); color: var(--theme-color-1, #3C2A21); border: 1px solid rgba(var(--bs-primary-rgb), 0.15); font-size: 11.5px; font-weight: 600; padding: 4px 10px; border-radius: 8px;">
                    <i class="ti ti-calendar me-1"></i>{{ date('d M Y', strtotime($tanggal)) }}
                </span>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('presensi.update') }}" method="POST" id="formEditPresensi">
    @csrf
    <input type="hidden" value="{{ Crypt::encrypt($karyawan->nik) }}" name="nik">
    <input type="hidden" value="{{ $tanggal }}" name="tanggal">

    <div class="row g-3">
        <!-- Status Kehadiran -->
        <div class="col-md-6 col-12">
            <label class="form-label fw-semibold" style="font-size: 12.5px; color: #0F172A;">Status Kehadiran <span class="text-danger">*</span></label>
            <select name="status" id="status" class="form-select" style="height: 38px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px;" required>
                <option value="">Pilih Status</option>
                <option value="h" {{ $presensi != null && $presensi->status == 'h' ? 'selected' : '' }}>Hadir</option>
                <option value="i" {{ $presensi != null && $presensi->status == 'i' ? 'selected' : '' }}>Izin</option>
                <option value="s" {{ $presensi != null && $presensi->status == 's' ? 'selected' : '' }}>Sakit</option>
                <option value="c" {{ $presensi != null && $presensi->status == 'c' ? 'selected' : '' }}>Cuti</option>
                <option value="a" {{ $presensi != null && $presensi->status == 'a' ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>

        <!-- Shift Jam Kerja -->
        <div class="col-md-6 col-12">
            <label class="form-label fw-semibold" style="font-size: 12.5px; color: #0F172A;">Shift Jam Kerja <span class="text-danger">*</span></label>
            <select name="kode_jam_kerja" id="kode_jam_kerja" class="form-select" style="height: 38px; border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px;" required>
                <option value="">Pilih Shift</option>
                @foreach ($jam_kerja as $d)
                    <option value="{{ $d->kode_jam_kerja }}"
                        {{ $presensi != null && $presensi->kode_jam_kerja == $d->kode_jam_kerja ? 'selected' : '' }}>
                        {{ $d->kode_jam_kerja }} - {{ $d->nama_jam_kerja }} ({{ date('H:i', strtotime($d->jam_masuk)) }} - {{ date('H:i', strtotime($d->jam_pulang)) }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Jam Masuk & Jam Pulang (Pure Clock Picker) -->
        <div class="col-md-6 col-12 section-jam-hadir">
            <x-input-with-icon icon="ti ti-clock" label="Jam Absen Masuk" name="jam_in" id="jam_in"
                datepicker="flatpickr-time" placeholder="Contoh: 08:00"
                value="{{ $presensi != null && $presensi->jam_in ? date('H:i', strtotime($presensi->jam_in)) : '' }}" />
        </div>
        <div class="col-md-6 col-12 section-jam-hadir">
            <x-input-with-icon icon="ti ti-clock" label="Jam Absen Pulang" name="jam_out" id="jam_out"
                datepicker="flatpickr-time" placeholder="Contoh: 17:00"
                value="{{ $presensi != null && $presensi->jam_out ? date('H:i', strtotime($presensi->jam_out)) : '' }}" />
        </div>

        <!-- Alasan Koreksi (Audit Trail) -->
        <div class="col-12">
            <label class="form-label fw-semibold" style="font-size: 12.5px; color: #0F172A;">Alasan Koreksi <span class="text-danger">*</span></label>
            <textarea name="alasan_koreksi" id="alasan_koreksi" class="form-control" rows="2" placeholder="Contoh: Lupa clock-out, kendala GPS, HP rusak, atau konfirmasi supervisor" style="border-radius: 10px; border: 1px solid #E2E8F0; font-size: 13px;" required>{{ $presensi != null ? $presensi->last_correction_reason : '' }}</textarea>
            @if(!empty($presensi?->last_corrected_at))
                <div class="mt-1 text-muted" style="font-size: 11px;">
                    <i class="ti ti-history me-1"></i>Koreksi terakhir: {{ date('d/m/Y H:i', strtotime($presensi->last_corrected_at)) }}
                    @if(!empty($presensi->corrector?->name)) oleh {{ $presensi->corrector->name }} @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
        <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal" style="height: 38px; border-radius: 10px; font-weight: 500;">Batal</button>
        <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4" id="btnSimpan" style="height: 38px; border-radius: 10px; font-weight: 600;">
            <i class="ti ti-device-floppy"></i>
            <span>Simpan Koreksi</span>
        </button>
    </div>
</form>

<script>
    $(function() {
        function toggleJamFields() {
            var status = $('#status').val();
            if (status !== 'h') {
                $('#jam_in, #jam_out').prop('disabled', true);
                $('.section-jam-hadir').css('opacity', '0.45');
            } else {
                $('#jam_in, #jam_out').prop('disabled', false);
                $('.section-jam-hadir').css('opacity', '1');
            }
        }
        toggleJamFields();

        $('#status').on('change', function() {
            toggleJamFields();
        });

        // Ensure global timepicker activates cleanly for the dynamic modal
        if (typeof initGlobalFlatpickr === 'function') {
            initGlobalFlatpickr('#formEditPresensi');
        }

        $('#formEditPresensi').submit(function(e) {
            let status = $('#status').val();
            let kode_jam_kerja = $('#kode_jam_kerja').val();
            let alasan = ($('#alasan_koreksi').val() || '').trim();

            var themeColor = getComputedStyle(document.documentElement).getPropertyValue('--theme-color-1').trim() || '#3C2A21';
            if (!status) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Status Belum Dipilih',
                    text: 'Status kehadiran wajib dipilih!',
                    confirmButtonColor: themeColor
                });
                return false;
            }
            if (!kode_jam_kerja) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Jam Kerja Belum Dipilih',
                    text: 'Shift jam kerja wajib dipilih!',
                    confirmButtonColor: themeColor
                });
                return false;
            }
            if (!alasan) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Alasan Belum Diisi',
                    text: 'Alasan koreksi presensi wajib diisi!',
                    confirmButtonColor: themeColor
                });
                return false;
            }
        });
    });
</script>
