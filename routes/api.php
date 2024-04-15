<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
//v1 of the API --> future versions should not affect apps using this set up

// Version 1 of the API
Route::prefix('v1')->group(function () {
    // Open routes (no authentication required)
    Route::post('register', [ApiController::class, 'register']);
    Route::post('login', [ApiController::class, 'login']);

    // Protected routes (require authentication)
    Route::middleware('auth:api')->group(function () {      //para estos dos metodos primero hay que pasar seguridad del middleware
        Route::get('profile', [ApiController::class, 'profile']);
        Route::get('logout', [ApiController::class, 'logout']);
    });
});
