<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Edit Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseEdit"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit">
                    <input type="hidden" id="EditId" name="id">
                    <div class="mb-3">
                        <label for="EditName" class="form-label fw-bold fs-6">Nama Warga</label>
                        <input type="text" class="form-control" id="EditName" name="name" placeholder="Tambah nama warga">
                        <p id="errorEditName" class="text-danger small"></p>
                    </div>

                    <div class="mb-3">
                        <label for="EditPhone" class="form-label fw-bold fs-6">Nomor Telepon</label>
                        <input type="text" class="form-control" id="EditPhone" name="phone" placeholder="Masukkan nomor telepon">
                        <p id="errorEditPhone" class="text-danger small"></p>
                    </div>

                    <!-- Bagian untuk Luar RW -->
                    <div class="mb-3">
                        <label for="EditAddress" class="form-label fw-bold fs-6">Alamat</label>
                        <textarea class="form-control" id="EditAddress" name="address" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                        <p id="errorEditAddress" class="text-danger small"></p>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold fs-6">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>