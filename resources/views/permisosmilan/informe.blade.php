<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe de Permisos - Bicicletas Milan</title>
    <style>
        @page {
            margin: 15px;
            size: landscape;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 10px;
            padding: 0;
        }
        .header {
            text-align: center;
            background-color: #78b6f2;
            color: white;
            padding: 15px;
            margin-bottom: 15px;
            width: 100%;
        }
        .logo {
            max-width: 120px;
            margin-bottom: 10px;
            mix-blend-mode: multiply;
            background-color: transparent;
        }
        .titulo {
            font-size: 24px;
            margin-bottom: 10px;
            color: #f8f9fa; /* Color más claro para el título */
        }
        th {
            background-color: #78b6f2; /* Mantener consistencia con el header */
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            border: 1px solid #fff;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        td {
            padding: 8px;
            font-size: 12px;
            border: 1px solid #ddd;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            table-layout: fixed;
        }
        /* Ajustar anchos de columnas para formato horizontal */
        .col-cedula { width: 8%; }
        .col-nombre { width: 20%; }
        .col-cargo { width: 12%; }
        .col-fecha { width: 10%; }
        .col-desde-hasta { width: 12%; }
        .col-motivo { width: 14%; }
        .col-estado { width: 6%; }
        
        th, td {
            padding: 6px;
            font-size: 11px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .text-center {
            text-align: center;
        }
        .periodo, .total-registros {
            font-size: 12px;
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/Logo_Milan.png') }}" alt="Logo Milan" class="logo">
        <h1 class="titulo">Informe de Permisos</h1>
        <div class="periodo">
            Período: {{ \Carbon\Carbon::parse($fechaDesde)->format('Y-m-d H:i:s') }} al {{ \Carbon\Carbon::parse($fechaHasta)->format('Y-m-d H:i:s') }}
        </div>
        <div class="total-registros">
            Total de registros: {{ $permisos->count() }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th class="col-cedula">CÉDULA</th>
                <th class="col-nombre">NOMBRE</th>
                <th class="col-cargo">CARGO</th>
                <th class="col-fecha">FECHA AUT.</th>
                <th class="col-desde-hasta">DESDE</th>
                <th class="col-desde-hasta">HASTA</th>
                <th class="col-motivo">MOTIVO</th>
                <th class="col-estado">DESC.</th>
                <th class="col-estado">AUT.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($permisos as $permiso)
                <tr>
                    <td class="col-cedula">{{ $permiso->cedula }}</td>
                    <td class="col-nombre">{{ $permiso->nombre }}</td>
                    <td class="col-cargo">{{ $permiso->cargo }}</td>
                    <td class="col-fecha">{{ \Carbon\Carbon::parse($permiso->fecha_autorizacion)->format('Y-m-d') }}</td>
                    <td class="col-desde-hasta">{{ \Carbon\Carbon::parse($permiso->desde)->format('Y-m-d H:i') }}</td>
                    <td class="col-desde-hasta">{{ \Carbon\Carbon::parse($permiso->hasta)->format('Y-m-d H:i') }}</td>
                    <td class="col-motivo">{{ $permiso->motivo_permiso }}</td>
                    <td class="col-estado text-center">{{ $permiso->descontable ? '1' : '0' }}</td>
                    <td class="col-estado text-center">{{ $permiso->autorizado ? '1' : '0' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>