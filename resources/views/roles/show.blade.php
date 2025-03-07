@extends('layouts.crud')

@section('title', 'Detalles del Rol')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-user-shield me-2"></i>Detalles del Rol</h3>
                    <div>
                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    <i class="fas fa-user-shield fa-2x"></i>
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $role->name }}</h4>
                                    <div class="text-muted">
                                        <span class="badge bg-secondary">Guard: {{ $role->guard_name }}</span>
                                        <span class="badge bg-info ms-2">{{ count($rolePermissions) }} permisos</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-key me-2"></i>Permisos Asignados</h5>
                            <div class="card">
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <div class="row">
                                        @forelse($permissions as $permission)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                        id="permission-{{ $permission->id }}"
                                                        {{ in_array($permission->name, $rolePermissions) ? 'checked' : '' }}
                                                        disabled>
                                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center py-3">
                                                <i class="fas fa-exclamation-circle text-muted mb-2"></i>
                                                <p class="mb-0">No hay permisos disponibles</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">ID: {{ $role->id }}</small>
                        <div>
                            <small class="text-muted me-3">Creado: {{ $role->created_at->format('d/m/Y H:i') }}</small>
                            <small class="text-muted">Actualizado: {{ $role->updated_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($role->name !== 'Super Admin')
                <div class="mt-4 text-center">
                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este rol? Esta acción no se puede deshacer.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>Eliminar Rol
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection 