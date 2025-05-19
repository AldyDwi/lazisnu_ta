var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'officers/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/admin/list_data',
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
        { data: 3, title: 'Kelurahan' },
        { data: 4, title: 'Ranting' },
        {
          data: 5,
          title: 'Jenis Kelamin',
          render: function (data, type, row) {
            return data === 'male' ? 'Laki-laki' : data === 'female' ? 'Perempuan' : 'Lainnya';
          },
        },
        { data: 6, title: 'No. HP' },
        { data: 7, title: 'Email' },
        {
          data: 8,
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
  $('#selectedRW').val('');
});

// dropdown
select('#CreateType');

select2_define(
  '#CreateRW',
  '/super-admin/rws/dropdown-admin',
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
  '/super-admin/admin/save',
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
  let branch_name = $(this).data('branch_name');
  let phone = $(this).data('phone');
  let gender = $(this).data('gender');
  let email = $(this).data('email');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditRW', 'EditPhone', 'EditType', 'EditEmail']);

  $('#EditId').val(id);
  $('#EditName').val(name);

  $('#EditRW').val(null).trigger('change');

  if (rw_id && branch_name) {
    let option = new Option(branch_name, rw_id, true, true);
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
  '/super-admin/rws/dropdown-admin',
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
  '/super-admin/admin/update',
  'modalEdit',
  ['EditName', 'EditRW', 'EditPhone', 'EditType', 'EditEmail'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/admin/delete', id, function () {
    loadData();
  });
});
