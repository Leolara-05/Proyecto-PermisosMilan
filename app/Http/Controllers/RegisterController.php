<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'cedula' => 'required'
        ]);

        // Crear usuario
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'NUMERODOCUMENTO' => $request->cedula
        ]);

        // Asignar rol de usuario
        DB::connection('sqlsrv')->table('USUARIOS_ROLES')->insert([
            'EMAIL' => $request->email,
            'ID_ROL' => 2 // ID del rol USUARIO
        ]);

        return redirect()->route('login')->with('success', 'Registro exitoso. Por favor inicia sesión.');
    }
}