<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        // Default to 'admin' if no roles were provided via alias usage
        $rolesList = [];
        if (empty($roles)) {
            $rolesList = ['admin'];
        } else {
            // Support pipe-delimited list like role:admin|manager
            foreach ($roles as $roleParam) {
                foreach (explode('|', (string) $roleParam) as $roleName) {
                    $trimmed = trim($roleName);
                    if ($trimmed !== '') {
                        $rolesList[] = $trimmed;
                    }
                }
            }
        }

        if (! $user || (! empty($rolesList) && ! $user->hasAnyRole($rolesList))) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}


