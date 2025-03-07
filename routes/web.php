<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PermisoMilanController;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\UsuarioController;
use App\Mail\NotificacionPrueba;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\HomeController;

// Redirige a la página de login por defecto
Route::get('/', function () {
    return redirect()->route('login');
});

// Ruta de logout (debe estar fuera del middleware para evitar problemas de sesión)
Route::post('/logout', function (Illuminate\Http\Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

// Middleware de autenticación
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Redirección automática después del login
    Route::get('/dashboard', [HomeController::class, 'index'])
        ->name('dashboard');

    // Rutas para gestión de usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/create', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');

    // Grupo de rutas protegidas para permisos Milan
    Route::prefix('permisosmilan')->name('permisosmilan.')->group(function () {
        Route::get('/', [PermisoMilanController::class, 'index'])->name('index');
        Route::get('/create', [PermisoMilanController::class, 'create'])->name('create');
        Route::post('/{permiso}/autorizar', [PermisoMilanController::class, 'autorizar'])->name('autorizar');
        Route::post('/{permiso}/rechazar', [PermisoMilanController::class, 'rechazar'])->name('rechazar');
        Route::post('/', [PermisoMilanController::class, 'store'])->name('store');
        Route::get('/{id}', [PermisoMilanController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [PermisoMilanController::class, 'edit'])->name('edit');
        Route::put('/{id}', [PermisoMilanController::class, 'update'])->name('update');
        Route::delete('/{id}', [PermisoMilanController::class, 'destroy'])->name('destroy');
        // Ruta de descarga corregida
        Route::get('/{id}/descargar', [PermisoMilanController::class, 'descargar'])->name('descargar');
        // Corregir esta ruta
        Route::post('/filtrar-mes', [PermisoMilanController::class, 'filtrarMes'])->name('filtrarMes');
        // Ruta para ver todos los permisos (movida dentro del grupo)
        Route::get('/ver-todos', [PermisoMilanController::class, 'verTodos'])->name('verTodos');
    });

    // Rutas para búsqueda de usuario y permisos
    Route::get('/buscar-usuario/{cedula}', [PermisoMilanController::class, 'buscarUsuario'])->name('buscar.usuario');
    Route::post('/api/permisos', [PermisoMilanController::class, 'guardarPermiso'])->name('guardar.permiso');
    Route::get('/test-connection', [PermisoMilanController::class, 'testConnection'])->name('test.connection');

    // Ruta para buscar datos de una persona por cédula
    Route::get('/buscar-persona', [PermisoMilanController::class, 'buscarEmpleadoNomina'])->name('buscar.persona');

    // Verificación de conexión a la base de datos
    Route::get('/verificar-conexion', function () {
        try {
            DB::connection('sqlsrv_interno')->getPdo();
            return response()->json(['message' => '✅ Conexión a SQL Server establecida correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => '❌ Error de conexión: ' . $e->getMessage()], 500);
        }
    })->name('verificar.conexion');
    
    // Rutas para gestión de usuarios, roles y permisos
    Route::resource('users', UserController::class);
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    
    // Ruta para generar informe
    Route::post('/permisosmilan/generar-informe', [PermisoMilanController::class, 'generarInforme'])
        ->name('permisosmilan.generarInforme');
});

// Rutas de PersonaController
Route::get('/buscar-correo', [PersonaController::class, 'buscarCorreo'])->name('buscar.correo');
Route::get('/buscar-datos/{cedula}', [PersonaController::class, 'buscarDatos'])->name('buscar.datos');

// Prueba de envío de correo
Route::get('/enviar-correo', function () {
    Mail::to('leolar_0515@hotmail.com')->send(new NotificacionPrueba());
    return 'Correo enviado con éxito';
})->name('enviar.correo');

// Ruta temporal para verificar roles (eliminar después de usar)
Route::get('/verificar-roles', function() {
    try {
        $roles = DB::connection('sqlsrv')->table('ROLES_SISTEMA')->get();
        $usuarios = DB::connection('sqlsrv')
            ->table('USUARIOS_ROLES as ur')
            ->leftJoin('ROLES_SISTEMA as rs', 'ur.ID_ROL', '=', 'rs.ID')
            ->select('ur.EMAIL', 'rs.NOMBRE_ROL')
            ->get();
        
        return [
            'roles' => $roles,
            'usuarios' => $usuarios
        ];
    } catch (\Exception $e) {
        return ['error' => $e->getMessage()];
    }
});
