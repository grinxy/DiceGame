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
        $user->assignRole('admin');

        // Actuar como el usuario creado
        Passport::actingAs($user);

        // Realizar una solicitud para jugar
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->postJson("/api/v1/players/{$user->id}/games");


        $response->assertStatus(403);
    }
}
