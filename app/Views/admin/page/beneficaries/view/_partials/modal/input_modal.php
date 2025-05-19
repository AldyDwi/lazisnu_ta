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
                        <label for="CreateName" class="form-label fw-bold fs-6">Nama Penerima</label>
                        <input type="text" class="form-control" id="CreateName" name="name" placeholder="Tambah nama penerima">
                        <p id="errorCreateName" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="CreateType" class="form-label fw-bold fs-6">Tipe Penerima</label>
                        <select id="CreateType" name="type_id" class="form-select">
                        </select>
                        <input type="hidden" id="selectedType">
                        <div id="errorCreateType" class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>