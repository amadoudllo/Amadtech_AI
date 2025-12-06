<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin - Connexion - Amadtech_AI</title>
    <link href="/css/auth.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        .admin-badge {
            display: inline-block;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .admin-form-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }

        .admin-form-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .admin-form-header p {
            font-size: 0.95rem;
            color: var(--text-secondary);
        }
    </style>
</head>
<body>
    <!-- Auth Container -->
    <div class="auth-container">
        <!-- Right Section - Login Form -->
        <div class="auth-right-section">
            <div class="auth-form-wrapper">
                <!-- Header -->
                <div class="admin-form-header">
                    <div class="admin-badge">🔐 Zone Admin</div>
                    <h1>Administration</h1>
                    <p>Connexion pour les administrateurs</p>
                </div>

                <!-- Error Alert -->
                @if ($errors->any())
                    <div style="background: #fee; border: 1px solid #fcc; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #c33; font-size: 0.9rem;">
                        @foreach ($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                @if (session('success'))
                    <div style="background: #efe; border: 1px solid #cfc; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #3c3; font-size: 0.9rem;">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Admin Login Form -->
                <form class="auth-form" method="POST" action="{{ route('admin.login.post') }}">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label class="form-label" for="email">Adresse email</label>
                        <input
                            class="form-input"
                            id="email"
                            name="email"
                            placeholder="admin@exemple.com"
                            required
                            type="email"
                            value="{{ old('email') }}"
                        />
                        @error('email')
                            <span class="form-error" style="display: block; margin-top: 0.5rem;">{{ $message }}</span>
                        @enderror
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
                        @error('password')
                            <span class="form-error" style="display: block; margin-top: 0.5rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Remember -->
                    <div class="form-footer">
                        <label class="form-checkbox">
                            <input name="remember" type="checkbox" value="on"/>
                            <span>Se souvenir de moi</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button class="btn btn-primary" type="submit">
                        <span>Se connecter en tant qu'admin</span>
                    </button>

                    <!-- Back Link -->
                    <div class="auth-form-footer">
                        <a href="{{ route('login') }}" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la connexion utilisateur</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
