var url_controller_attendance = baseUrl + '/' + prefix_folder_admin + 'rw/';

function loadData() {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/distribution-page/list_data',
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
        { data: 3, title: 'Tanggal' },
        {
          data: 4,
          title: 'Deskripsi',
          render: function (data, type, row) {
            return truncateText(data, 10);
          },
        },
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
      drawCallback: function () {
        const lightbox = GLightbox({
          selector: '.glightbox',
        });
      },
    });
  });
}

function truncateText(text, maxWords) {
  if (!text) return '-';

  let words = text.split(/\s+/);
  if (words.length > maxWords) {
    return words.slice(0, maxWords).join(' ') + '...';
  }
  return text;
}

function detail(id) {
  $.ajax({
    url: baseUrl + '/distribution-page/get_detail',
    type: 'GET',
    data: { id: id },
    dataType: 'json',
    success: function (response) {
      if (response.status) {
        let data = response.distribution_page;
        let images = response.distribution_image;

        // Masukkan data ke dalam modal
        $('#detailProgram').text(data.program_name ?? '-');
        $('#detailBranch').text(data.branch_name ?? '-');
        $('#detailDescription').html(data.description ?? '-');

        let date = new Date(data.date);
        $('#detailDate').text(
          date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
          })
        );

        // Kosongkan dan tambahkan gambar ke dalam daftar
        $('#detailImages').empty();
        if (images.length > 0) {
          images.forEach((img) => {
            $('#detailImages').append(
              `<a href="${img}" class="glightbox">
                            <img src="${img}" alt="Gambar Distribusi" class="img-fluid rounded border my-1" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px;">
                          </a>`
            );
          });

          if (typeof lightbox !== 'undefined') {
            lightbox.destroy();
          }

          lightbox = GLightbox({
            selector: '.glightbox',
          });
        } else {
          $('#detailImages').append(
            '<p class="text-muted">Tidak ada gambar.</p>'
          );
        }

        // Tampilkan modal
        $('#modalDetail').modal('show');
      } else {
        Toastify({
          text: 'Gagal mengambil data.',
          duration: 3000,
          gravity: 'top',
          position: 'right',
          style: {
            background: '#E53E3E',
          },
          stopOnFocus: true,
        }).showToast();
      }
    },
    error: function () {
      Toastify({
        text: 'Terjadi kesalahan dalam mengambil data.',
        duration: 3000,
        gravity: 'top',
        position: 'right',
        style: {
          background: '#E53E3E',
        },
        stopOnFocus: true,
      }).showToast();
    },
  });
}

function textareaCreate() {
  $('textarea#CreateDescription').tinymce({
    height: 500,
    menubar: false,
    plugins: [
      'advlist',
      'autolink',
      'lists',
      'link',
      'image',
      'charmap',
      'preview',
      'anchor',
      'searchreplace',
      'visualblocks',
      'fullscreen',
      'insertdatetime',
      'media',
      'table',
      'code',
      'help',
      'wordcount',
    ],
    toolbar:
      'undo redo | blocks | bold italic backcolor | ' +
      'alignleft aligncenter alignright alignjustify | ' +
      'bullist numlist outdent indent | removeformat | help',
  });
}

function textareaEdit(description) {
  if (tinymce.get('EditDescription')) {
    tinymce.get('EditDescription').remove(); // Pastikan tidak ada instance lama
  }

  $('textarea#EditDescription').tinymce({
    height: 500,
    menubar: false,
    plugins: [
      'advlist',
      'autolink',
      'lists',
      'link',
      'image',
      'charmap',
      'preview',
      'anchor',
      'searchreplace',
      'visualblocks',
      'fullscreen',
      'insertdatetime',
      'media',
      'table',
      'code',
      'help',
      'wordcount',
    ],
    toolbar:
      'undo redo | blocks | bold italic backcolor | ' +
      'alignleft aligncenter alignright alignjustify | ' +
      'bullist numlist outdent indent | removeformat | help',
    init_instance_callback: function (editor) {
      editor.setContent(description || '');
    },
  });
}

function decodeHtml(html) {
  let txt = document.createElement("textarea");
  txt.innerHTML = html;
  return txt.value;
}

const pond = FilePond.create(document.querySelector('#CreateImage'), {
  allowRevert: true,
  allowRemove: true,
});

const pondEdit = FilePond.create(document.querySelector('#EditImage'), {
  allowRevert: true,
  allowRemove: true,
  allowReplace: true,
  storeAsFile: true,
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

// Detail Data
$(document).on('click', '#btnDetail', function () {
  let id = $(this).data('id');

  NProgress.start();
  detail(id);
  NProgress.done();
});

$('#btnCloseDetail').click(function () {
  closeModal('modalDetail');
});

// Create Data
$('#btnCreate').click(function () {
  datepicker('#CreateDate');

  textareaCreate();

  showModal('modalCreate', 'formCreate', [
    'CreateProgram',
    'CreateImage',
    'CreateDescription',
    'CreateDate',
  ]);
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

$('#btnSubmitSave').click(function () {
  let form = $('#formCreate');
  let formData = new FormData(form[0]);

  // Jika form ini menggunakan FilePond, tambahkan file secara manual
  if ($('#CreateImage').length > 0 && pond.getFiles().length > 0) {
    pond.getFiles().forEach((file, index) => {
      formData.append(`images[]`, file.file); // Kirim semua file yang dipilih
    });
  }

  const description = tinymce.get('CreateDescription').getContent();
  formData.set('description', description);

  // Kirim ke fungsi handleAddForm dengan FormData yang telah dimodifikasi
  handleAddForm(
    '#formCreate',
    '/super-admin/distribution-page/save',
    'modalCreate',
    ['SelectedProgram', 'CreateImage', 'CreateDescription', 'CreateDate'],
    function () {
      loadData();
    },
    formData
  );
});

// Edit Data
$(document).on('click', '#btnEdit', function () {
  datepicker('#EditDate');

  let id = $(this).data('id');
  // let description = $(this).data('description');
  let program_id = $(this).data('program_id');
  let program_name = $(this).data('program_name');
  let date = $(this).data('date');

  let imageData = $(this).attr('data-image');

  let rawDescription = $(this).data('description');
  let description = decodeHtml(rawDescription);

  textareaEdit(description);
  tinymce.get('EditDescription').setContent(description || '');

  console.log('Raw imageData:', imageData);

  let images = [];

  try {
    images = imageData ? JSON.parse(imageData) : [];
    console.log('Parsed images:', images);
  } catch (error) {
    console.error('JSON Parsing Error:', error);
  }

  showModal('modalEdit', 'formEdit', [
    'EditImage',
    'EditProgram',
    'EditDescription',
  ]);

  $('#EditId').val(id);
  // $('#EditDescription').val(description);
  $('#EditDate').val(date);

  $('#EditProgram').val(null).trigger('change');

  if (program_id && program_name) {
    let option = new Option(program_name, program_id, true, true);
    $('#EditProgram').append(option).trigger('change');
  }

  $('#editImage').data('data-old-image', images);

  // Cek apakah ada gambar lama
  if (images) {
    // Reset FilePond agar tidak menumpuk file sebelumnya
    pondEdit.removeFiles();

    images.forEach((image) => {
      const imageUrl = `/assets/themes/super_admin/uploads/${image.image}`;
      pondEdit
        .addFile(imageUrl)
        .then(() => {
          console.log(`Gambar ${image.image} berhasil dimuat.`);
        })
        .catch((error) => {
          console.log(`Gagal menampilkan gambar ${image.image}:`, error);
        });
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

$('#btnSubmitEdit').click(function () {
  let form = $('#formEdit');
  let formData = new FormData(form[0]);

  // Jika form ini menggunakan FilePond, tambahkan file secara manual
  if ($('#EditImage').length > 0 && pond.getFiles().length > 0) {
    pond.getFiles().forEach((file, index) => {
      formData.append(`images[]`, file.file);
    });
  }

  const deskripsi = tinymce.get('EditDescription').getContent();
  formData.set('description', deskripsi);

  handleEditForm(
    '#formEdit',
    '/super-admin/distribution-page/update',
    'modalEdit',
    ['EditImage', 'EditProgram', 'EditDescription'],
    function () {
      loadData();
    },
    formData
  );
});

// Delete Data
$(document).on('click', '#btnDelete', function () {
  let id = $(this).data('id');

  handleDelete('/super-admin/distribution-page/delete', id, function () {
    loadData();
  });
});
