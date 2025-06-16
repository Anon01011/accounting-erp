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
use App\Http\Controllers\Asset\CategoryController as AssetCategoryController;
use App\Http\Controllers\AssetDocumentController;
use App\Http\Controllers\Settings\TaxGroupController;
use App\Http\Controllers\Settings\TaxRateController;
use App\Http\Controllers\Audit\AuditLogController;

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
    Route::prefix('chart-of-accounts')->name('chart-of-accounts.')->group(function () {
        // API routes first
        Route::get('/api/account-groups', [ChartOfAccountController::class, 'getAccountGroups'])->name('account-groups');
        Route::get('/api/account-classes', [ChartOfAccountController::class, 'getAccountClasses'])->name('account-classes');
        
        // Other specific routes
        Route::get('/', [ChartOfAccountController::class, 'index'])->name('index');
        Route::get('/create', [ChartOfAccountController::class, 'create'])->name('create');
        Route::post('/', [ChartOfAccountController::class, 'store'])->name('store');
        Route::post('/import', [ChartOfAccountController::class, 'import'])->name('import');
        Route::get('/export', [ChartOfAccountController::class, 'export'])->name('export');
        Route::get('/template', [ChartOfAccountController::class, 'template'])->name('template');
        Route::get('/load-more', [ChartOfAccountController::class, 'loadMore'])->name('load-more');
        
        // Parameterized routes last
        Route::get('/{account}', [ChartOfAccountController::class, 'show'])->name('show');
        Route::get('/{account}/edit', [ChartOfAccountController::class, 'edit'])->name('edit');
        Route::put('/{account}', [ChartOfAccountController::class, 'update'])->name('update');
        Route::delete('/{account}', [ChartOfAccountController::class, 'destroy'])->name('destroy');
        Route::post('/{account}/status', [ChartOfAccountController::class, 'updateStatus'])->name('status');
    });
    
    // Journal Entries Routes
    Route::resource('journal-entries', JournalEntryController::class);
    Route::post('journal-entries/{journalEntry}/post', [JournalEntryController::class, 'post'])->name('journal-entries.post');
    Route::post('journal-entries/{journalEntry}/void', [JournalEntryController::class, 'void'])->name('journal-entries.void');
    Route::get('journal-entries/export/pdf', [JournalEntryController::class, 'exportPdf'])->name('journal-entries.export.pdf');
    Route::get('journal-entries/export/excel', [JournalEntryController::class, 'exportExcel'])->name('journal-entries.export.excel');
    Route::get('journal-entries/{journalEntry}/export/pdf', [JournalEntryController::class, 'exportSinglePdf'])->name('journal-entries.export.single.pdf');
    Route::get('journal-entries/{journalEntry}/export/excel', [JournalEntryController::class, 'exportSingleExcel'])->name('journal-entries.export.single.excel');

    // Financial Reports Routes
    Route::prefix('financial-reports')->name('financial-reports.')->middleware(['auth'])->group(function () {
        Route::get('/balance-sheet', [FinancialReportController::class, 'balanceSheet'])->name('balance-sheet');
        Route::get('/income-statement', [FinancialReportController::class, 'incomeStatement'])->name('income-statement');
        Route::get('/trial-balance', [FinancialReportController::class, 'trialBalance'])->name('trial-balance');
    });

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
    Route::prefix('reports')->name('reports.')->middleware(['auth'])->group(function () {
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
        Route::get('/', [SettingController::class, 'index'])->name('index');
        Route::resource('users', UserController::class);
        Route::resource('company', CompanyController::class);
        Route::resource('security', SecurityController::class);
        Route::resource('localization', LocalizationController::class);
        
        // Tax Management Routes
        Route::prefix('tax')->name('tax.')->group(function () {
            Route::get('groups', [TaxGroupController::class, 'index'])->name('groups.index');
            Route::get('groups/create', [TaxGroupController::class, 'create'])->name('groups.create');
            Route::post('groups', [TaxGroupController::class, 'store'])->name('groups.store');
            Route::get('groups/{taxGroup}', [TaxGroupController::class, 'show'])->name('groups.show');
            Route::get('groups/{taxGroup}/edit', [TaxGroupController::class, 'edit'])->name('groups.edit');
            Route::put('groups/{taxGroup}', [TaxGroupController::class, 'update'])->name('groups.update');
            Route::delete('groups/{taxGroup}', [TaxGroupController::class, 'destroy'])->name('groups.destroy');
            Route::patch('/groups/{taxGroup}/status', [TaxGroupController::class, 'updateStatus'])->name('groups.status');

            Route::get('rates', [TaxRateController::class, 'index'])->name('rates.index');
            Route::get('rates/create', [TaxRateController::class, 'create'])->name('rates.create');
            Route::post('rates', [TaxRateController::class, 'store'])->name('rates.store');
            Route::get('rates/{taxRate}', [TaxRateController::class, 'show'])->name('rates.show');
            Route::get('rates/{taxRate}/edit', [TaxRateController::class, 'edit'])->name('rates.edit');
            Route::put('rates/{taxRate}', [TaxRateController::class, 'update'])->name('rates.update');
            Route::delete('rates/{taxRate}', [TaxRateController::class, 'destroy'])->name('rates.destroy');
        });
    });

    // Assets Routes
    Route::prefix('assets')->name('assets.')->group(function () {
        // Main asset routes
        Route::get('/', [AssetController::class, 'index'])->name('index');
        Route::get('/create', [AssetController::class, 'create'])->name('create');
        Route::post('/', [AssetController::class, 'store'])->name('store');
        Route::get('/{asset}', [AssetController::class, 'show'])->name('show');
        Route::get('/{asset}/edit', [AssetController::class, 'edit'])->name('edit');
        Route::put('/{asset}', [AssetController::class, 'update'])->name('update');
        Route::delete('/{asset}', [AssetController::class, 'destroy'])->name('destroy');
        
        // Additional asset routes
        Route::post('/{asset}/depreciation', [AssetController::class, 'calculateDepreciation'])->name('depreciation');
        Route::post('/{asset}/maintenance', [AssetController::class, 'recordMaintenance'])->name('maintenance');
        Route::post('/{asset}/dispose', [AssetController::class, 'dispose'])->name('dispose');
        Route::get('/report', [AssetController::class, 'report'])->name('report');
        
        // Asset transactions route
        Route::post('/{asset}/transactions', [AssetController::class, 'recordTransaction'])->name('transactions');
        
        // Asset categories
        Route::resource('categories', AssetCategoryController::class);
        
        // Document routes
        Route::post('/{asset}/documents', [AssetDocumentController::class, 'store'])->name('documents.store');
        Route::get('/documents/{document}/download', [AssetDocumentController::class, 'download'])->name('documents.download');
        Route::delete('/documents/{document}', [AssetDocumentController::class, 'destroy'])->name('documents.destroy');

        // New route for generating asset codes
        Route::get('/generate-code/{category}', [AssetController::class, 'generateCode'])->name('generate-code');
    });

    // Audit Logs Routes
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    // Document Management Routes
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
});