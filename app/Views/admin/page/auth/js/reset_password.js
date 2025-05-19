$(document).ready(function() {
    $('#formAuthentication').on('submit', function(e) {
        e.preventDefault();

        var base_url = $("meta[name='base_url']").attr("content");
        
        var password = $("#password").val();
        var confirm_password = $("#confirm_password").val();
        var email = localStorage.getItem("email");

        NProgress.start();

        $.ajax({
            url: base_url + "do-reset-password",
            type: "POST",
            data: {
                email: email,
                password: password,
                confirm_password: confirm_password
            },
            success: function(response) {
                if (response.status === true) {
                    NProgress.done();
                    Toastify({
                        text: response.message,
                        duration: 3000,
                        gravity: 'top',
                        position: 'right',
                        style: {
                          background: '#4CAF50',
                        },
                        stopOnFocus: true,
                      }).showToast();
                      
                    localStorage.removeItem("email");
                    window.location.href = base_url + "login";
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
                }
            }
        });
    });
});