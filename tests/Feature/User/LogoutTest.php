<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;
class LogoutTest extends TestCase
{

    /**
     * A basic feature test example.
     */

//TEST EndPoint: Route::post('v1/players/{id}/logout', [UserController::class, 'logout']);

    public function test_logout_valid_credentials(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);

        //autenticacion del user
        Passport::actingAs($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)
        ->postJson("/api/v1/players/{$user->id}/logout");
       // $response = $this->postJson("/api/v1/players/{$user->id}/logout");

        $response->assertStatus(200);

        $response->assertJson([
            'status' => true,
            'message' => 'User is now logged out',
        ]);
    }
     public function test_logout_user_not_authorized(): void
    {

        $user = User::create([
            'name' => 'Test',
            'email' => 'test@mail.com',
            'password' => '1234'
        ]);
        $user2 = User::create([
            'name' => 'Test2',
            'email' => 'test2@mail.com',
            'password' => '1234'
        ]);

        Passport::actingAs($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)
        ->postJson("/api/v1/players/{$user2->id}/logout");
      //  $response = $this->postJson("/api/v1/players/{$user2->id}");


        $response->assertStatus(403);
    }
}

