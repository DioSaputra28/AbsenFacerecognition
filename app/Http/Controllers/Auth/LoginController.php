<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Determine which field to use for authentication (email or username).
     *
     * @return string
     */
    public function username()
    {
        $loginField = filter_var(request()->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        request()->merge([$loginField => request()->input('username')]);

        return $loginField;
    }
}
