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
        return Socialite::driver('google')->redirect();
    }

    /**
     * Redirect the user to the Google authentication page for registration.
     */
    public function redirectToGoogleRegister()
    {
        session()->put('google_action', 'register');
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $action = session('google_action', 'login'); // Default line

            $user = User::where('google_id', $googleUser->getId())->orWhere('email', $googleUser->getEmail())->first();

            if ($action === 'login') {
                if ($user) {
                    // Update google_id if missing but email matched
                    if (!$user->google_id) {
                        $user->update(['google_id' => $googleUser->getId()]);
                    }
                    Auth::login($user);
                    return redirect()->intended(route('dashboard', absolute: false));
                } else {
                    // Belum memiliki akun
                    return redirect(route('register'))->withErrors([
                        'email' => __('Akun belum terdaftar, silahkan buat akun terlebih dahulu.'),
                    ]);
                }
            } else {
                // Register action
                if ($user) {
                    // Sudah punya akun
                    return redirect(route('login'))->withErrors([
                        'email' => __('Akun sudah terdaftar, silakan login.'),
                    ]);
                } else {
                    // Generate username from email or name
                    $emailUsername = explode('@', $googleUser->getEmail())[0];
                    $baseUsername = preg_replace('/[^a-zA-Z0-9]/', '', $emailUsername);
                    $username = $baseUsername;
                    $counter = 1;
                    while (\App\Models\User::where('username', $username)->exists()) {
                        $username = $baseUsername . $counter;
                        $counter++;
                    }

                    $newUser = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'username' => $username,
                        'google_id' => $googleUser->getId(),
                        'password' => null, // Password can be null for Google signups
                        'email_verified_at' => now(), // Mark email as verified since it came from Google
                    ]);

                    Auth::login($newUser);
                    return redirect()->intended(route('dashboard', absolute: false));
                }
            }

        } catch (Exception $e) {
            return redirect(route('login'))->withErrors([
                'email' => __('auth.failed') ?? 'Failed to authenticate with Google.',
            ]);
        }
    }
}
