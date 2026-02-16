<!-- plantilla_mail_confirmacion_agendamiento.php -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Clase Teórica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <div class="container mt-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                Detalles de la Clase Teórica
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <img src="<?= $instructor_foto_url ?>" alt="Foto del Instructor" class="img-fluid rounded">
                    </div>
                    <div class="col-md-8">
                        <h5 class="card-title">Instructor: <?= $instructor_nombre ?></h5>
                        <p class="card-text"><strong>Programa:</strong> <?= $programa_nombre ?></p>
                        <p class="card-text"><strong>Clase:</strong> <?= $clase_nombre ?></p>
                        <p class="card-text"><strong>Aula:</strong> <?= $aula_nombre ?></p>
                        <p class="card-text"><strong>Fecha y Hora:</strong> <?= $fecha_hora ?></p>
                    </div>
                </div>
            </div>
            <div class="card-footer text-end">
                <p class="text-muted">Este es un correo automatizado, por favor no responda a este mensaje.</p>
            </div>
        </div>
    </div>

</body>

</html>
