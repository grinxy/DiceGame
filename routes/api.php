<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\GameController;
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
    Route::post('/players', [UserController::class, 'register']);
    Route::post('/players/login', [UserController::class, 'login']);

    // Protected routes (require authentication)

    Route::middleware('auth:api')->group(function () {
        Route::put('/players/{id}', [UserController::class, 'nameChange']);
        Route::post('/players/{id}/logout', [UserController::class, 'logout']);
    });
    Route::middleware('auth:api','checkPlayerRole')->group(function () {
        Route::post('/players/{id}/games/', [GameController::class, 'play']);
        Route::get('/players/{id}/games', [GameController::class, 'gamesHistory']);
        Route::delete('players/{id}/games', [GameController::class, 'deleteHistory']);


    });
    Route::middleware('auth:api','checkAdminRole')->group(function () {
        Route::get('/players', [UserController::class, 'listPlayers']);
    });
});

/*
GET /players/ranking: retorna el rànquing mitjà de tots els jugadors/es del sistema. És a dir, el percentatge mitjà d’èxits.
GET /players/ranking/loser: retorna el jugador/a amb pitjor percentatge d’èxit.
GET /players/ranking/winner: retorna el jugador/a amb millor percentatge d’èxit.
*/
