<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asignación de Clase Práctica</title>
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

        .btn {
            display: inline-block;
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
        }

        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>¡Tienes una nueva Clase Práctica Asignada!</h1>
        </div>
        <div class="content">
            <p>Estimado(a) {nombre_estudiante},</p>
            <p>Nos complace informarte que se te ha asignado una nueva clase práctica. A continuación, los detalles:</p>
            <ul>
                <li><strong>Fecha:</strong> {fecha_clase}</li>
                <li><strong>Horario:</strong> {hora_clase}</li>
                <li><strong>Programa:</strong> {programa}</li>
                <li><strong>Clase:</strong> {clase}</li>
                <li><strong>Instructor:</strong> {nombre_instructor}</li>
                <li><strong>Vehículo:</strong> {vehiculo}</li>
            </ul>
            <p>Te recomendamos asistir puntualmente a tu clase práctica y comunicarte con nosotros si tienes alguna duda.</p>
            <!--<a href="{enlace_clase}" class="btn">Ver Más Detalles</a>-->
        </div>
        <div class="footer">
            <p>Este correo es generado automáticamente. Por favor, no respondas a este mensaje.</p>
            <p>&copy; 2024 CeaCloud</p>
        </div>
    </div>
</body>

</html>