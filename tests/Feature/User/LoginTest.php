<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use App\Models\User;

class LoginTest extends TestCase
{


    public function test_login_with_valid_credentials()
    {
        // Crear un usuario de prueba
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        // Envía una solicitud de inicio de sesión con credenciales válidas
        $response = $this->postJson('/api/v1/players/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);

        // Verifica que la respuesta tenga el estado 200 (OK)
        $response->assertStatus(200);

        // Verifica que la respuesta contiene el token de acceso
        $response->assertJsonStructure([
            'status',
            'message',
            'token',
        ]);
    }

    public function test_login_with_invalid_credentials()
    {
       /* // Envía una solicitud de inicio de sesión con credenciales inválidas
        $response = $this->postJson('/api/players/login', [
            'email' => 'invalid@example.com',
            'password' => 'invalidpassword',
        ]);

        // Verifica que la respuesta tenga el estado 401 (Unauthorized)
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);

        // Verifica que la respuesta contiene un mensaje de error
        $response->assertJson([
            'status' => false,
            'message' => 'Invalid login details',
        ]);*/
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
