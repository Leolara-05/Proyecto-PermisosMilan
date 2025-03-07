@extends('layouts.crud')

@section('title', 'Detalles del Usuario')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-user me-2"></i>Detalles del Usuario</h3>
                    <div>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit me-1"></i>Editar
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar bg-primary text-white rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="mb-1">{{ $user->name }}</h4>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-envelope me-1"></i>{{ $user->email }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h5 class="border-bottom pb-2 mb-3"><i class="fas fa-user-tag me-2"></i>Roles Asignados</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        @forelse($roles as $role)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                        id="role-{{ $role->id }}"
                                                        {{ in_array($role->id, $userRoles) ? 'checked' : '' }}
                                                        disabled>
                                                    <label class="form-check-label" for="role-{{ $role->id }}">
                                                        {{ $role->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center py-3">
                                                <i class="fas fa-exclamation-circle text-muted mb-2"></i>
                                                <p class="mb-0">No hay roles disponibles</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-3">
                                <div class="card-header bg-light">
                                    <i class="fas fa-calendar me-1"></i>Información de Registro
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Creado:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
                                    <p class="mb-0"><strong>Actualizado:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <i class="fas fa-shield-alt me-1"></i>Seguridad
                                </div>
                                <div class="card-body">
                                    <p class="mb-1"><strong>Email verificado:</strong> 
                                        @if($user->email_verified_at)
                                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Sí</span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-times-circle me-1"></i>No</span>
                                        @endif
                                    </p>
                                    <p class="mb-0"><strong>2FA:</strong> 
                                        @if($user->two_factor_secret)
                                            <span class="text-success"><i class="fas fa-check-circle me-1"></i>Activado</span>
                                        @else
                                            <span class="text-danger"><i class="fas fa-times-circle me-1"></i>Desactivado</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">ID: {{ $user->id }}</small>
                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="fas fa-trash me-1"></i>Eliminar Usuario
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection