<div class="modal fade" id="modalPDF" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Export PDF</h5>
                <button type="button" class="btn-close fs-3" id="btnClosePDF"></button>
            </div>
            <div class="modal-body">
                <form id="formPDF">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="PDFMonth" class="form-label fw-bold fs-6">Bulan:</label>
                                <input type="text" class="form-control datepicker-month" id="PDFMonth" name="month_pdf" placeholder="Pilih Bulan">
                                <p id="errorPDFMonth" class="text-danger small"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="PDFYear" class="form-label fw-bold fs-6">Tahun:</label>
                                <input type="text" class="form-control datepicker-year" id="PDFYear" name="year_pdf" placeholder="Pilih Tahun">
                                <p id="errorPDFYear" class="text-danger small"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="PDFBranch" class="form-label fw-bold fs-6">Ranting</label>
                        <select id="PDFBranch" name="branch_id" class="form-select">
                        </select>
                        <input type="hidden" id="selectedBranch">
                        <div id="errorPDFBranch" class="invalid-feedback"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning fw-bold fs-6">Export</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
