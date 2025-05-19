<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- PAGE TITLE HERE -->
	<!-- Layout container -->
    <title><?= esc($page_title) ?></title>

    <!-- SEO Friendly -->
    <meta name="description" content="Lazisnu Jamsaren Kediri menjadi jembatan kebaikan, menyalurkan donasi untuk yatim, piatu, dhuafa, serta membantu kebutuhan sosial dan santunan kematian.">
    <meta name="author" content="Lazisnu">
    <meta name="robots" content="index, follow">

    <!-- Favicon -->
    <link rel="icon" href="<?= base_url('/assets/themes/landing_page/images/logonu.png')?>" type="image/x-icon">

    <!-- jQuery CDN -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Iconify/icon -->
    <script src="https://code.iconify.design/3/3.1.1/iconify.min.js"></script>

    <!-- Font -->
    <!-- <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@300;400;700&display=swap" rel="stylesheet"> -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">


    <!-- Toastify -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    
    <!-- datatable -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.css">

    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
    <link href="https://unpkg.com/filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.css" rel="stylesheet">

    <!-- Glightbox -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/themes/landing_page/css/swiper-bundle.min.css') ?>" />

    <!-- SAL.js -->
    <link rel="stylesheet" href="https://unpkg.com/sal.js/dist/sal.css" />
    <!-- Smooth Scrollbar -->
    <script src="https://cdn.jsdelivr.net/npm/smooth-scrollbar@8.7.4/dist/smooth-scrollbar.js"></script>
    <!-- SAL.js -->
    <script src="https://unpkg.com/sal.js/dist/sal.js"></script>

    <!-- Style css -->
    <link rel="stylesheet" href="<?= base_url('assets/themes/landing_page/css/style.css') ?>">
    
</head>
<body>

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <!-- Loader -->
    <div id="loading">
        <div class="spinner"></div>
    </div>

    <!-- content -->
    <div id="content">
        <?= $this->include('landing_page/layout/view/header'); ?>
        <div id="scrollbar">
            <div id="my-scrollbar">
                <div class="scroll-content">
                    <?= $this->renderSection('content');?>

                    <?= $this->include('landing_page/layout/view/footer'); ?>
                </div>
            </div>
        </div>
    </div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- FilePond js -->
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.js"></script>
    <script src="https://unpkg.com/jquery-filepond/filepond.jquery.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-exif-orientation/dist/filepond-plugin-image-exif-orientation.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-pdf-preview/dist/filepond-plugin-pdf-preview.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.min.js"></script>

    <!-- Glightbox js -->
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <!-- Swiper js -->
    <script src="<?= base_url('assets/themes/landing_page/js/swiper-bundle.min.js') ?>"></script>

    <!-- Typed js -->
    <script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.9"></script>

    <!--**********************************
        Scripts End
    ***********************************-->

    <!-- core app -->
    <?php 
    $folder_directory = str_replace('/', '/', str_replace('view/', '', $folder_directory));
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