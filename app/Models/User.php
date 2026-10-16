<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_name',
        'email',
        'phone_num',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /** The database stores password hashes in password_hash rather than password. */
    public function getAuthPasswordName(): string
    {
        return 'password_hash';
    }

    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }

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

    public function getUserByName(string $userName): ?User
    {
        return User::where('user_name', $userName)->first();
    }

    public function getUserById(int $userId): ?User
    {
        return User::find($userId);
    }

    public function updateUser(int $userId, ?string $userName = null, ?string $email = null, ?string $phoneNum = null): bool
    {
        $user = $this->getUserById($userId);
        if (!$user) {
            return false;
        }

        if ($userName) {
            if (User::where('user_name', $userName)->where('user_id', '!=', $userId)->exists()) {
                return false;
            }
            $user->user_name = $userName;
        }

        if ($phoneNum) {
            if (User::where('phone_num', $phoneNum)->where('user_id', '!=', $userId)->exists()) {
                return false;
            }
            $user->phone_num = $phoneNum;
        }

        if ($email) {
            if (User::where('email', $email)->where('user_id', '!=', $userId)->exists()) {
                return false;
            }
            $user->email = $email;
        }

        $user->save();
        return true;
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
