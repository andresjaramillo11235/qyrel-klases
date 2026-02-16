<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperación de Contraseña</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 30px auto;
            background: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            border-radius: 10px 10px 0 0;
            font-size: 20px;
            font-weight: bold;
        }

        .content {
            margin-top: 20px;
            color: #333333;
            font-size: 16px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            background-color: #28a745;
            color: #ffffff;
            padding: 12px 20px;
            font-size: 16px;
            font-weight: bold;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }

        .btn:hover {
            background-color: #218838;
        }

        .footer {
            margin-top: 25px;
            font-size: 12px;
            color: #888888;
            text-align: center;
        }

        .code {
            font-size: 22px;
            font-weight: bold;
            color: #ff0000;
            background-color: #f8d7da;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 15px;
        }

    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            Recuperación de Contraseña
        </div>

        <div class="content">
            <p>Hola <strong>{nombre_usuario}</strong>,</p>
            <p>Hemos recibido una solicitud para restablecer tu contraseña <strong>{nombre_empresa}</strong>.  
            Si no realizaste esta solicitud, simplemente ignora este correo.</p>
            
            <p>Para continuar con el proceso de restablecimiento de contraseña, por favor hacer clic en el siguiente botón:</p>
            <a href="{enlace_recuperacion}" class="btn">Restablecer Contraseña</a>

            <p>O puedes copiar y pegar esta ruta (URL) en cualquier navegador:</p>
            <code>{enlace_recuperacion}</code>

            <p><strong>Nota:</strong> Este enlace expirará en <strong>30 minutos</strong>.</p>
        </div>

        <div class="footer">
            <p>Este correo ha sido generado automáticamente. No respondas a este mensaje.</p>
            <p>&copy; 2025 CeaCloud</p>
        </div>
    </div>
</body>

</html>
