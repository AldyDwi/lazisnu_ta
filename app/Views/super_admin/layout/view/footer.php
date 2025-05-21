<!-- ...existing code... -->
            
  <!-- Core JS -->
  <!-- build:js assets/vendor/js/core.js -->
  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/vendor/libs/jquery/jquery.js"></script>
  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/vendor/libs/popper/popper.js"></script>
  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/vendor/js/bootstrap.js"></script>
  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/vendor/js/menu.js"></script>
  <!-- endbuild -->

  <!-- Vendors JS -->
  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/vendor/libs/apex-charts/apexcharts.js"></script>

  <!-- Main JS -->
  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/js/main.js"></script>

  <!-- Page JS -->
  <script src="<?= base_url() ?>/assets/themes/admin/template/assets/js/dashboards-analytics.js"></script>

  <!-- Place this tag in your head or just before your close body tag. -->
  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

  <!--Datatables -->
  <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

  <!-- FilePond JS -->
  <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
  <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
  <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
  <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
  <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
  <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js"></script>
  <script src="https://unpkg.com/filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.js"></script>
  <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>

  <!-- GLightbox -->
  <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

  <!-- Select2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

  <!-- Cleave.js -->
  <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/cleave.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/cleave.js@1.6.0/dist/addons/cleave-phone.id.js"></script>

  <!-- Litepicker JS -->
  <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/litepicker.js"></script>

  <!-- NProgress JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/nprogress/0.2.0/nprogress.min.js"></script>

  <!-- Bootstrap Datepicker JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

  <!-- Tinymce -->
  <script src="https://cdn.tiny.cloud/1/4wk1su1j1eogwmohvp2j0dl81kqgy1z70k7zq5ki2cpbo8fw/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tinymce/tinymce-jquery@2/dist/tinymce-jquery.min.js"></script>

  <!-- js -->
  <script src="<?= base_url('assets/themes/super_admin/js/main.js') ?>"></script>

<!-- core app -->
<?php 
$folder_directory = str_replace('\\', '/', str_replace('view\\', '', $folder_directory));
$group_folder = explode('/', $folder_directory)[0];
?>
<script src="<?= base_url('assets/modules/'.$group_folder.'/layout/js/configuration.js?v=' . time()) ?>"></script>
<?php 
    foreach($js as $js_file) {
        echo '<script src="'. base_url('assets/modules/'.$folder_directory.'/js/'.$js_file.'.js?v=' . time()) .'"></script>';
    }
?>
</body>

</html>