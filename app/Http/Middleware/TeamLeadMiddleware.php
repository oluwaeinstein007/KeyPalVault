<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TeamLeadMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if ($user && in_array($user->user_role_id, [1, 2])) {
            return $next($request);
        }

        abort(403, 'Unauthorized: Only admin and super admin can enter allowed routes');
    }
}
