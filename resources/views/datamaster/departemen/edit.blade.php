<form action="{{ route('departemen.update', Crypt::encrypt($departemen->kode_dept)) }}" method="POST" id="formDepartemen">
   @csrf
   @method('PUT')
   <x-input-with-icon label="Kode Departemen" name="kode_dept" icon="ti ti-barcode" value="{{ $departemen->kode_dept }}" maxlength="3" placeholder="Maksimal 3 karakter" />
   <x-input-with-icon label="Nama Departemen" name="nama_dept" icon="ti ti-building" value="{{ $departemen->nama_dept }}" maxlength="30" placeholder="Maksimal 30 karakter" required />
   <div class="modal-footer-standard d-flex align-items-center justify-content-end gap-2 mt-4 pt-3 border-top">
      <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">Batal</button>
      <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1.5 px-4">
         <i class="ti ti-device-floppy"></i>
         <span>Simpan Perubahan</span>
      </button>
   </div>
</form>
<script src="{{ asset('assets/js/pages/departemen.js') }}"></script>
