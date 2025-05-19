$(document).ready(function () {
    $("#formAuthentication").on("submit", function (e) {
        e.preventDefault();

        let username = $("#email").val();
        let password = $("#password").val();
        NProgress.start();

        $.ajax({
            url: "do-login",
            type: "POST",
            data: {
                username: username,
                password: password
            },
            dataType: "json",
            success: function (response) {
                if (response.status === true) {
                    NProgress.done();
                    localStorage.setItem("token", response.data.token);

                    Toastify({
                        text: 'Login berhasil!',
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        style: {
                          background: '#4CAF50',
                        },
                        stopOnFocus: true,
                      }).showToast();
        
                      setTimeout(() => {
                        window.location.href = response.data.redirect;
                      }, 1500);
                } else {
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
                    console.log('Response Error:', response);
                    validation_error(response);
                }
            },
            error: function (xhr, status, error) {
                console.log(xhr.responseText);
                Toastify({
                    text: 'Terjadi kesalahan pada server.',
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
    });
});
