var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'rw/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/beneficaries/list_data',
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
  showModal('modalCreate', 'formCreate', ['CreateName', 'CreateType', 'CreateBranch']);
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateType').val(null).trigger('change');
  $('#CreateBranch').val(null).trigger('change');
});

// dropdown
select2_define(
  '#CreateType',
  '/super-admin/beneficaries-type/list_data',
  {},
  true,
  $('#modalCreate')
);

$('#CreateType').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedType').val(selectedData.id);
});

select2_define(
  '#CreateBranch',
  '/super-admin/branches/list_data',
  {},
  true,
  $('#modalCreate')
);

$('#CreateBranch').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedBranch').val(selectedData.id);
});

handleAddForm(
  '#formCreate',
  '/super-admin/beneficaries/save',
  'modalCreate',
  ['CreateName', 'SelectedType', 'SelectedBranch'],
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
  let branch_id = $(this).data('branch_id');
  let branch_name = $(this).data('branch_name');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditType', 'EditBranch']);

  $('#EditId').val(id);
  $('#EditName').val(name);

  $('#EditType').val(null).trigger('change');

  if (type_id && type_name) {
    let option = new Option(type_name, type_id, true, true);
    $('#EditType').append(option).trigger('change');
  }

  $('#EditBranch').val(null).trigger('change');

  if (branch_id && branch_name) {
    let option = new Option(branch_name, branch_id, true, true);
    $('#EditBranch').append(option).trigger('change');
  }
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
// select('#EditType');
select2_define(
  '#EditType',
  '/super-admin/beneficaries-type/list_data',
  {},
  true,
  $('#modalEdit')
);

$('#EditType').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedType').val(selectedData.id);
});

select2_define(
  '#EditBranch',
  '/super-admin/branches/list_data',
  {},
  true,
  $('#modalEdit')
);

$('#EditBranch').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedBranch').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/super-admin/beneficaries/update',
  'modalEdit',
  ['EditName', 'EditType', 'EditBranch'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/beneficaries/delete', id, function () {
    loadData();
  });
});