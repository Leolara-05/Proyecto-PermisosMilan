@extends('layouts.main')

@section('content')
<div class="container">
    <h1>Detalles del Permiso</h1>
    <p><strong>Cédula:</strong> {{ $permisoMilan->cedula }}</p>
    <p><strong>Nombre:</strong> {{ $permisoMilan->nombre }}</p>
    <!-- Repite para los demás campos -->
    <a href="{{ route('permisosmilan.index') }}" class="btn btn-secondary">Volver</a>
</div>
@endsection