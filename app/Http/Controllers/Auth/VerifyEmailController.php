<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VerifyEmailController extends Controller
{
    /**
     * Mark the user's email address as verified.
     * This route is accessible without login (via signed URL from email).
     */
    public function __invoke(Request $request): RedirectResponse
    {
        // Find the user by ID
        $user = User::find($request->route('id'));

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // Verify the signed URL hash matches
        $hash = sha1($user->getEmailForVerification());
        if ($hash !== $request->route('hash')) {
            return redirect()->route('login')->with('error', 'Link verifikasi tidak valid.');
        }

        // Check if already verified
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('verification.status', ['status' => 'already_verified']);
        }

        // Mark email as verified
        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect()->route('verification.status', ['status' => 'success']);
    }
}