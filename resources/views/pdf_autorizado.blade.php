<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Permiso Autorizado</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 20px; }
        .title { font-size: 24px; font-weight: bold; }
        .details { margin-top: 30px; }
        .detail-item { margin-bottom: 10px; }
        .footer { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Permiso Autorizado</div>
        <div>Bicicletas Milan</div>
    </div>

    <div class="details">
        <div class="detail-item"><strong>Nombre:</strong> {{ $permiso->nombre }}</div>
        <div class="detail-item"><strong>Cédula:</strong> {{ $permiso->cedula }}</div>
        <div class="detail-item"><strong>Cargo:</strong> {{ $permiso->cargo }}</div>
        <div class="detail-item"><strong>Fecha de Autorización:</strong> {{ $permiso->fecha_autorizacion }}</div>
        <div class="detail-item"><strong>Desde:</strong> {{ $permiso->desde }}</div>
        <div class="detail-item"><strong>Hasta:</strong> {{ $permiso->hasta }}</div>
        <div class="detail-item"><strong>Motivo:</strong> {{ $permiso->motivo_permiso }}</div>
    </div>

    <div class="footer">
        <div>_________________________</div>
        <div>Firma Autorizador</div>
    </div>
</body>
</html>
