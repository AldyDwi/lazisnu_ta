var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'dashboard/';

function loadData(branchId = null) {
  $.ajax({
    url: baseUrl + '/dashboard/list_data',
    type: 'GET',
    dataType: 'json',
    data: { branch_id: branchId },
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
        $('#admins_total').text(response.data.admins_total + ' orang');
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

function loadChart(year, branchId = null) {
  $.ajax({
    url: baseUrl + '/dashboard/get_chart_data/' + year,
    method: 'GET',
    data: { branch_id: branchId },
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

function getYear(branchId = null) {
  $('#SelectYear').select2({
    placeholder: 'Pilih Tahun',
    allowClear: true,
    width: 'resolve',
    theme: 'bootstrap-5',
  });

  // Ambil daftar tahun dari database
  $.ajax({
    url: baseUrl + '/dashboard/get_year',
    method: 'GET',
    data: { branch_id: branchId },
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
      loadChart(latestYear, branchId);
    },
  });
}

function loadLatestTransactions(branchId = null) {
  $.ajax({
    url: baseUrl + '/dashboard/get_latest_transactions',
    method: 'GET',
    data: { branch_id: branchId },
    success: function (response) {
      let html = '';

      response.transactions.forEach((transaction) => {
        let amountClass = transaction.is_credit
          ? 'text-danger'
          : 'text-success';

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
    },
  });
}

function getBranch() {
  $('#SelectBranch').select2({
    placeholder: 'Pilih Ranting',
    allowClear: true,
    width: 'resolve',
    theme: 'bootstrap-5',
  });

  // Ambil daftar branch dari database
  $.ajax({
    url: baseUrl + '/dashboard/get_branch',
    method: 'GET',
    success: function (response) {
      var selectBranch = $('#SelectBranch');
      selectBranch.empty();

      // Tambahkan opsi untuk menampilkan semua data
      selectBranch.append('<option value="">Semua Ranting</option>');

      // Tambahkan data ranting dari response
      response.forEach(function (data) {
        selectBranch.append(
          `<option value="${data.branch_id}">${data.branch_name}</option>`
        );
      });

      // Ambil branch_id dari localStorage, jika tidak ada set default ke "Semua Ranting"
      var savedBranch = localStorage.getItem('selectedBranch') || '';
      selectBranch.val(savedBranch).trigger('change');
    },
  });

  // Event saat memilih branch
  $('#SelectBranch').on('change', function () {
    var branchId = $(this).val(); // "" jika "Semua Ranting" dipilih

    // Simpan ke Local Storage agar tidak hilang saat refresh halaman
    localStorage.setItem('selectedBranch', branchId);

    NProgress.start();
    // Panggil fungsi-fungsi yang membutuhkan data berdasarkan branch_id
    loadData(branchId);
    loadChart(branchId);
    loadLatestTransactions(branchId);

    NProgress.done();
  });
}

$(document).ready(function () {
  getBranch();

  // Ambil branch_id yang disimpan sebelumnya
  var savedBranch = localStorage.getItem('selectedBranch');
  if (savedBranch) {
    loadData(savedBranch);
    loadChart(savedBranch);
    loadLatestTransactions(savedBranch);
  } else {
    loadData('');
    loadChart('');
    loadLatestTransactions('');
  }
});

$('#SelectYear').on('change', function () {
  var selectedYear = $(this).val();
  $('#selectedYear').val(selectedYear);

  var branchId = $('#SelectBranch').val();
  loadChart(selectedYear, branchId);
});

$('#SelectBranch').on('change', function () {
  var branchId = $(this).val();
  getYear(branchId);
});
