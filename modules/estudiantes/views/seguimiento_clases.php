<!-- modules/estudiantes/views/seguimiento_clases.php -->

<h1>Seguimiento de Clases Prácticas</h1>

<?php foreach ($seguimiento as $programaData): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h3><?= htmlspecialchars($programaData['programa']['programa_nombre']) ?></h3>
            <p><?= htmlspecialchars($programaData['programa']['programa_descripcion']) ?></p>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Clase</th>
                        <th>Duración (horas)</th>
                        <th>Estado</th>
                        <th>Fecha</th>
                        <th>Hora de Inicio</th>
                        <th>Hora de Fin</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($programaData['clases'] as $clase): ?>
                        <tr>
                            <td><?= htmlspecialchars($clase['nombre_clase']) ?></td>
                            <td><?= htmlspecialchars($clase['duracion']) ?></td>
                            <td><?= $clase['completada'] ? 'Completada' : 'Pendiente' ?></td>
                            <td><?= $clase['completada'] ? htmlspecialchars($clase['fecha']) : 'N/A' ?></td>
                            <td><?= $clase['completada'] ? htmlspecialchars(date('H:i', strtotime($clase['hora_inicio']))) : 'N/A' ?></td>
                            <td><?= $clase['completada'] ? htmlspecialchars(date('H:i', strtotime($clase['hora_fin']))) : 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endforeach; ?>
