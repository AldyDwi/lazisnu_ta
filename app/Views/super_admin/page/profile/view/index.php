<?= $this->extend("super_admin/layout/view/index") ?>
<?= $this->section('content'); ?>
    <div class="container mt-4">
        <div class="card shadow-lg">
            <div class="card-body">
                <h1 class="h4 fw-bold mb-3">Profil Super Admin</h1>
                <div id="profile-container">
                    
                </div>
                
                <div id="data-table" class="mt-4">
                    <!-- <button class="btn btn-primary fw-bold">Edit Profil</button>
                    <button class="btn btn-warning fw-bold">Edit Password</button> -->
                </div>
            </div>
        </div>
    </div>

<?= $this->include($folder_directory . '_partials\\modal\\edit_password') ?>
<?= $this->include($folder_directory . '_partials\\modal\\edit_modal') ?>


<?= $this->endSection('content'); ?>
