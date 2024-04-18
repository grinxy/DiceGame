<?php

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
class LoginTest extends TestCase
{


    public function test_login_with_valid_credentials()
    {

       // Artisan::call('passport:install');   //al rehacer migraciones con cada test, se desisntalan las keys y hay que volver a instalarlas

         // usuario de prueba
        User::create([
            'name' => 'test',
            'email' => 'test@example.com',
            'password' => '1234'
        ]);


        $response = $this->postJson('/api/v1/players/login', [
            'email' => 'test@example.com',
            'password' => '1234',
        ]);


        $response->assertStatus(200);


        $response->assertJsonStructure([
            'status' ,
            'message' ,
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


