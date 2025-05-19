var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'rw/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/program-page/list_data',
        type: 'GET',
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 1, title: 'Nama Program' },
        { data: 2, title: 'Ranting' },
        { data: 3, title: 'Gambar' },
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
      drawCallback: function () {
        const lightbox = GLightbox({
          selector: '.glightbox',
        });
      },
    });
  });
}

const pond = FilePond.create(document.querySelector('#CreateImage'), {
  allowRevert: true,
  allowRemove: true,
});

const pondEdit = FilePond.create(document.querySelector('#EditImage'), {
  allowRevert: true,
  allowRemove: true,
  allowReplace: true,
  acceptedFileTypes: ['image/*'],
  server: {
    load: 'assets/themes/super_admin/uploads/',
  },
});

$(document).ready(function () {
  NProgress.start();
  loadData();
  NProgress.done();
});

// Create Data
$('#btnCreate').click(function () {
  showModal('modalCreate', 'formCreate', ['CreateProgram', 'CreateImage']);
});

$('#btnCloseCreate').click(function () {
  pond.removeFiles();
  closeModal('modalCreate');
  $('#CreateProgram').val(null).trigger('change');
});

// dropdown
select2_define(
  '#CreateProgram',
  '/super-admin/fund-distribution/dropdown',
  {},
  true,
  $('#modalCreate')
);

$('#CreateProgram').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedProgram').val(selectedData.id);
});

handleAddForm(
  '#formCreate',
  '/super-admin/program-page/save',
  'modalCreate',
  ['SelectedProgram', 'CreateImage'],
  function () {
    loadData();
  }
);

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let image = $(this).data('image');
  let program_id = $(this).data('program_id');
  let program_name = $(this).data('program_name');

  showModal('modalEdit', 'formEdit', ['EditImage', 'EditProgram']);

  $('#EditId').val(id);

  $('#EditProgram').val(null).trigger('change');

  if (program_id && program_name) {
    let option = new Option(program_name, program_id, true, true);
    $('#EditProgram').append(option).trigger('change');
  }

  $('#editImage').data('data-old-image', image);

  // Cek apakah ada gambar lama
  if (image) {
    const imageUrl = `/assets/themes/super_admin/uploads/${image}`;

    // Reset FilePond agar tidak menumpuk file sebelumnya
    pondEdit.removeFiles();

    // Tambahkan gambar lama ke FilePond
    pondEdit
      .addFile(imageUrl)
      .then(() => {
        console.log('Gambar lama berhasil dimuat di FilePond.');
      })
      .catch((error) => {
        console.log('Gagal menampilkan gambar lama:', error);
      });
  }
});

// Tombol Tutup Modal Edit
$('#btnCloseEdit').click(function () {
  closeModal('modalEdit');
  pondEdit.removeFiles();
});

// dropdown
select2_define(
  '#EditProgram',
  '/super-admin/fund-distribution/dropdown',
  {},
  true,
  $('#modalEdit')
);

$('#EditProgram').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedProgram').val(selectedData.id);
});

handleEditForm(
  '#formEdit',
  '/super-admin/program-page/update',
  'modalEdit',
  ['EditImage', 'EditProgram'],
  function () {
    loadData();
  }
);

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/program-page/delete', id, function () {
    loadData();
  });
});
