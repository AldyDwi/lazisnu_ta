var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'branches/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/branches/list_data',
        type: 'GET',
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 2, title: 'Nama' },
        { data: 3, title: 'Kelurahan' },
        { data: 4, title: 'Alamat Lengkap', render: (data) => (data ? data : '-') },
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
  showModal('modalCreate', 'formCreate', ['CreateName', 'CreateRegion', 'CreateAddress']);
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateRegion').val(null).trigger('change');
});

// dropdown
select2_define(
  '#CreateRegion',
  '/super-admin/region/list_data',
  {},
  true,
  $('#modalCreate')
);

$('#CreateRegion').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedRegion').val(selectedData.id);
});

handleAddForm(
  '#formCreate',
  '/super-admin/branches/save',
  'modalCreate',
  ['CreateName', 'SelectedRegion', 'CreateAddress'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let address = $(this).data('address');
  let region_id = $(this).data('region_id');
  let region_name = $(this).data('region_name');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditRegion']);

  $('#EditId').val(id);
  $('#EditName').val(name);
  $('#EditAddress').val(address);

  $('#EditRegion').val(null).trigger('change');

  if (region_id && region_name) {
    let option = new Option(region_name, region_id, true, true);
    $('#EditRegion').append(option).trigger('change');
  }
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
select2_define('#EditRegion', '/super-admin/region/list_data', {}, true, $('#modalEdit'));

$('#EditRegion').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedRegion').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/super-admin/branches/update',
  'modalEdit',
  ['EditName', 'EditRegion', 'EditAddress'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/branches/delete', id, function () {
    loadData();
  });
});
