$(document).ready(function () {
  setTimeout(() => {
    loadDonations();
  }, 10000);

  loadStatistic();
  loadPrograms();
  loadDistributionData();
});

function showDonationToast(donations) {
  let index = 0;

  function showNextToast() {
    if (donations.length === 0) return;

    Toastify({
      text: `<span class="d-flex justify-content-between align-items-center w-100">
                        <span class="iconify fs-3" data-icon="mdi:donation"></span>
                        <b class="ms-3">${donations[index].name} telah berdonasi!</b>
                   </span>`,
      duration: 3000,
      gravity: 'top',
      position: 'right',
      offset: { y: '70px' },
      style: {
        background: '#009688',
        borderRadius: '8px',
        minWidth: '250px',
        width: '270px',
        boxShadow: '0px 4px 10px rgba(0, 0, 0, 0.2)',
      },
      stopOnFocus: true,
      escapeMarkup: false,
    }).showToast();

    index = (index + 1) % donations.length;

    setTimeout(showNextToast, 30000);
  }

  showNextToast();
}

function fetchData(type, callback) {
  $.ajax({
    url: '/get_data',
    method: 'GET',
    data: { type: type },
    dataType: 'json',
    success: function (response) {
      if (response.success) {
        callback(response.data);
      } else {
        console.error('Error:', response.message);
      }
    },
    error: function (xhr, status, error) {
      console.error('Error fetching data:', error);
    },
  });
}

// Load citizens data
function loadDonations() {
  fetchData('citizens', function (data) {
    if (data.length > 0) {
      showDonationToast(data);
    }
  });
}

// Load statistics data
function loadStatistic() {
  fetchData('statistics', function (data) {
    $('#beneficaries_total').text(data.beneficaries_total + ' orang');
    // $('#beneficaries_monthly').text(data.beneficaries_monthly);
    $('#citizens_total').text(data.citizens_total + ' orang');
    $('#saldo_total').text(data.saldo_total);
  });
}

// Load programs data
async function loadPrograms() {
  fetchData('programs', function (data) {
    let html = '';
    if (data.length > 0) {
      data.forEach((program) => {
        html += `
          <div class="col-md-6 col-lg-4 mb-3">
            <div class="bg-gray text-white p-4 rounded d-flex align-items-center" style="min-height: 110px;">
              <div class="flex-shrink-0 me-3">
                <img src="${program.image}" alt="Icon" class="img-fluid" style="width: 50px; height: 50px;">
              </div>
              <p class="fw-bold mb-0 fs-5">${program.program_name}</p>
            </div>
          </div>`;
      });
    } else {
      html =
        '<p class="text-center text-muted">Tidak ada program tersedia.</p>';
    }
    $('#program_list').html(html);
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

function truncateText(text, maxLength) {
  if (text.length <= maxLength) return text;
  return text.slice(0, maxLength) + '...';
}
