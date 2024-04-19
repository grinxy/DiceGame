<?php

namespace Tests\Feature\Game;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use App\Models\User;


class AdminTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    //TEST EndPoint:   Route::get('v1/players', [PlayerController::class, 'listPlayers']);
    public function test_admin_get_players(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        Passport::actingAs($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/");


        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'data'
        ]);

        $response->assertJson([
            'message' => 'Players list:'
        ]);
    }
    public function test_player_cant_get_players(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');

        Passport::actingAs($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/");


        $response->assertStatus(403);

    }
    public function test_admin_unauthenticated_cant_get_players(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        $response = $this->getJson("/api/v1/players/");


        $response->assertStatus(401);

    }

    //TEST EndPoint: Route::get('v1/players/ranking', [PlayerController::class, 'ranking']);
    public function test_admin_get_ranking(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        Passport::actingAs($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/ranking");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message' ,
        ]);
    }
   public function test_player_cant_get_ranking(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');

        Passport::actingAs($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/ranking");


        $response->assertStatus(403);

    }

    public function test_admin_unauthenticated_cant_get_ranking(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        $response = $this->getJson("/api/v1/players/ranking");


        $response->assertStatus(401);

    }
     //TEST EndPoint: Route::get('v1/players/ranking/winner', [PlayerController::class, 'rankingWinner']);
    public function test_admin_get_winner(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        Passport::actingAs($user);

        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/ranking/winner");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'Winner(s)' => [
            '*' => [                    //* array con uno o mas elementos
                'name',
                'success_rate'
            ]
        ]
    ]);
    }
   public function test_player_cant_get_winner(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');

        Passport::actingAs($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/ranking/winner");


        $response->assertStatus(403);

    }

    public function test_admin_unauthenticated_cant_get_winner(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        $response = $this->getJson("/api/v1/players/ranking/winner");


        $response->assertStatus(401);

    }

    //TEST EndPoint: Route::get('v1/players/ranking/loser', [PlayerController::class, 'rankingLoser']);
    public function test_admin_get_loser(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        Passport::actingAs($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/ranking/loser");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'message',
            'Loser(s)' => [
            '*' => [
                'name',
                'success_rate'
            ]
        ]
    ]);
    }
   public function test_player_cant_get_loser(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('player');

        Passport::actingAs($user);
        $response = $this->withHeader('Authorization', 'Bearer ' . $user->createToken('TestToken')->accessToken)->getJson("/api/v1/players/ranking/loser");


        $response->assertStatus(403);

    }

    public function test_admin_unauthenticated_cant_get_loser(): void
    {

        $user = User::create([
            'name' => 'user',
            'email' => 'user@mail.com',
            'password' => '1234'
        ]);
        $user->assignRole('admin');


        $response = $this->getJson("/api/v1/players/ranking/loser");


        $response->assertStatus(401);

    }
}

