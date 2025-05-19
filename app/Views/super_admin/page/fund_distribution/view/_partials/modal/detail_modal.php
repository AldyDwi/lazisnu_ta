<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Detail Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseDetail"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Kolom Informasi Program -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <h5 class="fw-bold">Informasi Program</h5>
                            <div class="row g-2">
                                <div class="col-5 fw-bold">Program</div>
                                <div class="col-1 text-center fw-bold">:</div>
                                <div class="col-6 fw-bold" id="detailProgram"></div>

                                <div class="col-5 fw-bold">Ranting</div>
                                <div class="col-1 text-center fw-bold">:</div>
                                <div class="col-6 fw-bold" id="detailBranch"></div>

                                <div class="col-5 fw-bold">Kredit</div>
                                <div class="col-1 text-center fw-bold">:</div>
                                <div class="col-6 fw-bold" id="detailCredit"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Kolom Informasi Beneficiaries -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded bg-light">
                            <h5 class="fw-bold">Penerima Manfaat</h5>
                            <ul id="detailBeneficiaries" class="mb-0"></ul>
                        </div>
                    </div>
                </div>

                <!-- Tambahan Informasi -->
                <div class="mt-4 text-center">
                    <p class="fw-semibold">Tanggal Distribusi Dana: <span id="detailDate"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>
