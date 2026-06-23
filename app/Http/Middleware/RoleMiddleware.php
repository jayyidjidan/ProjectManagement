<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response
    {
        if (!auth()->check()) {

            return redirect()
                ->route('login');
        }

        $userRole =
            auth()->user()->id_role;

        $allowedRoles =
            array_map(
                'intval',
                $roles
            );

        if (
            !in_array(
                $userRole,
                $allowedRoles
            )
        ) {

            abort(403);
        }

        return $next($request);
    }
}    