<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de Matrícula</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            color: #333333;
        }

        .content {
            margin-top: 20px;
            color: #555555;
        }

        .footer {
            margin-top: 30px;
            font-size: 12px;
            color: #aaaaaa;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a {nombre_empresa}!</h1>
        </div>
        <div class="content">
            <p>Estimado estudiante,</p>
            <p>Nos complace informarte que tu matrícula ha sido registrada con éxito. A continuación, te proporcionamos la información de tu nueva matrícula:</p>
            <ul>
                <li><strong>ID Matrícula:</strong> {id_matricula}</li>
                <li><strong>Fecha de Inscripción:</strong> {fecha_inscripcion}</li>
            </ul>
            <p>Estamos felices de tenerte como parte de nuestra comunidad.</p>
        </div>
        <div class="footer">
            <p>Este correo es generado automáticamente. Por favor, no respondas a este mensaje.</p>
            <p>&copy; 2024 CeaCloud</p>
        </div>
    </div>
</body>

</html>