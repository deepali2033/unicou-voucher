<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\agent\Agentcontroller;
use App\Http\Controllers\student\StudentController;

Route::get('/', function () {
    return view('home.home');
})->name('home');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::get('/test', [AuthController::class, 'test']);

Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Additional Registration Forms
Route::middleware('auth')->group(function () {
    Route::get('/register/agent-details', [AuthController::class, 'showAgentForm'])->name('auth.forms.B2BResellerAgent');
    Route::post('/register/agent-details', [AuthController::class, 'storeAgentDetails'])->name('auth.form.agent.post');

    Route::get('/register/student-details', [AuthController::class, 'showStudentForm'])->name('auth.form.student');
    Route::post('/register/student-details', [AuthController::class, 'storeStudentDetails'])->name('auth.form.student.post');
});
// Route::get('/index', [Agentcontroller::class, 'getLocationData']);

// Placeholder Admin Routes to prevent Layout Errors
Route::prefix('admin')->name('admin.')->middleware(['auth', 'account_type:admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue.index');
    Route::get('/stock-alerts', [AdminController::class, 'stockAlerts'])->name('stock.alerts');
    Route::get('/system-control', [AdminController::class, 'systemControl'])->name('system.control');
    Route::post('/system-control/toggle', [AdminController::class, 'toggleSystem'])->name('system.toggle');
    
    Route::get('/approvals', [AdminController::class, 'approvals'])->name('approvals.index');
    Route::post('/approvals/{user}/approve', [AdminController::class, 'approveUser'])->name('approvals.approve');

    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications.index');

    Route::get('/account', [AdminController::class, 'manageAccount'])->name('account.manage');
    Route::post('/account/update', [AdminController::class, 'updateAccount'])->name('account.update');

    Route::get('/vouchers', function () {
        return "Voucher Control";
    })->name('vouchers.control');
    Route::get('/users', [AdminController::class, 'usersManagemt'])->name('users.management');

    Route::get('/users/{user}', [AdminController::class, 'viewUser'])->name('users.show');
    Route::post('/users/{user}/password', [AdminController::class, 'updatePassword'])->name('users.password.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/disputes', function () {
        return "Disputes";
    })->name('disputes.index');
    Route::get('/credits', function () {
        return "Add Credit";
    })->name('credits.add');
    Route::get('/reports', function () {
        return "Reports";
    })->name('reports.index');
    Route::get('/contact-us', function () {
        return "Contact-Us";
    })->name('contact-us.index');
    Route::get('/notifications', function () {
        return "Notifications";
    })->name('notifications.index');
    Route::get('/categories', function () {
        return "Categories";
    })->name('categories.index');
    Route::get('/job-categories', function () {
        return "Job Categories";
    })->name('job-categories.index');
    Route::get('/analytics', function () {
        return "Analytics";
    })->name('analytics');
});

// Agent Routes
Route::prefix('agent')->name('agent.')->middleware(['auth', 'account_type:reseller_agent'])->group(function () {
    Route::get('/dashboard', [Agentcontroller::class, 'dashboard'])->name('dashboard');
    Route::get('/vouchers', [Agentcontroller::class, 'vouchers'])->name('vouchers');
    Route::get('/banks', [Agentcontroller::class, 'banks'])->name('banks');
    Route::get('/orders/history', [Agentcontroller::class, 'orderHistory'])->name('orders.history');
    Route::get('/deposit-store-credit', [Agentcontroller::class, 'deposit'])->name('deposit.store.credit');
    Route::get('/bank-link', [Agentcontroller::class, 'bankLink'])->name('bank.link');
    Route::post('/bank-link', [Agentcontroller::class, 'storeBank'])->name('bank.store');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'account_type:student'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
});
