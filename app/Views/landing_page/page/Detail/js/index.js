$(document).ready(function () {
  loadDetail();
  loadDistributionData();
  share();
});

function loadDetail() {
  const slug = window.location.pathname.split('/').pop();

  $.ajax({
    url: '/get_detail',
    type: 'GET',
    data: { slug: slug },
    dataType: 'json',
    success: function (response) {
      if (!response.status) {
        Toastify({
          text: response.message,
          duration: 3000,
          gravity: 'top',
          position: 'right',
          style: { background: '#E53E3E' },
        }).showToast();
        return;
      }

      $('#program_name').text(response.distribution_page.program_name);
      $('#branch_name').text(response.distribution_page.branch_name);
      $('#description').html(response.distribution_page.description);
      let date = new Date(response.distribution_page.date);
      $('#date').text(
        date.toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'long',
          year: 'numeric',
        })
      );

      // Load Gambar Utama & Thumbnail
      const perSlide = 3;
      const chunked = chunkArray(response.distribution_image, perSlide);

      let carouselHtml = '';
      if (response.distribution_image.length > 0) {
        let mainImage = response.distribution_image[0];
        $('#main_image').attr('src', mainImage);
        $('#main_image_link').attr('href', mainImage);

        chunked.forEach((group, index) => {
          carouselHtml += `<div class="carousel-item ${index === 0 ? 'active' : ''}">
            <div class="d-flex justify-content-center gap-2">`;
        
          group.forEach((image, i) => {
            carouselHtml += `
              <a href="${image}" class="img-detail d-flex justify-content-center align-items-center">
                <img src="${image}" class="img-thumbnail bg-gray" style="height: 100px; width: 150px;">
              </a>
            `;
          });

          // Tambah placeholder jika kurang dari 3
          const placeholders = 3 - group.length;
          for (let i = 0; i < placeholders; i++) {
            carouselHtml += `
              <div style="width: 150px; height: 100px;"></div>
            `;
          }
        
          carouselHtml += `</div></div>`;
        });
      }

      // $('#image_thumbnails').html(imagesHtml);
      $('#carousel-inner-thumbs').html(carouselHtml);

      // Inisialisasi Glightbox
      let lightbox;
      lightbox = GLightbox({ selector: '.glightbox' });

      // Event ganti gambar utama
      $('#carousel-inner-thumbs').on('click', '.img-detail', function (e) {
        e.preventDefault();
        const newSrc = $(this).attr('href');
        $('#main_image').attr('src', newSrc);
        $('#main_image_link').attr('href', newSrc);

        // Re-inisialisasi GLightbox
        if (lightbox) {
          lightbox.destroy();
        }
        lightbox = GLightbox({ selector: '.glightbox' });
      });
    },
    error: function (response) {
      Toastify({
        text: 'Gagal mengambil detail data.',
        duration: 3000,
        gravity: 'top',
        position: 'right',
        style: { background: '#E53E3E' },
      }).showToast();
    },
  });
}

function loadDistributionData() {
  $.ajax({
    url: '/detail_list',
    type: 'GET',
    dataType: 'json',
    success: function (response) {
      let html = '';
      response.data.forEach(function (item) {
        let date = new Date(item.date);
        let formattedDate = date.toLocaleDateString('id-ID', {
          day: '2-digit',
          month: 'long',
          year: 'numeric',
        });
        html += `
                  <div class="card-swiper swiper-slide bg-white">
                      <div class="image-box">
                          <img src="${item.image}" alt="Foto" />
                      </div>
                      <div class="details">
                          <p class="fw-bold fs-5 mb-3 card-title">${truncateText(item.program_name, 39)}</p>
                          <p class="mb-3 secondary">${formattedDate}</p>
                          <a href="/detail/${item.slug}" class="btn btn-green w-100 fw-bold text-white">Detail</a>
                      </div>
                  </div>
              `;
      });
      $('#distribution_list').html(html);
    },
    error: function () {
      Toastify({
        text: 'Gagal mengambil data riwayat penyaluran.',
        duration: 3000,
        gravity: 'top',
        position: 'right',
        style: { background: '#E53E3E' },
      }).showToast();
    },
  });
}

function share() {
  const scrollbar = Scrollbar.init(document.querySelector('#my-scrollbar'), {
    damping: 0.07,
  });

  // Panggil sekali saat halaman dimuat
  updateShareButton(scrollbar.offset.y);

  // Lalu dengarkan scroll
  scrollbar.addListener(({ offset }) => {
    updateShareButton(offset.y);
  });

  // Tombol share
  document.getElementById('share_btn').addEventListener('click', async () => {
    if (navigator.share) {
      await navigator.share({
        title: document.getElementById('program_name').textContent,
        text: 'Lihat penyaluran donasi ini!',
        url: window.location.href,
      });
    } else {
      Toastify({
        text: 'Fitur berbagi tidak didukung di browser ini.',
        duration: 3000,
        gravity: 'top',
        position: 'right',
        style: {
          background: '#E53E3E',
        },
        stopOnFocus: true,
      }).showToast();
    }
  });
}

const shareBtn = document.getElementById('share_btn');

// posisi tombol share saat halaman dimuat
function updateShareButton(scrollY) {
  shareBtn.style.top = `${scrollY + window.innerHeight - 100}px`;
  shareBtn.style.right = '40px';
  shareBtn.style.opacity = 1;
}

function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text;
  return text.slice(0, maxLength) + '...';
}

function chunkArray(array, chunkSize) {
  const chunks = [];
  for (let i = 0; i < array.length; i += chunkSize) {
    chunks.push(array.slice(i, i + chunkSize));
  }
  return chunks;
}

function getThumbsPerSlide() {
  return 3;
}