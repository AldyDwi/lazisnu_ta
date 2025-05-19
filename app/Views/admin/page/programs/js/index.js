var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'programs/';

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
    'CreatePercentage',
    'CreateType',
  ]);
  cleave_define('#CreatePercentage', 'persentage');
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
});

// dropdown
select('#CreateType');

handleAddForm(
  '#formCreate',
  '/programs/save',
  'modalCreate',
  ['CreateName', 'CreatePercentage', 'CreateType'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let percentage = $(this).data('percentage');
  let type = $(this).data('type');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditPercentage', 'EditType']);
  cleave_define('#EditPercentage', 'persentage');

  $('#EditId').val(id);
  $('#EditName').val(name);
  $('#EditPercentage').val(percentage);
  $('#EditType').val(type).change();
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
});

// dropdown
select('#EditType');

handleEditForm(
  '#formEdit',
  '/programs/update',
  'modalEdit',
  ['EditName', 'EditPercentage', 'EditType'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/programs/delete', id, function () {
    loadData();
  });
});
