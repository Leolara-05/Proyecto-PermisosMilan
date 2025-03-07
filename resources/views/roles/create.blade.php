@extends('layouts.crud')

@section('title', 'Crear Rol')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-user-shield me-2"></i>Crear Nuevo Rol</h3>
                    <a href="{{ route('roles.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Nombre del Rol</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-tag"></i></span>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                    id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Permisos</label>
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <span>Selecciona los permisos para este rol</span>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="select-all">Seleccionar Todos</button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselect-all">Deseleccionar Todos</button>
                                    </div>
                                </div>
                                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                                    <div class="row">
                                        @foreach($permissions as $permission)
                                            <div class="col-md-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox" 
                                                        name="permissions[]" 
                                                        value="{{ $permission->id }}" 
                                                        id="permission-{{ $permission->id }}"
                                                        {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @error('permissions')
                                    <div class="card-footer text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Guardar Rol
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Seleccionar todos los permisos
        document.getElementById('select-all').addEventListener('click', function() {
            document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                checkbox.checked = true;
            });
        });
        
        // Deseleccionar todos los permisos
        document.getElementById('deselect-all').addEventListener('click', function() {
            document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                checkbox.checked = false;
            });
        });
    });
</script>
@endpush
@endsection