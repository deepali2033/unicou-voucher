<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\agent\Agentcontroller;
use App\Http\Controllers\student\StudentController;

Route::get('/', function () {
    return view('home.home');
})->name('home');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');
Route::get('/getLocationData', [Agentcontroller::class, 'getLocationData']);

// Placeholder Admin Routes to prevent Layout Errors
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/vouchers', function () {
        return "Voucher Control";
    })->name('vouchers.control');
    Route::get('/users', function () {
        return "User Management";
    })->name('users.index');
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
