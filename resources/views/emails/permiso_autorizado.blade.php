<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Permiso Autorizado</title>
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
        <h1>Permiso Autorizado</h1>
        <div class="info">
            <p><strong>Nombre:</strong> {{ $permiso->nombre }}</p>
            <p><strong>Cédula:</strong> {{ $permiso->cedula }}</p>
            <p><strong>Cargo:</strong> {{ $permiso->cargo }}</p>
            <p><strong>Desde:</strong> {{ $permiso->desde }}</p>
            <p><strong>Hasta:</strong> {{ $permiso->hasta }}</p>
            <p><strong>Motivo:</strong> {{ $permiso->motivo_permiso }}</p>
        </div>
    </div>
</body>
</html>
