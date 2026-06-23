<?php

namespace App\Http\Controllers;


use App\Models\Members;
use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        $responsibles =
            Members::whereHas(
                'user',
                fn($q) =>
                    $q->whereIn(
                        'id_role',
                        [1,2]
                    )
            )
            ->get();

        return view(
            'auth.login',
            compact('responsibles')
        );
    }

    public function login(Request $request)
{
    $request->validate([
        'login' => 'required',
        'password' => 'required'
    ]);

    $login = $request->login;

    $field = filter_var(
        $login,
        FILTER_VALIDATE_EMAIL
    )
        ? 'email'
        : 'username';

    $credentials = [
        $field => $login,
        'password' => $request->password
    ];

    if (
        Auth::attempt(
            $credentials,
            $request->remember
        )
    ) {

        $request
            ->session()
            ->regenerate();

        return redirect()
            ->route('dashboard');
    }

    return back()
        ->withErrors([
            'login' =>
                'Invalid username/email or password.'
        ])
        ->onlyInput('login');
}

    public function logout(
        Request $request
    )
    {
        Auth::logout();

        $request
            ->session()
            ->invalidate();

        $request
            ->session()
            ->regenerateToken();

        return redirect()
            ->route(
                'login'
            );
    }

    public function showForgotPassword()
{
    return view(
        'auth.forgot-password'
    );
}

public function resetForgotPassword(
    Request $request
)
{
    $request->validate([

        'email' =>
            'required|email',

        'password' =>
            'required|min:8|confirmed'

    ]);

    $user =
        Users::where(
            'email',
            $request->email
        )->first();

    if (!$user) {

        return back()->withErrors([

            'email' =>
                'Email tidak terdaftar.'

        ]);
    }

    $user->update([

        'password' =>
            Hash::make(
                $request->password
            )

    ]);

    return redirect()
        ->route('login')
        ->with(
            'success',
            'Password berhasil diperbarui.'
        );
}
}