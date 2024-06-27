<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function __construct()
    {
    }

    public function providerRedirect(Request $request, string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function providerCallback(Request $request, string $provider)
    {
        $socialiteUser = Socialite::driver($provider)->user();

        $user = User::query()->where('email', $socialiteUser->email)->first();
        if (is_null($user)) {
            $user = new User();
            $user->name = $socialiteUser->name;
            $user->email = $socialiteUser->email;
        }
        $user->provider =  $provider;
        $user->provider_id =  $socialiteUser->provider_id;
        $user->provider_token =  $socialiteUser->provider_token;
        $user->save();

        Auth::login($user);
        return redirect('/');
    }

    public function login()
    {
        return Inertia::render('Auths/Login');
    }

    public function handleLogin(Request $request)
    {
        $input = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ]);

        if (!Auth::attempt($input)) {
            return redirect()->back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        // regenerate the session
        $request->session()->regenerate();
        // regenerate the token
        $request->session()->regenerateToken();

        return redirect('/')->with('message', 'Login successful.');;
    }

    public function register()
    {
        return Inertia::render('Auths/Register');
    }

    public function handleRegister(Request $request)
    {
        $input = $request->validate([
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'password_confirmation' => ['same:password'],
        ]);

        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()->withErrors([
                'email' => 'The email has already been taken, try login or use another email.',
            ]);
        }

        User::create(array_merge($input, ['password' => bcrypt($request->password)]));

        return redirect('/login')->with('message', 'Register successful, please login.');
    }

    public function handleLogout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login')->with('message', 'Logout successful, please login.');
    }

    public function forgotPassword()
    {
        return Inertia::render('Auths/ForgotPassword');
    }

    public function handleForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $message = Password::sendResetLink(
            $request->only('email')
        );

        return $message === Password::RESET_LINK_SENT
            ? back()->with(['message' => __($message)])
            : back()->withErrors(['email' => __($message)]);
    }

    public function resetPassword(Request $request, string $token)
    {
        $props = ['email' => $request->email, 'token' => $token];
        return Inertia::render('Auths/ResetPassword', $props);
    }

    public function handleResetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $message = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => bcrypt($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $message === Password::PASSWORD_RESET
            ? redirect('/login')->with('message', __($message))
            : redirect()->back()->withErrors(['email' => [__($message)]]);
    }
}
