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
                        <label for="EditCitizen" class="form-label fw-bold fs-6">Warga</label>
                        <select id="EditCitizen" name="citizen_id" class="form-select">
                        </select>
                        <input type="hidden" id="selectedCitizen">
                        <div id="errorEditCitizen" class="invalid-feedback"></div>
                    </div>
                    <div class="mb-3">
                        <label for="EditDebit" class="form-label fw-bold fs-6">Nominal Uang</label>
                        <input type="text" class="form-control" id="EditDebit" name="debit" placeholder="Edit nominal uang">
                        <p id="errorEditDebit" class="text-danger small"></p>
                    </div>
                    <div class="mb-3">
                        <label for="EditNote" class="form-label fw-bold fs-6">Catatan <span class="text-warning">(Biarkan kosong jika tidak ada catatan)</span></label>
                        <textarea class="form-control" id="EditNote" name="note" rows="3" placeholder="Edit catatan"></textarea>
                        <p id="errorEditNote" class="text-danger small"></p>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold fs-6">Ubah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
