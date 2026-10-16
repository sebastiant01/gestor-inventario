<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_panel_redirects_guests_to_login(): void
    {
        $this->get('/users')->assertRedirect(route('login'));
    }

    public function test_user_can_log_in_and_log_out(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('password-segura'),
        ]);

        $this->post(route('login.attempt'), [
            'user_name' => $user->user_name,
            'password' => 'password-segura',
        ])->assertRedirect(route('users.index'));

        $this->assertAuthenticatedAs($user);
        $this->get(route('users.index'))->assertOk();

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_login_rejects_an_incorrect_password(): void
    {
        $user = User::factory()->create([
            'password_hash' => Hash::make('password-correcta'),
        ]);

        $this->from(route('login'))->post(route('login.attempt'), [
            'user_name' => $user->user_name,
            'password' => 'incorrecta',
        ])->assertRedirect(route('login'))->assertSessionHasErrors('user_name');

        $this->assertGuest();
    }
}
