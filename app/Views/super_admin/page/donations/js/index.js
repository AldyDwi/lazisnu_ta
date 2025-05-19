var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'donations/';

function loadData(startDate = '', endDate = '') {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data-table')) {
      $('#data-table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/donations/list_data',
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
        { data: 1, title: 'Nama Warga' },
        { data: 2, title: 'Nama Petugas' },
        { data: 3, title: 'RW' },
        { data: 8, title: 'Ranting' },
        { data: 4, title: 'Nominal' },
        { data: 5, title: 'Tanggal' },
        { data: 7, title: 'Catatan', render: (data) => data ? data : '-' },
        { data: 6, 
          title: 'Status',
          render: function (data, type, row) {
            if (data === 'close') {
              return '<span class="text-white bg-danger rounded px-2 py-1 d-inline-block fw-bold">Tutup</span>';
            } else if (data === 'open') {
              return '<span class="text-white bg-success rounded px-2 py-1 d-inline-block fw-bold">Buka</span>';
            } else {
              return '<span class="text-white bg-secondary rounded px-2 py-1 d-inline-block fw-bold">Lainnya</span>';
            }
          },
        },
        {
          data: 9,
          title: 'Aksi',
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            return row[6] === 'close' ? '-' : data;
          },
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

function updateStatus(status) {
  var month = $('#StatusMonth').val();
  var year = $('#StatusYear').val();

  if (!month || !year) {
    Toastify({
      text: 'Silakan pilih bulan dan tahun terlebih dahulu.',
      duration: 3000,
      gravity: 'top',
      position: 'right',
      style: {
        background: '#E53E3E',
      },
      stopOnFocus: true,
    }).showToast();
    return;
  }

  $.ajax({
    url: baseUrl + '/transaction/update_status',
    type: 'POST',
    data: { month: month, year: year, status: status },
    dataType: 'json',
    success: function (response) {
      if (response.status === true) {
        Toastify({
          text:
            'Status Donasi Telah di' +
            status +
            ' pada bulan ' +
            month +
            ' tahun ' +
            year,
          duration: 6000,
          gravity: 'top',
          position: 'right',
          style: {
            background: '#4CAF50',
          },
          stopOnFocus: true,
        }).showToast();
        closeModal('modalStatus');
        $('#formStatus')[0].reset();
        loadData();
      } else {
        Toastify({
          text: response.message,
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
        text: 'Terjadi kesalahan, coba lagi!',
        duration: 3000,
        gravity: 'top',
        position: 'right',
        style: {
          background: '#E53E3E',
        },
        stopOnFocus: true,
      }).showToast();
    },
  });
}

// Panggil loadData pertama kali tanpa filter (menampilkan bulan ini)
$(document).ready(function () {
  NProgress.start();
  loadData();
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

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let debit = $(this).data('debit');
  let citizen_id = $(this).data('citizen_id');
  let citizen_name = $(this).data('citizen_name');
  let note = $(this).data('note');

  showModal('modalEdit', 'formEdit', ['EditDebit', 'EditCitizen', 'EditNote']);

  cleave_define('#EditDebit', 'currency');

  $('#EditId').val(id);
  $('#EditDebit').val(debit);
  $('#EditNote').val(note);

  $('#EditCitizen').val(null).trigger('change');

  if (citizen_id && citizen_name) {
    let option = new Option(citizen_name, citizen_id, true, true);
    $('#EditCitizen').append(option).trigger('change');
  }
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
select2_define(
  '#EditCitizen',
  '/super-admin/donations/citizen_dropdown',
  {},
  true,
  $('#modalEdit')
);

$('#EditCitizen').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedCitizen').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/super-admin/donations/update',
  'modalEdit',
  ['EditDebit', 'SelectedCitizen', 'EditNote'],
  function () {
    loadData();
  }
);

// Update Status
$('#btnStatus').click(function () {
  showModal('modalStatus', 'formStatus', ['StatusMonth', 'StatusYear']);

  $('.datepicker-month').datepicker({
    format: "mm",
    viewMode: "months",
    minViewMode: "months",
    autoclose: true
  });

  $('.datepicker-year').datepicker({
      format: "yyyy",
      viewMode: "years",
      minViewMode: "years",
      autoclose: true
  });
});

$('#btnOpen').click(function () {
  NProgress.start();
  updateStatus("open");
  NProgress.done();
});

$('#btnClose').click(function () {
  NProgress.start();
  updateStatus("close");
  NProgress.done();
});

$('#btnCloseStatus').click(function () {
  closeModal('modalStatus');
});