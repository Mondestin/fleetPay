<?php

use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentReportController;
use App\Http\Controllers\Api\PlatformEarningController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\SubscriptionController;
use Illuminate\Support\Facades\Route;

//Route::middleware('auth:sanctum')->group(function () {

    //Reports 
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

    // Settings
    Route::post('settings/commission', [SettingController::class, 'postCommission']);
    Route::get('settings/commission', [SettingController::class, 'getCommission']);

    // Users
    Route::apiResource('users', UserController::class);
//});
