@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center bravo-login-form-page bravo-login-page">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Reset Password') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('password.update') }}">
                            @include('Layout::admin.message')
                            @csrf
                            <input type="hidden" name="token" value="{{ request()->route('token') }}">
                            <div class="form-group row">
                                <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                                <div class="col-md-6">
                                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email',request()->email) }}" required autofocus>
                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ __($errors->first('email')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="password" type="password" class="form-control" name="password" required minlength="8">
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                                <i class="fa fa-eye" id="togglePasswordIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted" id="passwordHelp">Password must be at least 8 characters long.</small>
                                    <div class="invalid-feedback" id="passwordError" style="display: none;"></div>
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ __($errors->first('password')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="password-confirm" class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                                <i class="fa fa-eye" id="togglePasswordConfirmIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback" id="passwordConfirmError" style="display: none;"></div>
                                    @if ($errors->has('password_confirmation'))
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ __($errors->first('password_confirmation')) }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Reset Password') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password-confirm');
    const passwordError = document.getElementById('passwordError');
    const passwordConfirmError = document.getElementById('passwordConfirmError');
    const form = document.querySelector('form');
    
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const togglePasswordIcon = document.getElementById('togglePasswordIcon');
    const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
    const togglePasswordConfirmIcon = document.getElementById('togglePasswordConfirmIcon');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            togglePasswordIcon.classList.toggle('fa-eye');
            togglePasswordIcon.classList.toggle('fa-eye-slash');
        });
    }
    
    if (togglePasswordConfirm) {
        togglePasswordConfirm.addEventListener('click', function() {
            const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordConfirmInput.setAttribute('type', type);
            togglePasswordConfirmIcon.classList.toggle('fa-eye');
            togglePasswordConfirmIcon.classList.toggle('fa-eye-slash');
        });
    }
    
    // Validation functions
    function validatePassword() {
        const password = passwordInput.value;
        let isValid = true;
        let errorMessage = '';
        
        if (password.length < 8) {
            isValid = false;
            errorMessage = 'Password must be at least 8 characters long.';
        }
        
        if (isValid) {
            passwordInput.classList.remove('is-invalid');
            passwordInput.classList.add('is-valid');
            passwordError.style.display = 'none';
        } else {
            passwordInput.classList.remove('is-valid');
            passwordInput.classList.add('is-invalid');
            passwordError.textContent = errorMessage;
            passwordError.style.display = 'block';
        }
        
        return isValid;
    }
    
    function validatePasswordConfirm() {
        const password = passwordInput.value;
        const passwordConfirm = passwordConfirmInput.value;
        let isValid = true;
        let errorMessage = '';
        
        if (passwordConfirm.length === 0) {
            isValid = false;
            errorMessage = 'Please confirm your password.';
        } else if (password !== passwordConfirm) {
            isValid = false;
            errorMessage = 'Passwords do not match.';
        }
        
        if (isValid) {
            passwordConfirmInput.classList.remove('is-invalid');
            passwordConfirmInput.classList.add('is-valid');
            passwordConfirmError.style.display = 'none';
        } else {
            passwordConfirmInput.classList.remove('is-valid');
            passwordConfirmInput.classList.add('is-invalid');
            passwordConfirmError.textContent = errorMessage;
            passwordConfirmError.style.display = 'block';
        }
        
        return isValid;
    }
    
    // Real-time validation
    passwordInput.addEventListener('input', function() {
        validatePassword();
        if (passwordConfirmInput.value.length > 0) {
            validatePasswordConfirm();
        }
    });
    
    passwordConfirmInput.addEventListener('input', function() {
        validatePasswordConfirm();
    });
    
    // Form submission validation
    form.addEventListener('submit', function(e) {
        const isPasswordValid = validatePassword();
        const isPasswordConfirmValid = validatePasswordConfirm();
        
        if (!isPasswordValid || !isPasswordConfirmValid) {
            e.preventDefault();
            e.stopPropagation();
            
            // Focus on first invalid field
            if (!isPasswordValid) {
                passwordInput.focus();
            } else if (!isPasswordConfirmValid) {
                passwordConfirmInput.focus();
            }
            
            return false;
        }
    });
});
</script>
@endpush

@push('css')
<style>
.input-group-append .btn {
    border-left: 0;
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
.input-group .form-control.is-valid {
    border-color: #28a745;
}
.input-group .form-control.is-invalid {
    border-color: #dc3545;
}
</style>
@endpush
