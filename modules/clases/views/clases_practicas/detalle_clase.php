<?php include '../shared/utils/AjustarImagen.php' ?>
<?php include '../shared/utils/FormatearFechaHumana.php' ?>


<div class="row">

    <div class="col-sm-12">

        <div class="row">
            <div class="col-lg-5 col-xxl-3">
                <div class="card overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="text-center mt-3">
                            <div class="chat-avtar d-inline-flex mx-auto">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#lightboxModal">
                                    <?php
                                    $estudiantePhotoPath = "../files/fotos_estudiantes/" . $claseDetalle['estudiante_foto'];
                                    if (file_exists($estudiantePhotoPath)) {
                                        list($width, $height) = ajustarImagen($estudiantePhotoPath, 150, 150);
                                        echo "<img src=\"$estudiantePhotoPath\" alt=\"Foto Estudiante\" style=\"width: {$width}px; height: {$height}px; object-fit: cover;\">";
                                    } else {
                                        echo "<img src=\"../assets/images/user/avatar-2.jpg\" alt=\"Sin foto\" style=\"width: 60px; height: 60px; object-fit: cover;\">";
                                    }
                                    ?>
                                </a>
                            </div>
                            <br><br>
                            <h5 class="mb-0"><strong>Estudiante:<br></strong> <?= htmlspecialchars(strtoupper($claseDetalle['estudiante_nombre'])) ?></h5>
                        </div>
                    </div>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="text-center mt-3">
                            <div class="chat-avtar d-inline-flex mx-auto">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#lightboxModal">
                                    <?php
                                    $instructorPhotoPath = "../files/fotos_instructores/" . ($claseDetalle['instructor_foto'] ?? '');
                                    if (!empty($claseDetalle['instructor_foto']) && file_exists($instructorPhotoPath)) {
                                        list($width, $height) = ajustarImagen($instructorPhotoPath, 150, 150);
                                        echo "<img src=\"$instructorPhotoPath\" alt=\"Foto Instructor\" style=\"width: {$width}px; height: {$height}px; object-fit: cover;\">";
                                    } else {
                                        echo "<img src=\"../assets/images/user/avatar-2.jpg\" alt=\"Sin foto\" style=\"width: 60px; height: 60px; object-fit: cover;\">";
                                    }
                                    ?>
                                </a>
                            </div>
                            <br><br>
                            <h5 class="mb-0">
                                <strong>Instructor:<br></strong> <?= htmlspecialchars(strtoupper($claseDetalle['instructor_nombre'])) ?>
                            </h5>
                        </div>
                    </div>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-body position-relative">
                        <div class="text-center mt-3">
                            <div class="chat-avtar d-inline-flex mx-auto">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#lightboxModal">
                                    <?php
                                    $vehiculoPhotoPath = "../files/fotos_vehiculos/" . $claseDetalle['vehiculo_foto'];
                                    if (!empty($claseDetalle['vehiculo_foto']) && file_exists($vehiculoPhotoPath)) {
                                        list($width, $height) = ajustarImagen($vehiculoPhotoPath, 150, 150);
                                        echo "<img src=\"$vehiculoPhotoPath\" alt=\"Foto Vehículo\" style=\"width: {$width}px; height: {$height}px; object-fit: cover;\">";
                                    } else {
                                        echo "<img src=\"../files/fotos_vehiculos/default-vehicle.png\" alt=\"Sin foto\" style=\"width: 60px; height: 60px; object-fit: cover;\">";
                                    }
                                    ?>
                                </a>
                            </div>
                            <br><br>
                            <h5 class="mb-0">
                                <?= htmlspecialchars(strtoupper($claseDetalle['vehiculo_placa'])) ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>



            <div class="col-lg-7 col-xxl-9">

                <div class="card">
                    <div class="card-header">
                        <h5>Clase Práctica
                            <span class="badge bg-primary">
                                <?= !empty($claseDetalle['estado_clase_nombre']) ? htmlspecialchars($claseDetalle['estado_clase_nombre']) : 'Sin estado definido' ?>
                            </span>
                        </h5>
                    </div>

                    <div class="card-body">

                        <ul class="list-group list-group-flush">

                            <li class="list-group-item px-0 pt-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Fecha:</p>
                                        <p class="mb-0"><?= formatearFechaHumana($claseDetalle['fecha']); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Horario:</p>
                                        <p class="mb-0"><?= htmlspecialchars($claseDetalle['hora_inicio']) ?> - <?= htmlspecialchars($claseDetalle['hora_fin']) ?></p>
                                    </div>
                                </div>
                            </li>

                            <li class="list-group-item px-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Matrícula:</p>
                                        <p class="mb-0"><?= htmlspecialchars($claseDetalle['matricula_id']) ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Programa y clase:</p>
                                        <p class="mb-0">
                                            <?= htmlspecialchars($claseDetalle['programa_nombre']) ?>
                                            <br>
                                            <?= htmlspecialchars($claseDetalle['clase_nombre']) ?>
                                        </p>
                                    </div>
                                </div>
                            </li>







                        </ul>

                    </div>

                </div>

                <!-- ########################################################################## -->
                <div class="card">
                    <div class="card-header">
                        <h5>Calificaciones
                            <span class="badge bg-primary">
                                <?= !empty($claseDetalle['estado_clase']) ? htmlspecialchars($claseDetalle['estado_clase']) : 'Sin estado definido' ?>
                            </span>
                        </h5>
                    </div>

                    <div class="card-body">

                        <ul class="list-group list-group-flush">

                            <li class="list-group-item px-0 pt-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Calificación Estudiante:</p>
                                        <p class="mb-0"><?= !empty($claseDetalle['estudiante_calificacion']) ? htmlspecialchars($claseDetalle['estudiante_calificacion']) : 'No calificado' ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Observaciones Estudiante:</p>
                                        <p class="mb-0"><?= !empty($claseDetalle['estudiante_observaciones']) ? htmlspecialchars($claseDetalle['estudiante_observaciones']) : 'Sin observaciones' ?></p>
                                    </div>
                                </div>
                            </li>

                            <li class="list-group-item px-0">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Calificación Instructor:</p>
                                        <p class="mb-0"><?= !empty($claseDetalle['instructor_calificacion']) ? htmlspecialchars($claseDetalle['instructor_calificacion']) : 'No calificado' ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1 text-muted">Observaciones Instructor:</p>
                                        <p class="mb-0"><?= !empty($claseDetalle['instructor_observaciones']) ? htmlspecialchars($claseDetalle['instructor_observaciones']) : 'Sin observaciones' ?>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

 <!-- ########################################################################## -->
 <div class="card">
                    <div class="card-header">
                        <h5>Recorrido</h5>
                    </div>

                    <div class="card-body">

                        
                    </div>
                </div>


            </div>
        </div>

      
      