function showModal(modalId, formId, fields) {
  let modal = new bootstrap.Modal(document.getElementById(modalId));
  modal.show();

  $('#' + formId)[0].reset();
  fields.forEach((field) => {
    $('#' + field).removeClass('is-invalid');
    $('#error' + field.charAt(0).toUpperCase() + field.slice(1)).text('');
  });
}

function closeModal(modalId) {
  let modalElement = document.getElementById(modalId);
  let modalInstance = bootstrap.Modal.getInstance(modalElement);
  if (modalInstance) modalInstance.hide();
}