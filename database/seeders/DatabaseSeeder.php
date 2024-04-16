<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Roles y permmisos
        $this->call(RoleSeeder::class);

        //User Admin
        $this->call(UserSeeder::class);

        User::factory(10)->create()->each(function($user){
            $user->assignRole('player');
        });

        $this->call(GameSeeder::class);
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
