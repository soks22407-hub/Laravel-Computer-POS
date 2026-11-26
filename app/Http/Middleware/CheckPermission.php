<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $module, string $action = 'view')
    {
        $user = $request->user();

        if (!$user || !$user->can_do($module, $action)) {
            abort(403, 'You do not have permission to do this.');
        }

        return $next($request);
    }
}