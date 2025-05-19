var url_controller_attendance =
  baseUrl + '/' + prefix_folder_admin + 'profile/';

function loadData() {
  $(document).ready(function () {
    $.ajax({
      url: "profile/list_data",
      type: "GET",
      dataType: "json",
      success: function (response) {
          if (response.data.length > 0) {
              let user = response.data[0];
              let gender = (user[3] === 'male') ? 'Laki-laki' : 'Perempuan';

              let profileHtml = `
                    <div class="row g-3">
                        <!-- Kolom Informasi Akun -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h5 class="fw-bold">Informasi Akun</h5>
                                <div class="row g-2">
                                  <!-- Username -->
                                  <div class="col-md-4 col-6 fw-bold mb-md-3 mb-0">Username</div>
                                  <div class="col-md-1 col-2 text-center fw-bold mb-md-3 mb-0">:</div>
                                  <div class="col-md-7 col-12 fw-bold mb-md-3 mb-2">${user[2]}</div>

                                  <!-- Email -->
                                  <div class="col-md-4 col-6 fw-bold mb-md-3 mb-0">Email</div>
                                  <div class="col-md-1 col-2 text-center fw-bold mb-md-3 mb-0">:</div>
                                  <div class="col-md-7 col-12 fw-bold mb-md-3 mb-2">${user[5]}</div>

                                  <!-- Role -->
                                  <div class="col-md-4 col-6 fw-bold mb-md-3 mb-0">Role</div>
                                  <div class="col-md-1 col-2 text-center fw-bold mb-md-3 mb-0">:</div>
                                  <div class="col-md-7 col-12 fw-bold mb-md-3 mb-2">${user[6]}</div>

                              </div>
                            </div>
                        </div>
                        
                        <!-- Kolom Informasi Pribadi -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded bg-light">
                                <h5 class="fw-bold">Informasi Pribadi</h5>
                                <div class="row g-2">
                                  <!-- Nama -->
                                  <div class="col-md-4 col-6 fw-bold mb-md-3 mb-0">Nama</div>
                                  <div class="col-md-1 col-2 text-center fw-bold mb-md-3 mb-0">:</div>
                                  <div class="col-md-7 col-12 fw-bold mb-md-3 mb-2">${user[1]}</div>

                                  <!-- No. HP -->
                                  <div class="col-md-4 col-6 fw-bold mb-md-3 mb-0">No. HP</div>
                                  <div class="col-md-1 col-2 text-center fw-bold mb-md-3 mb-0">:</div>
                                  <div class="col-md-7 col-12 fw-bold mb-md-3 mb-2">${user[4]}</div>

                                  <!-- Jenis Kelamin -->
                                  <div class="col-md-4 col-6 fw-bold mb-md-3 mb-0">Jenis Kelamin</div>
                                  <div class="col-md-1 col-2 text-center fw-bold mb-md-3 mb-0">:</div>
                                  <div class="col-md-7 col-12 fw-bold mb-md-3 mb-2">${gender}</div>
                              </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                      ${user[7]}
                    </div>
              `;

              $("#profile-container").html(profileHtml);
          } else {
              $("#profile-container").html("<p class='text-danger'>Data tidak ditemukan</p>");
          }
        },
        error: function () {
            $("#profile-container").html("<p class='text-danger'>Gagal mengambil data</p>");
        },
    });
  });
}

$(document).ready(function () {
  NProgress.start();
  loadData();
  NProgress.done();
});

// Edit Data
$(document).on('click', '#btnEdit', function () {
  let id = $(this).data('id');
  let name = $(this).data('name');
  let phone = $(this).data('phone');
  let gender = $(this).data('gender');
  let email = $(this).data('email');

  showModal('modalEdit', 'formEdit', ['EditName', 'EditPhone', 'EditType', 'EditEmail']);

  $('#EditId').val(id);
  $('#EditName').val(name);
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

handleEditForm(
  '#formEdit',
  '/super-admin/profile/update',
  'modalEdit',
  ['EditName', 'EditPhone', 'EditType', 'EditEmail'],
  function () {
    loadData();
  }
);

// Edit Password
$(document).on('click', '#btnEditPassword', function () {
  showModal('modalEditPassword', 'formEditPassword', ['current_password', 'new_password', 'confirm_password']);
});

// Tombol Tutup Modal Edit
$('#btnCloseEditPassword').click(function () {
  closeModal('modalEditPassword');
});

handleEditPassword(
  '#formEditPassword',
  '/change-password',
  'modalEditPassword',
  ['current_password', 'new_password', 'confirm_password'],
  function () {
    loadData();
  }
);
