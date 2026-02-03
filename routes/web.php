<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\BankController;
use App\Http\Controllers\Dashboard\CustomerController;

use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\VoucherController;
use App\Http\Controllers\Dashboard\InventoryController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\WalletController;
use App\Http\Controllers\Dashboard\ComplianceController;
use App\Http\Controllers\Dashboard\SystemController;
use App\Http\Controllers\Dashboard\ReportController;
use App\Http\Controllers\Dashboard\SettingsController;
use App\Http\Controllers\Dashboard\AgentController;
use App\Http\Controllers\Dashboard\BonusController;
use App\Http\Controllers\Dashboard\StudentController;
use App\Http\Controllers\Dashboard\ManagerController;
use App\Http\Controllers\Dashboard\PricingController;
use App\Http\Controllers\Dashboard\ReferralController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

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

    $user = auth()->user();
    if ($user->account_type === 'student' && !$user->exam_purpose) {
        return redirect()->route('auth.form.student')->with('success', 'Email verified. Please complete your profile.');
    }
    if ($user->account_type === 'reseller_agent' && !$user->business_name) {
        return redirect()->route('auth.forms.B2BResellerAgent')->with('success', 'Email verified. Please complete your profile.');
    }

    return redirect()->route('dashboard')->with('success', 'Email verified successfully.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerification'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// Additional Registration Forms
Route::middleware('auth')->group(function () {
    Route::get('/register/agent-details', [AuthController::class, 'showAgentForm'])->name('auth.forms.B2BResellerAgent');
    Route::post('/register/agent-details', [AuthController::class, 'storeAgentDetails'])->name('auth.form.agent.post');

    Route::get('/register/student-details', [AuthController::class, 'showStudentForm'])->name('auth.form.student');
    Route::post('/register/student-details', [AuthController::class, 'storeStudentDetails'])->name('auth.form.student.post');
});

// Dashboard Routes (Unified Prefix)
Route::prefix('dashboard')->middleware(['auth'])->group(function () {

    Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications.index');
    Route::get('/my-profile', [UserController::class, 'profile'])->name('profile.index');

    Route::get('/stock-alerts', [DashboardController::class, 'stockAlerts'])->name('stock.alerts');
    Route::get('/account', [DashboardController::class, 'manageAccount'])->name('account.manage');
    Route::post('/account/update', [DashboardController::class, 'updateAccount'])->name('account.update');

    Route::get('/pricing', [PricingController::class, 'index'])->name('pricing.index');
    Route::post('/pricing/store', [PricingController::class, 'store'])->name('pricing.store');
    Route::delete('/pricing/{id}', [PricingController::class, 'destroy'])->name('pricing.destroy');

    // User Management
    Route::get('/users', [UserController::class, 'index'])->name('users.management');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{id}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle');
    Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password.update');
    Route::get('/users-download-pdf', [UserController::class, 'downloadPDF'])->name('users.pdf');
    Route::get('/mangers', [UserController::class, 'managers'])->name('manager.page');
    Route::get('/support-team', [UserController::class, 'SupportTeam'])->name('support.team');
    Route::get('/reseller-agents', [UserController::class, 'ResellerAgent'])->name('reseller.agent');
    Route::get('/regular-agents', [UserController::class, 'RegularAgent'])->name('regular.agent');
    Route::get('/student-list', [UserController::class, 'student'])->name('student.page');
    // Voucher Management
    Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers');
    Route::get('/vouchers/order/{id}', [VoucherController::class, 'showOrder'])->name('vouchers.order');
    Route::post('/vouchers/order/{id}', [VoucherController::class, 'placeOrder'])->name('vouchers.order.post');
    Route::get('/vouchers/create', [VoucherController::class, 'create'])->name('vouchers.create');
    Route::post('/vouchers/store', [VoucherController::class, 'store'])->name('vouchers.store');
    Route::get('/vouchers/{id}/edit', [VoucherController::class, 'edit'])->name('vouchers.edit');
    Route::post('/vouchers/{id}/update', [VoucherController::class, 'update'])->name('vouchers.update');
    Route::delete('/vouchers/{id}/delete', [VoucherController::class, 'destroy'])->name('vouchers.destroy');
    Route::get('/vouchers-export', [VoucherController::class, 'export'])->name('vouchers.export');
    Route::post('/vouchers-import', [VoucherController::class, 'import'])->name('vouchers.import');

    // Inventory Management
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::get('/inventory/create', [InventoryController::class, 'create'])->name('inventory.create');
    Route::post('/inventory/store', [InventoryController::class, 'store'])->name('inventory.store');
    Route::get('/inventory/{id}/edit', [InventoryController::class, 'edit'])->name('inventory.edit');
    Route::post('/inventory/{id}/update', [InventoryController::class, 'update'])->name('inventory.update');
    Route::delete('/inventory/{id}/delete', [InventoryController::class, 'destroy'])->name('inventory.destroy');
    Route::get('/inventory-export', [InventoryController::class, 'export'])->name('inventory.export');
    Route::post('/inventory-import', [InventoryController::class, 'import'])->name('inventory.import');
    Route::post('/inventory-upload', [InventoryController::class, 'upload'])->name('inventory.upload');

    // Order Management
    Route::middleware(['account_type:admin,manager,support'])->group(function () {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders-export', [OrderController::class, 'export'])->name('orders.export');
        Route::post('/orders/{id}/deliver', [OrderController::class, 'deliver'])->name('orders.deliver');
        Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    });
    Route::get('/orders/history', [OrderController::class, 'orderHistory'])->name('orders.history');


    // Wallet Management
    Route::get('/wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('/wallet/credit', [WalletController::class, 'credit'])->name('wallet.credit');
    Route::post('/wallet/debit', [WalletController::class, 'debit'])->name('wallet.debit');

    // Compliance & KYC
    Route::get('/kyc-compliance', [ComplianceController::class, 'index'])->name('kyc.compliance');
    Route::get('/kyc-compliance/{user}', [ComplianceController::class, 'show'])->name('kyc.show');
    Route::post('/approvals/{user}/status', [ComplianceController::class, 'updateStatus'])->name('approvals.update-status');
    Route::post('/approvals/{user}/approve', [ComplianceController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{user}/reject', [ComplianceController::class, 'reject'])->name('approvals.reject');

    // System Control
    Route::get('/system-control', [SystemController::class, 'controlIndex'])->name('system.control');
    Route::post('/system-control/update', [SystemController::class, 'updateControl'])->name('system.control.update');
    Route::post('/system-control/toggle', [SystemController::class, 'toggleSystem'])->name('system.toggle');
    Route::get('/audit-logs', [SystemController::class, 'auditLogs'])->name('audit.index');

    // Reports & Revenue
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue.index');

    // Settings
    Route::get('/settings/risk-levels', [SettingsController::class, 'riskLevels'])->name('settings.risk-levels');
    Route::get('/settings/risk-levels/export', [SettingsController::class, 'exportRiskLevelsCsv'])->name('settings.risk-levels.export');
    Route::post('/settings/update-risk-level', [SettingsController::class, 'updateRiskLevel'])->name('settings.update-risk-level');
    Route::delete('/settings/risk-level/{id}', [SettingsController::class, 'deleteRiskLevel'])->name('settings.risk-level.delete');

    // BANK
    Route::get('/bank-link', [BankController::class, 'bankLink'])->name('bank.link');
    Route::post('/bank-link', [BankController::class, 'storeBank'])->name('bank.store');

    // Customer Support
    Route::get('/customer-support', [CustomerController::class, 'supportindex'])->name('customer.support');
    Route::post('/customer-support', [CustomerController::class, 'storeSupportQuery'])->name('customer.support.store');
    Route::get('/customer-query', [CustomerController::class, 'CustomerQuery'])->name('customer.query');
    Route::get('/support-options/issues/{topicId}', [CustomerController::class, 'getIssuesByTopic'])->name('support.options.issues');
    Route::post('/support-options', [CustomerController::class, 'storeSupportOption'])->name('support.options.store');
    Route::delete('/support-options/{id}', [CustomerController::class, 'deleteSupportOption'])->name('support.options.destroy');

    //Referral Points
    Route::get('/referral', [ReferralController::class, 'referral'])->name('referral');

    //Bonus Points
    Route::get('/bonus-point', [BonusController::class, 'bonus'])->name('bonus');

    // Other placeholder routes
    Route::get('/disputes', function () {
        return "Disputes";
    })->name('disputes.index');
    Route::get('/credits', function () {
        return "Add Credit";
    })->name('credits.add');
    Route::get('/contact-us', function () {
        return "Contact-Us";
    })->name('contact-us.index');
    Route::get('/categories', function () {
        return "Categories";
    })->name('categories.index');
    Route::get('/job-categories', function () {
        return "Job Categories";
    })->name('job-categories.index');
    Route::get('/analytics', function () {
        return "Analytics";
    })->name('analytics');

    // Agent Specific
    Route::prefix('agent')->name('agent.')->middleware(['account_type:reseller_agent'])->group(function () {
        Route::get('/', [AgentController::class, 'dashboard'])->name('dashboard');
        Route::get('/vouchers', [AgentController::class, 'vouchers'])->name('vouchers');
        Route::get('/banks', [AgentController::class, 'banks'])->name('banks');

        Route::get('/deposit-store-credit', [AgentController::class, 'deposit'])->name('deposit.store.credit');
        Route::get('/bank-link', [AgentController::class, 'bankLink'])->name('bank.link');
        Route::post('/bank-link', [AgentController::class, 'storeBank'])->name('bank.store');
    });


    // Manager Specific
    Route::prefix('manager')->name('manager.')->middleware(['account_type:manager'])->group(function () {
        Route::get('/', [ManagerController::class, 'dashboard'])->name('dashboard');
        Route::get('/audit', [ManagerController::class, 'auditTransactions'])->name('audit');
        Route::get('/users', [ManagerController::class, 'manageUsers'])->name('users');
        Route::get('/users/{user}', [ManagerController::class, 'approveDetails'])->name('users.show');
        Route::post('/users/{user}/approve', [ManagerController::class, 'approveUser'])->name('users.approve');
        Route::post('/users/{user}/add-credit', [ManagerController::class, 'addCredit'])->name('users.add_credit');
        Route::get('/vouchers/stock', [ManagerController::class, 'voucherStock'])->name('vouchers.stock');
        Route::get('/disputes', [ManagerController::class, 'disputes'])->name('disputes');
        Route::get('/system/stop', [ManagerController::class, 'stopSystem'])->name('system.stop');
        Route::get('/reports', [ManagerController::class, 'reports'])->name('reports');
    });

    // Student Specific
    Route::prefix('student')->name('student.')->middleware(['account_type:student'])->group(function () {
        Route::get('/', [StudentController::class, 'dashboard'])->name('dashboard');
    });
});
