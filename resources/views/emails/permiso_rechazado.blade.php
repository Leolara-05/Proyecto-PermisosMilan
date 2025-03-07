<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Permiso Rechazado</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
            position: relative;
        }
        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            border: 1px solid #ddd;
            position: relative;
            z-index: 10;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.1;
            z-index: 1;
        }
        .watermark img {
            max-width: 400px;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }
        .info {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .info p {
            margin: 5px 0;
            font-size: 16px;
            color: #555;
        }
        .info strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="watermark">
        <img src="{{ public_path('images/logo.png') }}" alt="Bicicletas Milan">
    </div>
    <div class="container">
        <h1>Permiso Rechazado</h1>
        <div class="info">
            <p>Hola,</p>
            <p>Le informamos que el permiso solicitado por <strong>{{ $permiso->nombre }}</strong> ha sido <strong>rechazado</strong>.</p>
            <p><strong>Detalles del Permiso:</strong></p>
            <ul>
                <li><strong>Cédula:</strong> {{ $permiso->cedula }}</li>
                <li><strong>Cargo:</strong> {{ $permiso->cargo }}</li>
                <li><strong>Motivo:</strong> {{ $permiso->motivo_permiso }}</li>
                <li><strong>Desde:</strong> {{ $permiso->desde }}</li>
                <li><strong>Hasta:</strong> {{ $permiso->hasta }}</li>
            </ul>
            <p>Si necesita más información, por favor contacte con Recursos Humanos.</p>
        </div>
    </div>
</body>
</html>
