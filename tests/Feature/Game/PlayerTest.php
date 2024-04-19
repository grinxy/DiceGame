<?php

namespace Tests\Feature\Game;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\User;

class PlayerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
   //TEST EndPoint: Route::post('v1/players/{id}/games', [GameController::class, 'play']);
    public function test_player_can_play(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');


        Passport::actingAs($user);
        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->postJson("/api/v1/players/{$user->id}/games");


        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'game' => [
                'user_id',
                'dice1_value',
                'dice2_value',
                'sum',
                'result',
            ]
        ]);

        // Verificar que el mensaje sea "Game stored successfully"
        $response->assertJson([
            'message' => 'Game stored successfully'
        ]);
    }
    public function test_player_cannot_play(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');
        $user2 = User::create([
            'name' => 'user2',
            'email' => 'user2@mail.com',
            'password' => '1234'
        ]);
        $user2->assignRole('player');

        // Actuar como el usuario creado
        Passport::actingAs($user);

        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->postJson("/api/v1/players/{$user2->id}/games");


        $response->assertStatus(403);
    }
    public function test_admin_cannot_play(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');

        // Actuar como el usuario creado
        Passport::actingAs($user);

        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->postJson("/api/v1/players/{$user->id}/games");


        $response->assertStatus(403);
    }

    //TEST EndPoint:  Route::get('v1/players/{id}/games', [PlayerController::class, 'gamesHistory']);
    public function test_player_get_games_history(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');
        $token = $user->createToken('TestToken')->accessToken;

        Passport::actingAs($user);
        // Realizar al menos una jugada
        $this->withHeader('Authorization', 'Bearer ' . $token)->postJson("/api/v1/players/{$user->id}/games");
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->getJson("/api/v1/players/{$user->id}/games");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'games_played',
            'success_rate',
            'data' => [
                '*' => [
                    'id',
                    'user_id',
                    'dice1_value',
                    'dice2_value',
                    'sum',
                    'result',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }
    public function test_player_not_authorized_get_games_history(): void
    {
        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');
        $user2 = User::create([
            'name' => 'user2',
            'email' => 'user2@mail.com',
            'password' => '1234'
        ]);
        $user2->assignRole('player');

        // Actuar como el usuario creado
        Passport::actingAs($user);

        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/{$user2->id}/games");


        $response->assertStatus(403);
    }
    public function test_admin_not_authorized_get_games_history(): void
    {
        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');

        // Actuar como el usuario creado
        Passport::actingAs($user);

        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/{$user->id}/games");


        $response->assertStatus(403);
    }

    //TEST EndPoint:  Route::delete('v1/players/{id}/games', [PlayerController::class, 'deleteHistory']);
    public function test_player_delete_games_history(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');
        $token = $user->createToken('TestToken')->accessToken;

        Passport::actingAs($user);
        // Realizar al menos una jugada
        $this->withHeader('Authorization', 'Bearer ' . $token)->postJson("/api/v1/players/{$user->id}/games");
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)->deleteJson("/api/v1/players/{$user->id}/games");

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Game history deleted for user: ' . $user->name
        ]);
    }
    public function test_player_not_authorized_delete_games_history(): void
    {
        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');
        $user2 = User::create([
            'name' => 'user2',
            'email' => 'user2@mail.com',
            'password' => '1234'
        ]);
        $user2->assignRole('player');

        // Actuar como el usuario creado
        Passport::actingAs($user);

        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->deleteJson("/api/v1/players/{$user2->id}/games");


        $response->assertStatus(403);
    }
    public function test_admin_not_authorized_delete_games_history(): void
    {
        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');

        // Actuar como el usuario creado
        Passport::actingAs($user);

        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->deleteJson("/api/v1/players/{$user->id}/games");


        $response->assertStatus(403);
    }
}
