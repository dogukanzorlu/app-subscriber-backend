<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\Mock\GoogleController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Device Resource
Route::resource('/devices', DeviceController::class);

// Subscription Save and Check
Route::post('/subscriptions/check', [SubscriptionController::class, 'checkSubscription']);
Route::post('/subscriptions', [SubscriptionController::class, 'saveSubscription']);

// Google Subscription Mock Service
Route::post('google/service/subscription', [GoogleController::class, 'post']);
