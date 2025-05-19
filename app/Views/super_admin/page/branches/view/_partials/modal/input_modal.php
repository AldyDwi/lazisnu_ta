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
                        <label for="CreateName" class="form-label fw-bold fs-6">Nama Ranting</label>
                        <input type="text" class="form-control" id="CreateName" name="name" placeholder="Tambah nama ranting">
                        <p id="errorCreateName" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="CreateRegion" class="form-label fw-bold fs-6">Kelurahan</label>
                        <select id="CreateRegion" name="region_id" class="form-select dropdown">
                        </select>
                        <input type="hidden" id="selectedRegion">
                        <div id="errorCreateRegion" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                         <label for="CreateAddress" class="form-label fw-bold fs-6">Alamat Lengkap</label>
                         <textarea id="CreateAddress" name="address" class="form-control" rows="3" placeholder="Tambah alamat lengkap"></textarea>
                         <div id="errorCreateAddress" class="invalid-feedback"></div>
                     </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>