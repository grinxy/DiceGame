<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

class RegisterTest extends TestCase
{

    /**
     * A basic feature test example.
     */

//TEST EndPoint: Route::post('v1/players', [UserController::class, 'register']);
    public function test_register_user(): void
    {

        $data = [
            'name' => 'example2',
            'email' => 'example2@example.com',
            'password' => '1234'
        ];
      //  $this->withoutExceptionHandling();
        $response = $this->postJson('/api/v1/players', $data);


        $response->assertStatus(201); //201 es 'created'

        $this->assertDatabaseHas('users', [
            'name' => 'example2',
            'email' => 'example2@example.com'
        ]);
    }
    //Test registro incorrecto si email no es único
      public function test_register_not_unique_email()
    {
        User::create([
            'name' => 'Juanjo',
            'email' => 'juan@mail.com',
            'password' => '1234'
        ]);


        $data = [
            'name' => 'Juan',
            'email' => 'juan@mail.com',
            'password' => '1234',
        ];

        $response = $this->call('POST','/api/v1/players/', $data);

        $response->assertStatus(422);

        //comprobar que no ha guardado el user
        $this->assertDatabaseMissing('users', [
            'name' => 'Juan',
            'email' => 'juan@mail.com'
        ]);
    }
    //test 'name' único
    public function test_register_not_unique_name()
    {
        User::create([
            'name' => 'Paloma',
            'email' => 'paloma@mail.com',
            'password' => '1234'
        ]);


        $data = [
            'name' => 'Paloma',
            'email' => 'palo@mail.com',
            'password' => '1234',
        ];

        $response = $this->call('POST','/api/v1/players/', $data);

        $response->assertStatus(422);

        //comprobar que no ha guardado el user
        $this->assertDatabaseMissing('users', [
            'name' => 'Paloma',
            'email' => 'palo@mail.com'
        ]);
    }
    //Test registro correcto sin aportar nombre por defecto anonimo
    public function test_register_anonimo()
    {
        $data = [
            'email' => 'mail@example.com',
            'password' => '1234',
        ];

        $response = $this->call('POST','/api/v1/players/', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => 'anonimo',
            'email' => 'mail@example.com'
        ]);
    }
    //Test user anonimo no único
    public function test_register_many_anonimo()
    {
        User::create([
            'email' => 'anonimo1@mail.com',
            'password' => '1234'
        ]);


        $data = [
            'email' => 'anonimo2@mail.com',
            'password' => '1234',
        ];

        $response = $this->postJson('/api/v1/players/', $data);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => 'anonimo',
            'email' => 'anonimo2@mail.com'
        ]);
    }
    public function test_register_wrong_email_format()
    {
          $data = [
            'email' => 'hello',
            'password' => '1234',
        ];

        $response = $this->postJson('/api/v1/players/', $data);

        $response->assertStatus(422);


    }
}
