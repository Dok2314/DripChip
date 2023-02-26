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
    Route::group(['prefix' => 'accounts'], function () {
        Route::get('search', [ApiControllers\AccountController::class, 'searchUserByAccount']);
        Route::get('{accountId}', [ApiControllers\AccountController::class, 'getInfo']);
        Route::put('{accountId}', [ApiControllers\AccountController::class, 'updateAccount']);
        Route::delete('{accountId}', [ApiControllers\AccountController::class, 'deleteAccount']);
    });

    Route::group(['prefix' => 'locations'], function () {
        Route::post('/', [ApiControllers\LocationController::class, 'createLocation']);
        Route::get('{locationId}', [ApiControllers\LocationController::class, 'getInfo']);
        Route::put('{locationId}', [ApiControllers\LocationController::class, 'updateLocation']);
        Route::delete('{locationId}', [ApiControllers\LocationController::class, 'deleteLocation']);
    });

    Route::group(['prefix' => 'animals/types'], function () {
        Route::post('/', [ApiControllers\AnimalTypeController::class, 'createAnimalType']);
        Route::get('{typeId}', [ApiControllers\AnimalTypeController::class, 'getInfo']);
        Route::put('{typeId}', [ApiControllers\AnimalTypeController::class, 'updateAnimalType']);
        Route::delete('{typeId}', [ApiControllers\AnimalTypeController::class, 'deleteAnimalType']);
    });

    Route::group(['prefix' => 'animals'], function () {
        Route::get('search', [ApiControllers\AnimalController::class, 'searchAnimal']);
        Route::post('/', [ApiControllers\AnimalController::class, 'createAnimal']);

        Route::group(['prefix' => '{animalId}'], function () {
            Route::get('/', [ApiControllers\AnimalController::class, 'getInfo']);
            Route::put('/', [ApiControllers\AnimalController::class, 'updateAnimal']);
            Route::delete('/', [ApiControllers\AnimalController::class, 'deleteAnimal']);

            Route::group(['prefix' => 'types'], function() {
                Route::post('{typeId}', [ApiControllers\AnimalController::class, 'addAnimalTypeToAnimal']);
                Route::delete('{typeId}', [ApiControllers\AnimalController::class, 'deleteAnimalTypeFromAnimal']);
                Route::put('/', [ApiControllers\AnimalController::class, 'updateAnimalTypeToAnimal']);
            });

            Route::post('locations/{pointId}', [ApiControllers\AnimalVisitLocationController::class, 'createVisitLocation']);
        });
    });
});
