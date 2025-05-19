var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'region/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/region/list_data',
        type: 'GET',
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 2, title: 'Nama' },
        { data: 3, title: 'Kecamatan' },
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
  showModal('modalCreate', 'formCreate', ['CreateName', 'CreateDistrict']);
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateDistrict').val(null).trigger('change');
});

// dropdown
select2_define(
  '#CreateDistrict',
  '/super-admin/district/list_data',
  {},
  true,
  $('#modalCreate')
);

$('#CreateDistrict').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedDistrict').val(selectedData.id);
});

handleAddForm(
  '#formCreate',
  '/super-admin/region/save',
  'modalCreate',
  ['CreateName', 'SelectedDistrict'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let district_id = $(this).data('district_id');
  let district_name = $(this).data('district_name');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditDistrict']);

  $('#EditId').val(id);
  $('#EditName').val(name);

  $('#EditDistrict').val(null).trigger('change');

  if (district_id && district_name) {
    let option = new Option(district_name, district_id, true, true);
    $('#EditDistrict').append(option).trigger('change');
  }
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
select2_define(
  '#EditDistrict',
  '/super-admin/district/list_data',
  {},
  true,
  $('#modalEdit')
);

$('#EditDistrict').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedDistrict').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/super-admin/region/update',
  'modalEdit',
  ['EditName', 'EditDistrict'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/region/delete', id, function () {
    loadData();
  });
});
