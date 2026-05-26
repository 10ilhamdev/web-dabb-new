<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect the user to the Google authentication page for login.
     */
    public function redirectToGoogleLogin()
    {
        session()->put('google_action', 'login');
        return Socialite::driver('google')->redirect() ?? redirect('/');
    }

    /**
     * Redirect the user to the Google authentication page for registration.
     */
    public function redirectToGoogleRegister()
    {
        session()->put('google_action', 'register');
        return Socialite::driver('google')->redirect() ?? redirect('/');
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $action = session('google_action', 'login');

            $userByGoogleId = User::where('google_id', $googleUser->getId())->first();
            $userByEmail = User::whereRaw('LOWER(email) = ?', [strtolower($googleUser->getEmail())])->first();

            // Case 1: google_id match — user previously registered via Google
            if ($userByGoogleId) {
                $user = $userByGoogleId;
                if ($userByEmail && $userByEmail->id !== $userByGoogleId->id) {
                    return redirect(route('login'))->withErrors([
                        'email' => __('Akun dengan email ini sudah digunakan oleh akun lain.'),
                    ]);
                }
                Auth::login($user);
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Case 2: only email match — link google_id to existing account
            if ($userByEmail && !$userByGoogleId) {
                $userByEmail->update(['google_id' => $googleUser->getId()]);
                Auth::login($userByEmail);
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Case 3: no match at all — new user
            if ($action === 'register') {
                $emailUsername = explode('@', $googleUser->getEmail())[0];
                $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', $emailUsername);
                $username = $baseUsername;
                $counter = 1;
                while (User::where('username', $username)->exists()) {
                    $username = $baseUsername . $counter;
                    $counter++;
                }

                $newUser = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'username' => $username,
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'email_verified_at' => now(),
                ]);

                Auth::login($newUser);
                return redirect()->intended(route('dashboard', absolute: false));
            }

            // Login action but no account found
            return redirect(route('register'))->withErrors([
                'email' => __('Akun belum terdaftar, silakan buat akun terlebih dahulu.'),
            ]);

        } catch (Exception $e) {
            report($e);
            return redirect(route('login'))->withErrors([
                'email' => __('Gagal login dengan Google. Silakan coba lagi atau gunakan login manual.'),
            ]);
        }
    }
}