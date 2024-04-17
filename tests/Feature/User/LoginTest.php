<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
/*Route::prefix('v1')->group(function () {
    // Open routes (no authentication required)
    Route::post('/players', [UserController::class, 'register']);
    Route::post('/players/login', [UserController::class, 'login']);


    Route::middleware('auth:api', 'authenticated')->group(function () {
        Route::put('/players/{id}', [UserController::class, 'nameChange']);
        Route::post('/players/{id}/logout', [UserController::class, 'logout']);
    });*/
