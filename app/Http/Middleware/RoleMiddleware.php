<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        $roleSet = array_map(function (string $role): string {
            return $role === 'siswa' ? 'user' : $role;
        }, $roles);

        abort_unless($user && in_array($user->role === 'siswa' ? 'user' : $user->role, $roleSet, true), 403);

        return $next($request);
    }
}
