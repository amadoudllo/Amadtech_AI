<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Laravel\Fortify\Http\Controllers\AuthenticatedSessionController;

Route::get('/', function () {
    return redirect('/chat');
})->name('home');

// Custom Auth Routes (pure PHP views without Vite)
Route::middleware('guest:web')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    
    // Registration routes
    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Email verification routes
    Route::get('/verify-email', [RegisterController::class, 'showVerifyEmail'])->name('verify.email.show');
    Route::post('/verify-email', [RegisterController::class, 'verifyEmail'])->name('verify.email');
    Route::post('/resend-verification-email', [RegisterController::class, 'resendVerificationEmail'])->name('resend.verification.email');
});

// Route de vérification d'email (accessible sans authentification)
Route::get('/verify-email/{token}', [VerifyEmailController::class, 'verify'])->name('verify.email.link');

// Admin Login Routes
Route::middleware(\App\Http\Middleware\AdminGuestOrAdmin::class)->group(function () {
    Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AdminLoginController::class, 'login'])->name('admin.login.post');
});

Route::post('/admin/logout', [AdminLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('admin.logout');

Route::post('/logout', function () {
    auth()->logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->middleware('auth')->name('logout');

// Chat routes (apply guest-count middleware only to POST message sends)
Route::get('/chat', [ChatController::class, 'show'])
    ->name('chat.show');

Route::post('/chat/send', [ChatController::class, 'sendMessage'])
    ->middleware(\App\Http\Middleware\RequireLoginForChatAfterThreeRequests::class)
    ->name('chat.send');

// API endpoints for conversation management - NO middleware (allow unlimited guest access for reading history)
Route::get('/api/chat/conversations', [ChatController::class, 'getConversations']);
Route::get('/api/chat/conversations/{id}/messages', [ChatController::class, 'getConversationMessages']);
Route::put('/api/chat/conversations/{id}', [ChatController::class, 'updateConversation'])->middleware('auth');
Route::delete('/api/chat/conversations/{id}', [ChatController::class, 'deleteConversation']);

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

// Admin routes - Protected by AdminMiddleware
Route::middleware(['auth', \App\Http\Middleware\AdminMiddleware::class])->prefix('admin')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('admin.users');
    Route::post('/users/{user}/role', [AdminDashboardController::class, 'updateUserRole'])->name('admin.user.role');
    Route::post('/users/{user}/toggle-active', [AdminDashboardController::class, 'toggleUserActive'])->name('admin.user.toggle');
    Route::post('/users/{user}/block', [AdminDashboardController::class, 'blockUser'])->name('admin.user.block');
    Route::post('/users/{user}/unblock', [AdminDashboardController::class, 'unblockUser'])->name('admin.user.unblock');
    Route::post('/users/{user}/delete', [AdminDashboardController::class, 'deleteUser'])->name('admin.user.delete');
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('admin.settings');
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('admin.settings.update');
    Route::get('/logs/requests', [AdminDashboardController::class, 'requestLogs'])->name('admin.logs.requests');
    Route::get('/logs/activity', [AdminDashboardController::class, 'activityLogs'])->name('admin.logs.activity');
});
