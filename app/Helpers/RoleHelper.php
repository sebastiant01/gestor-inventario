<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class RoleHelper
{
    public static function isAuthorized(string $permissionCode, ?User $user = null): bool
    {
        $user ??= Auth::user();

        return $user instanceof User && $user->hasPermission($permissionCode);
    }

    public static function firstAccessibleRouteName(?User $user = null): ?string
    {
        $user ??= Auth::user();

        if (! $user instanceof User) {
            return null;
        }

        foreach ([
            ['products.show', 'products.index'],
            ['categories.show', 'categories.index'],
            ['suppliers.show', 'suppliers.index'],
            ['inventories.show', 'inventories.index'],
            ['roles.show', 'roles.index'],
            ['users.show', 'users.index'],
        ] as [$permissionCode, $routeName]) {
            if ($user->hasPermission($permissionCode)) {
                return $routeName;
            }
        }

        return null;
    }
}
