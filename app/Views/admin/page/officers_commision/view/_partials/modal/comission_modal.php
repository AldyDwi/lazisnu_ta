<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Hitung Komisi</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseCreate"></button>
            </div>
            <div class="modal-body">
                <form id="formCreate">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreateMonth" class="form-label fw-bold fs-6">Bulan:</label>
                                <input type="text" class="form-control datepicker-month" id="CreateMonth" name="month" placeholder="Pilih Bulan">
                                <p id="errorCreateMonth" class="text-danger small"></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="CreateYear" class="form-label fw-bold fs-6">Tahun:</label>
                                <input type="text" class="form-control datepicker-year" id="CreateYear" name="year" placeholder="Pilih Tahun">
                                <p id="errorCreateYear" class="text-danger small"></p>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary fw-bold fs-6">Tambah</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
