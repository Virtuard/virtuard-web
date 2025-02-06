$('.register-form [type=submit]').click(function (e) {
    e.preventDefault();
    let form = $(this).closest('.register-form');

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': form.find('meta[name="csrf-token"]').attr('content')
        }
    });
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

// const registerForm = document.querySelector('.register-form');

// // const apiUrl = 'http://localhost:8000/api';
// const apiUrl = 'https://virtuard.com/api';

// registerForm.addEventListener('submit', async (e) => {
//     e.preventDefault();
    
//     registerForm.querySelector('.btn-primary').setAttribute('type', 'button');
//     registerForm.querySelector('.loader').classList.add('active');
//     registerForm.querySelector('.alert-message.success').classList.add('hidden');
//     registerForm.querySelector('.alert-message.error').classList.add('hidden');
//     registerForm.querySelectorAll('.error-message').forEach((error) => {
//         error.classList.add('hidden');
//     });
    
//     const formData = new FormData(registerForm);
//     const data = Object.fromEntries(formData);
    
//     try {
//         const response = await fetch(`${apiUrl}/auth/register`, {
//             method: 'POST',
//             headers: {
//                 'Content-Type': 'application/json',
//             },
//             body: JSON.stringify(data), // Send data in the body
//         });

//         const json = await response.json();
        
//         if(json.status) {
//             registerForm.querySelector('.alert-message.success').classList.remove('hidden');
//             registerForm.reset();

//             loggedInUser(data)
//         } else {
//             if(!json.message) {
//                 registerForm.querySelector('.alert-message.error').classList.remove('hidden');
//             }
            
//             for(const key in json.message) {
//                 const error = json.message[key];
//                 const errorElement = registerForm.querySelector(`.error-message.${key}`);
//                 errorElement.textContent = error;
//                 errorElement.classList.remove('hidden');
//             }

//             const checkEmailResponse = await fetch(`${apiUrl}/auth/check-email-availability?email=${data.email}`, {
//                 method: 'GET',
//                 headers: {
//                     'Content-Type': 'application/json',
//                 },
//             });
//             const checkEmailJson = await checkEmailResponse.json()
            
//             if(checkEmailJson.status == 0 && !json.message.email) {
//                 registerForm.querySelector('.alert-message.success').classList.remove('hidden');
//                 registerForm.reset();

//                 loggedInUser(data)
//             }
//         }
//     } catch (error) {
//         console.error('There was a problem with the fetch operation:', error);

//         const checkEmailResponse = await fetch(`${apiUrl}/auth/check-email-availability?email=${data.email}`, {
//             method: 'GET',
//             headers: {
//                 'Content-Type': 'application/json',
//             },
//         });
//         const checkEmailJson = await checkEmailResponse.json()
        
//         if(checkEmailJson.status == 0) {
//             registerForm.querySelector('.alert-message.success').classList.remove('hidden');
//             registerForm.reset();

//             loggedInUser(data)
//         }
//     } finally {
//         registerForm.querySelector('.btn-primary').setAttribute('type', 'submit');
//         registerForm.querySelector('.loader').classList.remove('active');
//     }
// })

// async function loggedInUser(data) {
//     const loginResponse = await fetch(`${apiUrl}/auth/login`, {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({ email: data.email, password: data.password, device_name: navigator.userAgent }),
//     });
//     const loginResponseJson = await loginResponse.json()

//     // console.log(loginResponseJson)

//     window.location.href = `https://virtuard.com/login?token=${loginResponseJson.token}`;
// } 