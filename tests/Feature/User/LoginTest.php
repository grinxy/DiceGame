<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Laravel\Passport\Passport;

class LoginTest extends TestCase
{

    //TEST EndPoint: Route::post('v1/players/login', [UserController::class, 'login']);
    public function test_login_with_valid_credentials()
    {

        // Crear usuario de prueba
        User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '1234'
        ]);
        // Realizar solicitud de inicio de sesión con las credenciales del usuario de prueba

        $response = $this->postJson("/api/v1/players/login", [
            'email' => 'test@example.com',
            'password' => '1234',
        ]);
        $response->assertStatus(200);

        // Verificar la estructura de la respuesta JSON y confirmar que se crea un token
        $response->assertJsonStructure([
            'status',
            'message',
            'token',
        ]);
    }

    public function test_login_not_registered_user()
    {

        $response = $this->postJson('/api/v1/players/login', [
            'email' => 'test2@example.com',
            'password' => '1234',
        ]);


        $response->assertStatus(401);


        $response->assertJson([
            'status' => false,
            'message' => 'Invalid login details',
        ]);
    }
    public function test_login_with_invalid_credentials()
    {
        User::create([
            'name' => 'test3',
            'email' => 'test3@example.com',
            'password' => '1234'
        ]);


        $response = $this->postJson('/api/v1/players/login', [
            'email' => 'test3@example.com',
            'password' => '0123',
        ]);


        $response->assertStatus(401);

        $response->assertJson([
            'status' => false,
            'message' => 'Invalid login details',
        ]);
    }
}
