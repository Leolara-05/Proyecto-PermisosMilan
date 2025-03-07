<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;

class PersonaController extends Controller
{
    public function buscarDatos($cedula)
    {
        try {
            $empleado = DB::connection('sqlsrv')
                ->table('HV_PERSONA')
                ->where('NUMERODOCUMENTO', $cedula)
                ->first();
    
            if (!$empleado) {
                return response()->json(['error' => 'Empleado no encontrado'], 404);
            }
    
            // Construir el nombre completo
            $nombre = trim($empleado->PRIMERNOMBRE . ' ' . 
                          ($empleado->SEGUNDONOMBRE ? $empleado->SEGUNDONOMBRE . ' ' : '') . 
                          $empleado->PRIMERAPELLIDO . ' ' . 
                          ($empleado->SEGUNDOAPELLIDO ? $empleado->SEGUNDOAPELLIDO : ''));
            
            return response()->json([
                'nombre' => $nombre,
                'cargo' => 'DESARROLLADOR', // Este campo no está en los datos, ajustar según necesidad
                'correo_electronico' => $empleado->EMAIL,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al buscar empleado: ' . $e->getMessage()], 500);
        }
    }

    public function buscarCorreo(Request $request)
    {
        $cedula = $request->query('cedula');
        $persona = Persona::where('NUMERODOCUMENTO', $cedula)->first();

        if ($persona) {
            return response()->json(['correo' => $persona->EMAIL]);
        }

        return response()->json(['correo' => null], 404);
    }
}
