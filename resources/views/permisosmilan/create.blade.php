@extends('layouts.main')

@section('styles')
<style>
    .form-container {
        position: relative;
        z-index: 1;
    }
    
    .form-container::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: url('{{ asset('img/logo-milan.png') }}');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 50%;
        opacity: 0.05;
        z-index: -1;
        pointer-events: none;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <!-- Logo centrado -->
    <div class="text-center mb-4">
        <img src="{{ asset('/images/Logo_Milan.png') }}" alt="Logo Milan" style="max-width: 300px;">
    </div>

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Crear Nuevo Permiso</h4>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Agregar esto justo después del inicio del formulario -->
            <div id="mensaje-busqueda" style="display: none;" class="alert"></div>
            <form action="{{ route('permisosmilan.store') }}" method="POST" class="row g-3" enctype="multipart/form-data" id="permisoForm">
                @csrf
                <!-- Información del Empleado -->
                <div class="col-md-4">
                    <label for="cedula" class="form-label">Cédula</label>
                    <input type="text" id="cedula" name="cedula" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label for="nombre" class="form-label">Nombre Completo</label>
                    <input type="text" id="nombre" name="nombre" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label for="cargo" class="form-label">Cargo</label>
                    <input type="text" id="cargo" name="cargo" class="form-control" required>
                </div>

                <!-- Fechas -->
                <div class="col-md-4">
                    <label for="fecha_autorizacion" class="form-label">Fecha de Autorización (2 días antes)</label>
                    <input type="date" name="fecha_autorizacion" class="form-control"
                           min="{{ now()->subDays(2)->format('Y-m-d') }}"
                           value="{{ old('fecha_autorizacion', now()->subDays(2)->format('Y-m-d') ) }}" required>
                </div>

                <div class="col-md-4">
                    <label for="desde_display" class="form-label">Fecha y Hora Inicio</label>
                    <input type="datetime-local" name="desde_display" class="form-control"
                           value="{{ old('desde_display') }}" required>
                </div>

                <div class="col-md-4">
                    <label for="hasta_display" class="form-label">Fecha y Hora Fin</label>
                    <input type="datetime-local" name="hasta_display" class="form-control"
                           value="{{ old('hasta_display') }}" required>
                </div>

                <!-- Detalles del Permiso -->
                <div class="col-md-4">
                    <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                    <input type="email" id="correo_electronico" name="correo_electronico" class="form-control" value="{{ old('correo_electronico') }}" required>
                </div>

                <div class="col-md-4">
                    <label for="motivo_permiso" class="form-label">Motivo de Permiso</label>
                    <select id="motivo_permiso" name="motivo_permiso" class="form-select" required>
                        <option value="" disabled selected>Seleccione un motivo</option>
                        <option value="Cita médica">Cita médica</option>
                        <option value="Cita odontológica">Cita odontológica</option>
                        <option value="Evento de la empresa">Evento de la empresa</option>
                        <option value="Licencia remunerada">Licencia remunerada</option>
                        <option value="Covid">Covid</option>
                        <option value="Permiso por estudio">Permiso por estudio</option>
                        <option value="Calamidad doméstica">Calamidad doméstica</option>
                        <option value="Licencia de luto">Licencia de luto</option>
                        <option value="Lactancia">Lactancia</option>
                        <option value="Otros">Otros</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="descontable" class="form-label">¿Descontable?</label>
                    <select id="descontable" name="descontable" class="form-select" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="1" {{ old('descontable') == '1' ? 'selected' : '' }}>Sí</option>
                        <option value="0" {{ old('descontable') == '0' ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <div class="col-12">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones') }}</textarea>
                </div>

                <div class="col-md-6">
                    <label for="documento" class="form-label">Documento de Respaldo</label>
                    <input type="file" name="documento" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <small class="text-muted">Formatos permitidos: PDF, DOC, DOCX, JPG, PNG. Máximo 2MB</small>
                </div>

                <div class="col-md-6">
                    <label for="firma_trabajador" class="form-label">Firma del Trabajador</label>
                    <input type="text" name="firma_trabajador" id="firma_trabajador" class="form-control" value="{{ old('firma_trabajador') }}">
                </div>
                
                <!-- Botones restaurados -->
                <div class="col-12 text-end">
                    <a href="{{ route('permisosmilan.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Permiso</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('El DOM está completamente cargado.');

        document.getElementById('cedula').addEventListener('input', function() {
            const cedula = this.value.trim();

            if (cedula.length > 0) {
                fetch(`/buscar-usuario/${cedula}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Usuario no encontrado');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        console.log('Datos recibidos:', data);
                        document.getElementById('nombre').value = data.nombre || '';
                        document.getElementById('cargo').value = data.cargo || '';
                        document.getElementById('correo_electronico').value = data.correo_electronico || '';
                        document.getElementById('firma_trabajador').value = data.nombre || '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('No se pudo completar la búsqueda. Por favor, intente nuevamente.');
                    document.getElementById('nombre').value = '';
                    document.getElementById('cargo').value = '';
                    document.getElementById('correo_electronico').value = '';
                    document.getElementById('firma_trabajador').value = '';
                });
            }
        });
    });
</script>
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('cedula').addEventListener('input', function() {
            const cedula = this.value.trim();

            if (cedula.length > 0) {
                fetch(`/buscar-usuario/${cedula}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Usuario no encontrado');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                    } else {
                        <div class="col-md-6">
                            <label for="nombre" class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" readonly>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="correo_electronico" class="form-label">Correo Electrónico</label>
                            <input type="email" name="correo_electronico" id="correo_electronico" class="form-control" readonly>
                        </div>
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('No se pudo completar la búsqueda. Por favor, intente nuevamente.');
                    document.getElementById('nombre').value = '';
                    document.getElementById('correo_electronico').value = '';
                });
            }
        });
    });
</script>
@endsection
