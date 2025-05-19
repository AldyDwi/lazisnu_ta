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
                        <label for="CreateName" class="form-label fw-bold fs-6">Nama Program</label>
                        <input type="text" class="form-control" id="CreateName" name="name" placeholder="Tambah nama program">
                        <p id="errorCreateName" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="CreatePersentage" class="form-label fw-bold fs-6">Persentase <span class="text-warning">(Jika tidak ada persentase maka diisi 0)</span></label>
                        <input type="text" class="form-control" id="CreatePercentage" name="percentage" placeholder="Tambah persentase pengeluaran">
                        <p id="errorCreateName" class="invalid-feedback text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="CreateType" class="form-label fw-bold fs-6">Jenis Program</label>
                        <select id="CreateType" name="type" class="form-select">
                            <option value="routine">Routine</option>
                            <option value="incidental">Incidental</option>
                        </select>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
