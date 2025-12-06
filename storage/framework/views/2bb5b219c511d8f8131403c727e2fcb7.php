<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"/>
    <title>Dashboard Admin - Amadtech_AI</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#f97316",
                        secondary: "#8b5cf6",
                        success: "#10b981",
                        danger: "#ef4444",
                        warning: "#f59e0b",
                        info: "#3b82f6",
                    },
                },
            },
        }
    </script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #e2e8f0; }
        
        .sidebar { width: 280px; background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-right: 1px solid #334155; }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #334155; }
        .sidebar-menu { padding: 1rem 0; }
        .sidebar-item {
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #cbd5e1;
            font-size: 0.95rem;
        }
        .sidebar-item:hover { background: rgba(249, 115, 22, 0.1); color: #f97316; }
        .sidebar-item.active { background: rgba(249, 115, 22, 0.2); color: #f97316; border-left: 3px solid #f97316; }
        
        .stat-card {
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            border: 1px solid #475569;
            border-radius: 12px;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            border-color: #f97316;
            box-shadow: 0 0 20px rgba(249, 115, 22, 0.15);
            transform: translateY(-2px);
        }
        .stat-value { font-size: 2rem; font-weight: 700; color: #f1f5f9; }
        .stat-label { font-size: 0.875rem; color: #94a3b8; margin-top: 0.5rem; }
        .stat-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
        
        .chart-container { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid #475569; border-radius: 12px; padding: 1.5rem; }
        
        .table-container { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: 1px solid #475569; border-radius: 12px; overflow: hidden; }
        .table-header { background: rgba(249, 115, 22, 0.1); border-bottom: 1px solid #475569; padding: 1rem 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        
        .btn-primary {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover { box-shadow: 0 0 20px rgba(249, 115, 22, 0.4); transform: translateY(-2px); }
        
        .badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge-success { background: rgba(16, 185, 129, 0.2); color: #10b981; }
        .badge-danger { background: rgba(239, 68, 68, 0.2); color: #ef4444; }
        .badge-warning { background: rgba(245, 158, 11, 0.2); color: #f59e0b; }
        .badge-info { background: rgba(59, 130, 246, 0.2); color: #3b82f6; }
        
        .gradient-text { background: linear-gradient(135deg, #f97316 0%, #8b5cf6 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        
        @keyframes slideIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-in { animation: slideIn 0.3s ease-out; }
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
            <div class="sidebar-item active" onclick="goTo('dashboard')">
                <span class="material-symbols-outlined">dashboard</span>
                <span>Dashboard</span>
            </div>
            <div class="sidebar-item" onclick="goTo('users')">
                <span class="material-symbols-outlined">people</span>
                <span>Utilisateurs</span>
            </div>
            <div class="sidebar-item" onclick="goTo('ai-config')">
                <span class="material-symbols-outlined">smart_toy</span>
                <span>Configuration IA</span>
            </div>
            <div class="sidebar-item" onclick="goTo('logs')">
                <span class="material-symbols-outlined">history</span>
                <span>Logs</span>
            </div>
            <div class="sidebar-item" onclick="goTo('settings')">
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
            <h2 class="text-2xl font-bold text-slate-100">Dashboard</h2>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-100">Admin User</p>
                    <p class="text-xs text-slate-400">Dernière connexion: maintenant</p>
                </div>
                <div class="w-10 h-10 bg-gradient-to-br from-primary to-secondary rounded-full flex items-center justify-center">
                    <span class="material-symbols-outlined text-white">account_circle</span>
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div id="content" class="flex-1 overflow-y-auto p-8">
            <!-- Dashboard View -->
            <div id="dashboard-view">
                <!-- Statistics Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="stat-card animate-in">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="stat-value"><?php echo e($stats['total_users']); ?></div>
                                <div class="stat-label">Utilisateurs totaux</div>
                            </div>
                            <div class="stat-icon" style="background: rgba(249, 115, 22, 0.1); color: #f97316;">👥</div>
                        </div>
                    </div>
                    
                    <div class="stat-card animate-in" style="animation-delay: 0.1s;">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="stat-value"><?php echo e($stats['active_users']); ?></div>
                                <div class="stat-label">Utilisateurs actifs</div>
                            </div>
                            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">✓</div>
                        </div>
                    </div>
                    
                    <div class="stat-card animate-in" style="animation-delay: 0.2s;">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="stat-value"><?php echo e($stats['total_requests_today']); ?></div>
                                <div class="stat-label">Requêtes aujourd'hui</div>
                            </div>
                            <div class="stat-icon" style="background: rgba(139, 92, 246, 0.1); color: #8b5cf6;">📊</div>
                        </div>
                    </div>
                    
                    <div class="stat-card animate-in" style="animation-delay: 0.3s;">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="stat-value"><?php echo e($stats['avg_response_time']); ?>ms</div>
                                <div class="stat-label">Temps moyen réponse</div>
                            </div>
                            <div class="stat-icon" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">⚡</div>
                        </div>
                    </div>
                </div>

                <!-- Server Metrics -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                    <div class="stat-card">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-slate-400">CPU Usage</span>
                            <span class="badge badge-info"><?php echo e($serverMetrics['cpu_usage']); ?>%</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-primary to-secondary h-2 rounded-full" style="width: <?php echo e($serverMetrics['cpu_usage']); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-slate-400">Mémoire</span>
                            <span class="badge badge-warning"><?php echo e($serverMetrics['memory_usage']); ?>%</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-warning to-danger h-2 rounded-full" style="width: <?php echo e($serverMetrics['memory_usage']); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-slate-400">Disque</span>
                            <span class="badge badge-danger"><?php echo e($serverMetrics['disk_usage']); ?>%</span>
                        </div>
                        <div class="w-full bg-slate-700 rounded-full h-2">
                            <div class="bg-gradient-to-r from-danger h-2 rounded-full" style="width: <?php echo e($serverMetrics['disk_usage']); ?>%"></div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-slate-400">Connexions</span>
                            <span class="badge badge-success"><?php echo e($serverMetrics['active_connections']); ?></span>
                        </div>
                        <div class="text-2xl font-bold text-primary"><?php echo e($serverMetrics['active_connections']); ?></div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="chart-container">
                        <h3 class="text-lg font-semibold mb-4 text-slate-100">Requêtes (7 derniers jours)</h3>
                        <canvas id="requestsChart"></canvas>
                    </div>
                    
                    <div class="chart-container">
                        <h3 class="text-lg font-semibold mb-4 text-slate-100">Modèles utilisés</h3>
                        <canvas id="modelsChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="table-container">
                    <div class="table-header">
                        <h3 class="text-lg font-semibold">Activité récente</h3>
                        <a href="#" class="text-primary text-sm hover:underline">Voir tout →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-800 text-slate-300">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm">Utilisateur</th>
                                    <th class="px-6 py-3 text-left text-sm">Action</th>
                                    <th class="px-6 py-3 text-left text-sm">Détails</th>
                                    <th class="px-6 py-3 text-left text-sm">Heure</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700">
                                <?php $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-slate-800/50 transition">
                                    <td class="px-6 py-3 text-sm"><?php echo e($log->user?->name ?? 'Système'); ?></td>
                                    <td class="px-6 py-3 text-sm"><span class="badge badge-info"><?php echo e($log->action); ?></span></td>
                                    <td class="px-6 py-3 text-sm"><?php echo e($log->description); ?></td>
                                    <td class="px-6 py-3 text-sm text-slate-400"><?php echo e($log->created_at->diffForHumans()); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart.js - Requests by day
        const ctx1 = document.getElementById('requestsChart').getContext('2d');
        new Chart(ctx1, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($requestsByDay->pluck('date'), 15, 512) ?>,
                datasets: [{
                    label: 'Requêtes',
                    data: <?php echo json_encode($requestsByDay->pluck('count'), 15, 512) ?>,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#334155' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { color: '#334155' }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        // Chart.js - Top Models
        const ctx2 = document.getElementById('modelsChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($topModels->pluck('model'), 15, 512) ?>,
                datasets: [{
                    data: <?php echo json_encode($topModels->pluck('count'), 15, 512) ?>,
                    backgroundColor: ['#f97316', '#8b5cf6', '#10b981', '#3b82f6', '#f59e0b'],
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom', labels: { color: '#e2e8f0' } } }
            }
        });

        function goTo(page) {
            if (page === 'users') {
                window.location.href = '/admin/users';
            } else if (page === 'settings') {
                window.location.href = '/admin/settings';
            } else if (page === 'logs') {
                window.location.href = '/admin';
            }
        }

        function logout() {
            if (confirm('Voulez-vous vraiment vous déconnecter?')) {
                document.createElement('form').setAttribute('method', 'POST');
                const form = new FormData();
                fetch('/admin/logout', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                }).then(() => {
                    window.location.href = '/admin/login';
                });
            }
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Amadtech_AI\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>