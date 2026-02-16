<h1 class="mt-5">Clases para Matrícula <?= $matriculaId ?></h1>
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">Información de la Matrícula</h5>
        <div class="row">
            <div class="col-md-4">
                <p><strong>ID Matrícula:</strong> <?= $matricula['id'] ?></p>
                <p><strong>Fecha de Inscripción:</strong> <?= $matricula['fecha_inscripcion'] ?></p>
                <p><strong>Fecha de Enrolamiento:</strong> <?= $matricula['fecha_enrolamiento'] ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>Estudiante:</strong> <?= $matricula['estudiante_nombres'] ?> <?= $matricula['estudiante_apellidos'] ?></p>
                <p><strong>Programa:</strong> <?= $matricula['programa_nombre'] ?></p>
                <p><strong>Fecha de Vencimiento:</strong> <?= $matricula['fecha_vencimiento'] ?></p>
            </div>
            <div class="col-md-4">
                <p><strong>Fecha Aprobación Teórico:</strong> <?= $matricula['fecha_aprovacion_teorico'] ?></p>
                <p><strong>Fecha Aprobación Práctico:</strong> <?= $matricula['fecha_aprovacion_practico'] ?></p>
                <p><strong>Fecha Certificación:</strong> <?= $matricula['fecha_certificacion'] ?></p>
            </div>
        </div>
    </div>
</div>
<a href="/clases/create/<?= $matriculaId ?>" class="btn btn-primary mb-3">Crear Clase</a>
<table class="table table-striped">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Fecha</th>
            <th>Hora Inicio</th>
            <th>Hora Fin</th>
            <th>Vehículo</th>
            <th>Lugar</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clases as $clase) : ?>
            <tr>
                <td><?= $clase['id'] ?></td>
                <td><?= $clase['nombre'] ?></td>
                <td><?= $clase['descripcion'] ?></td>
                <td><?= $clase['tipo_nombre'] ?></td>
                <td><?= $clase['estado_nombre'] ?></td>
                <td><?= $clase['fecha'] ?></td>
                <td><?= $clase['hora_inicio'] ?></td>
                <td><?= $clase['hora_fin'] ?></td>
                <td><?= $clase['vehiculo_placa'] ?></td>
                <td><?= $clase['lugar'] ?></td>
                <td>
                    <a href="/clases/edit/<?= $clase['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="/clases/delete/<?= $clase['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a>
                    <a href="/clases/detail/<?= $clase['id'] ?>" class="btn btn-info btn-sm">Detalle</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<br>
<a href="/matriculas/" class="btn btn-secondary">Volver a Matrículas</a>
