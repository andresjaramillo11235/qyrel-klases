
<?php $routes = include '../config/Routes.php'; ?>
<?php include_once '../shared/utils/ObtenerCalificacionBadge.php'; ?>
<?php include_once '../shared/utils/ObtenerClaseColor.php'; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $routes['calificaciones_index'] ?>">Calificaciones</a></li>
                    <li class="breadcrumb-item" aria-current="page">Detalle de la Calificación</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color:rgb(245, 223, 155); color: #333;">
                <h5>Información de la Clase
                    <?php
                    list($estadoClaseColor, $estadoClaseTexto) = obtenerClaseEstado(
                        $calificacion['clase_estado_id'],
                        $calificacion['clase_fecha'],
                        $calificacion['clase_hora_inicio'],
                        $calificacion['clase_hora_fin']
                    );
                    ?>
                    <span class="badge <?= $estadoClaseColor ?>">
                        <?= htmlspecialchars($estadoClaseTexto) ?>
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Nombre de la Clase:</strong>
                            <?= htmlspecialchars($calificacion['clase_nombre'] ?? 'No disponible') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Fecha:</strong>
                            <?= htmlspecialchars($calificacion['clase_fecha'] ?? 'No disponible') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Hora Inicio:</strong>
                            <?= htmlspecialchars($calificacion['clase_hora_inicio'] ?? 'No disponible') ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <strong>Hora Fin:</strong>
                            <?= htmlspecialchars($calificacion['clase_hora_fin'] ?? 'No disponible') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background-color:rgb(245, 223, 155); color: #333;">
                    <h5>Información del estudiante</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <?php if (!empty($calificacion['estudiante_foto'])) : ?>
                                    <img src="../files/fotos_estudiantes/<?= $calificacion['estudiante_foto'] ?>" alt="Foto del estudiante" class="img-thumbnail mt-2" style="max-width: 150px;">
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong>Nombre del Estudiante:</strong>
                                <?php
                                $nombresEstudiante = htmlspecialchars($calificacion['estudiante_nombres'] ?? 'Sin nombre');
                                $apellidosEstudiante = htmlspecialchars($calificacion['estudiante_apellidos'] ?? 'Sin apellido');
                                echo $nombresEstudiante . ' ' . $apellidosEstudiante;
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <strong>Documento:</strong>
                                <?= htmlspecialchars($calificacion['estudiante_documento'] ?? 'No disponible') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background-color:rgb(245, 223, 155); color: #333;">
                    <h5>Información del Instructor</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <strong>Nombre del Instructor:</strong>
                                <?php
                                $nombresInstructor = htmlspecialchars(strtoupper($calificacion['instructor_nombres']) ?? 'Sin nombre');
                                $apellidosInstructor = htmlspecialchars(strtoupper($calificacion['instructor_apellidos']) ?? 'Sin apellido');
                                echo $nombresInstructor . ' ' . $apellidosInstructor;
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="background-color:rgb(245, 223, 155); color: #333;">
                    <h5>Calificación</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                Calificación instructor
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <?php echo obtenerCalificacionBadge($calificacion['instructor_calificacion']); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3"><strong>Observaciones:</strong>
                                <?php echo $calificacion['instructor_observaciones'] ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                Calificación estudiante
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <?php echo obtenerCalificacionBadge($calificacion['estudiante_calificacion']); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3"><strong>Observaciones:</strong>
                                <?php echo $calificacion['estudiante_observaciones'] ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>