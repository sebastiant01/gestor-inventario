<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class Hashing
{
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    public function checkPassword(string $passwordInput, string $passwordHash): bool
    {
        return Hash::check($passwordInput, $passwordHash);
    }
}
