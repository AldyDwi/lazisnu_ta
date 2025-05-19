var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'dashboard/';

function loadData() {
  $.ajax({
    url: baseUrl + '/list_data',
    type: 'GET',
    dataType: 'json',
    success: function (response) {
      if (response.data) {
        $('#beneficaries_total').text(
          response.data.beneficaries_total + ' orang'
        );
        $('#citizens_total').text(response.data.citizens_total + ' orang');
        $('#donations_total_monthly').text(
          response.data.donations_total_monthly
        );
        $('#donations_total_overall').text(
          response.data.donations_total_overall
        );
        $('#officers_total').text(response.data.officers_total + ' orang');
        $('#saldo_total').text(response.data.saldo_total);
      }
    },
    error: function (xhr, status, error) {
      console.error('Gagal mengambil data:', error);
    },
  });
}

function formatRupiah(number) {
  return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
}

function loadChart(year) {
  $.ajax({
    url: baseUrl + `/get_chart_data/${year}`,
    method: 'GET',
    success: function (response) {
      var options = {
        series: [
          {
            name: 'Donasi Masuk',
            data: response.debit,
            color: '#A0C878',
          },
          {
            name: 'Pengeluaran',
            data: response.credit,
            color: '#EF5A6F',
          },
        ],
        chart: {
          type: 'area',
          height: 300,
          toolbar: { show: false },
        },
        stroke: {
          width: 3,
          curve: 'smooth',
        },
        fill: {
          type: 'solid',
          opacity: 0.2,
        },
        dataLabels: {
          enabled: false,
          formatter: function (val) {
            return formatRupiah(val);
          },
        },
        xaxis: {
          categories: [
            'Jan',
            'Feb',
            'Mar',
            'Apr',
            'May',
            'Jun',
            'Jul',
            'Aug',
            'Sep',
            'Oct',
            'Nov',
            'Dec',
          ],
        },
        tooltip: {
          theme: 'light',
          y: {
            formatter: function (val) {
              return formatRupiah(val);
            },
          },
        },
      };

      // Hapus chart lama sebelum render ulang
      $('#chart').html('');
      var chart = new ApexCharts(document.querySelector('#chart'), options);
      chart.render();
    },
  });
}

function getYear() {
  $('#SelectYear').select2({
    placeholder: 'Pilih Tahun',
    allowClear: true,
    width: 'resolve',
    theme: 'bootstrap-5',
  });

  // Ambil daftar tahun dari database
  $.ajax({
    url: baseUrl + '/get_year',
    method: 'GET',
    success: function (response) {
      var selectYear = $('#SelectYear');
      selectYear.empty();

      // Tambahkan pilihan default
      selectYear.append(
        '<option value="" disabled selected>Pilih Tahun</option>'
      );

      // Tambahkan data tahun dari response
      response.forEach(function (year) {
        selectYear.append(`<option value="${year.year}">${year.year}</option>`);
      });

      // Set tahun default ke tahun terbaru jika ada data
      var latestYear =
        response.length > 0 ? response[0].year : new Date().getFullYear();
      selectYear.val(latestYear).trigger('change');
      $('#selectedYear').val(latestYear);

      // Load chart pertama kali
      loadChart(latestYear);
    },
  });
}

function loadLatestTransactions() {
  $.ajax({
      url: baseUrl + '/get_latest_transactions',
      method: 'GET',
      success: function (response) {
          let html = '';

          response.transactions.forEach(transaction => {
              let amountClass = transaction.is_credit ? 'text-danger' : 'text-success';

              html += `
                  <div class="border-bottom pb-2 mb-2 fw-semibold">
                      <p class="mb-0">${transaction.name}</p>
                      <div class="d-flex justify-content-between">
                          <p class="mb-0 ${amountClass}">${transaction.amount}</p>
                          <p class="mb-0 text-muted">${transaction.date}</p>
                      </div>
                      <div class="d-flex justify-content-between">
                          <p class="mb-0">${transaction.type}</p>
                          <p class="mb-0">${transaction.branch}</p>
                      </div>
                  </div>
              `;
          });

          $('#latest-transactions').html(html);
      }
  });
}

$(document).ready(function () {
  NProgress.start();
  loadData();
  getYear();
  loadLatestTransactions();
  NProgress.done();
});

$('#SelectYear').on('change', function () {
  var selectedYear = $(this).val();
  $('#selectedYear').val(selectedYear);
  loadChart(selectedYear);
});
