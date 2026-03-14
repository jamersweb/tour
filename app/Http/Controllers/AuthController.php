<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function showLogin(): Response
    {
        return Inertia::render('Auth/Login', [
            'seo' => [
                'title' => 'Login',
                'description' => 'Sign in to your Acute Tourism account.',
            ],
        ]);
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        if (! Auth::attempt($request->validated(), true)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('account.dashboard');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Auth/Register', [
            'seo' => [
                'title' => 'Register',
                'description' => 'Create your Acute Tourism account.',
            ],
        ]);
    }

    public function showForgotPassword(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'seo' => [
                'title' => 'Forgot Password',
                'description' => 'Request a password reset link for your Acute Tourism account.',
            ],
        ]);
    }

    public function sendPasswordResetLink(ForgotPasswordRequest $request): RedirectResponse
    {
        Password::sendResetLink($request->validated());

        return back()->with('success', 'If that email exists, a reset link has been sent.');
    }

    public function showResetPassword(Request $request, string $token): Response
    {
        return Inertia::render('Auth/ResetPassword', [
            'seo' => [
                'title' => 'Reset Password',
                'description' => 'Set a new password for your Acute Tourism account.',
            ],
            'reset' => [
                'token' => $token,
                'email' => (string) $request->query('email', ''),
            ],
        ]);
    }

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $status = Password::reset(
            $request->validated(),
            function (User $user, string $password): void {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        if ($status !== Password::PASSWORD_RESET) {
            return back()->withErrors([
                'email' => __($status),
            ]);
        }

        return redirect()->route('login')->with('success', 'Password reset. You can now log in.');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create($request->validated() + [
            'is_admin' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
