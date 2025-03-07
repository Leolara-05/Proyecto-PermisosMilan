<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Manejar una solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (Auth::check()) {
            if (Auth::user()->role == $role) {
                return $next($request);
            }
            return redirect()->route('permisosmilan.create'); // Redirigir a la forma de permisos
        }

        return redirect()->route('login'); // Redirigir a la página de login si no está autenticado
    }
}
