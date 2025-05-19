var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'rw/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/list_data',
        type: 'GET',
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 1, title: 'Nama' },
        { data: 2, title: 'Tipe Penerima' },
        { data: 3, title: 'Ranting' },
        {
          data: 4,
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
  showModal('modalCreate', 'formCreate', ['CreateName', 'CreateType']);
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
});

// dropdown
select2_define(
  '#CreateType',
  '/beneficaries/dropdown',
  {},
  true,
  $('#modalCreate')
);

$('#CreateType').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedType').val(selectedData.id);
});

handleAddForm(
  '#formCreate',
  '/beneficaries/save',
  'modalCreate',
  ['CreateName', 'SelectedType'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let type_id = $(this).data('type_id');
  let type_name = $(this).data('type_name');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditType']);

  $('#EditId').val(id);
  $('#EditName').val(name);

  $('#EditType').val(null).trigger('change');

  if (type_id && type_name) {
    let option = new Option(type_name, type_id, true, true);
    $('#EditType').append(option).trigger('change');
  }
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
select2_define(
  '#EditType',
  '/beneficaries/dropdown',
  {},
  true,
  $('#modalEdit')
);

$('#EditType').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedType').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/beneficaries/update',
  'modalEdit',
  ['EditName', 'EditType'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/beneficaries/delete', id, function () {
    loadData();
  });
});