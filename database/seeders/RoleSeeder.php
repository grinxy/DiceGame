<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { {
            // Verifica si los roles ya existen en la base de datos
            if (Role::where('name', 'admin')->exists() || Role::where('name', 'player')->exists()) {
                // Roles ya existen, no es necesario crearlos nuevamente
                return;
            }

            //Rol Admin + player
            $admin =  Role::create(['name' => 'admin']);
            $player = Role::create(['name' => 'player']);

            Permission::create(['name' => 'login'])->syncRoles([$admin, $player]);
            Permission::create(['name' => 'register'])->syncRoles([$admin, $player]);;
            Permission::create(['name' => 'profile'])->syncRoles([$admin, $player]);;
            Permission::create(['name' => 'logout'])->syncRoles([$admin, $player]);;
        }
    }
}
