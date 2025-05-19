var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'programs/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/programs/list_data',
        type: 'GET',
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 1, title: 'Nama' },
        { data: 2, title: 'Ranting' },
        { 
          data: 3, 
          title: 'Persentase',
          render: function (data, type, row) {
            return data == "0%" ? 'Tidak ada' : data;
          }
        },
        { data: 4, title: 'Tipe' },
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

$(document).ready(function () {
  NProgress.start();
  loadData();
  NProgress.done();
});

// Create Data
$('#btnCreate').click(function () {
  showModal('modalCreate', 'formCreate', [
    'CreateName',
    'CreateBranch',
    'CreatePercentage',
    'CreateType',
  ]);
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateBranch').val(null).trigger('change');
});

// dropdown
select('#CreateType');

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
  '/super-admin/programs/save',
  'modalCreate',
  ['CreateName', 'SelectedBranch', 'CreatePercentage', 'CreateType'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let branch_id = $(this).data('branch_id');
  let branch_name = $(this).data('branch_name');
  let percentage = $(this).data('percentage');
  let type = $(this).data('type');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditBranch', 'EditPercentage', 'EditType']);

  $('#EditId').val(id);
  $('#EditName').val(name);
  $('#EditPercentage').val(percentage);
  $('#EditType').val(type).change();

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
select('#EditType');

select2_define('#EditBranch', '/super-admin/branches/list_data', {}, true, $('#modalEdit'));

$('#EditBranch').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedBranch').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/super-admin/programs/update',
  'modalEdit',
  ['EditName', 'SelectedBranch', 'EditPercentage', 'EditType'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/programs/delete', id, function () {
    loadData();
  });
});
