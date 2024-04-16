<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Game;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {

        $users = User::all();

        // asignar juegos aleatorio a usuarios
        for ($i = 0; $i < 30; $i++) {
            $randomUser = $users->random();
            Game::factory()->create([
                'user_id' => $randomUser->id,
            ]);
        }

    }
}
