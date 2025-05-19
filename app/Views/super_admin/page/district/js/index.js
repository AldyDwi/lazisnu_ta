var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'district/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/district/list_data',
        type: 'GET',
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 2, title: 'Nama' },
        {
          data: 3,
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

$(document).ready(function () {
  NProgress.start();
  loadData();
  NProgress.done();
});

// Create Data
$('#btnCreate').click(function () {
  showModal('modalCreate', 'formCreate', ['CreateName']);
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
});

handleAddForm(
  '#formCreate',
  '/super-admin/district/save',
  'modalCreate',
  ['CreateName'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');

  showModal('modalEdit', 'formEdit', ['EditName']);

  $('#EditId').val(id);
  $('#EditName').val(name);
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

handleEditForm(
  '#formEdit',
  '/super-admin/district/update',
  'modalEdit',
  ['EditName'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/district/delete', id, function () {
    loadData();
  });
});
