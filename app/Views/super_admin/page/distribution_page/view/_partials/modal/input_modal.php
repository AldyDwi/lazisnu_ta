<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Tambah Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseCreate"></button>
            </div>
            <div class="modal-body">
                <form id="formCreate" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="CreateProgram" class="form-label fw-bold fs-6">Program</label>
                        <select id="CreateProgram" name="program_id" class="form-select dropdown">
                        </select>
                        <input type="hidden" id="selectedProgram">
                        <div id="errorCreateProgram" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="CreateDate" class="form-label mb-0">Tanggal</label>
                        <input type="text" id="CreateDate" name="date" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="CreateDescription" class="form-label fw-bold fs-6 description">Deskripsi</label>
                        <textarea id="CreateDescription" name="description" class="form-control" rows="5" placeholder="Tambah deskripsi penyaluran"></textarea>
                        <div id="errorCreateDescription" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-4">
                        <label for="CreateImage" class="form-label fw-bold fs-6">Foto Penyaluran <span class="text-warning">(Bisa lebih dari 1)</span></label>
                        <input type="file" id="CreateImage" name="images[]" multiple>
                        <div id="errorCreateImage" class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6" id="btnSubmitSave">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>