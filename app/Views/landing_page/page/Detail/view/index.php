<?= $this->extend('landing_page/layout/view/index'); ?>

<?= $this->section('content'); ?>

<!-- <section class="py-5">
    <div class="container">
        <div class="row history_detail px-3 py-4 px-md-5 py-md-5 mx-2 mt-mx-0">
            
            <div class="col-lg-5">
                <a href="" class="glightbox" id="main_image_link">
                    <img id="main_image" src="assets/default.jpg" class="img-fluid rounded shadow" alt="Gambar Utama" style="width: 100%; height: 300px; object-fit: cover;">
                </a>

                
                <div id="image_thumbnails_carousel" class="carousel slide mt-3 p-3" data-bs-ride="carousel">
                    <div class="carousel-inner" id="carousel-inner-thumbs"></div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#image_thumbnails_carousel" data-bs-slide="prev">
                        <div class="bg-green rounded p-1 d-flex justify-content-center align-items-center position-relative" style="right: 20px;">
                            <span class="carousel-control-prev-icon"></span>
                        </div>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#image_thumbnails_carousel" data-bs-slide="next">
                        <div class="bg-green rounded p-1 d-flex justify-content-center align-items-center position-relative" style="left: 20px;">
                            <span class="carousel-control-next-icon"></span>
                        </div>
                    </button>
                </div>
            </div>

            
            <div class="col-lg-7 mt-4 mt-lg-0">
                <h2 id="program_name" class="fw-bold"></h2>
                <p class="text-muted mt-4"><strong>Ranting:</strong> <span id="branch_name"></span></p>
                <p class="text-muted"><strong>Tanggal:</strong> <span id="date"></span></p>
                <p id="description" class="lead fs-6" style="text-align: justify;"></p>
            </div>
        </div>
    </div>
</section> -->

<section class="py-5">
  <div class="container">
    <div class="history_detail px-3 py-4 px-md-5 py-md-5 mx-2 mt-mx-0">
      <!-- Wrapper Gambar dan Deskripsi -->
      <div class="clearfix">
        <!-- Kolom Kiri: Gambar -->
        <div class="float-lg-start me-lg-4 mb-3 mb-lg-0 images-wrapper">
            <a href="" class="glightbox" id="main_image_link">
            <img id="main_image" src="assets/default.jpg" class="img-fluid rounded shadow w-100" alt="Gambar Utama" style="height: 300px; object-fit: cover;">
          </a>

          <!-- Carousel Thumbnail -->
          <div id="image_thumbnails_carousel" class="carousel slide mt-3 p-3 mx-0 mx-md-4 mx-lg-0" data-bs-ride="carousel">
            <div class="carousel-inner" id="carousel-inner-thumbs"></div>
            <button class="carousel-control-prev" type="button" data-bs-target="#image_thumbnails_carousel" data-bs-slide="prev">
              <div class="bg-green rounded p-1 d-flex justify-content-center align-items-center position-relative control-prev">
                <span class="carousel-control-prev-icon"></span>
              </div>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#image_thumbnails_carousel" data-bs-slide="next">
              <div class="bg-green rounded p-1 d-flex justify-content-center align-items-center position-relative control-next">
                <span class="carousel-control-next-icon"></span>
              </div>
            </button>
          </div>
        </div>

        <!-- Kolom Kanan: Informasi -->
        <div class="content">
          <h2 id="program_name" class="fw-bold"></h2>
          <p class="text-muted mt-4"><strong>Ranting:</strong> <span id="branch_name"></span></p>
          <p class="text-muted"><strong>Tanggal:</strong> <span id="date"></span></p>
          <p id="description" class="lead fs-6" style="text-align: justify;"></p>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- Distributon -->
<section id="distribution" class="py-5 mt-2">
    <div class="container">
        <h1 class="text-center text-lg-start fw-bold text-green mb-3 w-100">Berita Penyaluran Dana Lainnya</h1>
    </div>
    <div class="container-swiper mx-2 mx-md-5 mx-lg-0">
        <div class="container-content swiper">
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

<button id="share_btn" class="bg-green text-white rounded share-btn">
    <span class="iconify" data-icon="ri:share-forward-fill"></span> Bagikan
</button>

<div class="position-absolute d-none d-lg-block leaf5">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf1">
</div>

<div class="position-absolute d-none d-lg-block leaf6">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf2">
</div>

<div class="position-absolute d-none d-lg-block leaf7">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf3" style="transform: scaleX(-1);">
</div>

<div class="position-absolute d-none d-lg-block leaf8">
    <img src="<?= base_url('assets/themes/landing_page/images/leaf.png') ?>" alt="leaf4" style="transform: scaleX(-1);">
</div>


<script src="/assets/themes/landing_page/js/detail.js"></script>

<?= $this->endSection(); ?>