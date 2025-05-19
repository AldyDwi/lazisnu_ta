<div class="modal fade" id="modalExcel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Export Excel</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseExcel"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold">Jika ingin export semua data, dapat langsung klik Export</p>
                <form id="formExcel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ExcelStart" class="form-label fw-bold fs-6">Mulai:</label>
                                <input type="text" class="form-control" id="ExcelStart" name="start_excel" placeholder="Pilih Tanggal Mulai">
                                <p id="errorExcelStart" class="text-danger small"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ExcelEnd" class="form-label fw-bold fs-6">Sampai:</label>
                                <input type="text" class="form-control" id="ExcelEnd" name="end_excel" placeholder="Pilih Tanggal Selesai">
                                <p id="errorExcelEnd" class="text-danger small"></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
