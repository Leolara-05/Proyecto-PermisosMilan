<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class UsuarioController extends Controller
{
    public function index()
    {
        if (session('user_role') !== 'administrador') {
            return redirect()->route('permisosmilan.create')
                ->with('error', 'No tienes permisos para acceder a esta página');
        }
        
        $users = User::all();
        return view('usuarios.index', compact('users'));
    }

    public function create()
    {
        if (session('user_role') !== 'administrador') {
            return redirect()->route('permisosmilan.create')
                ->with('error', 'No tienes permisos para acceder a esta página');
        }
        
        return view('usuarios.create');
    }

    public function store(Request $request)
    {
        if (session('user_role') !== 'administrador') {
            return redirect()->route('permisosmilan')
                ->with('error', 'No tienes permisos para acceder a esta página');
        }

        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:administrador,usuario',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'email_verified_at' => now(),
            ]);

            // Guardar el rol en una tabla o en la sesión
            DB::table('user_roles')->insert([
                'user_id' => $user->id,
                'role' => $validated['role'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            Log::info('Usuario creado: ' . $user->email . ' con rol: ' . $validated['role']);
            
            return redirect()->route('usuarios.index')
                ->with('success', 'Usuario creado correctamente');
        } catch (\Exception $e) {
            Log::error('Error al crear usuario: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Error al crear usuario: ' . $e->getMessage()])->withInput();
        }
    }

    public function buscarUsuario($cedula)
    {
        $usuario = DB::connection('sqlsrv')->table('HV_PERSONA')
            ->where('CEDULA', $cedula)
            ->select('NOMBRE as nombre', 'EMAIL as correo_electronico')
            ->first();

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        return response()->json($usuario);
    }
}
