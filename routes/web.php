<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\OwnerController as AdminOwnerController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;
use App\Http\Controllers\Owner\BookingController;
use App\Http\Controllers\Owner\RoomController;
use App\Http\Controllers\Owner\CleanerController as OwnerCleanerController;
use App\Http\Controllers\Owner\CleaningTaskController;
use App\Http\Controllers\Owner\DayCapacityController;
use App\Http\Controllers\Owner\IssueController as OwnerIssueController;
use App\Http\Controllers\Cleaner\DashboardController as CleanerDashboardController;
use App\Http\Controllers\Cleaner\TaskController;
use App\Http\Controllers\Cleaner\IssueController as CleanerIssueController;
use Illuminate\Support\Facades\Route;

// Redirect home to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Role-based dashboard routing
Route::get('/dashboard', function () {
    $user = auth()->user();

    return match($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'owner', 'authed-user' => redirect()->route('owner.dashboard'),
        'cleaner' => redirect()->route('cleaner.dashboard'),
        default => abort(403, 'No portal assigned to your role'),
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Portal
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Owners management
    Route::resource('owners', AdminOwnerController::class);
    Route::post('/owners/{owner}/deactivate', [AdminOwnerController::class, 'deactivate'])->name('owners.deactivate');
    Route::post('/owners/{owner}/activate', [AdminOwnerController::class, 'activate'])->name('owners.activate');

    // Audit log
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
});

// Owner Portal - Setup routes (no email verification required)
Route::middleware(['auth', 'role:owner,authed-user'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/setup/account', [\App\Http\Controllers\Owner\SetupController::class, 'showAccountSetup'])->name('setup.account');
    Route::post('/setup/account', [\App\Http\Controllers\Owner\SetupController::class, 'storeAccountSetup'])->name('setup.account.store');
});

// Owner Portal - Main routes (email verification required after setup)
Route::middleware(['auth', 'verified', 'role:owner,authed-user'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerDashboardController::class, 'accordion'])->name('dashboard');

    // Hotels (for creating first hotel)
    Route::post('/hotels', [\App\Http\Controllers\Owner\HotelController::class, 'store'])->name('hotels.store');

    // Rooms
    Route::resource('rooms', RoomController::class);

    // Bookings
    Route::resource('bookings', BookingController::class);

    // Cleaners
    Route::get('/cleaners', [OwnerCleanerController::class, 'index'])->name('cleaners.index');
    Route::get('/cleaners/create', [OwnerCleanerController::class, 'create'])->name('cleaners.create');
    Route::post('/cleaners', [OwnerCleanerController::class, 'store'])->name('cleaners.store');
    Route::get('/cleaners/{cleaner}', [OwnerCleanerController::class, 'show'])->name('cleaners.show');
    Route::post('/cleaners/{cleaner}/deactivate', [OwnerCleanerController::class, 'deactivate'])->name('cleaners.deactivate');
    Route::post('/cleaners/{cleaner}/activate', [OwnerCleanerController::class, 'activate'])->name('cleaners.activate');

    // Cleaning Tasks
    Route::get('/cleaning-tasks/{cleaningTask}', [CleaningTaskController::class, 'show'])->name('cleaning-tasks.show');

    // Day Capacity
    Route::get('/capacity', [DayCapacityController::class, 'index'])->name('capacity.index');
    Route::post('/capacity', [DayCapacityController::class, 'store'])->name('capacity.store');
    Route::post('/capacity/bulk', [DayCapacityController::class, 'bulkStore'])->name('capacity.bulk');
    Route::delete('/capacity/{capacity}', [DayCapacityController::class, 'destroy'])->name('capacity.destroy');

    // Issues
    Route::resource('issues', OwnerIssueController::class);
    Route::post('/issues/{issue}/mark-fixed', [OwnerIssueController::class, 'markFixed'])->name('issues.mark-fixed');
    Route::post('/issues/{issue}/reopen', [OwnerIssueController::class, 'reopen'])->name('issues.reopen');

    // Reports
    Route::get('/reports/daily', [App\Http\Controllers\Owner\ReportController::class, 'dailyOverview'])->name('reports.daily');
    Route::post('/reports/export-csv', [App\Http\Controllers\Owner\ReportController::class, 'exportCsv'])->name('reports.export-csv');
});

// Cleaner Portal (mobile-first)
Route::middleware(['auth', 'verified', 'role:cleaner'])->prefix('cleaner')->name('cleaner.')->group(function () {
    Route::get('/dashboard', [CleanerDashboardController::class, 'index'])->name('dashboard');

    // Tasks
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tasks.show');
    Route::post('/tasks/{task}/start', [TaskController::class, 'start'])->name('tasks.start');
    Route::post('/tasks/{task}/stop', [TaskController::class, 'stop'])->name('tasks.stop');
    Route::post('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');

    // Issues (reporting)
    Route::get('/issues/create', [CleanerIssueController::class, 'create'])->name('issues.create');
    Route::post('/issues', [CleanerIssueController::class, 'store'])->name('issues.store');
    Route::get('/issues/{issue}', [CleanerIssueController::class, 'show'])->name('issues.show');
});

// Profile routes (available to all authenticated users)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
