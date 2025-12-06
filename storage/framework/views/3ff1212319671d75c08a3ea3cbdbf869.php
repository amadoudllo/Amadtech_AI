<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"/>
    <title>Gestion des Utilisateurs - Admin</title>
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
        .table-container { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid #475569; border-radius: 12px; overflow: hidden; }
        .table-header { background: rgba(249, 115, 22, 0.1); border-bottom: 1px solid #475569; padding: 1rem 1.5rem; }
        .btn-primary { background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: white; padding: 0.75rem 1.5rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; }
        .btn-primary:hover { box-shadow: 0 0 20px rgba(249, 115, 22, 0.4); }
        .btn-danger { background: #ef4444; color: white; padding: 0.5rem 1rem; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 600; }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .gradient-text { background: linear-gradient(135deg, #f97316 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
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
            <div class="sidebar-item active" onclick="window.location.href='/admin/users'">
                <span class="material-symbols-outlined">people</span>
                <span>Utilisateurs</span>
            </div>
            <div class="sidebar-item" onclick="window.location.href='/admin/settings'">
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
            <h2 class="text-2xl font-bold text-slate-100">Gestion des Utilisateurs</h2>
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
            <!-- Search and Filters -->
            <div class="stat-card mb-6">
                <form method="GET" class="flex gap-4">
                    <input type="text" name="search" placeholder="Rechercher par nom ou email..." value="<?php echo e(request('search')); ?>" class="flex-1 px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-100 placeholder-slate-400" />
                    <select name="role" class="px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-100">
                        <option value="">Tous les rôles</option>
                        <option value="admin" <?php echo e(request('role') === 'admin' ? 'selected' : ''); ?>>Admin</option>
                        <option value="user" <?php echo e(request('role') === 'user' ? 'selected' : ''); ?>>Utilisateur</option>
                    </select>
                    <select name="status" class="px-4 py-2 bg-slate-800 border border-slate-600 rounded-lg text-slate-100">
                        <option value="">Tous les statuts</option>
                        <option value="active" <?php echo e(request('status') === 'active' ? 'selected' : ''); ?>>Actif</option>
                        <option value="inactive" <?php echo e(request('status') === 'inactive' ? 'selected' : ''); ?>>Inactif</option>
                        <option value="blocked" <?php echo e(request('status') === 'blocked' ? 'selected' : ''); ?>>Bloqué</option>
                    </select>
                    <button type="submit" class="btn-primary">Filtrer</button>
                </form>
            </div>

            <!-- Users Table -->
            <div class="table-container">
                <div class="table-header">
                    <h3 class="text-lg font-semibold">Utilisateurs (<?php echo e($users->total()); ?>)</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-800 text-slate-300">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm">Nom</th>
                                <th class="px-6 py-3 text-left text-sm">Email</th>
                                <th class="px-6 py-3 text-left text-sm">Rôle</th>
                                <th class="px-6 py-3 text-left text-sm">Statut</th>
                                <th class="px-6 py-3 text-left text-sm">Email Vérifié</th>
                                <th class="px-6 py-3 text-left text-sm">Créé le</th>
                                <th class="px-6 py-3 text-left text-sm">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700">
                            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-slate-800/50 transition">
                                <td class="px-6 py-3 text-sm"><?php echo e($user->name); ?></td>
                                <td class="px-6 py-3 text-sm text-slate-400"><?php echo e($user->email); ?></td>
                                <td class="px-6 py-3 text-sm">
                                    <span class="badge <?php echo e($user->role === 'admin' ? 'badge-danger' : 'badge-success'); ?>">
                                        <?php echo e(ucfirst($user->role)); ?>

                                    </span>
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <?php if($user->is_blocked): ?>
                                        <span class="badge badge-danger">Bloqué</span>
                                    <?php elseif(!$user->is_active): ?>
                                        <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #f59e0b;">Inactif</span>
                                    <?php else: ?>
                                        <span class="badge badge-success">Actif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    <?php if($user->email_verified_at): ?>
                                        <span class="badge badge-success">✓ Vérifié</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">✗ Non</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-3 text-sm text-slate-400"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                                <td class="px-6 py-3 text-sm">
                                    <div class="flex gap-2">
                                        <button class="btn-danger" onclick="blockUser(<?php echo e($user->id); ?>)">
                                            <?php echo e($user->is_blocked ? 'Débloquer' : 'Bloquer'); ?>

                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-6 text-center text-slate-400">Aucun utilisateur trouvé</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <?php if($users->hasPages()): ?>
            <div class="mt-6 flex justify-center gap-2">
                <?php echo e($users->links()); ?>

            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function blockUser(userId) {
            if (confirm('Êtes-vous sûr?')) {
                const form = new FormData();
                fetch(`/admin/users/${userId}/block`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: form
                }).then(() => location.reload());
            }
        }

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
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/admin/users/index.blade.php ENDPATH**/ ?>