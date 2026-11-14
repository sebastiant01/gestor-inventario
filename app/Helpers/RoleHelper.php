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
}
