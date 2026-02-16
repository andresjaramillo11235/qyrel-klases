

<div class="container mt-3">
    <h1 class="mt-5">Cronograma Diario del Instructor</h1>
    <div class="row" id="clasesProgramadas">
        <?php foreach ($clasesProgramadas as $clase): ?>
            <div class="col-md-3 mb-3">
                <div class="card h-100">
                    <img src="../../assets/uploads/<?= $clase['estudiante_foto'] ?>" class="card-img-top estudiante-foto" alt="Foto del Estudiante">
                    <div class="card-body">
                        <h5 class="card-title"><?= $clase['nombre'] ?></h5>
                        <p class="card-text"><?= $clase['descripcion'] ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Curso:</strong> <?= $clase['curso_nombre'] ?></li>
                        <li class="list-group-item"><strong>Fecha:</strong> <?= $clase['fecha'] ?></li>
                        <li class="list-group-item">
                            <strong>Hora:</strong> 
                            <?= date('H:i', strtotime($clase['hora_inicio'])) ?> - 
                            <?= date('H:i', strtotime($clase['hora_fin'])) ?>
                        </li>
                        <li class="list-group-item"><strong>Estudiante:</strong> <?= $clase['estudiante_nombres'] ?> <?= $clase['estudiante_apellidos'] ?></li>
                        <li class="list-group-item"><strong>Celular:</strong> <?= $clase['estudiante_celular'] ?></li>
                        <li class="list-group-item"><strong>Vehículo:</strong> <?= $clase['vehiculo_placa'] ?></li>
                        <li class="list-group-item"><strong>Lugar de Recogida:</strong> <?= $clase['lugar'] ?></li>
                    </ul>
                    <div class="card-body">
                        <a href="#" class="card-link" data-bs-toggle="modal" data-bs-target="#modalDetalleClase" data-clase-id="<?= $clase['id'] ?>">Ver Detalles</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Include the modal -->
<?php include 'modal_detalle_clase.php'; ?>

<!-- Include the specific JavaScript files for managing the instructor's class modal -->
<script src="../../assets/js/instructores/initCronogramaDiario.js"></script>
