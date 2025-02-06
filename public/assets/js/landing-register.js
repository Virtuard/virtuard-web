$('.register-form [type=submit]').click(function (e) {
    e.preventDefault();
    let form = $(this).closest('.register-form');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': form.find('meta[name="csrf-token"]').attr('content')
        }
    });
    console.log(form)
    console.log(form.find('input[name=email]').val());
    $.ajax({
        'url':  '/register',
        'data': {
            'email': form.find('input[name=email]').val(),
            'password': form.find('input[name=password]').val(),
            'first_name': form.find('input[name=first_name]').val(),
            'last_name': form.find('input[name=last_name]').val(),
            'phone': form.find('input[name=phone]').val(),
            'term': form.find('input[name=term]').is(":checked") ? 1 : '',
            'g-recaptcha-response': form.find('[name=g-recaptcha-response]').val(),
            'is_auto_login': true
        },
        'type': 'POST',
        beforeSend: function () {
            form.find('.error').hide();
            form.find('.loader').addClass('active')
            $(".form-submit").attr('disabled', true);
        },
        success: function (data) {
            console.log(data)
            form.find('.loader').removeClass('active');
            $(".form-submit").attr('disabled', false);
            if (data.error === true) {
                if (data.messages !== undefined) {
                    for(var item in data.messages) {
                        var msg = data.messages[item];
                        form.find('.error-'+item).show().text(msg[0]);
                    }
                }
                if (data.messages.message_error !== undefined) {
                    form.find('.alert-message.error').removeClass('hidden').html(data.messages.message_error[0]);

                }
            }
            if (typeof BravoReCaptcha !== 'undefined') {
                BravoReCaptcha.reset('register');
                BravoReCaptcha.reset('register_normal');
            }
            if (data.redirect !== undefined) {
                window.location.href = data.redirect
            }
        },
        error:function (e) {
            console.log(e)
            form.find('.loader').removeClass('active');
            $(".form-submit").attr('disabled', false);
            if(typeof e.responseJSON !== "undefined" && typeof e.responseJSON.message !='undefined'){
                form.find('.message-error').show().html('<div class="alert alert-danger">' + e.responseJSON.message + '</div>');
            }

            if (typeof BravoReCaptcha !== 'undefined') {
                BravoReCaptcha.reset('register');
                BravoReCaptcha.reset('register_normal');
            }
        }
    });
})