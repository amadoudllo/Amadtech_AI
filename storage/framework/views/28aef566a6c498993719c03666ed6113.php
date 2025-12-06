<?php
// Pure PHP/HTML login page - no Blade processing to avoid Vite manifest errors
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Se connecter - Amadtech_AI</title>
    <link href="/css/auth.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
</head>
<body>
    <!-- Auth Container -->
    <div class="auth-container">
        <!-- Right Section - Login Form -->
        <div class="auth-right-section">
            <div class="auth-form-wrapper">
                <!-- Header -->
                <div class="auth-form-header">
                    <h1>Connexion</h1>
                    <p>Accédez à votre compte pour continuer</p>
                </div>

                <!-- Social Login -->
                <div class="auth-social-login">
                    <button class="btn-social-google" type="button" title="Continuer avec Google">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="currentColor"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="currentColor"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="currentColor"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="currentColor"/>
                        </svg>
                        <span>Continuer avec Google</span>
                    </button>
                </div>

                <!-- Divider -->
                <div class="form-divider">
                    <span>ou</span>
                </div>

                <!-- Error Alert -->
                <div id="alertContainer"></div>

                <!-- Login Form -->
                <form id="loginForm" class="auth-form" method="POST" action="/login">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <input
                            class="form-input"
                            id="email"
                            name="email"
                            placeholder="vous@exemple.com"
                            required
                            type="email"
                            value="<?php echo old('email') ?? ''; ?>"
                        />
                        <span class="form-error" id="emailError"></span>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Mot de passe</label>
                        <input
                            class="form-input"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            required
                            type="password"
                        />
                        <span class="form-error" id="passwordError"></span>
                    </div>

                    <!-- Remember & Forgot -->
                    <div class="form-footer">
                        <label class="form-checkbox">
                            <input name="remember" type="checkbox" value="on"/>
                            <span>Se souvenir de moi</span>
                        </label>
                        <a class="form-link" href="#">Mot de passe oublié ?</a>
                    </div>

                    <!-- Submit Button -->
                    <button class="btn btn-primary" id="submitBtn" type="submit">
                        <span>Se connecter</span>
                    </button>

                    <!-- Sign Up Link -->
                    <div class="auth-form-footer">
                        Pas encore de compte ? <a href="/register">S'inscrire</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form Handling
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const alertContainer = document.getElementById('alertContainer');
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');

        function showAlert(message, type = 'error') {
            const icons = {
                error: 'error',
                success: 'check_circle',
                info: 'info'
            };
            alertContainer.innerHTML = `
                <div class="alert alert-${type}">
                    <span class="material-symbols-outlined alert-icon">${icons[type]}</span>
                    <div>${message}</div>
                </div>
            `;
            // Auto-hide success alerts after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
            }
        }

        // Check for error messages in URL or session
        function checkForErrors() {
            const urlParams = new URLSearchParams(window.location.search);
            const error = urlParams.get('error');
            
            if (error) {
                const errorMessages = {
                    'invalid_credentials': 'Email ou mot de passe incorrect',
                    'user_not_found': 'Cet email n\'existe pas',
                    'too_many_attempts': 'Trop de tentatives. Veuillez réessayer plus tard',
                    'account_disabled': 'Votre compte a été désactivé',
                    'email_not_verified': 'Veuillez vérifier votre email avant de vous connecter'
                };
                showAlert(errorMessages[error] || 'Une erreur est survenue', 'error');
            }
        }

        // Call on page load
        document.addEventListener('DOMContentLoaded', checkForErrors);

        // Validate email format
        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = emailInput.value.trim();
            const password = passwordInput.value;

            // Clear previous errors
            document.getElementById('emailError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            alertContainer.innerHTML = '';

            // Validation
            let hasError = false;

            if (!email) {
                document.getElementById('emailError').textContent = 'L\'adresse email est requise';
                emailInput.classList.add('input-error');
                hasError = true;
            } else if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = 'Veuillez entrer une adresse email valide';
                emailInput.classList.add('input-error');
                hasError = true;
            }

            if (!password) {
                document.getElementById('passwordError').textContent = 'Le mot de passe est requis';
                passwordInput.classList.add('input-error');
                hasError = true;
            } else if (password.length < 6) {
                document.getElementById('passwordError').textContent = 'Le mot de passe doit avoir au moins 6 caractères';
                passwordInput.classList.add('input-error');
                hasError = true;
            }

            if (hasError) {
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Connexion...';

            // Soumettre le formulaire
            setTimeout(() => {
                loginForm.submit();
            }, 300);
        });

        // Social Login
        document.querySelectorAll('.btn-social-google').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                showAlert('Les connexions sociales sont en cours de configuration', 'info');
            });
        });

        // Clear errors on input focus and remove error styling
        [emailInput, passwordInput].forEach(input => {
            input.addEventListener('focus', () => {
                input.classList.remove('input-error');
                document.getElementById(input.id + 'Error').textContent = '';
                alertContainer.innerHTML = '';
            });

            input.addEventListener('input', () => {
                if (input.classList.contains('input-error')) {
                    input.classList.remove('input-error');
                }
            });
        });

        // Check for Laravel validation errors
        function checkValidationErrors() {
            const bodyClasses = document.body.className;
            if (bodyClasses.includes('has-errors')) {
                const errorMessages = document.querySelectorAll('[data-error-message]');
                if (errorMessages.length > 0) {
                    errorMessages.forEach(el => {
                        const message = el.getAttribute('data-error-message');
                        const field = el.getAttribute('data-error-field');
                        if (message && field) {
                            document.getElementById(field + 'Error').textContent = message;
                            document.getElementById(field)?.classList.add('input-error');
                        }
                    });
                }
            }
        }

        document.addEventListener('DOMContentLoaded', checkValidationErrors);
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/auth/login.blade.php ENDPATH**/ ?>