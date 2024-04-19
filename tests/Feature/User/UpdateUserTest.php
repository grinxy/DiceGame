<?php

namespace Tests\Feature\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Laravel\Passport\Passport;

class UpdateUserTest extends TestCase
{

    /**
     * A basic feature test example.
     */

    public function test_update_user_name(): void
    {
        $user = User::create([
            'name' => 'OldName',
            'email' => 'name@mail.com',
            'password' => '1234'
        ]);


        //autenticacion del user
        Passport::actingAs($user);


        $response = $this->putJson("/api/v1/players/{$user->id}", [
            'name' => 'NewName'
        ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'name' => 'NewName',
            'email' => 'name@mail.com'
        ]);
    }
    public function test_update_user_name_not_authenticated(): void
    {

        $user = User::create([
            'name' => 'OldName',
            'email' => 'name@mail.com',
            'password' => '1234'
        ]);
        //  No hay autenticacion

        $response = $this->putJson("/api/v1/players/{$user->id}", [
            'name' => 'NewName'
        ]);


        $response->assertStatus(401);


        $this->assertDatabaseMissing('users', [
            'name' => 'NewName',
            'email' => 'name@mail.com'
        ]);
    }
    public function test_update_user_name_not_authorized(): void
    {

        $user = User::create([
            'name' => 'TestUser',
            'email' => 'test@mail.com',
            'password' => '1234'
        ]);
        $user2 = User::create([
            'name' => 'OldName',
            'email' => 'name2@mail.com',
            'password' => '1234'
        ]);
        Passport::actingAs($user);

        $response = $this->putJson("/api/v1/players/{$user2->id}", [
            'name' => 'NewName'
        ]);


        $response->assertStatus(403);


        $this->assertDatabaseMissing('users', [
            'name' => 'NewName',
            'email' => 'name@mail.com'
        ]);
    }
}
