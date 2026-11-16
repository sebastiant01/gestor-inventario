<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'user_name' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'phone_num' => fake()->unique()->numerify('3#########'),
            'password_hash' => static::$password ??= Hash::make('password'),
            'role_id' => Role::query()->firstOrCreate(
                ['role_name' => 'admin'],
                ['role_description' => 'Administrador del sistema']
            )->role_id,
            'remember_token' => Str::random(10),
        ];
    }
}
