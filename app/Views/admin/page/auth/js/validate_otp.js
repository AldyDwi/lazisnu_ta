$(document).ready(function() {
    $('#formAuthentication').on('submit', function(e) {
        e.preventDefault();

        var base_url = $("meta[name='base_url']").attr("content");

        var otp = $('#otp').val();

        NProgress.start();

        $.ajax({
            url: base_url + "do-validate-otp",
            type: "POST",
            data: {
                otp: otp
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
                      
                    // localStorage.removeItem("resetToken");
                    window.location.href = base_url + "reset-password";
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