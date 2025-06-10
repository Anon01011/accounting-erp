<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JournalEntryController;
use App\Http\Controllers\FinancialReportController;
use App\Http\Controllers\Sales\OrderController;
use App\Http\Controllers\Sales\DeliveryController;
use App\Http\Controllers\Sales\InvoiceController;
use App\Http\Controllers\Sales\ReturnController;
use App\Http\Controllers\Sales\CustomerController;
use App\Http\Controllers\Sales\ProductController;
use App\Http\Controllers\Sales\CategoryController;
use App\Http\Controllers\Sales\PriceListController;
use App\Http\Controllers\Sales\QuotationController;
use App\Http\Controllers\Sales\PaymentController;
use App\Http\Controllers\Sales\ReportController;
use App\Http\Controllers\Sales\SettingController;
use App\Http\Controllers\Purchases\PurchaseOrderController;
use App\Http\Controllers\Purchases\PurchaseReceiptController;
use App\Http\Controllers\Purchases\PurchaseBillController;
use App\Http\Controllers\Purchases\PurchaseReturnController;
use App\Http\Controllers\Inventory\InventoryProductController;
use App\Http\Controllers\Inventory\WarehouseController;
use App\Http\Controllers\Inventory\StockTransferController;
use App\Http\Controllers\Inventory\StockCountController;
use App\Http\Controllers\Accounting\ChartOfAccountController;
use App\Http\Controllers\Accounting\AccountingPaymentController;
use App\Http\Controllers\Accounting\AccountingReceiptController;
use App\Http\Controllers\Reports\CashFlowController;
use App\Http\Controllers\HR\EmployeeController;
use App\Http\Controllers\HR\PayrollController;
use App\Http\Controllers\HR\AttendanceController;
use App\Http\Controllers\HR\DocumentController;
use App\Http\Controllers\Settings\UserController;
use App\Http\Controllers\Settings\CompanyController;
use App\Http\Controllers\Settings\SecurityController;
use App\Http\Controllers\Settings\LocalizationController;
use App\Http\Controllers\Sales\SalesReturnController;

// Redirect root to login
Route::get('/', function () {
    return redirect('/dashboard');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Chart of Accounts Routes
    Route::controller(\App\Http\Controllers\Accounting\ChartOfAccountController::class)->group(function () {
        Route::get('chart-of-accounts', 'index')->name('chart-of-accounts.index');
        Route::get('chart-of-accounts/create', 'create')->name('chart-of-accounts.create');
        Route::post('chart-of-accounts', 'store')->name('chart-of-accounts.store');
        Route::get('chart-of-accounts/{account}/edit', 'edit')->name('chart-of-accounts.edit');
        Route::put('chart-of-accounts/{account}', 'update')->name('chart-of-accounts.update');
        Route::delete('chart-of-accounts/{account}', 'destroy')->name('chart-of-accounts.destroy');
        Route::post('chart-of-accounts/{account}/status', 'updateStatus')->name('chart-of-accounts.status');
    });
    
    // Journal Entries Routes
    Route::resource('journal-entries', JournalEntryController::class);
    Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
    Route::post('journal-entries/{journalEntry}/void', [JournalEntryController::class, 'void'])->name('journal-entries.void');

    // Financial Reports
    Route::get('/financial-reports/balance-sheet', [FinancialReportController::class, 'balanceSheet'])->name('financial-reports.balance-sheet');
    Route::get('/financial-reports/income-statement', [FinancialReportController::class, 'incomeStatement'])->name('financial-reports.income-statement');
    Route::get('/financial-reports/trial-balance', [FinancialReportController::class, 'trialBalance'])->name('financial-reports.trial-balance');

    // Sales Module Routes
    Route::prefix('sales')->name('sales.')->group(function () {
        // Orders
        Route::resource('orders', OrderController::class);
        Route::post('orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
        Route::post('orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
        Route::get('orders/{order}/pdf', [OrderController::class, 'pdf'])->name('orders.pdf');

        // Deliveries
        Route::resource('deliveries', DeliveryController::class);
        Route::post('deliveries/{delivery}/complete', [DeliveryController::class, 'complete'])->name('deliveries.complete');

        // Invoices
        Route::resource('invoices', InvoiceController::class);
        Route::post('invoices/{invoice}/post', [InvoiceController::class, 'post'])->name('invoices.post');
        Route::post('invoices/{invoice}/void', [InvoiceController::class, 'void'])->name('invoices.void');

        // Returns
        Route::resource('returns', SalesReturnController::class);
        Route::post('returns/{return}/approve', [SalesReturnController::class, 'approve'])->name('returns.approve');

        // Customers
        Route::resource('customers', CustomerController::class);

        // Products
        Route::resource('products', ProductController::class);

        // Categories
        Route::resource('categories', CategoryController::class);

        // Price Lists
        Route::resource('price-lists', PriceListController::class);

        // Quotations
        Route::resource('quotations', QuotationController::class);
        Route::get('quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
        Route::post('quotations/send-email', [QuotationController::class, 'sendEmail'])->name('quotations.send-email');

        // Payments
        Route::resource('payments', PaymentController::class);
        Route::post('payments/{payment}/post', [PaymentController::class, 'post'])->name('payments.post');

        // Reports
        Route::get('reports/sales-summary', [ReportController::class, 'salesSummary'])->name('reports.sales-summary');
        Route::get('reports/customer-statement', [ReportController::class, 'customerStatement'])->name('reports.customer-statement');
        Route::get('reports/product-performance', [ReportController::class, 'productPerformance'])->name('reports.product-performance');

        // Settings
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Purchases Module Routes
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::resource('orders', PurchaseOrderController::class);
        Route::resource('receipts', PurchaseReceiptController::class);
        Route::resource('bills', PurchaseBillController::class);
        Route::resource('returns', PurchaseReturnController::class);
    });

    // Inventory Module Routes
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::resource('products', InventoryProductController::class);
        Route::resource('warehouses', WarehouseController::class);
        Route::resource('transfers', StockTransferController::class);
        Route::resource('counts', StockCountController::class);
    });

    // Accounting Module Routes
    Route::prefix('accounting')->name('accounting.')->group(function () {
        Route::resource('payments', AccountingPaymentController::class);
        Route::resource('receipts', AccountingReceiptController::class);
    });

    // Reports Module Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('cash-flow', [CashFlowController::class, 'index'])->name('cash-flow.index');
    });

    // HR & Payroll Module Routes
    Route::prefix('hr')->name('hr.')->group(function () {
        Route::resource('employees', EmployeeController::class);
        Route::resource('payroll', PayrollController::class);
        Route::resource('attendance', AttendanceController::class);
        Route::resource('documents', DocumentController::class);
    });

    // Settings Module Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::resource('users', UserController::class);
        Route::resource('company', CompanyController::class);
        Route::resource('security', SecurityController::class);
        Route::resource('localization', LocalizationController::class);
    });

    // Assets Routes
    Route::resource('assets', AssetController::class);
    Route::get('/assets/{asset}/edit', [AssetController::class, 'edit'])->name('assets.edit');

    // Explicit status update route
    Route::post('chart-of-accounts/{account}/status', [ChartOfAccountController::class, 'updateStatus'])
        ->name('chart-of-accounts.status');
});