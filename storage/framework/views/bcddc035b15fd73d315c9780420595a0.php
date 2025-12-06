<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"/>
    <title>Paramètres - Admin</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #e2e8f0; }
        .sidebar { width: 280px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-right: 1px solid #334155; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #334155; }
        .sidebar-menu { padding: 1rem 0; }
        .sidebar-item { padding: 0.75rem 1.5rem; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.75rem; color: #cbd5e1; }
        .sidebar-item:hover { background: rgba(249, 115, 22, 0.1); color: #f97316; }
        .sidebar-item.active { background: rgba(249, 115, 22, 0.2); color: #f97316; border-left: 3px solid #f97316; }
        .stat-card { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid #475569; border-radius: 12px; padding: 1.5rem; }
        .btn-primary { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
        .btn-primary:hover { box-shadow: 0 0 20px rgba(249, 115, 22, 0.4); }
        .gradient-text { background: linear-gradient(135deg, #f97316 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .form-group { margin-bottom: 1.5rem; }
        .form-label { display: block; margin-bottom: 0.5rem; color: #e2e8f0; font-weight: 600; }
        .form-input { width: 100%; padding: 0.75rem; background: #334155; border: 1px solid #475569; border-radius: 8px; color: #e2e8f0; }
        .form-input:focus { outline: none; border-color: #f97316; }
    </style>
</head>
<body class="flex h-screen bg-slate-950 overflow-hidden">
    <!-- Sidebar -->
    <div class="sidebar overflow-y-auto">
        <div class="sidebar-header">
            <h1 class="gradient-text text-2xl font-bold">🛡️ Admin</h1>
            <p class="text-sm text-slate-400 mt-1">Amadtech_AI</p>
        </div>
        <div class="sidebar-menu">
            <div class="sidebar-item" onclick="window.location.href='/admin'">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </div>
            <div class="sidebar-item" onclick="window.location.href='/admin/users'">
                <span class="material-symbols-outlined">people</span>
                <span>Utilisateurs</span>
            </div>
            <div class="sidebar-item active" onclick="window.location.href='/admin/settings'">
                <span class="material-symbols-outlined">settings</span>
                <span>Paramètres</span>
            </div>
            <hr class="my-4 border-slate-700">
            <div class="sidebar-item" onclick="logout()">
                <span class="material-symbols-outlined">logout</span>
                <span>Déconnexion</span>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Bar -->
        <div class="bg-slate-900 border-b border-slate-700 px-8 py-4 flex justify-between items-center">
            <h2 class="text-2xl font-bold text-slate-100">Paramètres</h2>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-100"><?php echo e(auth()->user()->name); ?></p>
                    <p class="text-xs text-slate-400">Administrateur</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-purple-500 rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-white">account_circle</span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="flex-1 overflow-y-auto p-8">
            <div class="max-w-2xl">
                <!-- Settings Form -->
                <div class="stat-card">
                    <h3 class="text-xl font-semibold mb-6">Configuration Générale</h3>
                    
                    <form method="POST" action="<?php echo e(route('admin.settings.update')); ?>">
                        <?php echo csrf_field(); ?>
                        
                        <div class="form-group">
                            <label class="form-label">Nom de l'application</label>
                            <input type="text" name="app_name" class="form-input" placeholder="Amadtech_AI" value="Amadtech_AI" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Maintenance (Activé)</label>
                            <input type="checkbox" name="maintenance_mode" class="w-4 h-4" />
                            <span class="text-sm text-slate-400 ml-2">Activer le mode maintenance</span>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nombre de requêtes max par jour (gratuit)</label>
                            <input type="number" name="daily_request_limit" class="form-input" placeholder="50" value="50" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email d'administration</label>
                            <input type="email" name="admin_email" class="form-input" placeholder="admin@example.com" value="<?php echo e(auth()->user()->email); ?>" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Support email</label>
                            <input type="email" name="support_email" class="form-input" placeholder="support@example.com" />
                        </div>

                        <div class="form-group">
                            <label class="form-label">Clé API Groq</label>
                            <input type="password" name="groq_api_key" class="form-input" placeholder="gsk_..." />
                            <p class="text-xs text-slate-400 mt-2">Laisser vide pour garder la valeur actuelle</p>
                        </div>

                        <button type="submit" class="btn-primary w-full">Sauvegarder les paramètres</button>
                    </form>
                </div>

                <!-- Statistics -->
                <div class="stat-card mt-8">
                    <h3 class="text-xl font-semibold mb-4">Informations Système</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-slate-400">Version PHP</p>
                            <p class="text-lg font-semibold"><?php echo e(phpversion()); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Laravel</p>
                            <p class="text-lg font-semibold"><?php echo e(app()->version()); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Environnement</p>
                            <p class="text-lg font-semibold"><?php echo e(env('APP_ENV')); ?></p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-400">Mode Debug</p>
                            <p class="text-lg font-semibold"><?php echo e(env('APP_DEBUG') ? 'Activé' : 'Désactivé'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            if (confirm('Voulez-vous vraiment vous déconnecter?')) {
                fetch('/admin/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => {
                    window.location.href = '/admin/login';
                });
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/admin/settings/index.blade.php ENDPATH**/ ?>