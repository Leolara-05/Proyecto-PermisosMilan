<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        // Verificar si el usuario está autenticado
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $user = Auth::user();
        Log::info('Usuario logueado: ' . $user->email);
        
        try {
            // Consultar el rol del usuario en la base de datos
            $userRole = DB::connection('sqlsrv')
                ->table('USUARIOS_ROLES as ur')
                ->where('ur.EMAIL', $user->email)
                ->join('ROLES_SISTEMA as rs', 'ur.ID_ROL', '=', 'rs.ID')
                ->select('rs.NOMBRE_ROL')
                ->first();
            
            // Verificar el rol y redirigir según corresponda
            if ($userRole && ($userRole->NOMBRE_ROL === 'SISTEMAS' || $userRole->NOMBRE_ROL === 'ADMINTALENTO')) {
                Log::info('Redirigiendo a lista de permisos: ' . $user->email . ' con rol: ' . $userRole->NOMBRE_ROL);
                return redirect('/permisosmilan');
            } else {
                Log::info('Redirigiendo a creación de permisos: ' . $user->email);
                return redirect('/permisosmilan/create');
            }
        } catch (\Exception $e) {
            Log::error('Error al verificar rol: ' . $e->getMessage());
            // En caso de error, redirigir al formulario de creación por defecto
            return redirect('/permisosmilan/create');
        }
    }
}