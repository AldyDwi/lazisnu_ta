var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'transaction/';

function loadData(startDate = '', endDate = '') {
  $(document).ready(function () {
    if ($.fn.DataTable.isDataTable('#data_table')) {
      $('#data_table').DataTable().destroy();
    }

    $('#data-table').DataTable({
      ajax: {
        url: baseUrl + '/transaction/list_data',
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
        { data: 9, title: 'Tanggal', render: (data) => (data ? data : '-') },
        {
          data: 11,
          title: 'Tipe',
          render: function (data, type, row) {
            return data === 'fund_distribution'
              ? 'Distribusi Dana'
              : data === 'donations'
              ? 'Donasi'
              : data === 'field_officer_commision'
              ? 'Komisi Petugas'
              : 'Lainnya';
          },
        },
        { data: 1, title: 'Nama Warga', render: (data) => (data ? data : '-') },
        {
          data: 2,
          title: 'Nama Petugas',
          render: (data) => (data ? data : '-'),
        },
        { data: 3, title: 'RW', render: (data) => (data ? data : '-') },
        { data: 12, title: 'Ranting', render: (data) => (data ? data : '-') },
        { data: 4, title: 'Program', render: (data) => (data ? data : '-') },
        { data: 5, title: 'Debit', render: (data) => (data ? data : '-') },
        { data: 6, title: 'Kredit', render: (data) => (data ? data : '-') },
        { data: 7, title: 'Saldo', render: (data) => (data ? data : '-') },
        {
          data: 8,
          title: 'Catatan',
          visible: false,
          render: (data) => (data ? data : '-'),
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

function exportExcel() {
  let startDate = $("#ExcelStart").val();
  let endDate = $("#ExcelEnd").val();

  NProgress.start();

  // Kirim request AJAX
  $.ajax({
      url: "/super-admin/export_transaction/excel", // Sesuaikan dengan URL endpoint ekspor
      type: "GET",
      data: {
          start_excel: startDate,
          end_excel: endDate
      },
      xhrFields: {
          responseType: 'blob' // Untuk menangani file binary (Excel)
      },
      beforeSend: function () {
          // Tambahkan loader jika diperlukan
      },
      success: function (response, status, xhr) {
          $("#modalExcel").modal("hide");

          NProgress.done();

          let filename = "data_export.xlsx";

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
  let branch_id = $('#PDFBranch').val();
  
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

  if (!branch_id) {
    $('#errorPDFBranch').text('Ranting harus diisi!');
    return;
  } else {
    $('#errorPDFBranch').text('');
  }

  NProgress.start();

  // Kirim request AJAX
  $.ajax({
    url: '/super-admin/export_transaction/pdf',
    type: 'POST',
    data: {
      month_pdf: month,
      year_pdf: year,
      branch_id: branch_id,
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

// export excel
$('#btnExcel').click(function () {
  showModal('modalExcel', 'formExcel', ['ExcelStart', 'ExcelEnd']);

  datepicker('#ExcelStart');
  datepicker('#ExcelEnd');
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