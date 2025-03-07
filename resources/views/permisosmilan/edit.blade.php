@extends('layouts.main')

@section('content')
<div class="container">
    <h1>Editar Permiso</h1>
    <form action="{{ route('permisosmilan.update', $permisoMilan->id) }}" method="POST">
        @csrf
        @method('PUT')
        <!-- Campos del formulario -->
        <div class="form-group">
            <label for="cedula">Cédula</label>
            <input type="text" name="cedula" class="form-control" value="{{ $permisoMilan->cedula }}" required>
        </div>
        <!-- Repite para los demás campos -->
        <button type="submit" class="btn btn-primary">Actualizar</button>
    </form>
</div>
@endsection