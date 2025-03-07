<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HistorialPermiso;
use Illuminate\Support\Facades\Log; // Importar Log

class HistorialPermisoController extends Controller {
    public function store(Request $request) {
        Log::info('Datos recibidos en store:', $request->all()); // Registra los datos en laravel.log

        $request->validate([
            'cedula' => 'required|string|max:20',
            'nombre_completo' => 'required|string|max:255',
            'correo' => 'required|email|max:255',
            'tipo_permiso' => 'required|string|max:100',
            'fecha_solicitud' => 'required|date',
            'fecha_permiso' => 'required|date',
        ]);

        $permiso = HistorialPermiso::create($request->all());

        if ($permiso) {
            return response()->json(['message' => '✅ Permiso guardado correctamente'], 201);
        } else {
            return response()->json(['error' => '❌ Error al guardar el permiso'], 500);
        }
    }
}
