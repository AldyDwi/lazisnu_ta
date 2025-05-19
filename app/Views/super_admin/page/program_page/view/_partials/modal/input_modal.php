<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
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
                    <div class="mb-4">
                        <label for="CreateImage" class="form-label fw-bold fs-6">Gambar</label>
                        <input type="file" id="CreateImage" name="image">
                        <div id="errorCreateImage" class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>