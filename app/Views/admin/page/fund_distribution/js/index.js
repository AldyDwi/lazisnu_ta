var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'fund_distribution/';

function loadData(startDate = '', endDate = '') {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data-table')) {
      $('#data-table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/list_data',
        type: 'GET',
        data: {
          start_date: startDate,
          end_date: endDate,
        },
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 1, title: 'Nama Program' },
        { data: 2, title: 'Kredit' },
        { data: 3, title: 'Ranting' },
        { data: 4, title: 'Tanggal' },
        {
          data: 5,
          title: 'Aksi',
          orderable: false,
          searchable: false,
        },
      ],
      destroy: true,
      responsive: true,
      serverside: true,
      headerCallback: function (thead, data, start, end, display) {
        $(thead).find('th').css({
          'font-weight': 'bold',
          'font-size': '15px',
          'text-transform': 'uppercase',
        });
      },
    });
  });
}

function fetchTotalMonthlyIncome(callback) {
  $.ajax({
      url: baseUrl + '/get-income',
      type: 'GET',
      success: function (response) {
          let totalIncome = parseInt(response.total_monthly_income.replace(/\./g, '')) || 0;
          console.log("Total Monthly Income (Parsed):", totalIncome);
          if (callback) callback(totalIncome);
      },
      error: function () {
          console.error('Gagal mengambil total pemasukan');
          if (callback) callback(0);
      }
  });
}

// Fungsi untuk mengisi input kredit berdasarkan program yang dipilih
function updateCreditBasedOnProgram(selectedData) {
  let percentage = selectedData.percentage || 0;

  if (percentage > 0) {
    fetchTotalMonthlyIncome(function (totalIncome) {
      let calculatedCredit = (percentage / 100) * totalIncome;
      $('#CreateCredit').val(calculatedCredit.toFixed(0)); // Format tanpa desimal
      $('#CreateCredit').prop('readonly', true);
    });
  } 
  else {
    $('#CreateCredit').val(''); // Kosongkan jika persentase 0%
    $('#CreateCredit').prop('readonly', false); // Input bisa diisi manual
  }
}

function updateCreditBasedOnProgramEdit(percentage) {
  if (percentage > 0) {
    fetchTotalMonthlyIncome(function (totalIncome) {
      $('#EditCredit').prop('readonly', true);
    });
  } 
  else {
    $('#EditCredit').prop('readonly', false); // Input bisa diisi manual
  }
}

function loadBeneficaries(callback) {
  $.ajax({
      url: '/beneficaries/list_data',
      type: 'GET',
      success: function (response) {
          let beneficariesList = $('.beneficariesList');
          beneficariesList.empty();

          if (response.data.length > 0) {
              response.data.forEach(function (item) {
                  let checkbox = `
                      <div class="form-check mb-1">
                          <input class="form-check-input" type="checkbox" name="beneficaries[]" value="${item[0]}" id="beneficary_${item[0]}">
                          <label class="form-check-label fw-bold" for="beneficary_${item[0]}">
                              ${item[1]} (${item[2]}) - Ranting ${item[3]}
                          </label>
                      </div>
                  `;
                  beneficariesList.append(checkbox);
              });

              // Jalankan callback setelah daftar beneficiaries dimuat
              if (typeof callback === "function") {
                  callback();
              }
          } else {
              beneficariesList.append('<p class="text-muted">Tidak ada beneficiaries tersedia.</p>');
          }
      },
      error: function () {
          console.error('Gagal mengambil daftar beneficiaries');
      }
  });
}

function detail(id) {
  $.ajax({
      url: baseUrl + '/get-detail',
      type: 'GET',
      data: { id: id },
      dataType: 'json',
      success: function (response) {
          if (response.status) {
              let data = response.transaction;
              let beneficiaries = response.beneficiaries;

              // Masukkan data ke dalam modal
              $('#detailProgram').text(data.program_name);
              $('#detailBranch').text(data.branch_name);
              $('#detailCredit').text('Rp ' + new Intl.NumberFormat().format(data.credit));
              let date = new Date(data.created_at);
              $('#detailDate').text(date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' }));

              // Kosongkan dan tambahkan beneficiaries ke dalam daftar
              $('#detailBeneficiaries').empty();
              if (beneficiaries.length > 0) {
                  beneficiaries.forEach(name => {
                      $('#detailBeneficiaries').append(`<li class="fw-semibold">${name}</li>`);
                  });
              } else {
                  $('#detailBeneficiaries').append('<li class="fw-semibold">Tidak ada</li>');
              }

              // Tampilkan modal
              $('#modalDetail').modal('show');
          } else {
              Toastify({
                text: 'Gagal mengambil data.',
                duration: 3000,
                gravity: 'top',
                position: 'right',
                style: {
                  background: '#E53E3E',
                },
                stopOnFocus: true,
              }).showToast();
          }
      },
      error: function () {
          Toastify({
            text: 'Terjadi kesalahan dalam mengambil data.',
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

$(document).ready(function () {
  NProgress.start();
  loadData();
  loadBeneficaries();
  NProgress.done();
  datepicker('#start_date');
  datepicker('#end_date');
});

// Date FIlter
$('#filter-form').on('submit', function (e) {
  e.preventDefault();
  let startDate = $('#start_date').val();
  let endDate = $('#end_date').val();
  
  NProgress.start();
  loadData(startDate, endDate);
  NProgress.done();
});

// Detail Data
$(document).on('click', '#btnDetail', function () {
  let id = $(this).data('id');

  NProgress.start();
  detail(id);
  NProgress.done();
});

$('#btnCloseDetail').click(function () {
  closeModal('modalDetail');
});

// Create Data
$('#btnCreate').click(function () {
  showModal('modalCreate', 'formCreate', ['CreateProgram', 'CreateCredit']);
  cleave_define('#CreateCredit', 'currency');
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateProgram').val(null).trigger('change');
});

// dropdown
select2_define(
  '#CreateProgram',
  '/fund-distribution/dropdown',
  {},
  true,
  $('#modalCreate')
);

$('#CreateProgram').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedProgram').val(selectedData.id);

  NProgress.start();
  updateCreditBasedOnProgram(selectedData);
  NProgress.done();
});

handleAddForm(
  '#formCreate',
  '/fund-distribution/save',
  'modalCreate',
  ['SelectedProgram', 'CreateCredit'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
    let id = $(this).data('id');
    let credit = $(this).data('credit');
    let program_id = $(this).data('program_id');
    let program_name = $(this).data('program_name');
    let percentage = $(this).data('percentage');
    let beneficiariesData = $(this).data("beneficiaries");

    showModal('modalEdit', 'formEdit', ['EditCredit', 'EditProgram']);
    cleave_define('#EditCredit', 'currency');

    updateCreditBasedOnProgramEdit(percentage);

    $('#EditId').val(id);
    $('#EditCredit').val(credit);

    $('#EditProgram').val(null).trigger('change');

    if (program_id && program_name) {
      let option = new Option(program_name, program_id, true, true);
      $('#EditProgram').append(option).trigger('change');
    }

    loadBeneficaries(function () {
        let selectedIds = beneficiariesData.map(b => b.id); // Ambil hanya ID beneficiaries

        // Reset semua checkbox sebelum menampilkan modal
        $("input[name='beneficaries[]']").prop("checked", false);

        // Centang checkbox yang ID-nya ada di daftar beneficiaries
        selectedIds.forEach(id => {
            $("input[name='beneficaries[]'][value='" + id + "']").prop("checked", true);
        });
    });
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
select2_define(
  '#EditProgram',
  '/fund-distribution/dropdown',
  {},
  true,
  $('#modalEdit')
);

$('#EditProgram').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedProgram').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/fund-distribution/update',
  'modalEdit',
  ['EditCredit', 'SelectedProgram'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/fund-distribution/delete', id, function () {
    loadData();
  });
});