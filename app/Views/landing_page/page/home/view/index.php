<?= $this->extend('landing_page/layout/view/index'); ?>

<?= $this->section('content'); ?>


<!-- amanah dan transparan -->
<!-- Halaman Awal -->
<section id="home" class="px-3 px-md-0 py-5 py-md-5">
    <div class="background-image"></div>
    <div class="container pt-5 mt-2 mt-lg-5">
        <div class="d-flex justify-content-center align-items-center mt-3 mt-lg-5">
            <div class="headline mt-5">
                <h1 class="fw-bold text-center mb-4 text-title">Menyalurkan donasi dengan  <span id="typed" class="typed text-green"></span></h1>
                <p class="fs-3 text-center fw-bold text-green">Bersama Lazisnu, membantu sesama dengan kepedulian.</p>
            </div>
            <div class="donation-box position-absolute border border-3 rounded-4 shadow p-3 fs-5 text-green">
                <div class="d-flex justify-content-center align-items-center mt-2">
                    <p class="fw-semibold">+<span id="beneficaries_monthly">10</span> Penerima donasi bulanan</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="waves">
    <div class="wave wave1"></div>
    <div class="wave wave2"></div>
    <div class="wave wave3"></div>
    <div class="wave wave4"></div>
</section>

<!-- Apa itu Lazisnu -->
<section id="about" class="container px-3 px-md-5 py-5 py-lg-5 mt-lg-4">
    <div class="row d-lg-flex align-items-center">
        <div class="col-lg-6 pe-0 pe-lg-5" data-sal="zoom-in" data-sal-delay="200" data-sal-duration="800">
            <div class="banner-img-wp">
                <div class="banner-img d-none d-lg-block" style="background-image: url(/assets/themes/landing_page/images/foto2.jpg);">
                </div>
            </div>
        </div>
        <div class="col-lg-6" data-sal="zoom-in" data-sal-delay="200" data-sal-duration="800">
            <h1 class="fw-bold text-green text-center text-lg-start mb-4">Apa itu NU-Care Lazisnu Kota Kediri?</h1>
            <p class="fs-5 text-center text-lg-start">
            NU-Care Lazisnu Kota Kediri hadir sebagai jembatan kebaikan, mengumpulkan dan menyalurkan donasi dari masyarakat untuk membantu yatim, piatu, dhuafa, serta mendukung biaya kebutuhan sosial, termasuk santunan kematian. Setiap donasi adalah harapan, setiap uluran tangan adalah cahaya bagi mereka yang membutuhkan. Mari bergandengan tangan dalam kebaikan, karena sekecil apa pun kontribusi Anda akan membawa perubahan besar bagi mereka yang kurang beruntung.
            </p>
        </div>
    </div>
    
</section>

<!-- Jumlah donatur, uang, dll -->
<div class="mt-4 mt-lg-5" style="top: 2px; position: relative;">
    <svg id="wave" style="transform:rotate(0deg); transition: 0.3s" viewBox="0 0 1440 170" version="1.1" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="sw-gradient-0" x1="0" x2="0" y1="1" y2="0"><stop stop-color="#1A1A19" offset="0%"></stop><stop stop-color="#1A1A19" offset="100%"></stop></linearGradient></defs><path style="transform:translate(0, 0px); opacity:1" fill="url(#sw-gradient-0)" d="M0,85L26.7,79.3C53.3,74,107,62,160,65.2C213.3,68,267,85,320,79.3C373.3,74,427,45,480,45.3C533.3,45,587,74,640,82.2C693.3,91,747,79,800,76.5C853.3,74,907,79,960,85C1013.3,91,1067,96,1120,99.2C1173.3,102,1227,102,1280,96.3C1333.3,91,1387,79,1440,79.3C1493.3,79,1547,91,1600,87.8C1653.3,85,1707,68,1760,51C1813.3,34,1867,17,1920,22.7C1973.3,28,2027,57,2080,79.3C2133.3,102,2187,119,2240,124.7C2293.3,130,2347,125,2400,104.8C2453.3,85,2507,51,2560,39.7C2613.3,28,2667,40,2720,45.3C2773.3,51,2827,51,2880,53.8C2933.3,57,2987,62,3040,76.5C3093.3,91,3147,113,3200,127.5C3253.3,142,3307,147,3360,138.8C3413.3,130,3467,108,3520,90.7C3573.3,74,3627,62,3680,65.2C3733.3,68,3787,85,3813,93.5L3840,102L3840,170L3813.3,170C3786.7,170,3733,170,3680,170C3626.7,170,3573,170,3520,170C3466.7,170,3413,170,3360,170C3306.7,170,3253,170,3200,170C3146.7,170,3093,170,3040,170C2986.7,170,2933,170,2880,170C2826.7,170,2773,170,2720,170C2666.7,170,2613,170,2560,170C2506.7,170,2453,170,2400,170C2346.7,170,2293,170,2240,170C2186.7,170,2133,170,2080,170C2026.7,170,1973,170,1920,170C1866.7,170,1813,170,1760,170C1706.7,170,1653,170,1600,170C1546.7,170,1493,170,1440,170C1386.7,170,1333,170,1280,170C1226.7,170,1173,170,1120,170C1066.7,170,1013,170,960,170C906.7,170,853,170,800,170C746.7,170,693,170,640,170C586.7,170,533,170,480,170C426.7,170,373,170,320,170C266.7,170,213,170,160,170C106.7,170,53,170,27,170L0,170Z"></path></svg>
</div>

<section id="donation" class="px-3 px-md-5 pb-5 bg-gray">
    <div class="container py-5">
        <h1 class="fw-bold text-green text-center pt-5 pt-lg-2" data-sal="slide-down"  data-sal-duration="800">Statistik Donasi Terkini</h1>
        <div class="row justify-content-center mt-5">
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="shadow rounded p-4 pt-5 text-center bg-white">
                    <div class="d-flex justify-content-center" data-sal="zoom-in" data-sal-delay="200" data-sal-duration="800">
                        <div class="p-3 p-lg-4 d-flex justify-content-center align-items-center">
                             <img src="<?= base_url('assets/themes/landing_page/images/food-donation.png') ?>" alt="donasi1" class="w-50 img-statistic">
                        </div>
                    </div>
                    <div class="mt-5 mb-5" id="donatur" data-sal="fade" data-sal-delay="200" data-sal-duration="800">
                        <!-- Diganti ajax -->
                        <h1 class="fw-bold fs-3">+<span id="citizens_total"></span></h1>
                        <p class="fw-bold fs-5">Donatur</p>
                        <!-- Akhir ajax -->
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="shadow rounded p-4 pt-5 text-center bg-white">
                    <div class="d-flex justify-content-center" data-sal="zoom-in" data-sal-delay="400" data-sal-duration="800">
                        <div class="p-3 p-lg-4 d-flex justify-content-center align-items-center">
                            <img src="<?= base_url('assets/themes/landing_page/images/donation.png') ?>" alt="donasi1" class="w-50 img-statistic2">
                        </div>
                    </div>
                    <div class="mt-5 mb-5" id="nominal" data-sal="fade" data-sal-delay="400" data-sal-duration="800">
                        <!-- Diganti ajax -->
                        <h1 class="fw-bold fs-3"><span id="saldo_total"></span></h1>
                        <p class="fw-bold fs-5">Saldo Donasi</p>
                        <!-- Akhir ajax -->
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <div class="shadow rounded p-4 pt-5 text-center bg-white">
                    <div class="d-flex justify-content-center" data-sal="zoom-in" data-sal-delay="600" data-sal-duration="800">
                        <div class="p-3 p-lg-4 d-flex justify-content-center align-items-center">
                            <img src="<?= base_url('assets/themes/landing_page/images/healthcare.png') ?>" alt="donasi1" class="w-50 img-statistic3">
                        </div>
                    </div>
                    <div class="mt-5 mb-5" id="penerima" data-sal="fade" data-sal-delay="600" data-sal-duration="800">
                        <!-- Diganti ajax -->
                        <h1 class="fw-bold fs-3">+<span id="beneficaries_total"></span></h1>
                        <p class="fw-bold fs-5">Penerima</p>
                        <!-- Akhir ajax -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="mb-5" style="top: -2px; position: relative;">
    <svg id="wave" style="transform:rotate(180deg); transition: 0.3s" viewBox="0 0 1440 100" version="1.1" xmlns="http://www.w3.org/2000/svg"><defs><linearGradient id="sw-gradient-0" x1="0" x2="0" y1="1" y2="0"><stop stop-color="#1A1A19" offset="0%"></stop><stop stop-color="#1A1A19" offset="100%"></stop></linearGradient></defs><path style="transform:translate(0, 0px); opacity:1" fill="url(#sw-gradient-0)" d="M0,10L26.7,10C53.3,10,107,10,160,21.7C213.3,33,267,57,320,70C373.3,83,427,87,480,75C533.3,63,587,37,640,35C693.3,33,747,57,800,63.3C853.3,70,907,60,960,61.7C1013.3,63,1067,77,1120,70C1173.3,63,1227,37,1280,30C1333.3,23,1387,37,1440,38.3C1493.3,40,1547,30,1600,30C1653.3,30,1707,40,1760,50C1813.3,60,1867,70,1920,68.3C1973.3,67,2027,53,2080,48.3C2133.3,43,2187,47,2240,53.3C2293.3,60,2347,70,2400,75C2453.3,80,2507,80,2560,68.3C2613.3,57,2667,33,2720,26.7C2773.3,20,2827,30,2880,30C2933.3,30,2987,20,3040,26.7C3093.3,33,3147,57,3200,65C3253.3,73,3307,67,3360,53.3C3413.3,40,3467,20,3520,16.7C3573.3,13,3627,27,3680,38.3C3733.3,50,3787,60,3813,65L3840,70L3840,100L3813.3,100C3786.7,100,3733,100,3680,100C3626.7,100,3573,100,3520,100C3466.7,100,3413,100,3360,100C3306.7,100,3253,100,3200,100C3146.7,100,3093,100,3040,100C2986.7,100,2933,100,2880,100C2826.7,100,2773,100,2720,100C2666.7,100,2613,100,2560,100C2506.7,100,2453,100,2400,100C2346.7,100,2293,100,2240,100C2186.7,100,2133,100,2080,100C2026.7,100,1973,100,1920,100C1866.7,100,1813,100,1760,100C1706.7,100,1653,100,1600,100C1546.7,100,1493,100,1440,100C1386.7,100,1333,100,1280,100C1226.7,100,1173,100,1120,100C1066.7,100,1013,100,960,100C906.7,100,853,100,800,100C746.7,100,693,100,640,100C586.7,100,533,100,480,100C426.7,100,373,100,320,100C266.7,100,213,100,160,100C106.7,100,53,100,27,100L0,100Z"></path></svg>
</div>

<!-- Mengapa Berdonasi di NU-Care Lazisnu? -->
<section class="px-3 py-5 mt-5 container">
    <div class="row align-items-center">
        <div class="col-lg-6 text-center text-lg-start" data-sal="slide-right" data-sal-delay="200" data-sal-duration="800">
            <h1 class="fs-1 fw-bold text-green mb-4">Mengapa berdonasi di NU-Care Lazisnu?</h1>
        </div>
        <div class="col-lg-6 d-flex justify-content-center justify-content-lg-end" data-sal="slide-left" data-sal-delay="200" data-sal-duration="800">
            <div class="bg-gray text-white rounded-3 p-5 w-100" style="max-width: 600px; height: auto;">
                <div class="d-flex align-items-center mb-4 mt-3">
                    <div class="flex-shrink-0">
                        <span class="iconify fs-1 text-green" data-icon="lets-icons:check-fill"></span>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <p class="fw-bold fs-5 mb-0">Transparan - Laporan penggunaan dana tersedia untuk semua donatur.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-4">
                    <div class="flex-shrink-0">
                        <span class="iconify fs-1 text-green" data-icon="lets-icons:check-fill"></span>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <p class="fw-bold fs-5 mb-0">Amanah - 100% donasi Anda sampai kepada yang berhak menerima.</p>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-3">
                    <div class="flex-shrink-0">
                        <span class="iconify fs-1 text-green" data-icon="lets-icons:check-fill"></span>
                    </div>
                    <div class="ms-3 flex-grow-1">
                        <p class="fw-bold fs-5 mb-0">Profesional - Dikelola oleh tim yang kompeten dan berpengalaman di bidangnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Program yang kami lakukan -->
<section id="program" class="container py-5 mt-5">
    <div data-sal="zoom-in" data-sal-delay="200" data-sal-duration="800">
        <h1 class="text-green text-center fw-bold mb-3 display-5">Program yang kami lakukan</h1>
        <h2 class="text-green text-center fw-bold fs-4 mb-5">Agar menjadi yang terbaik</h2>
    </div>
    <div class="row justify-content-center" id="program_list" data-sal="fade" data-sal-delay="400" data-sal-duration="800">
        <!-- Diganti ajax -->
    </div>
</section>

<!-- Distributon -->
<section id="distribution" class="py-5 mt-5">
    <div class="container" data-sal="slide-left" data-sal-delay="200" data-sal-duration="800">
        <h1 class="text-center text-lg-start fw-bold text-green mb-3 w-100">Berita Penyaluran Dana</h1>
        <h3 class="text-center text-lg-start fw-bold text-green mb-3 w-100">Amanah dalam penyaluran</h3>
    </div>
    <div class="container-swiper mx-2 mx-md-5 mx-lg-0" data-sal="fade" data-sal-delay="400" data-sal-duration="800">
        <div class="container-content swiper" >
            <div class="slide-container">
                <div class="card-wrapper swiper-wrapper" id="distribution_list">
                    <!-- Diganti ajax -->
                </div>
            </div>
            <div class="swiper-button-next swiper-navBtn shadow"></div>
            <div class="swiper-button-prev swiper-navBtn shadow"></div>
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>

<div class="position-absolute d-none d-lg-block leaf1" data-sal="fade" data-sal-delay="200" data-sal-duration="800">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf1">
</div>

<div class="position-absolute d-none d-lg-block leaf2" data-sal="fade" data-sal-delay="400" data-sal-duration="800">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf2">
</div>

<div class="position-absolute d-none d-lg-block leaf3" data-sal="fade" data-sal-delay="200" data-sal-duration="800">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf3" style="transform: scaleX(-1);">
</div>

<div class="position-absolute d-none d-lg-block leaf4" data-sal="fade" data-sal-delay="400" data-sal-duration="800">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf4" style="transform: scaleX(-1);">
</div>


<script src="assets/themes/landing_page/js/home.js"></script>

<?= $this->endSection(); ?>