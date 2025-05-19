$(document).ready(function() {
    $('#formAuthentication').on('submit', function(e) {
        e.preventDefault();

        var base_url = $("meta[name='base_url']").attr("content");
        
        let username = $('#username').val().trim();
        NProgress.start();

        $.ajax({
            url: base_url + 'do-forgot-password',
            type: 'POST',
            data: { username: username },
            dataType: 'json',
            beforeSend: function() {
                $('.btn-green').attr('disabled', true).text('Mengirim...');
            },
            success: function(response) {
                if (response.status === true) {  
                    localStorage.setItem("email", response.email);
                    
                    Toastify({
                        text: 'Kode OTP telah dikirim ke ' + response.email + '!',
                        duration: 6000,
                        gravity: 'top',
                        position: 'right',
                        style: {
                          background: '#4CAF50',
                        },
                        stopOnFocus: true,
                      }).showToast();

                      setTimeout(function () {
                        window.location.href = base_url + "validate-otp";
                        NProgress.done();
                      }, 6000);
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
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
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
            },
            complete: function() {
                $('.btn-green').attr('disabled', false).text('Kirim');
            }
        });
    });
});
