<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\RequestStat;
use App\Models\AdminSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        // Real-time statistics
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->where('is_blocked', false)->count(),
            'total_requests' => RequestStat::count(),
            'total_requests_today' => RequestStat::whereDate('created_at', today())->count(),
            'avg_response_time' => round(RequestStat::avg('response_time_ms'), 2),
            'total_tokens' => RequestStat::sum(DB::raw('prompt_tokens + completion_tokens')),
            'success_rate' => round(RequestStat::where('success', true)->count() / max(RequestStat::count(), 1) * 100, 2),
        ];

        // Requests by day (last 7 days)
        $requestsByDay = RequestStat::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top models used
        $topModels = RequestStat::selectRaw('model, COUNT(*) as count')
            ->groupBy('model')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(5)
            ->get();

        // Recent activity
        $recentActivity = ActivityLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Server metrics
        $serverMetrics = $this->getServerMetrics();

        return view('admin.dashboard', [
            'stats' => $stats,
            'requestsByDay' => $requestsByDay,
            'topModels' => $topModels,
            'recentActivity' => $recentActivity,
            'serverMetrics' => $serverMetrics,
        ]);
    }

    public function users(Request $request)
    {
        $query = User::query();

        // Filters
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->role) {
            $query->where('role', $request->role);
        }

        if ($request->status) {
            if ($request->status === 'active') {
                $query->where('is_active', true)->where('is_blocked', false);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($request->status === 'blocked') {
                $query->where('is_blocked', true);
            }
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.users.index', ['users' => $users]);
    }

    public function updateUserRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => 'required|in:admin,moderator,user',
        ]);

        $oldRole = $user->role;
        $user->update(['role' => $validated['role']]);

        ActivityLog::log(
            'update_user_role',
            auth()->user(),
            'User',
            $user->id,
            "Rôle changé de {$oldRole} à {$validated['role']}",
            ['old_role' => $oldRole, 'new_role' => $validated['role']]
        );

        return response()->json(['success' => true, 'message' => 'Rôle mis à jour']);
    }

    public function toggleUserActive(Request $request, User $user)
    {
        $oldStatus = $user->is_active;
        $user->update(['is_active' => !$user->is_active]);

        ActivityLog::log(
            'toggle_user_active',
            auth()->user(),
            'User',
            $user->id,
            "Utilisateur " . ($user->is_active ? 'activé' : 'désactivé'),
            ['old_status' => $oldStatus, 'new_status' => $user->is_active]
        );

        return response()->json(['success' => true, 'is_active' => $user->is_active]);
    }

    public function blockUser(Request $request, User $user)
    {
        $user->update(['is_blocked' => true]);

        ActivityLog::log(
            'block_user',
            auth()->user(),
            'User',
            $user->id,
            'Utilisateur bloqué'
        );

        return response()->json(['success' => true, 'message' => 'Utilisateur bloqué']);
    }

    public function unblockUser(Request $request, User $user)
    {
        $user->update(['is_blocked' => false]);

        ActivityLog::log(
            'unblock_user',
            auth()->user(),
            'User',
            $user->id,
            'Utilisateur débloqué'
        );

        return response()->json(['success' => true, 'message' => 'Utilisateur débloqué']);
    }

    public function deleteUser(Request $request, User $user)
    {
        $userName = $user->name;
        $user->delete();

        ActivityLog::log(
            'delete_user',
            auth()->user(),
            'User',
            $user->id,
            "Utilisateur {$userName} supprimé"
        );

        return response()->json(['success' => true, 'message' => 'Utilisateur supprimé']);
    }

    public function settings()
    {
        $settings = AdminSetting::all()->keyBy('setting_key');

        return view('admin.settings.index', ['settings' => $settings]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'platform_slogan' => 'nullable|string|max:255',
            'platform_email' => 'required|email',
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_username' => 'required|string',
            'smtp_password' => 'nullable|string',
            'primary_color' => 'required|string',
            'secondary_color' => 'required|string',
            'enable_registration' => 'boolean',
            'enable_api' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            AdminSetting::setValue($key, $value);
        }

        ActivityLog::log(
            'update_settings',
            auth()->user(),
            'Settings',
            null,
            'Paramètres globaux mis à jour'
        );

        return response()->json(['success' => true, 'message' => 'Paramètres mis à jour']);
    }

    public function getServerMetrics()
    {
        return [
            'cpu_usage' => round(random_int(20, 80)), // Demo
            'memory_usage' => round(random_int(30, 70)),
            'disk_usage' => round(random_int(40, 80)),
            'active_connections' => random_int(10, 100),
        ];
    }

    public function requestLogs(Request $request)
    {
        $query = RequestStat::query();

        if ($request->model) {
            $query->where('model', $request->model);
        }

        if ($request->success_only) {
            $query->where('success', true);
        }

        if ($request->date_from) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $logs = $query->with('user')->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.logs.requests', ['logs' => $logs]);
    }

    public function activityLogs(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->action) {
            $query->where('action', $request->action);
        }

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(50);

        return view('admin.logs.activity', ['logs' => $logs]);
    }
}
