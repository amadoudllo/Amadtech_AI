<?php
// Pure PHP/HTML register page - no Blade processing to avoid Vite manifest errors
header('Content-Type: text/html; charset=utf-8');
?><!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>S'inscrire - Amadtech_AI</title>
    <link href="/css/auth.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
</head>
<body>
    <!-- Auth Container -->
    <div class="auth-container">
        <!-- Right Section - Register Form -->
        <div class="auth-right-section">
            <div class="auth-form-wrapper">
                <!-- Header -->
                <div class="auth-form-header">
                    <h1>Créer un compte</h1>
                    <p>Rejoignez-nous et commencez à utiliser notre assistant IA</p>
                </div>

                <!-- Social Register -->
                <div class="auth-social-login">
                    <button class="btn-social-google" type="button" title="S'inscrire avec Google">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="currentColor"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="currentColor"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="currentColor"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="currentColor"/>
                        </svg>
                        <span>S'inscrire avec Google</span>
                    </button>
                </div>

                <!-- Divider -->
                <div class="form-divider">
                    <span>ou</span>
                </div>

                <!-- Error Alert -->
                <div id="alertContainer"></div>

                <!-- Register Form -->
                <form id="registerForm" class="auth-form" method="POST" action="/register">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">

                    <!-- Name -->
                    <div class="form-group">
                        <label class="form-label" for="name">Nom complet</label>
                        <input
                            class="form-input"
                            id="name"
                            name="name"
                            placeholder="Binta Barry"
                            required
                            type="text"
                            value="<?php echo old('name') ?? ''; ?>"
                        />
                        <span class="form-error" id="nameError"></span>
                    </div>

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

                    <!-- Phone Number with Country Selector -->
                    <div class="form-group">
                        <label class="form-label">Numéro de téléphone</label>
                        <div class="phone-input-wrapper">
                            <select class="country-select" id="countryCode" name="country_code" title="Sélectionner un pays">
                                <option value="">🌍 Pays</option>
                                <option value="+221" data-flag="🇸🇳">🇸🇳 +221 (Sénégal)</option>
                                <option value="+225" data-flag="🇨🇮">🇨🇮 +225 (Côte d'Ivoire)</option>
                                <option value="+226" data-flag="🇧🇫">🇧🇫 +226 (Burkina Faso)</option>
                                <option value="+229" data-flag="🇧🇯">🇧🇯 +229 (Bénin)</option>
                                <option value="+230" data-flag="🇲🇺">🇲🇺 +230 (Maurice)</option>
                                <option value="+231" data-flag="🇱🇷">🇱🇷 +231 (Liberia)</option>
                                <option value="+232" data-flag="🇸🇱">🇸🇱 +232 (Sierra Leone)</option>
                                <option value="+233" data-flag="🇬🇭">🇬🇭 +233 (Ghana)</option>
                                <option value="+234" data-flag="🇳🇬">🇳🇬 +234 (Nigeria)</option>
                                <option value="+235" data-flag="🇹🇩">🇹🇩 +235 (Tchad)</option>
                                <option value="+236" data-flag="🇨🇫">🇨🇫 +236 (Rép. Centrafricaine)</option>
                                <option value="+237" data-flag="🇨🇲">🇨🇲 +237 (Cameroun)</option>
                                <option value="+238" data-flag="🇨🇻">🇨🇻 +238 (Cap-Vert)</option>
                                <option value="+239" data-flag="🇸🇹">🇸🇹 +239 (São Tomé)</option>
                                <option value="+224" data-flag="🇬🇳">🇬🇳 +224 (Guinée-Conakry)</option>
                                <option value="+240" data-flag="🇬🇶">🇬🇶 +240 (Guinée Équatoriale)</option>
                                <option value="+241" data-flag="🇬🇦">🇬🇦 +241 (Gabon)</option>
                                <option value="+242" data-flag="🇨🇬">🇨🇬 +242 (Congo)</option>
                                <option value="+243" data-flag="🇨🇩">🇨🇩 +243 (RDC)</option>
                                <option value="+244" data-flag="🇦🇴">🇦🇴 +244 (Angola)</option>
                                <option value="+245" data-flag="🇬🇼">🇬🇼 +245 (Guinée-Bissau)</option>
                                <option value="+246" data-flag="🇩🇬">🇩🇬 +246 (Diego Garcia)</option>
                                <option value="+248" data-flag="🇸🇨">🇸🇨 +248 (Seychelles)</option>
                                <option value="+249" data-flag="🇸🇩">🇸🇩 +249 (Soudan)</option>
                                <option value="+250" data-flag="🇷🇼">🇷🇼 +250 (Rwanda)</option>
                                <option value="+251" data-flag="🇪🇹">🇪🇹 +251 (Éthiopie)</option>
                                <option value="+252" data-flag="🇸🇴">🇸🇴 +252 (Somalie)</option>
                                <option value="+253" data-flag="🇩🇯">🇩🇯 +253 (Djibouti)</option>
                                <option value="+254" data-flag="🇰🇪">🇰🇪 +254 (Kenya)</option>
                                <option value="+255" data-flag="🇹🇿">🇹🇿 +255 (Tanzanie)</option>
                                <option value="+256" data-flag="🇺🇬">🇺🇬 +256 (Ouganda)</option>
                                <option value="+257" data-flag="🇧🇮">🇧🇮 +257 (Burundi)</option>
                                <option value="+258" data-flag="🇲🇿">🇲🇿 +258 (Mozambique)</option>
                                <option value="+260" data-flag="🇿🇲">🇿🇲 +260 (Zambie)</option>
                                <option value="+261" data-flag="🇲🇬">🇲🇬 +261 (Madagascar)</option>
                                <option value="+262" data-flag="🇷🇪">🇷🇪 +262 (Réunion)</option>
                                <option value="+263" data-flag="🇿🇼">🇿🇼 +263 (Zimbabwe)</option>
                                <option value="+264" data-flag="🇳🇦">🇳🇦 +264 (Namibie)</option>
                                <option value="+265" data-flag="🇲🇼">🇲🇼 +265 (Malawi)</option>
                                <option value="+266" data-flag="🇱🇸">🇱🇸 +266 (Lesotho)</option>
                                <option value="+267" data-flag="🇧🇼">🇧🇼 +267 (Botswana)</option>
                                <option value="+268" data-flag="🇪🇿">🇪🇿 +268 (Eswatini)</option>
                                <option value="+27" data-flag="🇿🇦">🇿🇦 +27 (Afrique du Sud)</option>
                            </select>
                            <input
                                class="form-input phone-input"
                                id="phone"
                                name="phone"
                                placeholder="629 00 00 00"
                                type="tel"
                                value="<?php echo old('phone') ?? ''; ?>"
                            />
                        </div>
                        <span class="form-error" id="phoneError"></span>
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Mot de passe</label>
                        <input
                            class="form-input"
                            id="password"
                            name="password"
                            placeholder="Au moins 8 caractères"
                            required
                            type="password"
                        />
                        <span class="form-error" id="passwordError"></span>
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirmer le mot de passe</label>
                        <input
                            class="form-input"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Confirmez votre mot de passe"
                            required
                            type="password"
                        />
                        <span class="form-error" id="confirmError"></span>
                    </div>

                    <!-- Terms & Conditions -->
                    <label class="form-checkbox">
                        <input name="terms" required type="checkbox"/>
                        <span>J'accepte les <a class="form-link" href="#">conditions d'utilisation</a></span>
                    </label>

                    <!-- Submit Button -->
                    <button class="btn btn-primary" id="submitBtn" type="submit">
                        <span>Créer mon compte</span>
                    </button>

                    <!-- Sign In Link -->
                    <div class="auth-form-footer">
                        Vous avez déjà un compte ? <a href="/login">Se connecter</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Form Handling
        const registerForm = document.getElementById('registerForm');
        const submitBtn = document.getElementById('submitBtn');
        const alertContainer = document.getElementById('alertContainer');
        const countrySelect = document.getElementById('countryCode');
        const phoneInput = document.getElementById('phone');

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
            if (type === 'success') {
                setTimeout(() => {
                    alertContainer.innerHTML = '';
                }, 5000);
            }
        }

        function validateEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }

        function validatePhone(phone, countryCode) {
            if (!countryCode || !phone) return false;
            const cleanPhone = phone.replace(/[\s\-\(\)]/g, '');
            return cleanPhone.length >= 9 && cleanPhone.length <= 15;
        }

        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const phone = phoneInput.value.trim();
            const countryCode = countrySelect.value;
            const password = document.getElementById('password').value;
            const passwordConfirm = document.getElementById('password_confirmation').value;
            const agreeTerms = document.querySelector('input[name="terms"]').checked;

            // Clear previous errors
            document.getElementById('nameError').textContent = '';
            document.getElementById('emailError').textContent = '';
            document.getElementById('phoneError').textContent = '';
            document.getElementById('passwordError').textContent = '';
            document.getElementById('confirmError').textContent = '';
            alertContainer.innerHTML = '';

            let hasError = false;

            if (!name) {
                document.getElementById('nameError').textContent = 'Le nom complet est requis';
                hasError = true;
            } else if (name.length < 3) {
                document.getElementById('nameError').textContent = 'Le nom doit avoir au moins 3 caractères';
                hasError = true;
            }

            if (!email) {
                document.getElementById('emailError').textContent = 'L\'adresse email est requise';
                hasError = true;
            } else if (!validateEmail(email)) {
                document.getElementById('emailError').textContent = 'Veuillez entrer une adresse email valide';
                hasError = true;
            }

            if (!countryCode || !phone) {
                document.getElementById('phoneError').textContent = 'Le numéro de téléphone est requis';
                hasError = true;
            } else if (!validatePhone(phone, countryCode)) {
                document.getElementById('phoneError').textContent = 'Le numéro de téléphone est invalide';
                hasError = true;
            }

            if (!password) {
                document.getElementById('passwordError').textContent = 'Le mot de passe est requis';
                hasError = true;
            } else if (password.length < 8) {
                document.getElementById('passwordError').textContent = 'Le mot de passe doit avoir au moins 8 caractères';
                hasError = true;
            }

            if (!passwordConfirm) {
                document.getElementById('confirmError').textContent = 'Veuillez confirmer le mot de passe';
                hasError = true;
            } else if (password !== passwordConfirm) {
                document.getElementById('confirmError').textContent = 'Les mots de passe ne correspondent pas';
                hasError = true;
            }

            if (!agreeTerms) {
                showAlert('Vous devez accepter les conditions d\'utilisation', 'error');
                hasError = true;
            }

            if (hasError) {
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner"></span> Création du compte...';

            try {
                const response = await fetch('/register', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    },
                    body: JSON.stringify({
                        name: name,
                        email: email,
                        password: password,
                        password_confirmation: passwordConfirm,
                        phone: phone,
                        country_code: countryCode,
                        terms: agreeTerms,
                    }),
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert(data.message || 'Compte créé avec succès! Vérifiez votre email.', 'success');
                    setTimeout(() => {
                        window.location.href = `/verify-email?email=${encodeURIComponent(email)}`;
                    }, 1500);
                } else {
                    showAlert(data.message || 'Une erreur s\'est produite lors de l\'inscription', 'error');
                    
                    // Show field-specific errors if available
                    if (data.errors) {
                        if (data.errors.email) {
                            document.getElementById('emailError').textContent = data.errors.email[0];
                        }
                        if (data.errors.phone) {
                            document.getElementById('phoneError').textContent = data.errors.phone[0];
                        }
                    }
                }
            } catch (error) {
                showAlert('Erreur réseau lors de l\'inscription', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<span>Créer mon compte</span>';
            }
        });

        // Social Register
        document.querySelectorAll('.btn-social-google').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                showAlert('Les connexions sociales sont en cours de configuration', 'info');
            });
        });

        // Clear errors on input focus
        [document.getElementById('name'), document.getElementById('email'), phoneInput, document.getElementById('password'), document.getElementById('password_confirmation')].forEach(input => {
            input.addEventListener('focus', () => {
                const errorId = input.id + 'Error';
                const errorElement = document.getElementById(errorId);
                if (errorElement) {
                    errorElement.textContent = '';
                }
                alertContainer.innerHTML = '';
            });
        });

        // Update phone input styling when country changes
        countrySelect.addEventListener('change', function() {
            phoneInput.focus();
        });
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/auth/register.blade.php ENDPATH**/ ?>