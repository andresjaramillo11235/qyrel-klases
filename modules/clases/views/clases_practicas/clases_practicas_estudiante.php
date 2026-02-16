<?php $routes = include '../config/Routes.php'; ?>
<?php include '../shared/utils/AjustarImagen.php' ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'No se puede calificar',
            text: '<?= $_SESSION['error_message']; ?>',
            confirmButtonText: 'Entendido'
        });
    </script>
    <?php unset($_SESSION['error_message']); // Eliminar el mensaje para que no se muestre nuevamente 
    ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "<?php echo $_SESSION['success_message']; ?>"
            });
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <h5><i class="ph-duotone ph-car"></i> Consulta tus clases prácticas programadas.</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">

                    <table class="table table-hover data-table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Clase</th>
                                <th>Programa</th>
                                <th>Vehículo</th>
                                <th>Instructor</th>
                                <th>Estado</th>
                                <th>Calificar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($clases)): ?>
                                <?php foreach ($clases as $clase): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($clase['fecha']) ?></td>
                                        <td><?= substr($clase['hora_inicio'], 0, 5) . " - " . substr($clase['hora_fin'], 0, 5) ?></td>
                                        <td><?= htmlspecialchars($clase['clase_nombre']) ?></td>
                                        <td><?= strtoupper(htmlspecialchars($clase['programa_nombre'])) ?></td>
                                        <td><?= htmlspecialchars(strtoupper($clase['vehiculo_placa'])) ?></td>
                                        <td><?= htmlspecialchars(strtoupper($clase['instructor_nombre'])) ?></td>
                                        <td><?= strtoupper(htmlspecialchars($clase['estado_clase'])) ?></td>

                                        <td>
                                            <?php
                                            // ----------------------------------------------------------
                                            // 🔹 Tiempos base de la clase
                                            // ----------------------------------------------------------
                                            $fechaClase = $clase['fecha'];
                                            $horaInicioClase = $clase['hora_inicio'];

                                            $fechaHoraInicio = strtotime("$fechaClase $horaInicioClase");

                                            // Ventanas de tiempo
                                            $fechaHoraHabilitaCalificacion = strtotime('+30 minutes', $fechaHoraInicio);
                                            $fechaHoraVenceCalificacion = strtotime('+24 hours', $fechaHoraInicio);

                                            // Hora actual
                                            $fechaHoraActual = time();

                                            // Estados lógicos
                                            $puedeCalificar =
                                                $fechaHoraActual >= $fechaHoraHabilitaCalificacion &&
                                                $fechaHoraActual <= $fechaHoraVenceCalificacion;

                                            $calificacionVencida = $fechaHoraActual > $fechaHoraVenceCalificacion;
                                            ?>

                                            <?php if (!empty($clase['estudiante_fecha_calificacion'])): ?>

                                                <!-- Clase ya calificada -->
                                                <span class="text-success iconoCalificacion"
                                                    data-clase-id="<?= $clase['clase_id']; ?>"
                                                    title="Clase calificada">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>

                                            <?php elseif ($puedeCalificar): ?>

                                                <!-- Dentro de la ventana válida -->
                                                <a href="<?= $routes['clases_practicas_estudiante_calificar'] ?><?= $clase['clase_id'] ?>"
                                                    class="btn btn-primary btn-sm">
                                                    Calificar
                                                </a>

                                            <?php elseif ($calificacionVencida): ?>

                                                <!-- Ventana vencida -->
                                                <span class="text-danger" title="Tiempo para calificar vencido">
                                                    <i class="fas fa-times-circle"></i> Tiempo vencido
                                                </span>

                                            <?php else: ?>

                                                <!-- Clase en curso / aún no habilitada -->
                                                <span class="text-muted" title="La calificación estará disponible 30 minutos después de iniciada la clase">
                                                    <i class="fas fa-clock"></i> En proceso
                                                </span>

                                            <?php endif; ?>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- <tr>
                                    <td colspan="7" class="text-center">No tienes clases programadas.</td>
                                </tr> -->
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCalificacionClase" tabindex="-1" aria-labelledby="modalCalificacionClaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCalificacionClaseLabel">Calificación de la Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Calificación:</strong> <span id="calificacionEstudiante"></span></p>
                <p><strong>Observaciones:</strong></p>
                <p id="observacionesEstudiante" class="border p-2 rounded"></p>
                <p><strong>Fecha de Calificación:</strong> <span id="fechaCalificacion"></span></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/datatables-config.js"></script>

<script>
    $(document).on('click', '.iconoCalificacion', function() {
        const claseId = $(this).data('clase-id'); // Obtener el ID de la clase desde el atributo data

        // Realizar una solicitud AJAX para obtener la calificación
        $.ajax({
            url: `<?= $routes['clases_practicas_obtener_calificacion_estudiante'] ?>${claseId}`, // Ajustar según la ruta del backend
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const calificacion = response.data;

                    // Llenar el modal con los datos obtenidos
                    $('#calificacionEstudiante').text(calificacion.estudiante_calificacion || 'Sin calificación');
                    $('#observacionesEstudiante').text(calificacion.estudiante_observaciones || 'Sin observaciones');
                    $('#fechaCalificacion').text(calificacion.estudiante_fecha_calificacion || 'Sin fecha');

                    // Mostrar el modal
                    $('#modalCalificacionClase').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'No se pudo obtener la calificación.'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al obtener la calificación. Inténtelo nuevamente.'
                });
            }
        });
    });
</script>