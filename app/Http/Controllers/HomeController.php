<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $feature = \App\Models\Feature::find(1);
        $requiresLoginModal = false;
        $loginModalPreviews = [];
        $loginModalPreview = null;
        $loginModalRoomNames = [];
        $loginModalRoomName = null;
        $loginModalPrompt = __('auth.login_required_prompt');

        if ($feature) {
            $requiresLoginModal = !\Illuminate\Support\Facades\Auth::check() && $feature->is_login_required;
        }

        return view('welcome', compact(
            'feature', 'requiresLoginModal', 'loginModalPreviews', 'loginModalPreview',
            'loginModalRoomNames', 'loginModalRoomName', 'loginModalPrompt'
        ));
    }


    public function switchLocale(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, ['id', 'en'], true)) {
            $locale = 'id';
        }

        $request->session()->put('locale', $locale);

        return redirect()->back();
    }
}
