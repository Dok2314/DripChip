<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api as ApiControllers;

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

Route::post('registration', [ApiControllers\UserController::class, 'registration']);

Route::post('login', [ApiControllers\UserController::class, 'login'])->name('api.login');

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('/accounts/{accountId}', [ApiControllers\AccountController::class, 'getInfo']);
});
