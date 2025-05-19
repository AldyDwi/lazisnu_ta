<?= $this->extend("admin/layout/view/index") ?>
<?= $this->section('content'); ?>
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-body">
            <h1 class="h4 fw-bold mb-3">Laporan Bulan Ini</h1>
            <div class="row mb-3">
                <div class="col-lg-6 d-flex align-items-center justify-content-center justify-content-lg-start">
                    <button type="button" class="btn fw-semibold mb-3 mb-lg-0 me-2 px-4 btn-success" id="btnExcel">
                        Export Excel
                    </button>
                    <button type="button" class="btn fw-semibold mb-3 mb-lg-0 px-4 btn-warning" id="btnPDF">
                    Export PDF
                    </button>
                </div>

                <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end flex-wrap">
                    <form id="filter-form" class="d-flex flex-column flex-md-row align-items-center gap-2">
                        <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                            <label for="start_date" class="form-label mb-0">Dari:</label>
                            <input type="text" id="start_date" name="start_date" class="form-control w-auto">
                        </div>

                        <div class="d-flex flex-column flex-md-row align-items-center gap-2">
                            <label for="end_date" class="form-label mb-0">Sampai:</label>
                            <input type="text" id="end_date" name="end_date" class="form-control w-auto">
                        </div>

                        <button type="submit" class="btn btn-secondary text-white fw-semibold">Filter</button>
                    </form>
                </div>
            </div>


            <div class="table-responsive">
                <table id="data-table" class="table table-striped text-dark fw-semibold" style="width:100%;">
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include($folder_directory . '_partials\\modal\\excel_modal') ?>
<?= $this->include($folder_directory . '_partials\\modal\\pdf_modal') ?>


<?= $this->endSection('content'); ?>
