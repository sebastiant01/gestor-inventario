<?php

namespace App\Http\Middleware;

use App\Helpers\RoleHelper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthorizedMiddleware
{
    public function handle(Request $request, Closure $next, string $permissionCode): Response
    {
        if (! $request->user()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->guest(route('login'));
        }

        if (! RoleHelper::isAuthorized($permissionCode, $request->user())) {
            abort(403, 'No tienes autorización para realizar esta acción.');
        }

        return $next($request);
    }
}
