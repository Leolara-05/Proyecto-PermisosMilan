@extends('layouts.main')

@section('content')
<div class="container py-4">
    <!-- Panel de estadísticas -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-custom bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Total Permisos</h6>
                            <h2 class="mb-0">{{ $permisos->count() }}</h2>
                        </div>
                        <i class="fas fa-clipboard-list fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-custom bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Permisos Activos</h6>
                            <h2 class="mb-0">{{ $permisos->where('estado', 'activo')->count() }}</h2>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-custom bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Este Mes</h6>
                            <h2 class="mb-0">{{ $mesSeleccionado }}</h2>
                            <h2 class="mb-0">{{ session('conteo_mes', $permisosDelMes) }}</h2>
                            <div class="d-flex mt-2">
                                <form action="{{ route('permisosmilan.filtrarMes') }}" method="POST" class="me-2">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <input type="month" name="mes_seleccionado" 
                                               class="form-control bg-transparent text-white border-white" 
                                               value="{{ $fechaSeleccionada }}"
                                               onchange="this.form.submit()">
                                        <span class="input-group-text bg-transparent border-white">
                                            <i class="fas fa-calendar-alt text-white"></i>
                                        </span>
                                    </div>
                                </form>
                                <!-- Se elimina el botón "Ver todos" de aquí -->
                            </div>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Encabezado con búsqueda -->
    <div class="card shadow-custom mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <!-- Eliminado el título "Permisos Milan" -->
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-end">
                        <form action="{{ route('permisosmilan.index') }}" method="GET" class="d-flex w-75">
                            <input type="text" name="buscar" class="form-control" placeholder="Buscar permiso..." value="{{ request('buscar') }}">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                        <!-- Eliminado el botón "Nuevo Permiso" -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla mejorada -->
    <div class="card shadow-custom">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Cargo</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($permisos as $permiso)
                            <tr>
                                <td class="text-center">{{ $permiso->id }}</td>
                                <td>{{ $permiso->cedula }}</td>
                                <td>{{ $permiso->nombre }}</td>
                                <td>{{ $permiso->cargo }}</td>
                                <td>
                                    <span class="badge bg-{{ $permiso->autorizado ? 'success' : 'warning' }}">
                                        {{ $permiso->autorizado ? 'Autorizado' : 'Pendiente' }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <!-- Botón Autorizar -->
                                    <form action="{{ route('permisosmilan.autorizar', $permiso->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm me-1" title="Autorizar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                
                                    <!-- Botón Rechazar -->
                                    <button type="button" class="btn btn-danger btn-sm me-1" title="Rechazar" 
                                            data-bs-toggle="modal" data-bs-target="#observacionesModal{{ $permiso->id }}">
                                        <i class="fas fa-times"></i>
                                    </button>
                                
                                    <!-- Botón Ver Detalles -->
                                    <button type="button" class="btn btn-primary btn-sm me-1" title="Ver detalles"
                                            data-bs-toggle="modal" data-bs-target="#modalDetalles{{ $permiso->id }}">
                                        <i class="fas fa-binoculars"></i>
                                    </button>
                                
                                    <!-- Botón Ver Documento -->
                                    @if($permiso->ruta_archivo || $permiso->nombre_archivo || $permiso->documento)
                                    <a href="{{ route('permisosmilan.descargar', $permiso->id) }}" 
                                       class="btn btn-secondary btn-sm" 
                                       title="Descargar documento">
                                        <i class="fas fa-file-download"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Paginación -->
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <!-- Eliminado el título "Permisos Milan" -->
                    </div>
                    <div>
                        <!-- Eliminado el botón "Nuevo Permiso" -->
                        <!-- Eliminado el botón "Generar Informe" -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulario para Generar Informe -->
    <div class="card shadow-custom mt-4">
        <div class="card-body">
            <form action="{{ route('permisosmilan.generarInforme') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label for="fecha_desde" class="form-label">Fecha Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" required>
                </div>
                <div class="col-md-4">
                    <label for="fecha_hasta" class="form-label">Fecha Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-file-download me-2"></i>Generar Informe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('cedula').addEventListener('input', function() {
    const cedula = this.value;

    if (cedula.length > 0) {
        fetch(`/buscar-persona?cedula=${cedula}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Persona no encontrada');
                }
                return response.json();
            })
            .then(data => {
                document.getElementById('nombre_completo').value = data.nombre_completo;
                document.getElementById('cargo').value = data.cargo || 'No especificado';
                document.getElementById('correo_electronico').value = data.correo_electronico || 'No especificado';
                document.getElementById('firma_trabajador').value = data.nombre_completo;
            })
            .catch(error => {
                console.error(error);
                document.getElementById('nombre_completo').value = '';
                document.getElementById('cargo').value = '';
                document.getElementById('correo_electronico').value = '';
                document.getElementById('firma_trabajador').value = '';
            });
    }
});
// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
});

// Función para confirmar eliminación usando SweetAlert2
function confirmarEliminacion(id) {
    Swal.fire({
        title: '¿Está seguro?',
        text: "Esta acción no se puede revertir",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('form-delete-' + id).submit();
        }
    });
}

// Función para ver detalles
function verDetalles(id) {
    // Implementar lógica para mostrar modal con detalles
    Swal.fire({
        title: 'Detalles del Permiso',
        text: 'Cargando detalles...',
        showConfirmButton: false,
        didOpen: () => {
            // Aquí puedes hacer una llamada AJAX para obtener los detalles
            // y actualizar el contenido del modal
        }
    });
}
</script>
@endsection
