<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Edit Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseEdit"></button>
            </div>
            <div class="modal-body">
                <form id="formEdit" enctype="multipart/form-data">
                    <input type="hidden" id="EditId" name="id">
                    <div class="mb-3">
                        <label for="EditProgram" class="form-label fw-bold fs-6">Program</label>
                        <select id="EditProgram" name="program_id" class="form-select">
                        </select>
                        <input type="hidden" id="selectedProgram">
                        <div id="errorEditProgram" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-4">
                        <label for="EditImage" class="form-label fw-bold fs-6">Gambar</label>
                        <input type="file" id="EditImage" name="image">
                        <div id="errorEditImage" class="invalid-feedback"></div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold fs-6">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>