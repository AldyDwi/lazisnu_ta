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
        { data: 2, title: 'RW' },
        { data: 3, title: 'Nominal' },
        { data: 4, title: 'Tanggal' },
        { data: 6, title: 'Catatan', render: (data) => data ? data : '-' },
        { data: 5, 
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
          data: 7,
          title: 'Aksi',
          orderable: false,
          searchable: false,
          render: function (data, type, row) {
            return row[5] === 'close' ? '-' : data;
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

function checkButtonCreate() {
  $.ajax({
    url: '/officer/donations/btn_create',
    type: 'GET',
    success: function (response) {
      if (response.hasClosedData) {
        $('#createDonations').addClass('d-none');
      } else {
        $('#createDonations').removeClass('d-none');
      }
    },
    error: function (xhr, status, error) {
      console.error('Error fetching data:', error);
    },
  });
}

// Panggil loadData pertama kali tanpa filter (menampilkan bulan ini)
$(document).ready(function () {
  NProgress.start();
  loadData();
  checkButtonCreate();
  NProgress.done();
  datepicker('#start_date');
  datepicker('#end_date');
});

// Date FIlter
$('#filter-form').on('submit', function (e) {
  e.preventDefault();
  let startDate = $('#start_date').val();
  let endDate = $('#end_date').val();
  loadData(startDate, endDate);
});

// Create Data
$('#btnCreate').click(function () {
  showModal('modalCreate', 'formCreate', ['CreateDebit', 'CreateCitizen', 'CreateNote']);
  cleave_define('#CreateDebit', 'currency');
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateCitizen').val(null).trigger('change');
});

// dropdown
select2_define(
  '#CreateCitizen',
  '/officer/donations/citizen_dropdown',
  {},
  true,
  $('#modalCreate')
);

$('#CreateCitizen').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedCitizen').val(selectedData.id);
});

handleAddForm(
  '#formCreate',
  '/officer/donations/save',
  'modalCreate',
  ['CreateDebit', 'SelectedCitizen', 'CreateNote'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let debit = $(this).data('debit');
  let note = $(this).data('note');
  let citizen_id = $(this).data('citizen_id');
  let citizen_name = $(this).data('citizen_name');

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
  $('#EditCitizen').val(null).trigger('change');
});

// dropdown
select2_define(
  '#EditCitizen',
  '/officer/donations/citizen_dropdown',
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
  '/officer/donations/update',
  'modalEdit',
  ['EditDebit', 'SelectedCitizen', 'EditNote'],
  function () {
    loadData();
  }
);
