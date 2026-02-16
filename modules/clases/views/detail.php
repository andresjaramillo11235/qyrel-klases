<div class="container mt-5">
    <h2>Detalle de la Clase</h2>
    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td><?= $clase['id'] ?></td>
        </tr>
        <tr>
            <th>Nombre</th>
            <td><?= $clase['nombre'] ?></td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td><?= $clase['descripcion'] ?></td>
        </tr>
        <tr>
            <th>Tipo de Clase</th>
            <td><?= $clase['tipo_clase_nombre'] ?></td>
        </tr>
        <tr>
            <th>Estado</th>
            <td><?= $clase['estado_clase_nombre'] ?></td>
        </tr>
        <tr>
            <th>Fecha</th>
            <td><?= $clase['fecha'] ?></td>
        </tr>
        <tr>
            <th>Hora de Inicio</th>
            <td><?= $clase['hora_inicio'] ?></td>
        </tr>
        <tr>
            <th>Hora de Fin</th>
            <td><?= $clase['hora_fin'] ?></td>
        </tr>
        <tr>
            <th>Matrícula ID</th>
            <td><?= $clase['matricula_id'] ?></td>
        </tr>
        <tr>
            <th>Lugar</th>
            <td><?= $clase['lugar'] ?></td>
        </tr>
        <tr>
            <th>Vehículo</th>
            <td><?= $clase['vehiculo_placa'] ?></td>
        </tr>
        <tr>
            <th>Instructor</th>
            <td><?= $clase['instructor_nombre'] ?></td>
        </tr>
        <tr>
            <th>Observaciones</th>
            <td><?= $clase['observaciones'] ?></td>
        </tr>
    </table>
    <a href="/clases/index/<?= $clase['matricula_id'] ?>" class="btn btn-secondary">Volver</a>
    <a href="/clases/edit/<?= $clase['id'] ?>" class="btn btn-primary">Editar</a>
</div>
<br><br>
