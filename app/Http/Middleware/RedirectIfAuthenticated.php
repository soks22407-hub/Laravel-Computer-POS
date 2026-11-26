<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return redirect($this->redirectTo());
            }
        }

        return $next($request);
    }

    /**
     * Send already-logged-in users to the right panel based on utype,
     * instead of always dumping everyone on the customer homepage.
     */
    protected function redirectTo(): string
    {
        $user = Auth::user();

        return match ($user->utype ?? null) {
            'ADM' => route('admin.index'),
            'EMP' => route('employee.index'),
            'CEO' => route('ceo.index'),
            default => route('home.index'),
        };
    }
}