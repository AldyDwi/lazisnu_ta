<?= $this->extend("super_admin/layout/view/index") ?>
<?= $this->section('content'); ?>
<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-body">
            <h1 class="h4 fw-bold mb-3">Data Kelurahan</h1>
            <button type="button" class="btn btn-primary mb-3 text-white fw-semibold" id="btnCreate">
                Tambah Data
            </button>
            <div class="table-responsive">
                <table id="data-table" class="table table-striped text-dark fw-semibold" style="width:100%;">
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->include($folder_directory . '_partials\\modal\\input_modal') ?>
<?= $this->include($folder_directory . '_partials\\modal\\edit_modal') ?>


<?= $this->endSection('content'); ?>
