var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'donations/';

function loadData(startDate = '', endDate = '') {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data-table')) {
      $('#data-table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/list_data',
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
        { data: 5, title: 'Ranting' },
        { data: 3, title: 'Perolehan' },
        { data: 4, title: '10%' },
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
      url: "/export_commision/excel",
      type: "POST",
      data: {
          month_excel: month_excel,
          year_excel: year_excel,
      },
      xhrFields: {
          responseType: 'blob' 
      },
      beforeSend: function () {
          // Tambahkan loader jika diperlukan
      },
      success: function (response, status, xhr) {
          $("#modalExcel").modal("hide");

          NProgress.done();

          let filename = "komisi_petugas.xlsx";

          // Buat link untuk mendownload file
          let blob = new Blob([response], { type: xhr.getResponseHeader('Content-Type') });
          let link = document.createElement('a');
          link.href = window.URL.createObjectURL(blob);
          link.download = filename;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
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
      }
  });
}

function exportPDF() {
  let month = $('#PDFMonth').val();
  let year = $('#PDFYear').val();
  
  // Validasi input
  if (!month) {
    $('#errorPDFMonth').text('Bulan harus diisi!');
    return;
  } else {
    $('#errorPDFMonth').text('');
  }

  if (!year) {
    $('#errorPDFYear').text('Tahun harus diisi!');
    return;
  } else {
    $('#errorPDFYear').text('');
  }

  NProgress.start();

  // Kirim request AJAX
  $.ajax({
    url: '/export_commision/pdf',
    type: 'POST',
    data: {
      month_pdf: month,
      year_pdf: year,
    },
    xhrFields: {
      responseType: 'blob',
    },
    beforeSend: function () {
      // Tambahkan loader jika diperlukan
    },
    success: function (response, status, xhr) {
      $('#modalPDF').modal('hide');

      NProgress.done();

      var blob = new Blob([response], { type: 'application/pdf' });
      var url = window.URL.createObjectURL(blob);

      // Buka di tab baru
      window.open(url, '_blank');
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
    format: "mm",
    viewMode: "months",
    minViewMode: "months",
    autoclose: true
  });

  // Datepicker untuk memilih Tahun saja
  $('.datepicker-year').datepicker({
      format: "yyyy",
      viewMode: "years",
      minViewMode: "years",
      autoclose: true
  });
});

$('#btnCloseCreate').click(function () {
  closeModal('modalCreate');
});

handleAddForm(
  '#formCreate',
  '/officers-commision/save',
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

$("#formExcel").submit(function (e) {
  e.preventDefault();
  exportExcel();
});

// export PDF
$('#btnPDF').click(function () {
  showModal('modalPDF', 'formPDF', ['PDFMonth', 'PDFYear']);

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

$('#formPDF').submit(function (e) {
  e.preventDefault();
  exportPDF();
});