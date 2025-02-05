const registerForm = document.querySelector('.register-form');

// const apiUrl = 'http://localhost:8000/api';
const apiUrl = 'https://virtuard.com/api';

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    registerForm.querySelector('.btn-primary').setAttribute('type', 'button');
    registerForm.querySelector('.loader').classList.add('active');
    registerForm.querySelector('.alert-message.success').classList.add('hidden');
    registerForm.querySelector('.alert-message.error').classList.add('hidden');
    registerForm.querySelectorAll('.error-message').forEach((error) => {
        error.classList.add('hidden');
    });
    
    const formData = new FormData(registerForm);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch(`${apiUrl}/auth/register`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data), // Send data in the body
        });

        const json = await response.json();
        
        if(json.status) {
            registerForm.querySelector('.alert-message.success').classList.remove('hidden');
            registerForm.reset();

            loggedInUser(data)
        } else {
            if(!json.message) {
                registerForm.querySelector('.alert-message.error').classList.remove('hidden');
            }
            
            for(const key in json.message) {
                const error = json.message[key];
                const errorElement = registerForm.querySelector(`.error-message.${key}`);
                errorElement.textContent = error;
                errorElement.classList.remove('hidden');
            }

            const checkEmailResponse = await fetch(`${apiUrl}/auth/check-email-availability?email=${data.email}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                },
            });
            const checkEmailJson = await checkEmailResponse.json()
            
            if(checkEmailJson.status == 0 && !json.message.email) {
                registerForm.querySelector('.alert-message.success').classList.remove('hidden');
                registerForm.reset();

                loggedInUser(data)
            }
        }
    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);

        const checkEmailResponse = await fetch(`${apiUrl}/auth/check-email-availability?email=${data.email}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
            },
        });
        const checkEmailJson = await checkEmailResponse.json()
        
        if(checkEmailJson.status == 0) {
            registerForm.querySelector('.alert-message.success').classList.remove('hidden');
            registerForm.reset();

            loggedInUser(data)
        }
    } finally {
        registerForm.querySelector('.btn-primary').setAttribute('type', 'submit');
        registerForm.querySelector('.loader').classList.remove('active');
    }
})

async function loggedInUser(data) {
    const loginResponse = await fetch(`${apiUrl}/auth/login`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: data.email, password: data.password, device_name: navigator.userAgent }),
    });
    const loginResponseJson = await loginResponse.json()

    // console.log(loginResponseJson)

    window.location.href = `https://virtuard.com/login?token=${loginResponseJson.token}`;
} 