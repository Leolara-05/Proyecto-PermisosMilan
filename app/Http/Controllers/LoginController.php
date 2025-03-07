<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\User;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        // Verificación específica para el administrador
        if ($credentials['email'] === 'talentohumanonacional@bicicletasmilan.com' && 
            $credentials['password'] === 'Milan2025*') {
            
            // Autenticar manualmente al usuario
            $user = User::where('email', $credentials['email'])->first();
            if ($user) {
                Auth::login($user);
            }
            
            return redirect('/permisosmilan');
        }
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect('/permisosmilan');
        }
    
        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}