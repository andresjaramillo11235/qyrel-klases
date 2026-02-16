<!-- modules/instructores/views/administracion.php -->

<?php if (!empty($clases)): ?>
    <h1>Administración de Clases Prácticas</h1>
    
    <table id="clasesTable" class="table table-striped">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Hora de Inicio</th>
                <th>Hora de Fin</th>
                <th>Estudiante</th>
                <th>Vehículo</th>
                <th>Programa</th>
                <th>Lugar de Recogida</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clases as $clase): ?>
                <tr>
                    <td><?php echo htmlspecialchars($clase['fecha'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars(date('H:i', strtotime($clase['hora_inicio'] ?? ''))); ?></td>
                    <td><?php echo htmlspecialchars(date('H:i', strtotime($clase['hora_fin'] ?? ''))); ?></td>
                    <td>
                        <?php echo htmlspecialchars($clase['estudiante_nombres'] . ' ' . $clase['estudiante_apellidos'] ?? ''); ?>
                    </td>
                    <td><?php echo htmlspecialchars($clase['vehiculo_placa'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($clase['programa_nombre'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($clase['lugar'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($clase['estado_nombre'] ?? ''); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Incluir el archivo JavaScript -->
    <script>
        $(document).ready(function() {
            $('#clasesTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json"
                }
            });
        });
    </script>
<?php else: ?>
    <p>No hay clases prácticas registradas.</p>
<?php endif; ?>
