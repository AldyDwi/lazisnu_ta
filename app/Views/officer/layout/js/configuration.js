var getUrl = window.location;
var baseHost = getUrl.protocol + '//' + getUrl.host + '/';
var baseUrl = baseHost + getUrl.pathname.split('/')[1];
var prefix_folder_admin = 'admin/';

$(document).ready(function () {});

// List helper function to faster the development add yours to this file
// Task:
// 1. This function is still has error because the library is missing, fix this to make it working
// 2. Use this function to your project

// Function to show loading
function showLoading() {
  $('#js-preloader').removeClass('loaded');
}
function hideLoading() {
  $('#js-preloader').addClass('loaded');
}

// Funciton to check all the error in input
function input_error(data) {
  // check data input error exist
  if (data.inputerror == undefined) {
    notif_error('Terjadi kesalahan, silahkan coba lagi');
    return;
  }
  for (var i = 0; i < data.inputerror.length; i++) {
    let split_inputerror = data.inputerror[i].split('.');

    if (
      $('[name="' + data.inputerror[i] + '"]')
        .parent()
        .hasClass('input-group')
    ) {
      $('[name="' + data.inputerror[i] + '"]')
        .addClass('is-invalid')
        .parent()
        .parent()
        .find('.invalid-feedback')
        .addClass('d-block')
        .text(data.error_string[i]);
    } else if (
      $('[name="' + data.inputerror[i] + '"]').hasClass('trumbowyg-js')
    ) {
      $('[name="' + data.inputerror[i] + '"]')
        .parent()
        .parent()
        .find('.invalid-feedback')
        .addClass('d-block')
        .text(data.error_string[i]);
    } else if (
      $('[name="' + data.inputerror[i] + '"]').parents('.filepond--root').length
    ) {
      $('[name="' + data.inputerror[i] + '"]')
        .parent()
        .addClass('is-invalid');
      $('[name="' + data.inputerror[i] + '"]')
        .parent()
        .siblings(':last')
        .text(data.error_string[i]);
      $('[name="' + data.inputerror[i] + '"]')
        .parent()
        .siblings(':last')
        .addClass('d-block');
    } else if (
      $('[name="' + data.inputerror[i] + '"]').siblings('.tox').length ||
      $('#' + data.inputerror[i]).siblings('.tox').length
    ) {
      $('[name="' + data.inputerror[i] + '"]')
        .siblings('.tox')
        .addClass('form-control is-invalid');
      $('[name="' + data.inputerror[i] + '"]')
        .siblings(':last')
        .text(data.error_string[i]);
      $('[name="' + data.inputerror[i] + '"]')
        .siblings(':last')
        .addClass('d-block');

      $('#' + data.inputerror[i])
        .siblings('.tox')
        .addClass('form-control is-invalid');
      $('#' + data.inputerror[i])
        .siblings(':last')
        .text(data.error_string[i]);
      $('#' + data.inputerror[i])
        .siblings(':last')
        .addClass('d-block');
    } else if (
      $('[name="' + data.inputerror[i] + '"]')
        .siblings(':last')
        .hasClass('litepicker-backdrop')
    ) {
      $('[name="' + data.inputerror[i] + '"]').addClass('is-invalid');
      $('[name="' + data.inputerror[i] + '"]')
        .siblings(':last')
        .prev()
        .text(data.error_string[i]);
      $('[name="' + data.inputerror[i] + '"]')
        .siblings(':last')
        .prev()
        .addClass('d-block');
    } else {
      let selector = $('[name="' + data.inputerror[i] + '"]');
      if (selector.length === 0) {
        selector = $('#' + data.inputerror[i]);
      }
      if (split_inputerror.length > 1) {
        selector = $('[name="' + split_inputerror[0] + '[]"]').eq(
          parseInt(split_inputerror[1])
        );
      }
      selector.addClass('is-invalid');
      let feedbackElement = selector.siblings(':last');
      if (!feedbackElement.hasClass('invalid-feedback')) {
        feedbackElement = selector.siblings(':nth-last-child(2)');
      }
      feedbackElement.text(data.error_string[i]);
      feedbackElement.addClass('d-block');
    }

    // Focus on the first invalid element
    if (i === 0) {
      if ($('[name="' + data.inputerror[i] + '"]').length) {
        $('[name="' + data.inputerror[i] + '"]').focus();
      } else if ($('#' + data.inputerror[i]).length) {
        $('#' + data.inputerror[i]).focus();
      }
    }
  }
}

function validation_error(data) {
  if (!data.inputerror) {
    notif_error('Terjadi kesalahan, silahkan coba lagi');
    return;
  }

  // Loop setiap input error
  for (var i = 0; i < data.inputerror.length; i++) {
    let inputName = data.inputerror[i];
    let errorMessage = data.error_string[i];

    let selector = $('[name="' + inputName + '"]');
    if (selector.length === 0) {
      selector = $('#' + inputName);
    }

    selector.addClass('is-invalid'); // Tambahkan class error

    // Cek apakah elemen sibling terakhir memiliki class "invalid-feedback"
    let feedbackElement = selector.siblings('.invalid-feedback');
    if (!feedbackElement.length) {
      feedbackElement = $(
        '#errorCreate' + inputName.charAt(0).toUpperCase() + inputName.slice(1)
      );
    }

    feedbackElement.text(errorMessage);
    feedbackElement.addClass('d-block');

    // Fokus pada input pertama yang error
    if (i === 0) {
      selector.focus();
    }
  }
}

// function to remove the invalid input
function empty_input() {
  remove_invalid();

  $('.form-group > input').removeClass('is-invalid');
  $('.form-group > select').removeClass('is-invalid');
  $('.form-group > textarea').removeClass('is-invalid');
  let form_element = $('form');
  form_element.find('select').not('.form-select-sm').val('').trigger('change');
  form_element.find('input').val('');
  form_element.find('textarea').text('');
  form_element.find('textarea').val('');
  if (form_element.find('.trumbowyg-js')) {
    $('.trumbowyg-js').each(function () {
      $(this).trumbowyg('html', '');
    });
  }
  if (form_element.find('[type=file]')) {
    $.each(
      form_element.find('[type=file]'),
      function (indexInArray, valueOfElement) {
        let this_target = $(valueOfElement).data('target');
        let target_show = this_target;
        if (this_target) {
          if (!this_target.includes('#') && !this_target.includes('.')) {
            target_show = '#' + this_target;
            if ($(target_show).length == 0) {
              target_show = '.' + this_target;
            }
          }

          if ($(target_show).length > 0) {
            $(target_show).empty();
          }
        }
      }
    );
  }
}

// select2
function select2_define(
  selector,
  url,
  custom_search_properties = data_search_select2_default,
  option_clear = false,
  dropdown_parent = $(selector).parent().parent(),
  method = 'GET'
) {
  var ajax_settings = {
    url: url,
    dataType: 'json',
    method: method,
    delay: 300,
    processResults: function (data) {
      return {
        results: data.data.map(function (item) {
          return {
            id: item[0],
            text: item[1],
            percentage: item[2],
          };
        }),
      };
    },
  };

  $(selector).select2({
    dropdownParent: dropdown_parent,
    allowClear: option_clear,
    width: '100%',
    ajax: {
      ...ajax_settings,
      ...custom_search_properties,
    },
    minimumResultsForSearch: -1,
    theme: 'bootstrap-5', // Add this line to style with Bootstrap 5
    placeholder: 'Opsi Pilihan', // Add a placeholder
    // minimumInputLength: 0, // Minimum characters to start searching
    // templateResult: formatResult, // Custom result formatting
    // templateSelection: formatSelection // Custom selection formatting
  });
}

function select(selector, dropdown_parent = $(selector).parent().parent()) {
  $(document).ready(function () {
    $(selector).select2({
      theme: 'bootstrap-5',
      width: '100%',
      dropdownParent: dropdown_parent,
    });
  });
}

// Litepicker
// function datepicker(selector) {
//   document.querySelectorAll(selector).forEach((el) => {
//     let settings = {
//       element: el,
//       allowInput: true,
//       format: 'YYYY-MM-DD',
//       lang: 'id-ID',
//       resetButton: true,
//       dropdowns: { minYear: 1990, maxYear: null, months: true, years: true },
//       highlightedDays: [
//         local_date.getFullYear() +
//           '-' +
//           String(local_date.getMonth() + 1).padStart(2, '0') +
//           '-' +
//           String(local_date.getDate()).padStart(2, '0'),
//       ],
//       plugins: ['mobilefriendly'],
//       resetButton: (picker) => {
//         let b = document.createElement('button');
//         b.innerText = 'Clear';
//         b.addEventListener('click', (evt) => {
//           evt.preventDefault();

//           // some custom action
//         });

//         return b;
//       },
//     };
//     new Litepicker(settings);
//   });
// }

function datepicker(selector) {
  document.querySelectorAll(selector).forEach((el) => {
    new Litepicker({
      element: el,
      allowInput: true,
      format: 'YYYY-MM-DD',
      lang: 'id-ID',
      resetButton: true,
      dropdowns: {
        minYear: 1990,
        maxYear: new Date().getFullYear(),
        months: true,
        years: true,
      },
      highlightedDays: [
        new Date().getFullYear() +
          '-' +
          String(new Date().getMonth() + 1).padStart(2, '0') +
          '-' +
          String(new Date().getDate()).padStart(2, '0'),
      ],
    });
  });
}

// Cleave JS
function cleave_define(selector, type_cleave) {
  var settings = {};

  if (type_cleave == 'phoneId') {
    settings = {
      phone: true,
      phoneRegionCode: 'ID',
      blocks: [4, 4, 4],
      delimiter: '-',
    };
  } else if (type_cleave == 'phone') {
    settings = {
      phone: true,
      blocks: [4, 4, 4],
      delimiter: '-',
    };
  } else if (type_cleave == 'currency') {
    settings = {
      numeral: true,
      prefix: 'Rp. ',
      delimiter: '.',
      numeralDecimalMark: ',',
    };
  } else if (type_cleave == 'number') {
    settings = {
      numeral: true,
      numeralDecimalMark: ',',
      delimiter: '.',
    };
  } else if (type_cleave == 'bank') {
    settings = {
      creditCard: true,
    };
  } else if (type_cleave == 'ktp') {
    settings = {
      blocks: [6, 4, 4, 2],
      numericOnly: true,
    };
  } else if(type_cleave == 'persentage') {
    settings = {
      numeral: true,
    };
  }

  const cleave = new Cleave(selector, settings);

  $(selector).data('cleave', cleave);

  // Saat form dikirim, ubah nilai ke raw value
  $(selector)
    .closest('form')
    .submit(function () {
      const rawValue = cleave.getRawValue();
      $(selector).val(rawValue);
    });

  return cleave;
}

// Filepond
FilePond.registerPlugin(
  FilePondPluginFileValidateSize,
  FilePondPluginImageExifOrientation,
  FilePondPluginImagePreview,
  FilePondPluginPdfPreview,
  FilePondPluginFileValidateType
);

function filepond_define(
  selector,
  config = {
    acceptedFileTypes: [
      'application/pdf',
      'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      'image/jpeg',
      'image/png',
      'application/vnd.ms-excel',
      'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    ],
    allowPdfPreview: true,
    allowImagePreview: true,
    allowMultiple: true,
    maxFileSize: '3MB',
    maxFiles: 12,
    pdfPreviewWidth: 190,
    pdfPreviewHeight: 190,
    imagePreviewHeight: 200,
    imagePreviewHeight: 200,
  }
) {
  const inputElement = document.querySelector(selector);
  const pond = FilePond.create(inputElement, config);
  pond.on('addfilestart', function (file) {
    var typeFile = file.fileType;
    var typeAccept = config.acceptedFileTypes;
    if (typeAccept.indexOf(typeFile) === -1) {
      notif_error('Ekstensi file tidak didukung');
      pond.removeFile(file.id);
    }
    const max_size_in_bytes = config.maxFileSize
      ? convert_to_bytes(config.maxFileSize)
      : 2 * 1024 * 1024;
    if (file.fileSize > max_size_in_bytes) {
      pond.removeFile(file.id);
      notif_error(`Ukuran file makasimal ${config.maxFileSize}.`);
      return;
    }

    generate_image(pond.getFiles());
  });
  return pond;
}

function generate_image(data) {
  data.forEach((val, index) => {
    var value = val.getMetadata().photo;
    value = value === undefined ? URL.createObjectURL(val.file) : value;
    console.log(value);

    if (!$(`#filepond--item-${val.id} > a.btn-view-image`).length) {
      $(`#filepond--item-${val.id}`).append(
        `<a href=${value} class="btn-view-image d-none" data-glightbox="type: image">Test</a>`
      );
      $(document).on(
        'click',
        `#filepond--item-${val.id} .filepond--image-preview-wrapper`,
        function (e) {
          lightbox = GLightbox({
            selector: `#filepond--item-${val.id} > .btn-view-image`,
            title: !1,
          });
          document
            .querySelector(`#filepond--item-${val.id} > .btn-view-image`)
            .click();
        }
      );
    }
  });
}

function convert_to_bytes(size) {
  const units = {
    B: 1,
    KB: 1024,
    MB: 1024 * 1024,
    GB: 1024 * 1024 * 1024,
  };

  const match = size.match(/^(\d+(?:\.\d+)?)\s*(B|KB|MB|GB)$/i);

  if (match) {
    const value = parseFloat(match[1]);
    const unit = match[2].toUpperCase();
    return value * (units[unit] || 1);
  } else {
    notif_error('Format ukuran file tidak valid');
  }
}

// Create Data
function handleAddForm(formId, url, modalId, fields, loadData) {
  $(formId).submit(function (e) {
    e.preventDefault();
    let formData = $(this).serialize();

    NProgress.start();

    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (data) {
        if (data.status === true) {
          
          closeModal(modalId);
          $(formId)[0].reset();
          $(formId).find('.dropdown').val(null).trigger('change');

          NProgress.done();

          Toastify({
            text: 'Data berhasil ditambahkan!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#4CAF50',
            },
            stopOnFocus: true,
          }).showToast();

          loadData();

          empty_input();
        } else {
          NProgress.done();
          Toastify({
            text: data.message || 'Terjadi kesalahan!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#E53E3E',
            },
            stopOnFocus: true,
          }).showToast();
          console.log('Response Error:', data);
          validation_error(data);
        }
      },
      error: function (xhr, status, error) {
        NProgress.done();
        console.log('XHR Error:', xhr.responseText);
        Toastify({
          text: 'Terjadi kesalahan, coba lagi!',
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
  });
}

// Update Data
function handleEditForm(formId, url, modalId, fields, loadData) {
  $(formId).submit(function (e) {
    e.preventDefault();
    let formData = $(this).serialize();
    let id = $('#EditId').val();

    NProgress.start();

    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (data) {
        if (data.status === true) {
          closeModal(modalId);
          $(formId)[0].reset();

          NProgress.done();

          Toastify({
            text: 'Data berhasil diperbarui!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#4CAF50',
            },
            stopOnFocus: true,
          }).showToast();

          loadData();

          empty_input();
        } else {
          NProgress.done();
          Toastify({
            text: data.message || 'Terjadi kesalahan!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#E53E3E',
            },
            stopOnFocus: true,
          }).showToast();
          console.log('Response Error:', data);
          validation_error(data);
        }
      },
      error: function (xhr, status, error) {
        NProgress.done();
        console.log('XHR Error:', xhr.responseText);
        Toastify({
          text: 'Terjadi kesalahan, coba lagi!',
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
  });
}

// Delete Data
function handleDelete(url, id, loadData) {
  Swal.fire({
    title: 'Apakah Anda yakin?',
    text: 'Data yang dihapus tidak dapat dikembalikan!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'Ya, hapus!',
    cancelButtonText: 'Batal',
  }).then((result) => {
    if (result.isConfirmed) {
      NProgress.start();
      $.ajax({
        url: url,
        type: 'POST',
        data: { id: id },
        dataType: 'json',
        success: function (data) {
          NProgress.done();
          Toastify({
            text: 'Data berhasil dihapus!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#4CAF50',
            },
            stopOnFocus: true,
          }).showToast();

          loadData();
        },
        error: function () {
          NProgress.done();
          Toastify({
            text: 'Terjadi kesalahan, coba lagi!',
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
  });
}

// loogout
$(document).ready(function () {
  $('#logout').click(function (e) {
    e.preventDefault();

    Swal.fire({
      title: 'Konfirmasi Logout',
      text: 'Apakah Anda yakin ingin keluar?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, Logout!',
      cancelButtonText: 'Batal'
    }).then((result) => {
      if (result.isConfirmed) {
        NProgress.start();

        $.ajax({
          url: '/logout',
          type: 'POST',
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('token'),
          },
          success: function (response) {
            NProgress.done();
            if (response.status) {
              localStorage.removeItem('token');

              Toastify({
                text: 'Logout berhasil!',
                duration: 3000,
                gravity: 'top',
                position: 'right',
                style: {
                  background: '#4CAF50',
                },
                stopOnFocus: true,
              }).showToast();

              setTimeout(() => {
                window.location.href = response.redirect;
              }, 1500);
            } else {
              Toastify({
                text: 'Terjadi kesalahan saat logout: ' + response.message,
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
            NProgress.done();
            Toastify({
              text: 'Terjadi kesalahan saat logout.',
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
    });
  });
});

// Update Password
function handleEditPassword(formId, url, modalId, fields, loadData) {
  $(formId).submit(function (e) {
    e.preventDefault();
    let formData = $(this).serialize();
    let id = $('#EditId').val();

    NProgress.start();

    $.ajax({
      url: url,
      type: 'POST',
      data: formData,
      dataType: 'json',
      success: function (data) {
        if (data.status === true) {
          closeModal(modalId);
          $(formId)[0].reset();

          NProgress.done();

          Toastify({
            text: 'Data berhasil diperbarui!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#4CAF50',
            },
            stopOnFocus: true,
          }).showToast();

          closeModal(modalId);
          $(formId)[0].reset();
          loadData();
          empty_input();
        } else {
          NProgress.done();
          Toastify({
            text: data.message || 'Terjadi kesalahan!',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#E53E3E',
            },
            stopOnFocus: true,
          }).showToast();
          validation_error(data);
        }
      },
      error: function (xhr, status, error) {
        NProgress.done();
        console.log('XHR Error:', xhr.responseText);
        Toastify({
          text: 'Terjadi kesalahan, coba lagi!',
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
  });
}