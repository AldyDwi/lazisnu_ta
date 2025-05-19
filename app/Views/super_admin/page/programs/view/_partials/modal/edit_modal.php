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
                        <label for="EditName" class="form-label fw-bold fs-6">Nama Program</label>
                        <input type="text" class="form-control" id="EditName" name="name" placeholder="Tambah nama program">
                        <p id="errorEditName" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="EditBranch" class="form-label fw-bold fs-6">Ranting</label>
                        <select id="EditBranch" name="branches_id" class="form-select">
                        </select>
                        <input type="hidden" id="selectedBranch">
                        <div id="errorEditBranch" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="EditPersentage" class="form-label fw-bold fs-6">Persentase <span class="text-warning">(Jika tidak ada persentase maka diisi 0)</span></label>
                        <input type="number" class="form-control" id="EditPercentage" name="percentage" placeholder="Ubah persentase pengeluaran">
                        <p id="errorEditName" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="EditType" class="form-label fw-bold fs-6">Pilih Jenis Program</label>
                        <select id="EditType" name="type" class="form-select">
                            <option value="routine">Routine</option>
                            <option value="incidental">Incidental</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
