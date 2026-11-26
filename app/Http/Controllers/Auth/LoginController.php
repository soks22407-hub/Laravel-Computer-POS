<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';
    protected $maxAttempts = 3;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->utype === 'ADM') {
            return redirect()->route('admin.index');
        } elseif ($user->utype === 'EMP') {
            return redirect()->route('employee.index');
        } elseif ($user->utype === 'CEO') {
            return redirect()->route('ceo.index');
        } else {
            return redirect('/');
        }
    }
    protected function incrementLoginAttempts(Request $request)
    {
        $key = $this->throttleKey($request);

        $this->limiter()->hit($key, 30);

        // On the last allowed attempt, restart the timer so the lock lasts a full 30 seconds
        if ($this->limiter()->attempts($key) >= $this->maxAttempts) {
            $this->limiter()->clear($key);

            for ($i = 0; $i < $this->maxAttempts; $i++) {
                $this->limiter()->hit($key, 30);
            }
        }
    }

    protected function sendLockoutResponse(Request $request)
    {
        $seconds = $this->limiter()->availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            $this->username() => "Too many login attempts. Please try again in {$seconds} seconds.",
        ])->status(429);
    }
}
