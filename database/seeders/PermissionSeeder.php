<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Gestión de usuarios.
            ['code' => 'users.show', 'description' => 'Consultar usuarios', 'module' => 'Usuarios'],
            ['code' => 'users.create', 'description' => 'Crear usuarios', 'module' => 'Usuarios'],
            ['code' => 'users.update', 'description' => 'Editar usuarios', 'module' => 'Usuarios'],
            ['code' => 'users.delete', 'description' => 'Eliminar usuarios', 'module' => 'Usuarios'],
            ['code' => 'users.assign-role', 'description' => 'Asignar roles a usuarios', 'module' => 'Usuarios'],

            // Categorías.
            ['code' => 'categories.show', 'description' => 'Consultar categorías', 'module' => 'Categorías'],
            ['code' => 'categories.create', 'description' => 'Crear categorías', 'module' => 'Categorías'],
            ['code' => 'categories.update', 'description' => 'Editar categorías', 'module' => 'Categorías'],
            ['code' => 'categories.delete', 'description' => 'Eliminar categorías', 'module' => 'Categorías'],

            // Proveedores.
            ['code' => 'suppliers.show', 'description' => 'Consultar proveedores', 'module' => 'Proveedores'],
            ['code' => 'suppliers.create', 'description' => 'Crear proveedores', 'module' => 'Proveedores'],
            ['code' => 'suppliers.update', 'description' => 'Editar proveedores', 'module' => 'Proveedores'],
            ['code' => 'suppliers.delete', 'description' => 'Eliminar proveedores', 'module' => 'Proveedores'],

            // Productos.
            ['code' => 'products.show', 'description' => 'Consultar productos', 'module' => 'Productos'],
            ['code' => 'products.create', 'description' => 'Crear productos', 'module' => 'Productos'],
            ['code' => 'products.update', 'description' => 'Editar productos', 'module' => 'Productos'],
            ['code' => 'products.delete', 'description' => 'Eliminar productos', 'module' => 'Productos'],

            // Inventarios.
            ['code' => 'inventories.show', 'description' => 'Consultar inventarios', 'module' => 'Inventarios'],
            ['code' => 'inventories.create', 'description' => 'Crear inventarios', 'module' => 'Inventarios'],
            ['code' => 'inventories.update', 'description' => 'Editar inventarios', 'module' => 'Inventarios'],
            ['code' => 'inventories.delete', 'description' => 'Eliminar inventarios', 'module' => 'Inventarios'],
            ['code' => 'inventories.products.manage', 'description' => 'Administrar productos en inventarios', 'module' => 'Inventarios'],
        ];

        foreach ($permissions as $permission) {
            Permission::query()->updateOrCreate(
                ['code' => $permission['code']],
                [
                    'description' => $permission['description'],
                    'module' => $permission['module'],
                ]
            );
        }

        $adminRole = Role::query()->where('role_name', 'admin')->firstOrFail();
        $userRole = Role::query()->where('role_name', 'user')->firstOrFail();

        // El administrador recibe todos los permisos definidos en el sistema.
        $adminRole->permissions()->sync(Permission::query()->pluck('permission_id')->all());

        // El usuario estándar únicamente puede consultar los módulos de inventario.
        $readOnlyPermissionIds = Permission::query()
            ->whereIn('code', [
                'categories.show',
                'suppliers.show',
                'products.show',
                'inventories.show',
            ])
            ->pluck('permission_id')
            ->all();

        $userRole->permissions()->sync($readOnlyPermissionIds);
    }
}
