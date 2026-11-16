<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::query()->updateOrCreate(
            ['role_name' => 'admin'],
            ['role_description' => 'Administrador con acceso a todos los módulos.']
        );

        Role::query()->updateOrCreate(
            ['role_name' => 'user'],
            ['role_description' => 'Usuario con permisos de consulta.']
        );
    }
}
