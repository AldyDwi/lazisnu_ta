var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'citizen/';

function loadData() {
  $(document).ready(function () {
    const columns = [
      {
        data: null,
        render: (data, type, row, meta) => meta.row + 1,
        title: 'No.',
      },
      { data: 1, title: 'Nama' },
      { data: 2, title: 'No. Hp', render: (data) => (data ? data : '-') },
      { data: 3, title: 'RW' },
      { data: 4, 
        title: 'Status',
        render: function (data, type, row) {
          if (data === 'not yet') {
            return '<span class="text-white bg-danger rounded px-2 py-1 d-inline-block fw-bold">Belum Donasi</span>';
          } else if (data === 'already') {
            return '<span class="text-white bg-success rounded px-2 py-1 d-inline-block fw-bold">Sudah Donasi</span>';
          } else {
            return '<span class="text-white bg-secondary rounded px-2 py-1 d-inline-block fw-bold">Lainnya</span>';
          }
        },
       },
      {
        data: 5,
        title: 'Aksi',
        orderable: false,
        searchable: false,
      },
    ];

    if ($.fn.DataTable.isDataTable('#data-table')) {
      $('#data-table').DataTable().clear().destroy();
    }

    setTableHeader(columns);

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/citizen/list_data',
        type: 'GET',
      },
      columns: columns,
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

function loadDataNotRW() {
  $(document).ready(function () {
    const columns = [
      {
        data: null,
        render: (data, type, row, meta) => meta.row + 1,
        title: 'No.',
      },
      { data: 1, title: 'Nama' },
      { data: 2, title: 'No. Hp' },
      { data: 3, title: 'Alamat' },
      { data: 4, 
        title: 'Status',
        render: function (data, type, row) {
          if (data === 'not yet') {
            return '<span class="text-white bg-danger rounded px-2 py-1 d-inline-block fw-bold">Belum Donasi</span>';
          } else if (data === 'already') {
            return '<span class="text-white bg-success rounded px-2 py-1 d-inline-block fw-bold">Sudah Donasi</span>';
          } else {
            return '<span class="text-white bg-secondary rounded px-2 py-1 d-inline-block fw-bold">Lainnya</span>';
          }
        },
       },
      {
        data: 5,
        title: 'Aksi',
        orderable: false,
        searchable: false,
      },
    ];

    if ($.fn.DataTable.isDataTable('#data-table')) {
      $('#data-table').DataTable().clear().destroy();
    }

    setTableHeader(columns);

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/citizen/list_data_not_rw',
        type: 'GET',
      },
      columns: columns,
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

function setTableHeader(columns) {
  const thead = $('#data-table thead');
  thead.empty(); // Kosongkan header lama
  const headerRow = $('<tr></tr>');

  columns.forEach((col) => {
    headerRow.append(`<th>${col.title || ''}</th>`);
  });

  thead.append(headerRow);
}

$(document).ready(function () {
  NProgress.start();
  loadData();
  NProgress.done();
  toggleRW();
});

let showingNotRW = false;

$('#btnOtherCitizen').on('click', function () {
  if (showingNotRW) {
    // Jika saat ini menampilkan data warga lainnya, kembalikan ke data warga RW
    loadData();
    $(this).text('Warga lainnya?');
  } else {
    // Jika saat ini menampilkan data warga RW, ganti ke data warga lainnya
    loadDataNotRW();
    $(this).text('Kembali ke Warga Dengan RW');
  }

  showingNotRW = !showingNotRW; // Toggle status
});

// Create Data
$('#btnCreate').click(function () {
  showModal('modalCreate', 'formCreate', ['CreateName', 'CreateRW', 'CreatePhone', 'CreateAddress']);
  cleave_define('#CreatePhone', 'phone');
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateRW').val(null).trigger('change');
});

handleAddForm(
  '#formCreate',
  '/officer/citizen/save',
  'modalCreate',
  ['CreateName', 'CreatePhone', 'CreateAddress'],
  function () {
    loadData();
    $('#btnOtherCitizen').text('Warga lainnya?');
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let phone = $(this).data('phone');
  let address = $(this).data('address');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditPhone', 'EditAddress']);
  cleave_define('#EditPhone', 'phone');

  $('#EditId').val(id);
  $('#EditName').val(name);
  $('#EditPhone').val(phone);
  $('#EditAddress').val(address);
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

handleEditForm(
  '#formEdit',
  '/officer/citizen/update',
  'modalEdit',
  ['EditName', 'EditPhone', 'EditAddress'],
  function () {
    loadData();
    $('#btnOtherCitizen').text('Warga lainnya?');
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');
  let address = $(this).data('address');

  handleDelete('/officer/citizen/delete', id, function () {
    if (address) {
      loadDataNotRW();
    } else {
      loadData();
    }
  });
});
