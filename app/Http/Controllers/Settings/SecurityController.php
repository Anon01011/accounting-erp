<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SecurityController extends Controller
{
    public function index()
    {
        return view('settings.security.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('settings.security.index')
            ->with('success', 'Password updated successfully.');
    }

    public function updateTwoFactor(Request $request)
    {
        $request->validate([
            'two_factor_enabled' => ['required', 'boolean'],
        ]);

        auth()->user()->update([
            'two_factor_enabled' => $request->two_factor_enabled,
        ]);

        return redirect()->route('settings.security.index')
            ->with('success', 'Two-factor authentication settings updated successfully.');
    }

    public function updateSession(Request $request)
    {
        $request->validate([
            'session_timeout' => ['required', 'integer', 'min:5', 'max:120'],
        ]);

        setting(['session_timeout' => $request->session_timeout])->save();

        return redirect()->route('settings.security.index')
            ->with('success', 'Session settings updated successfully.');
    }
} 