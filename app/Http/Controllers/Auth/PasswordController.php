<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\RoleColumn;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                'different:current_password',
                Password::min(9)
                    ->mixedCase()
                    ->symbols(),
            ],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }

    /**
     * Set the user's password if it is currently null (e.g. registered via Google).
     */
    public function set(Request $request): RedirectResponse
    {
        if ($request->user()->password !== null) {
            return back()->with('error', 'Password has already been set.');
        }

        $validated = $request->validateWithBag('setPassword', [
            'role' => ['required', Rule::in(Role::registerable()->pluck('name')->toArray())],
            'password' => [
                'required',
                'confirmed',
                Password::min(9)
                    ->mixedCase()
                    ->symbols(),
            ],
        ]);

        $user = $request->user();
        $user->update([
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // Build dynamic profile data from role_columns
        $roleModel = Role::where('name', $validated['role'])->first();
        $profileData = [];

        if ($roleModel) {
            $profileColumns = $roleModel->profileColumns();
            foreach ($profileColumns as $col) {
                // Only take fields that are submitted in the request (keperluan, etc.)
                if ($request->has($col->column_name)) {
                    $profileData[$col->column_name] = $request->input($col->column_name);
                }
            }
        }

        // Create profile dynamically using role's relation_name
        if ($roleModel && $roleModel->relation_name) {
            $relation = $roleModel->relation_name;
            if (method_exists($user, $relation)) {
                $user->{$relation}()->create($profileData);
            }
        }

        return redirect()->route('profile.show')->with('info', __('Silakan lengkapi data diri Anda untuk melanjutkan.'));
    }
}
