<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * The app uses a one-time registration screen to bootstrap its first account.
     * Keeping this seeder empty avoids creating a public, hard-coded admin password.
     */
    public function run(): void
    {
        $this->command?->info('No se creó una cuenta por defecto. Cuando la aplicación esté iniciada, visita /register para crear la primera cuenta.');
    }
}
