<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $credentials['email'])->first();
        $passwordMatches = false;

        if ($user !== null) {
            try {
                $passwordMatches = Hash::check($credentials['password'], $user->password);
            } catch (\Throwable) {
                // If password in DB is plain text or not bcrypt, compare directly & re-hash
                if ($user->password === $credentials['password']) {
                    $passwordMatches = true;
                    $user->password = Hash::make($credentials['password']);
                    $user->save();
                }
            }
        }

        logger()->info('Admin login verification', [
            'email' => $credentials['email'],
            'user_found' => $user !== null,
            'password_matches' => $passwordMatches,
            'database' => config('database.connections.mysql.database'),
        ]);

        if (! $passwordMatches) {
            throw ValidationException::withMessages(['email' => 'Email hoặc mật khẩu không chính xác.']);
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
