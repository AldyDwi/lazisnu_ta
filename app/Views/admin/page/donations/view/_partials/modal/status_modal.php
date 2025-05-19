<div class="modal fade" id="modalStatus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Update Status</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseStatus"></button>
            </div>
            <div class="modal-body">
                <form id="formStatus">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="StatusMonth" class="form-label fw-bold fs-6">Bulan:</label>
                                <input type="text" class="form-control datepicker-month" id="StatusMonth" name="month" placeholder="Pilih Bulan">
                                <p id="errorStatusMonth" class="text-danger small"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="StatusYear" class="form-label fw-bold fs-6">Tahun:</label>
                                <input type="text" class="form-control datepicker-year" id="StatusYear" name="year" placeholder="Pilih Tahun">
                                <p id="errorStatusYear" class="text-danger small"></p>
                            </div>
                        </div>
                    </div>

                    <div class="row d-flex">
                        <div class="col-md-6 d-flex mb-2">
                            <button type="button" class="btn btn-primary fw-bold fs-6 w-100" id="btnOpen">Buka</button>
                        </div>
                        <div class="col-md-6 d-flex mb-2">
                            <button type="button" class="btn btn-danger fw-bold fs-6 w-100" id="btnClose">Tutup</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
