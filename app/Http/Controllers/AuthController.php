<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'username.required' => 'O nome de usuário é obrigatório.',
            'password.required' => 'A senha é obrigatória.'
        ]);

        // Autentica usando a coluna 'name' do banco como username
        if (Auth::attempt(['name' => $credentials['username'], 'password' => $credentials['password']])) {
            $request->session()->regenerate();
            return redirect()->intended('/ocorrencias');
        }

        return back()->withErrors([
            'username' => 'As credenciais informadas estão incorretas.',
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
