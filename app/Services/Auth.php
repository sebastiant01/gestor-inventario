<?php

namespace App\Services;

use App\Models\User;
use App\Services\Hashing;
use App\Models\Role;


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
        $adminRoleId = Role::query()->where('role_name', 'admin')->value('role_id');

        if ($adminRoleId === null) {
            return 'No se pudo encontrar el rol de administrador.';
        }

        if (!$userName || !$email || !$phoneNum || !$password) {
            return false;
        }
        $password_hash = $this->hashing->hashPassword(password: $password);
        $result = $this->userCrud->createUser([
            'role_id' => $adminRoleId,
            'user_name' => $userName,
            'email' => $email,
            'phone_num' => $phoneNum,
            'password_hash' => $password_hash,
        ]);

        return true;
    }
}
