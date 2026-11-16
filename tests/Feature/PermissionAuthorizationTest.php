<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_admin_can_consult_and_manage_users(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertOk();

        $roleId = Role::query()->where('role_name', 'user')->value('role_id');

        $this->actingAs($admin)
            ->post(route('users.store'), [
                'user_name' => 'usuario.nuevo',
                'email' => 'nuevo@example.com',
                'phone_num' => '3001234567',
                'password' => 'password-segura',
                'password_confirmation' => 'password-segura',
                'role_id' => $roleId,
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'nuevo@example.com',
            'role_id' => $roleId,
        ]);

        $createdUser = User::query()->where('email', 'nuevo@example.com')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('users.edit', $createdUser->user_id))
            ->assertOk();

        $this->actingAs($admin)
            ->put(route('users.update', $createdUser->user_id), [
                'user_name' => 'usuario.editado',
                'email' => 'editado@example.com',
                'phone_num' => '3001234568',
                'role_id' => $roleId,
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'user_id' => $createdUser->user_id,
            'user_name' => 'usuario.editado',
            'email' => 'editado@example.com',
        ]);
    }


    public function test_admin_can_open_the_main_management_interfaces(): void
    {
        $admin = User::factory()->create();

        foreach ([
            route('users.index'),
            route('users.create'),
            route('categories.index'),
            route('categories.create'),
            route('suppliers.index'),
            route('suppliers.create'),
            route('products.index'),
            route('products.create'),
            route('inventories.index'),
            route('inventories.create'),
        ] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_read_only_role_can_consult_modules_but_cannot_create_or_edit(): void
    {
        $userRole = Role::query()->where('role_name', 'user')->firstOrFail();
        $user = User::factory()->create(['role_id' => $userRole->role_id]);

        foreach ([
            route('categories.index'),
            route('suppliers.index'),
            route('products.index'),
            route('inventories.index'),
        ] as $url) {
            $this->actingAs($user)->get($url)->assertOk();
        }

        foreach ([
            route('categories.create'),
            route('suppliers.create'),
            route('products.create'),
            route('inventories.create'),
            route('users.index'),
        ] as $url) {
            $this->actingAs($user)->get($url)->assertForbidden();
        }

        $this->actingAs($user)->post(route('categories.store'), [
            'category_name' => 'No autorizada',
        ])->assertForbidden();

        $this->actingAs($user)->put(route('products.update'), [
            'product_id' => 999,
        ])->assertForbidden();

        $this->actingAs($user)->delete(route('inventories.destroy', 999))
            ->assertForbidden();
    }

    public function test_user_without_user_management_permissions_is_forbidden(): void
    {
        $userRole = Role::query()->where('role_name', 'user')->firstOrFail();
        $user = User::factory()->create(['role_id' => $userRole->role_id]);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('categories.create'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('users.store'), [
                'user_name' => 'intruso',
                'email' => 'intruso@example.com',
                'phone_num' => '3007654321',
                'password' => 'password-segura',
                'password_confirmation' => 'password-segura',
            ])
            ->assertForbidden();
    }

    public function test_login_session_is_not_required_for_a_protected_action(): void
    {
        $this->get(route('users.index'))
            ->assertRedirect(route('login'));
    }
}
