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
                <?php if($errors->any()): ?>
                    <div style="background: #fee; border: 1px solid #fcc; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #c33; font-size: 0.9rem;">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div><?php echo e($error); ?></div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>

                <?php if(session('success')): ?>
                    <div style="background: #efe; border: 1px solid #cfc; border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; color: #3c3; font-size: 0.9rem;">
                        <?php echo e(session('success')); ?>

                    </div>
                <?php endif; ?>

                <!-- Admin Login Form -->
                <form class="auth-form" method="POST" action="<?php echo e(route('admin.login.post')); ?>">
                    <?php echo csrf_field(); ?>

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
                            value="<?php echo e(old('email')); ?>"
                        />
                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="form-error" style="display: block; margin-top: 0.5rem;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="form-error" style="display: block; margin-top: 0.5rem;"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
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
                        <a href="<?php echo e(route('login')); ?>" style="color: var(--primary); text-decoration: none; font-weight: 500;">← Retour à la connexion utilisateur</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/auth/admin-login.blade.php ENDPATH**/ ?>