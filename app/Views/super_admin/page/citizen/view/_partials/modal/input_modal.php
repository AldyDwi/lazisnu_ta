<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Tambah Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseCreate"></button>
            </div>
            <div class="modal-body">
                <form id="formCreate">
                    <div class="mb-3">
                        <button type="button" class="btn btn-outline-secondary fw-bold fs-6 w-auto toggleRW">
                            Luar RW / Warga Lainnya?
                        </button>
                    </div>

                    <div class="mb-3">
                        <label for="CreateName" class="form-label fw-bold fs-6">Nama Warga</label>
                        <input type="text" class="form-control" id="CreateName" name="name" placeholder="Tambah nama warga">
                        <p id="errorCreateName" class="text-danger small"></p>
                    </div>

                    <div class="mb-3">
                        <label for="CreatePhone" class="form-label fw-bold fs-6">Nomor Telepon</label>
                        <input type="text" class="form-control" id="CreatePhone" name="phone" placeholder="Masukkan nomor telepon">
                        <p id="errorCreatePhone" class="text-danger small"></p>
                    </div>

                    <div class="rwSection">
                        <div class="mb-3">
                            <label for="CreateRW" class="form-label fw-bold fs-6">RW</label>
                            <select id="CreateRW" name="rw_id" class="form-select dropdown">
                                <!-- Data RW akan dimuat dengan AJAX -->
                            </select>
                            <input type="hidden" id="selectedRW">
                            <div id="errorCreateRW" class="invalid-feedback"></div>
                        </div>
                    </div>

                    <!-- Bagian untuk Luar RW (disembunyikan saat awal) -->
                    <div class="outsideRWSection d-none">
                        <div class="mb-3">
                            <label for="CreateAddress" class="form-label fw-bold fs-6">Alamat</label>
                            <textarea class="form-control" id="CreateAddress" name="address" rows="3" placeholder="Masukkan alamat lengkap"></textarea>
                            <p id="errorCreateAddress" class="text-danger small"></p>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>