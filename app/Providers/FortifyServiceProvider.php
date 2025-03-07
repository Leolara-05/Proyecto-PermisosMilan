<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // Personalizar la autenticación para verificar roles
        Fortify::authenticateUsing(function (Request $request) {
            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Verificar si el usuario es administrador - corregido para usar la estructura correcta de la tabla
                $isAdmin = false;
                
                try {
                    // Intentar consultar con la estructura correcta de la tabla
                    $userRole = DB::connection('sqlsrv')
                        ->table('USUARIOS_ROLES')
                        ->where('EMAIL', $user->email)
                        ->join('ROLES_SISTEMA', 'USUARIOS_ROLES.ID_ROL', '=', 'ROLES_SISTEMA.ID')
                        ->select('ROLES_SISTEMA.NOMBRE_ROL')
                        ->first();
                    
                    // Determinar el rol y la redirección según el rol
                    if ($userRole) {
                        $rolNombre = $userRole->NOMBRE_ROL;
                        
                        if ($rolNombre === 'SISTEMAS' || $rolNombre === 'ADMINTALENTO') {
                            // Usuarios de SISTEMAS o ADMINTALENTO van a la lista de permisos
                            session(['user_role' => 'administrador']);
                            // No usar url.intended para evitar problemas con CSRF
                            session(['redirect_to' => '/permisosmilan']);
                        } else {
                            // Usuarios normales van al formulario de creación
                            session(['user_role' => 'usuario']);
                            session(['redirect_to' => '/permisosmilan/create']);
                        }
                    } else {
                        // Si no tiene rol asignado, se trata como usuario normal
                        session(['user_role' => 'usuario']);
                        session(['redirect_to' => '/permisosmilan/create']);
                    }
                } catch (\Exception $e) {
                    \Log::error('Error al verificar rol: ' . $e->getMessage());
                    // En caso de error, redirigir al formulario de creación por defecto
                    session(['user_role' => 'usuario']);
                    session(['redirect_to' => '/permisosmilan/create']);
                }
                
                // Ya no necesitamos estas líneas porque la redirección ya se estableció arriba
                // if ($isAdmin) {
                //     session(['url.intended' => '/permisosmilan']);
                // } else {
                //     session(['url.intended' => '/permisosmilan/create']);
                // }
                
                return $user;
            }
        });

        // Configurar la vista de login
        Fortify::loginView(function () {
            return view('auth.login');
        });

        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });
    }
}
