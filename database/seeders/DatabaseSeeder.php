<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /** Seed roles and the permission catalogue without creating a hard-coded user. */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
        ]);

        $this->command?->info('No se creó una cuenta por defecto. Cuando la aplicación esté iniciada, visita /register para crear la primera cuenta.');
    }
}
