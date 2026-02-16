<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelación de Clase Práctica</title>
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
            color: #d9534f;
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
            <h1>Cancelación de Clase Práctica</h1>
        </div>
        <div class="content">
            <p>Estimado(a) {nombre_estudiante},</p>
            <p>Lamentamos informarte que tu clase práctica ha sido cancelada. A continuación, los detalles de la clase eliminada:</p>
            <ul>
                <li><strong>Fecha:</strong> {fecha_clase}</li>
                <li><strong>Horario:</strong> {hora_clase}</li>
                <li><strong>Programa:</strong> {programa}</li>
                <li><strong>Clase:</strong> {clase}</li>
                <li><strong>Instructor:</strong> {nombre_instructor}</li>
                <li><strong>Vehículo:</strong> {vehiculo}</li>
            </ul>
            <p>Si tienes dudas o necesitas reprogramar tu clase, por favor comunícate con nuestro equipo de soporte.</p>
        </div>
        <div class="footer">
            <p>Este correo es generado automáticamente. Por favor, no respondas a este mensaje.</p>
            <p>&copy; 2025 CeaCloud</p>
        </div>
    </div>
</body>

</html>
