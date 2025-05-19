var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'officers/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/officers/list_data',
        type: 'GET',
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 1, title: 'Nama' },
        { data: 2, title: 'Username' },
        { data: 3, title: 'RW' },
        { data: 4, title: 'Desa/Kelurahan' },
        { data: 5, title: 'Ranting' },
        {
          data: 6,
          title: 'Jenis Kelamin',
          render: function (data, type, row) {
            return data === 'male' ? 'Laki-laki' : data === 'female' ? 'Perempuan' : 'Lainnya';
          },
        },
        { data: 7, title: 'No. HP' },
        { data: 8, title: 'Email' },
        {
          data: 9,
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
  showModal('modalCreate', 'formCreate', ['CreateName', 'CreateRW', 'CreatePhone', 'CreateType', 'CreateEmail', 'CreatePassword', 'CreatePasswordConfirmation']);

  cleave_define('#CreatePhone', 'phone');
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
  $('#CreateRW').val(null).trigger('change');
});

// dropdown
select('#CreateType');

select2_define(
  '#CreateRW',
  '/super-admin/rws/list_data',
  {},
  true,
  $('#modalCreate')
);

$('#CreateRW').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedRW').val(selectedData.id);
});

handleAddForm(
  '#formCreate',
  '/super-admin/officers/save',
  'modalCreate',
  ['CreateName', 'SelectedRW', 'CreatePhone', 'CreateType', 'CreateEmail', 'CreatePassword', 'CreatePasswordConfirmation'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let rw_id = $(this).data('rw_id');
  let rw_name = $(this).data('rw_name');
  let phone = $(this).data('phone');
  let gender = $(this).data('gender');
  let email = $(this).data('email');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditRW', 'EditPhone', 'EditType', 'EditEmail']);

  $('#EditId').val(id);
  $('#EditName').val(name);

  $('#EditRW').val(null).trigger('change');

  if (rw_id && rw_name) {
    let option = new Option(rw_name, rw_id, true, true);
    $('#EditRW').append(option).trigger('change');
  }
  $('#EditPhone').val(phone);
  $('#EditType').val(gender).trigger('change');
  $('#EditEmail').val(email);
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
select('#EditType');

select2_define(
  '#EditRW',
  '/super-admin/rws/list_data',
  {},
  true,
  $('#modalEdit')
);

$('#EditRW').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedRW').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/super-admin/officers/update',
  'modalEdit',
  ['EditName', 'EditRW', 'EditPhone', 'EditType', 'EditEmail'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/officers/delete', id, function () {
    loadData();
  });
});
