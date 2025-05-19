<div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold fs-4">Detail Data</h5>
                <button type="button" class="btn-close fs-3" id="btnCloseDetail"></button>
            </div>
            <div class="modal-body">
                
                    <!-- Kolom Informasi Program -->
                    <div class="col-md-12 mt-3">
                        <div class="p-3 border rounded bg-light">
                            <h5 class="fw-bold">Informasi Program</h5>
                            <div class="row g-2">
                                <div class="col-2 fw-bold">Program</div>
                                <div class="col-1 text-center fw-bold">:</div>
                                <div class="col-9 fw-bold" id="detailProgram"></div>

                                <div class="col-2 fw-bold">Ranting</div>
                                <div class="col-1 text-center fw-bold">:</div>
                                <div class="col-9 fw-bold" id="detailBranch"></div>

                                <div class="col-2 fw-bold">Tanggal</div>
                                <div class="col-1 text-center fw-bold">:</div>
                                <div class="col-9 fw-bold" id="detailDate"></div>
                            </div>
                        </div>
                    </div>
                

                <!-- Kolom Deskripsi -->
                <div class="col-md-12 mt-3">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold">Deskripsi</h5>
                        <div id="detailDescription" class="fw-bold"></div>
                    </div>
                </div>

                <!-- Kolom Informasi Gambar -->
                <div class="col-md-12 mt-3">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="fw-bold">Foto Penyaluran</h5>
                        <div id="detailImages" class="d-flex flex-wrap"></div>
                    </div>
                </div>
        </div>
    </div>
</div>
