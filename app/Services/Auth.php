<?php

namespace App\Services;

use App\Models\User;
use App\services\Hashing;


class Auth
{
    public function __construct(
        private Hashing $hashing,
        private User $userCrud
    ) {}

    public function login(string $userName, string $password): bool
    {
        $user = $this->userCrud->getUserByName($userName);
        if (!$user) {
            return false;
        }
        return $this->hashing->checkPassword(passwordInput: $password, passwordHash: $user->password_hash);
    }

    public function register(string $userName, string $email, string $phoneNum, string $password): bool | string
    {
        if (!$userName || !$email || !$phoneNum || !$password) {
            return false;
        }
        $passwordHash = $this->hashing->hashPassword(password: $password);
        return $this->userCrud->createUser(userName: $userName, email: $email, phoneNum: $phoneNum, passwordHash: $passwordHash);
    }
}
