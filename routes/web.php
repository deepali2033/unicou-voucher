<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\agent\Agentcontroller;
use App\Http\Controllers\student\StudentController;
use App\Http\Controllers\manager\ManagerController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::get('/', function () {
    return view('home.home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('auth.logout');

// Email Verification Routes
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/login')->with('success', 'Email verified successfully. Please login.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

Route::get('/test-mail', function () {
    try {
        \Illuminate\Support\Facades\Mail::raw('SMTP is working!', function ($message) {
            $message->to(auth()->user()->email)->subject('Test Mail');
        });
        return "Mail sent successfully to " . auth()->user()->email;
    } catch (\Exception $e) {
        return "Mail failed: " . $e->getMessage();
    }
})->middleware('auth');

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
    Route::post('/system-control/toggle', [AdminController::class, 'toggleSystem'])->name('system.toggle');

    Route::get('/approvals', [AdminController::class, 'approvals'])->name('approvals.index');
    Route::post('/approvals/{user}/status', [AdminController::class, 'updateUserStatus'])->name('approvals.update-status');
    Route::post('/approvals/{user}/approve', [AdminController::class, 'approveUser'])->name('approvals.approve');
    Route::post('/approvals/{user}/reject', [AdminController::class, 'rejectUser'])->name('approvals.reject');

    Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications.index');

    Route::get('/account', [AdminController::class, 'manageAccount'])->name('account.manage');
    Route::post('/account/update', [AdminController::class, 'updateAccount'])->name('account.update');

    Route::get('/kyc-compliance', [AdminController::class, 'kycCompliance'])->name('kyc.compliance');
    Route::get('/kyc-compliance/{user}', [AdminController::class, 'viewKyc'])->name('kyc.show');

    Route::get('/wallet', [AdminController::class, 'walletManagement'])->name('wallet.index');
    Route::post('/wallet/credit', [AdminController::class, 'creditWallet'])->name('wallet.credit');
    Route::post('/wallet/debit', [AdminController::class, 'debitWallet'])->name('wallet.debit');

    Route::get('/vouchers', [AdminController::class, 'vouchersControl'])->name('vouchers.control');
    Route::get('/vouchers/create', [AdminController::class, 'createVoucher'])->name('vouchers.create');
    Route::get('/vouchers/{id}/edit', [AdminController::class, 'editVoucher'])->name('vouchers.edit');

    // New Sections
    Route::get('/orders', [AdminController::class, 'ordersIndex'])->name('orders.index');
    Route::get('/orders/export', [AdminController::class, 'exportOrders'])->name('orders.export');
    Route::post('/orders/{id}/deliver', [AdminController::class, 'deliverOrder'])->name('orders.deliver');
    Route::post('/orders/{id}/cancel', [AdminController::class, 'cancelOrder'])->name('orders.cancel');

    Route::get('/pricing', [AdminController::class, 'pricingIndex'])->name('pricing.index');
    Route::post('/pricing/update', [AdminController::class, 'updatePricing'])->name('pricing.update');

    Route::get('/inventory', [AdminController::class, 'inventoryIndex'])->name('inventory.index');
    Route::get('/inventory/create', [AdminController::class, 'createInventory'])->name('inventory.create');
    Route::post('/inventory/store', [AdminController::class, 'storeInventory'])->name('inventory.store');
    Route::get('/inventory/{id}/edit', [AdminController::class, 'editInventory'])->name('inventory.edit');
    Route::post('/inventory/{id}/update', [AdminController::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/{id}/delete', [AdminController::class, 'destroyInventory'])->name('inventory.destroy');
    Route::get('/inventory/export', [AdminController::class, 'exportInventory'])->name('inventory.export');
    Route::post('/inventory/import', [AdminController::class, 'importInventory'])->name('inventory.import');
    Route::post('/inventory/upload', [AdminController::class, 'uploadStock'])->name('inventory.upload');

    // New Sections
    Route::get('/reports', [AdminController::class, 'reportsIndex'])->name('reports.index');
    Route::get('/system-control', [AdminController::class, 'systemControlIndex'])->name('system.control');
    Route::post('/system-control/update', [AdminController::class, 'updateSystemControl'])->name('system.control.update');
    Route::get('/audit-logs', [AdminController::class, 'auditLogsIndex'])->name('audit.index');

    Route::post('/vouchers/store', [AdminController::class, 'storeVoucher'])->name('vouchers.store');
    Route::post('/vouchers/{id}/update', [AdminController::class, 'updateVoucher'])->name('vouchers.update');
    Route::delete('/vouchers/{id}/delete', [AdminController::class, 'deleteVoucher'])->name('vouchers.destroy');
    Route::get('/vouchers/export', [AdminController::class, 'exportVouchers'])->name('vouchers.export');
    Route::post('/vouchers/import', [AdminController::class, 'importVouchers'])->name('vouchers.import');
    Route::get('/users', [AdminController::class, 'usersManagemt'])->name('users.management');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    // Using put for update
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
    Route::get('/users/download-pdf', [AdminController::class, 'downloadPDF'])->name('users.pdf');

    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])
        ->name('users.toggle');

    Route::get('/users/{user}', [AdminController::class, 'viewUser'])->name('users.show');
    Route::post('/users/{user}/password', [AdminController::class, 'updatePassword'])->name('users.password.update');
    Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
    Route::get('/disputes', function () {
        return "Disputes";
    })->name('disputes.index');
    Route::get('/credits', function () {
        return "Add Credit";
    })->name('credits.add');

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

// Manager Routes
Route::prefix('manager')->name('manager.')->middleware(['auth', 'account_type:manager'])->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/audit', [ManagerController::class, 'auditTransactions'])->name('audit');
    Route::get('/users', [ManagerController::class, 'manageUsers'])->name('users');
    Route::get('/users/{user}', [ManagerController::class, 'approveDetails'])->name('users.show');
    Route::post('/users/{user}/approve', [ManagerController::class, 'approveUser'])->name('users.approve');
    Route::post('/users/{user}/add-credit', [ManagerController::class, 'addCredit'])->name('users.add_credit');
    Route::get('/vouchers/stock', [ManagerController::class, 'voucherStock'])->name('vouchers.stock');
    Route::get('/disputes', [ManagerController::class, 'disputes'])->name('disputes');
    Route::get('/system/stop', [ManagerController::class, 'stopSystem'])->name('system.stop');
    Route::get('/reports', [ManagerController::class, 'reports'])->name('reports');


    Route::get('/users', [AdminController::class, 'usersManagemt'])->name('users.management');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    // Using put for update
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{user}/suspend', [AdminController::class, 'suspendUser'])->name('users.suspend');
    Route::get('/users/download-pdf', [AdminController::class, 'downloadPDF'])->name('users.pdf');

    Route::post('/users/{id}/toggle-status', [AdminController::class, 'toggleUserStatus'])
        ->name('users.toggle');

    Route::get('/users/{user}', [AdminController::class, 'viewUser'])->name('users.show');
    Route::post('/users/{user}/password', [AdminController::class, 'updatePassword'])->name('users.password.update');
    // Route::delete('/users/{user}', [AdminController::class, 'deleteUser'])->name('users.destroy');
});

// Student Routes
Route::prefix('student')->name('student.')->middleware(['auth', 'account_type:student'])->group(function () {
    Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
});
