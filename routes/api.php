<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Accounting\ChartOfAccountController;

// Chart of Accounts API Routes
Route::prefix('chart-of-accounts')->group(function () {
    Route::post('/generate-account-number', [ChartOfAccountController::class, 'generateAccountNumber']);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/account-groups', [ChartOfAccountController::class, 'getAccountGroups']);
    Route::get('/account-classes', [ChartOfAccountController::class, 'getAccountClasses']);
}); 