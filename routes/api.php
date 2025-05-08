<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentReportController;
use App\Http\Controllers\Api\PlatformEarningController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Reports 
    Route::post('reports/platforms/import/{platform}', [ReportController::class, 'importPlatformEarnings']);
    //Get the platform import data for the week
    Route::delete('reports/platforms/import/{platform}/{weekStartDate}', [ReportController::class, 'destroy']);


    //Get the status of the platforms
    Route::get('reports/platforms/import/status/{weekStartDate}', [ReportController::class, 'index']);

    // Drivers
    Route::apiResource('drivers', DriverController::class);
   
    // Platform Earnings
    Route::apiResource('platform-earnings', PlatformEarningController::class);
    
    // Invoices
    Route::apiResource('invoices', InvoiceController::class);
    
    // Subscriptions
    Route::apiResource('subscriptions', SubscriptionController::class);
    Route::get('subscriptions/{subscription}/invoices', [SubscriptionController::class, 'invoices']);
    Route::get('subscriptions/{user}/current', [SubscriptionController::class, 'current']);
    Route::put('subscriptions/{user}/{action}', [SubscriptionController::class, 'action']);

    // Settings
    Route::post('settings/commission', [SettingController::class, 'postCommission']);
    Route::get('settings/commission', [SettingController::class, 'getCommission']);

    // Users
    Route::apiResource('users', UserController::class);
    Route::post('users/{user}/password', [UserController::class, 'updatePassword']);
    Route::put('users/{user}/profile', [UserController::class, 'updateProfile']);

    // Company routes
    Route::get('/companies/{user}', [CompanyController::class, 'show']);
    Route::put('/companies/{user}', [CompanyController::class, 'update']);

    // Dashboard
    Route::get('/dashboard/{user}', [DashboardController::class, 'index']);

});
