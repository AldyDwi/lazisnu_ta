<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Tambah Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseCreate"></button>
            </div>
            <div class="modal-body">
                <form id="formCreate">
                    <input type="hidden" id="CreateId" name="id">
                    <div class="mb-3">
                        <label for="CreateCitizen" class="form-label fw-bold fs-6">Warga</label>
                        <select id="CreateCitizen" name="citizen_id" class="form-select">
                        </select>
                        <input type="hidden" id="selectedCitizen">
                        <div id="errorCreateCitizen" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="CreateDebit" class="form-label fw-bold fs-6">Nominal Uang</label>
                        <input type="text" class="form-control" id="CreateDebit" name="debit" placeholder="Create nominal uang">
                        <p id="errorCreateDebit" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="CreateNote" class="form-label fw-bold fs-6">Catatan <span class="text-warning">(Biarkan kosong jika tidak ada catatan)</span></label>
                        <textarea class="form-control" id="CreateNote" name="note" rows="3" placeholder="Tambah catatan"></textarea>
                        <p id="errorCreateNote" class="text-danger small"></p>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
