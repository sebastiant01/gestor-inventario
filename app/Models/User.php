<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function createUser(string $userName, string $email, string $phoneNum, string $passwordHash): bool | string
    {
        if (User::where('user_name', $userName)->exists()) {
            return "This username already exists.";
        }

        if (User::where('phone_num', $phoneNum)->exists()) {
            return "This phone number already exists.";
        }

        if (User::where('email', $email)->exists()) {
            return "This email already exists.";
        }
        User::create([
            'user_name' => $userName,
            'email' => $email,
            'phone_num' => $phoneNum,
            'password_hash' => $passwordHash,
        ]);

        return true;
    }

    public function getUserByName(string $userName): Model | null
    {
        $user = User::where('user_name', $userName)->first();
        if (!$user) {
            return null;
        }
        return $user;
    }

    public function getUserById(int $userId): Model | null
    {
        $user = User::where('user_id', $userId)->first();
        if (!$user) {
            return null;
        }
        return $user;
    }

    public function updateUser(int $userId): void
    {
        // I'll leave it like this for now.
    }

    public function deleteUser(int $userId): bool
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            return false;
        }
        $user->delete();
        return true;
    }
}
