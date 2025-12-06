<?php
// Pure PHP/HTML verify email page
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Vérifier votre email - Amadtech_AI</title>
    <link href="/css/auth.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
</head>
<body>
    <div class="auth-container">
        <div class="auth-right-section">
            <div class="auth-form-wrapper">
                <!-- Header -->
                <div class="auth-form-header">
                    <h1>Vérifiez votre email</h1>
                    <p>Un email de vérification a été envoyé à votre adresse</p>
                </div>

                <!-- Alert Container -->
                <div id="alertContainer"></div>

                <!-- Verification Form -->
                <form id="verifyForm" class="auth-form" method="POST" action="/verify-email">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                    <!-- Email (hidden) -->
                    <input type="hidden" id="email" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">

                    <!-- Token Input -->
                    <div class="form-group">
                        <label class="form-label" for="token">Code de vérification</label>
                        <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0.5rem 0 1rem;">
                            Entrez le code reçu dans l'email ou cliquez sur le lien fourni
                        </p>
                        <input
                            class="form-input"
                            id="token"
                            name="token"
                            placeholder="Collez le token du lien"
                            required
                            type="text"
                            autocomplete="off"
                        />
                        <span class="form-error" id="tokenError"></span>
                    </div>

                    <!-- Submit Button -->
                    <button class="btn btn-primary" type="submit">
                        <span class="spinner" id="spinner" style="display: none;"></span>
                        <span id="submitText">Vérifier mon email</span>
                    </button>
                </form>

                <!-- Resend Section -->
                <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border-color); text-align: center;">
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                        Vous n'avez pas reçu l'email?
                    </p>
                    <button id="resendBtn" type="button" class="btn btn-primary" style="background: var(--surface-light); color: var(--primary); border: 1px solid var(--primary);">
                        <span class="spinner" id="resendSpinner" style="display: none;"></span>
                        <span id="resendText">Renvoyer l'email</span>
                    </button>
                </div>

                <!-- Back to Login -->
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="/login" style="color: var(--text-secondary); text-decoration: none; font-size: 0.9rem; transition: color 0.3s;">
                        Retour à la connexion →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('verifyForm');
        const tokenInput = document.getElementById('token');
        const spinner = document.getElementById('spinner');
        const submitText = document.getElementById('submitText');
        const alertContainer = document.getElementById('alertContainer');
        const resendBtn = document.getElementById('resendBtn');
        const resendSpinner = document.getElementById('resendSpinner');
        const resendText = document.getElementById('resendText');
        const email = document.getElementById('email').value;

        // Handle form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const token = tokenInput.value.trim();

            if (!token) {
                showAlert('Veuillez entrer le code de vérification', 'error');
                return;
            }

            spinner.style.display = 'inline-block';
            submitText.style.display = 'none';

            try {
                const response = await fetch('/verify-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({
                        email: email,
                        token: token,
                    }),
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert(data.message || 'Email vérifié avec succès!', 'success');
                    setTimeout(() => {
                        window.location.href = '/login?verified=1';
                    }, 2000);
                } else {
                    showAlert(data.message || 'Une erreur s\'est produite', 'error');
                }
            } catch (error) {
                showAlert('Une erreur réseau s\'est produite', 'error');
            } finally {
                spinner.style.display = 'none';
                submitText.style.display = 'inline';
            }
        });

        // Handle resend button
        resendBtn.addEventListener('click', async () => {
            if (!email) {
                showAlert('Email invalide', 'error');
                return;
            }

            resendSpinner.style.display = 'inline-block';
            resendText.style.display = 'none';
            resendBtn.disabled = true;

            try {
                const response = await fetch('/resend-verification-email', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({ email: email }),
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert(data.message || 'Email de vérification renvoyé!', 'success');
                } else {
                    showAlert(data.message || 'Erreur lors de l\'envoi', 'error');
                }
            } catch (error) {
                showAlert('Une erreur réseau s\'est produite', 'error');
            } finally {
                resendSpinner.style.display = 'none';
                resendText.style.display = 'inline';
                resendBtn.disabled = false;
            }
        });

        // Show alert helper
        function showAlert(message, type) {
            const alert = document.createElement('div');
            alert.className = `alert alert-${type}`;
            alert.innerHTML = `
                <span class="material-symbols-outlined alert-icon">
                    ${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}
                </span>
                <span>${message}</span>
            `;
            alertContainer.innerHTML = '';
            alertContainer.appendChild(alert);

            if (type === 'success') {
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 5000);
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/auth/verify-email.blade.php ENDPATH**/ ?>