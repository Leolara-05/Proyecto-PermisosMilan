<?php

namespace App\Http\Controllers;

use App\Models\PermisoMilan;
use App\Models\Persona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\PermisoAutorizadoMail;
use App\Mail\PermisoRechazadoMail;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class PermisoMilanController extends Controller
{
    public function index(Request $request)
    {
        // Obtener la fecha seleccionada o usar la actual
        $fechaSeleccionada = session('fecha_seleccionada', now()->format('Y-m'));
        $fecha = Carbon::createFromFormat('Y-m', $fechaSeleccionada)->startOfMonth();
        
        // Iniciar la consulta base
        $query = PermisoMilan::leftJoin('archivos_permisos', 'permisosmilan.id', '=', 'archivos_permisos.permiso_id')
            ->select('permisosmilan.*', 'archivos_permisos.ruta_archivo', 'archivos_permisos.nombre_archivo');
    
        // Filtrar por mes seleccionado
        $query->whereMonth('permisosmilan.created_at', $fecha->month)
              ->whereYear('permisosmilan.created_at', $fecha->year);
    
        // Búsqueda
        if ($request->has('buscar') && !empty($request->buscar)) {
            $search = $request->buscar;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'LIKE', "%{$search}%")
                  ->orWhere('cedula', 'LIKE', "%{$search}%")
                  ->orWhere('cargo', 'LIKE', "%{$search}%");
            });
        }
    
        // Paginación
        $permisos = $query->paginate(15);
        
        // Usar directamente los valores de sesión o valores predeterminados
        $mesActual = now()->locale('es')->translatedFormat('F Y');
        $mesSeleccionado = session('mes_seleccionado', $mesActual);
        $permisosDelMes = session('conteo_mes', 0);
        
        return view('permisosmilan.index', compact('permisos', 'mesSeleccionado', 'permisosDelMes', 'fechaSeleccionada'));
    }

    public function create()
    {
        return view('permisosmilan.create');
    }

    public function buscarUsuario($cedula)
    {
        try {
            // Buscar a la persona en la tabla Persona
            $persona = Persona::where('NUMERODOCUMENTO', $cedula)->first();
    
            if ($persona) {
                try {
                    // Registrar el IDCARGO de la persona para depuración
                    \Log::info('IDCARGO de la persona:', ['idcargo' => $persona->IDCARGO]);
    
                    // Consultar el cargo desde la tabla PA_CARGOCOMPANIA
                    $cargo = DB::connection('sqlsrv')
                        ->table('PA_CARGOCOMPANIA')
                        ->where('ID', $persona->IDCARGO)
                        ->first();
    
                    // Registrar los datos del cargo para depuración
                    \Log::info('Datos del cargo:', ['cargo' => $cargo]);
    
                    // Formatear el nombre completo
                    $nombreCompleto = trim("{$persona->PRIMERNOMBRE} {$persona->SEGUNDONOMBRE} {$persona->PRIMERAPELLIDO} {$persona->SEGUNDOAPELLIDO}");
    
                    // Retornar los datos en formato JSON
                    return response()->json([
                        'cedula' => $persona->NUMERODOCUMENTO,
                        'nombre' => $nombreCompleto,
                        'cargo' => $cargo ? $cargo->NOMBRE : 'Cargo no encontrado',
                        'email' => $persona->EMAIL,
                        'firma_trabajador' => $nombreCompleto,
                        'correo_electronico' => $persona->EMAIL
                    ]);
                } catch (\Exception $e) {
                    // Registrar el error al consultar el cargo
                    \Log::error('Error SQL Server: ' . $e->getMessage());
                    return response()->json(['error' => 'Error al consultar el cargo: ' . $e->getMessage()], 500);
                }
            }
    
            // Si no se encuentra la persona, retornar un error 404
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        } catch (\Exception $e) {
            // Registrar cualquier error general
            \Log::error('Error general: ' . $e->getMessage());
            return response()->json(['error' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        // Validación y guardado del permiso
        $maxFechaAutorizacion = now()->subDays(3)->toDateString();
    
        $validated = $request->validate([
            'cedula'             => 'required|string|max:20',
            'nombre'             => 'required|string|max:100',
            'cargo'              => 'required|string|max:100',
            'fecha_autorizacion' => ['required', 'date', "after_or_equal:$maxFechaAutorizacion"],
            'desde_display'      => 'required|date',
            'hasta_display'      => 'required|date',
            'motivo_permiso'     => 'required|string',
            'descontable'        => 'required|in:0,1',
            'observaciones'      => 'nullable|string',
            'documento'          => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            'firma_trabajador'   => 'nullable|string|max:255',
            'correo_electronico' => 'required|email',
        ]);
    
        // ✅ Convertir fechas al formato correcto para la base de datos
        $validated['desde'] = Carbon::parse($request->desde_display)->format('Y-m-d H:i:s');
        $validated['hasta'] = Carbon::parse($request->hasta_display)->format('Y-m-d H:i:s');
        unset($validated['desde_display'], $validated['hasta_display']);
    
        // ✅ Convertir 'descontable' a booleano
        $validated['descontable'] = filter_var($request->descontable, FILTER_VALIDATE_BOOLEAN);
    
        // ✅ Crear el permiso (sin el documento)
        $permiso = PermisoMilan::create($validated);
    
        // ✅ Manejo de archivos
        if ($request->hasFile('documento')) {
            $archivo = $request->file('documento');
            $nombreArchivo = time() . '_' . $archivo->getClientOriginalName();
            
            // Guardar el archivo en storage/app/public/permisos
            $rutaArchivo = $archivo->storeAs('permisos', $nombreArchivo, 'public');
    
            // Guardar la información del archivo en la base de datos
            DB::table('archivos_permisos')->insert([
                'nombre_archivo' => $nombreArchivo,
                'ruta_archivo' => $rutaArchivo,
                'permiso_id' => $permiso->id,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    
        return redirect()->route('permisosmilan.index')
            ->with('success', 'Permiso creado exitosamente.');
    }

    public function autorizar(PermisoMilan $permiso)
    {
        $permiso->update(['autorizado' => true]);

        // Generar PDF
        $pdf = Pdf::loadView('emails.permiso_autorizado', compact('permiso'));

        // Enviar correo
        Mail::send([], [], function ($message) use ($permiso, $pdf) {
            $message->from('notificaciones@bicicletasmilan.com')
                    ->to('sistemas@bicicletasmilan.com')
                    ->subject('Permiso Autorizado')
                    ->attachData($pdf->output(), 'permiso_autorizado.pdf', [
                        'mime' => 'application/pdf',
                    ]);
        });

        return redirect()->route('permisosmilan.index')->with('success', 'Permiso autorizado y notificación enviada.');
    }

    public function rechazar(PermisoMilan $permiso, Request $request)
    {
        $request->validate([
            'observaciones_rechazo' => 'nullable|string|max:300',
        ]);

        $permiso->update([
            'autorizado' => false,
            'observaciones' => $request->input('observaciones_rechazo'),
        ]);

        // Generar PDF
        $pdf = Pdf::loadView('emails.permiso_rechazado', compact('permiso'));

        // Enviar correo
        Mail::send([], [], function ($message) use ($permiso, $pdf) {
            $message->from('notificaciones@bicicletasmilan.com')
                    ->to('sistemas@bicicletasmilan.com')
                    ->subject('Permiso Rechazado')
                    ->attachData($pdf->output(), 'permiso_rechazado.pdf', [
                        'mime' => 'application/pdf',
                    ]);
        });

        return redirect()->route('permisosmilan.index')->with('success', 'Permiso rechazado y notificación enviada.');
    }

    // En el método generarInforme, modifica la configuración del PDF
    public function generarInforme(Request $request)
    {
        $request->validate([
            'fecha_desde' => 'required|date',
            'fecha_hasta' => 'required|date|after_or_equal:fecha_desde'
        ]);

        $permisos = PermisoMilan::whereBetween('fecha_autorizacion', [
            $request->fecha_desde,
            $request->fecha_hasta
        ])->get();

        $pdf = PDF::loadView('permisosmilan.informe', [
            'permisos' => $permisos,
            'fechaDesde' => $request->fecha_desde,
            'fechaHasta' => $request->fecha_hasta
        ])->setPaper('a4', 'landscape');
    
        return $pdf->download('informe-permisos.pdf');
    }

    public function descargar($id)
    {
        // Obtener el permiso con su archivo adjunto
        $permiso = PermisoMilan::leftJoin('archivos_permisos', 'permisosmilan.id', '=', 'archivos_permisos.permiso_id')
            ->select('permisosmilan.*', 'archivos_permisos.ruta_archivo', 'archivos_permisos.nombre_archivo')
            ->where('permisosmilan.id', $id)
            ->first();
        
        if (!$permiso || !$permiso->ruta_archivo) {
            return back()->with('error', 'No se encontró el archivo adjunto.');
        }

        $path = storage_path('app/public/' . $permiso->ruta_archivo);
        
        if (!file_exists($path)) {
            return back()->with('error', 'El archivo no existe en el sistema.');
        }

        return response()->download($path, $permiso->nombre_archivo);
    }
    
    /**
     * Filtra los permisos por el mes seleccionado
     */
    public function filtrarMes(Request $request)
    {
        try {
            // Asegurarnos de que tenemos una instancia de Carbon
            $fecha = $request->mes_seleccionado ? Carbon::createFromFormat('Y-m', $request->mes_seleccionado) : now();
            
            // Guardar la fecha seleccionada en formato Y-m para el input month
            session(['fecha_seleccionada' => $fecha->format('Y-m')]);
            
            // Configurar locale para español y obtener el nombre del mes
            $fecha->locale('es');
            $nombreMes = $fecha->translatedFormat('F Y');
            
            // Obtener permisos del mes seleccionado
            $permisosDelMes = PermisoMilan::whereMonth('created_at', $fecha->month)
                                ->whereYear('created_at', $fecha->year)
                                ->count();
            
            // Guardar en sesión como strings
            session(['mes_seleccionado' => $nombreMes]);
            session(['conteo_mes' => $permisosDelMes]);
            
            return redirect()->route('permisosmilan.index')->with('success', 'Filtrado por: ' . $nombreMes);
        } catch (\Exception $e) {
            // En caso de cualquier error, usar valores predeterminados
            session(['fecha_seleccionada' => now()->format('Y-m')]);
            session(['mes_seleccionado' => now()->locale('es')->translatedFormat('F Y')]);
            session(['conteo_mes' => 0]);
            
            return redirect()->route('permisosmilan.index')
                ->with('error', 'Error al filtrar por mes: ' . $e->getMessage());
        }
    }
    
    /**
     * Muestra todos los permisos sin filtrar por mes
     */
    public function verTodos()
    {
        // Limpiar los filtros de sesión
        session()->forget(['fecha_seleccionada', 'mes_seleccionado', 'conteo_mes']);
        
        // Iniciar la consulta base sin filtro de mes
        $query = PermisoMilan::leftJoin('archivos_permisos', 'permisosmilan.id', '=', 'archivos_permisos.permiso_id')
            ->select('permisosmilan.*', 'archivos_permisos.ruta_archivo', 'archivos_permisos.nombre_archivo');
        
        // Paginación
        $permisos = $query->paginate(15);
        
        // Valores para la vista
        $mesSeleccionado = 'Todos los permisos';
        $permisosDelMes = $permisos->total();
        $fechaSeleccionada = now()->format('Y-m');
        
        return view('permisosmilan.index', compact('permisos', 'mesSeleccionado', 'permisosDelMes', 'fechaSeleccionada'))
            ->with('success', 'Mostrando todos los permisos');
    }
    
    /**
     * Muestra los detalles de un permiso específico
     */
    public function show($id)
    {
        // Obtener el permiso con su archivo adjunto
        $permiso = PermisoMilan::leftJoin('archivos_permisos', 'permisosmilan.id', '=', 'archivos_permisos.permiso_id')
            ->select('permisosmilan.*', 'archivos_permisos.ruta_archivo', 'archivos_permisos.nombre_archivo')
            ->where('permisosmilan.id', $id)
            ->first();
        
        if (!$permiso) {
            return back()->with('error', 'Permiso no encontrado.');
        }

        return view('permisosmilan.show', compact('permiso'));
    }

    public function buscarEmpleadoNomina($cedula)
        {
            try {
                // Buscar en la tabla HV_PERSONA usando el campo NUMERODOCUMENTO
                $empleado = DB::connection('sqlsrv')
                    ->table('HV_PERSONA')
                    ->select('NUMERODOCUMENTO', 'PRIMERNOMBRE', 'SEGUNDONOMBRE', 'PRIMERAPELLIDO', 'SEGUNDOAPELLIDO', 'IDSEXO', 'EMAIL', 'IDCARGO')
                    ->where('NUMERODOCUMENTO', $cedula)
                    ->first();
    
                if (!$empleado) {
                    return response()->json(['error' => 'Empleado no encontrado'], 404);
                }
    
                // Obtener el cargo desde PA_CARGOCOMPANIA
                $cargo = DB::connection('sqlsrv')
                    ->table('PA_CARGOCOMPANIA')
                    ->where('ID', $empleado->IDCARGO)
                    ->first();
    
                // Formatear el nombre completo
                $nombreCompleto = trim(
                    $empleado->PRIMERNOMBRE . ' ' . 
                    $empleado->SEGUNDONOMBRE . ' ' . 
                    $empleado->PRIMERAPELLIDO . ' ' . 
                    $empleado->SEGUNDOAPELLIDO
                );
    
                return response()->json([
                    'cedula' => $empleado->NUMERODOCUMENTO,
                    'nombre' => $nombreCompleto,
                    'cargo' => $cargo ? $cargo->NOMBRE : 'Sin cargo asignado',
                    'email' => $empleado->EMAIL ?? '',
                    'firma_trabajador' => $nombreCompleto,
                    'correo_electronico' => $empleado->EMAIL ?? ''
                ]);
                
            } catch (\Exception $e) {
                \Log::error('Error en buscarEmpleadoNomina: ' . $e->getMessage());
                return response()->json(['error' => 'Error al buscar empleado: ' . $e->getMessage()], 500);
            }
        }
} // fin de la clase
