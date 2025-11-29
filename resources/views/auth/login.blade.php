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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
</head>
<body>
    <!-- Auth Container -->
    <div class="auth-container">
        <div class="auth-content">
            <!-- Header -->
            <div class="auth-header">
                <div class="auth-logo">
                    <img src="/images/chat-logo.png" alt="Amadtech_AI" style="width: 80px; height: auto;"/>
                </div>
                <h1 class="auth-title">Bienvenue</h1>
                <p class="auth-subtitle">Connectez-vous pour continuer à utiliser notre assistant IA</p>
            </div>

            <!-- Card -->
            <div class="auth-card">
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
                </form>

                <!-- Divider -->
                <div class="form-divider">ou</div>

                <!-- Social Login -->
                <div class="social-buttons">
                    <button class="btn-social" type="button" title="Continuer avec Google">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="currentColor"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="currentColor"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="currentColor"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="currentColor"/>
                        </svg>
                    </button>
                    <button class="btn-social" type="button" title="Continuer avec GitHub">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v 3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Footer -->
            <div class="auth-footer">
                Pas encore de compte ? <a href="/register">S'inscrire</a>
            </div>
        </div>
    </div>

    <script>
        // Theme Toggle
        const themeToggle = document.getElementById('themeToggle');
        const htmlElement = document.documentElement;

        // Load saved theme
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            htmlElement.classList.add('dark');
            themeToggle.innerHTML = '<span class="material-symbols-outlined">light_mode</span>';
        }

        themeToggle.addEventListener('click', () => {
            const isDark = htmlElement.classList.toggle('dark');
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
            themeToggle.innerHTML = isDark 
                ? '<span class="material-symbols-outlined">light_mode</span>'
                : '<span class="material-symbols-outlined">dark_mode</span>';
        });

        // Form Handling
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const alertContainer = document.getElementById('alertContainer');

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
        }

        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            // Validation simple
            if (!email || !password) {
                showAlert('Veuillez remplir tous les champs', 'error');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Connexion...';

            // Soumettre le formulaire
            loginForm.submit();
        });

        // Social Login (à implémenter)
        document.querySelectorAll('.btn-social').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                showAlert('Les connexions sociales sont en cours de configuration', 'info');
            });
        });

        // Clear errors on input
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', () => {
                document.getElementById(input.id + 'Error').textContent = '';
            });
        });
    </script>
</body>
</html>
