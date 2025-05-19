var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'donations/';

function loadData(startDate = '', endDate = '') {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data-table')) {
      $('#data-table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/officers-commision/list_data',
        type: 'GET',
        data: {
          start_date: startDate,
          end_date: endDate,
        },
      },
      columns: [
        {
          data: null,
          render: (data, type, row, meta) => meta.row + 1,
          title: 'No.',
        },
        { data: 1, title: 'Nama Petugas' },
        { data: 2, title: 'RW' },
        { data: 3, title: 'Ranting' },
        { data: 4, title: 'Perolehan' },
        { data: 5, title: '10%' },
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

function exportExcel() {
  let month_excel = $('#ExcelMonth').val();
  let year_excel = $('#ExcelYear').val();

  NProgress.start();

  // Kirim request AJAX
  $.ajax({
    url: '/super-admin/export_commision/excel',
    type: 'POST',
    data: {
      month_excel: month_excel,
      year_excel: year_excel,
    },
    xhrFields: {
      responseType: 'blob',
    },
    beforeSend: function () {
      // Tambahkan loader jika diperlukan
    },
    success: function (response, status, xhr) {
      if (response.status === false) {
        NProgress.done();
        Toastify({
          text: response.message,
          duration: 3000,
          gravity: 'top',
          position: 'right',
          style: {
            background: '#E53E3E',
          },
          stopOnFocus: true,
        }).showToast();
      } else {
        $('#modalExcel').modal('hide');

        NProgress.done();

        let filename = 'komisi_petugas.xlsx';

        // Buat link untuk mendownload file
        let blob = new Blob([response], {
          type: xhr.getResponseHeader('Content-Type'),
        });
        let link = document.createElement('a');
        link.href = window.URL.createObjectURL(blob);
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }
    },
    error: function (xhr, status, error) {
      NProgress.done();

      Toastify({
        text: 'Gagal mengekspor data. Coba lagi.!',
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

function exportPDF() {
  let month = $('#PDFMonth').val();
  let year = $('#PDFYear').val();
  let branch_id = $('#PDFBranch').val();

  // Validasi input
  if (!month || !year || !branch_id) {
    alert('Semua input harus diisi!');
    return;
  }

  NProgress.start();

  $.ajax({
    url: '/super-admin/export_commision/pdf',
    type: 'POST',
    data: {
      month_pdf: month,
      year_pdf: year,
      branch_id: branch_id,
    },
    xhrFields: {
      responseType: 'blob',
    },
    success: function (response, status, xhr) {
      const contentType = xhr.getResponseHeader('Content-Type');

      if (contentType === 'application/json') {
        // Jika respons adalah JSON (error)
        const reader = new FileReader();
        reader.onload = function () {
          const errorData = JSON.parse(reader.result);
          Toastify({
            text: errorData.message || 'Terjadi kesalahan',
            duration: 3000,
            gravity: 'top',
            position: 'right',
            style: {
              background: '#E53E3E',
            },
            stopOnFocus: true,
          }).showToast();
        };
        reader.readAsText(response);
      } else if (contentType === 'application/pdf') {
        // Jika respons adalah PDF
        const blob = new Blob([response], { type: 'application/pdf' });
        const url = window.URL.createObjectURL(blob);
        window.open(url, '_blank');
      }

      NProgress.done();
    },
    error: function (xhr, status, error) {
      NProgress.done();
      Toastify({
        text: 'Gagal mengekspor data. Coba lagi.!',
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

$(document).ready(function () {
  NProgress.start();
  loadData();
  NProgress.done();
  datepicker('#start_date');
  datepicker('#end_date');
});

// Date FIlter
$('#filter-form').on('submit', function (e) {
  e.preventDefault();
  let startDate = $('#start_date').val();
  let endDate = $('#end_date').val();
  NProgress.start();
  loadData(startDate, endDate);
  NProgress.done();
});

// Create Comission
$('#btn-komisi').click(function () {
  showModal('modalCreate', 'formCreate', ['CreateMonth', 'CreateYear']);

  $('.datepicker-month').datepicker({
    format: 'mm',
    viewMode: 'months',
    minViewMode: 'months',
    autoclose: true,
  });

  // Datepicker untuk memilih Tahun saja
  $('.datepicker-year').datepicker({
    format: 'yyyy',
    viewMode: 'years',
    minViewMode: 'years',
    autoclose: true,
  });
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
});

handleAddForm(
  '#formCreate',
  '/super-admin/officers-commision/save',
  'modalCreate',
  ['CreateMonth', 'CreateYear'],
  function () {
    loadData();
  }
);

// export excel
$('#btnExcel').click(function () {
  showModal('modalExcel', 'formExcel', ['ExcelMonth', 'ExcelYear']);

  $('.datepicker-month').datepicker({
    format: 'mm',
    viewMode: 'months',
    minViewMode: 'months',
    autoclose: true,
  });

  // Datepicker untuk memilih Tahun saja
  $('.datepicker-year').datepicker({
    format: 'yyyy',
    viewMode: 'years',
    minViewMode: 'years',
    autoclose: true,
  });
});

$('#btnCloseExcel').click(function () {
  closeModal('modalExcel');
});

$('#formExcel').submit(function (e) {
  e.preventDefault();
  exportExcel();
});

// export PDF
$('#btnPDF').click(function () {
  showModal('modalPDF', 'formPDF', ['PDFMonth', 'PDFYear', 'PDFBranch']);

  $('.datepicker-month').datepicker({
    format: 'mm',
    viewMode: 'months',
    minViewMode: 'months',
    autoclose: true,
  });

  // Datepicker untuk memilih Tahun saja
  $('.datepicker-year').datepicker({
    format: 'yyyy',
    viewMode: 'years',
    minViewMode: 'years',
    autoclose: true,
  });
});

$('#btnClosePDF').click(function () {
  closeModal('modalPDF');
});

// dropdown
select2_define(
  '#PDFBranch',
  '/super-admin/branches/list_data',
  {},
  true,
  $('#modalPDF')
);

$('#PDFBranch').on('select2:select', function (e) {
  var selectedData = e.params.data;
  $('#selectedBranch').val(selectedData.id);
});

$('#formPDF').submit(function (e) {
  e.preventDefault();
  exportPDF();
});
