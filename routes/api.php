<?php

use App\Http\Controllers\Api\V1\Admin\IrtBracketController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\InstitutionController;
use App\Http\Controllers\Api\V1\PayrollController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        Route::apiResource('institutions', InstitutionController::class)
            ->only(['index', 'store', 'show', 'update'])
            ->where(['institution' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}']);

        Route::get('/institutions/{institution}/tax-configuration', [InstitutionController::class, 'showTaxConfiguration'])
            ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::put('/institutions/{institution}/tax-configuration', [InstitutionController::class, 'updateTaxConfiguration'])
            ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

        Route::get('/institutions/{institution}/bank-accounts', [InstitutionController::class, 'listBankAccounts'])
            ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::post('/institutions/{institution}/bank-accounts', [InstitutionController::class, 'createBankAccount'])
            ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

        Route::post('/institutions/{institution}/activate', [InstitutionController::class, 'activate'])
            ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        Route::post('/institutions/{institution}/deactivate', [InstitutionController::class, 'deactivate'])
            ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

        Route::post('/institutions/{institution}/payroll/calculate', [PayrollController::class, 'calculate'])
            ->where('institution', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

        Route::middleware('super_admin')->prefix('admin')->group(function () {
            Route::get('/irt-brackets', [IrtBracketController::class, 'index']);
            Route::post('/irt-brackets', [IrtBracketController::class, 'store']);
            Route::get('/irt-brackets/{irtBracket}', [IrtBracketController::class, 'show'])
                ->where('irtBracket', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
            Route::put('/irt-brackets/{irtBracket}', [IrtBracketController::class, 'update'])
                ->where('irtBracket', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
            Route::delete('/irt-brackets/{irtBracket}', [IrtBracketController::class, 'destroy'])
                ->where('irtBracket', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');
        });
    });
});
