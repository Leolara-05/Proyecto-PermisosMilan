<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener todos los roles con el guard web
        $roles = Role::where('guard_name', 'web')->get();
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Obtener todos los permisos con el guard web
        $permissions = Permission::where('guard_name', 'web')->get();
        return view('roles.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);
        
        // Obtener las instancias de Permission basadas en los IDs recibidos
        $permissions = Permission::whereIn('id', $request->permissions)
                                ->where('guard_name', 'web')
                                ->get();
        
        // Sincronizar los permisos con el rol
        $role->syncPermissions($permissions);

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Verificar que el rol use el guard web
        if ($role->guard_name !== 'web') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol no pertenece al guard web.');
        }
        
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        $permissions = Permission::where('guard_name', 'web')->get();
        
        return view('roles.show', compact('role', 'rolePermissions', 'permissions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Verificar que el rol use el guard web
        if ($role->guard_name !== 'web') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol no pertenece al guard web.');
        }
        
        $permissions = Permission::where('guard_name', 'web')->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();
        
        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:roles,name,'.$id,
            'permissions' => 'required|array',
        ]);
        
        $role = Role::findOrFail($id);
        
        // Verificar que el rol use el guard web
        if ($role->guard_name !== 'web') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol no pertenece al guard web.');
        }
        
        $role->name = $request->name;
        $role->save();
        
        // Obtener las instancias de Permission basadas en los IDs recibidos
        // y asegurarse de que usen el guard web
        $permissions = Permission::whereIn('id', $request->permissions)
                                ->where('guard_name', 'web')
                                ->get();
        
        // Sincronizar los permisos con el rol
        $role->syncPermissions($permissions);
        
        return redirect()->route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::findOrFail($id);
        
        // Verificar que el rol use el guard web
        if ($role->guard_name !== 'web') {
            return redirect()->route('roles.index')
                ->with('error', 'El rol no pertenece al guard web.');
        }
        
        if($role->name === 'Super Admin') {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar el rol de Super Admin.');
        }
        
        // Eliminar todas las asignaciones de permisos antes de eliminar el rol
        DB::table('role_has_permissions')->where('role_id', $id)->delete();
        
        $role->delete();
        
        return redirect()->route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}