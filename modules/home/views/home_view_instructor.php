<?php
$meses = [
    'January' => 'Enero',
    'February' => 'Febrero',
    'March' => 'Marzo',
    'April' => 'Abril',
    'May' => 'Mayo',
    'June' => 'Junio',
    'July' => 'Julio',
    'August' => 'Agosto',
    'September' => 'Septiembre',
    'October' => 'Octubre',
    'November' => 'Noviembre',
    'December' => 'Diciembre'
];
$nombreMes = $meses[date('F')];
?>

<div class="row">
    <div class="col-md-6 col-xxl-4">
        <div class="card statistics-card-1">
            <div class="card-body">
                <img src="../assets/images/widget/img-status-3.svg" alt="img" class="img-fluid img-bg" />
                <div class="d-flex align-items-start gap-3">
                    <div class="avtar bg-brand-color-3 text-white me-3">
                        <i class="ph-duotone ph-users-four f-26"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0">Tus clases para hoy</p>
                        <div class="d-flex align-items-end">
                            <h2 class="mb-0 f-w-500"><?= $clasesHoy ?></h2>
                            <span class="badge bg-light-success ms-2"><i class="ti ti-calendar"></i> <?php echo date('d/m/Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-12 col-xxl-4">
        <div class="card statistics-card-1">
            <div class="card-body">
                <img src="../assets/images/widget/img-status-2.svg" alt="img" class="img-fluid img-bg" />
                <div class="d-flex align-items-center">
                    <div class="avtar bg-brand-color-1 text-white me-3">
                        <i class="ph-duotone ph-currency-dollar f-26"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0">Tus clases en el mes</p>
                        <div class="d-flex align-items-end">
                            <h2 class="mb-0 f-w-500"><?php echo $clasesMes; ?></h2>
                            <span class="badge bg-light-success ms-2"><i class="ti ti-calendar"></i> <?php echo $nombreMes; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xxl-4">
        <div class="card statistics-card-1">
            <div class="card-body">
                <img src="../assets/images/widget/img-status-3.svg" alt="img" class="img-fluid img-bg" />
                <div class="d-flex align-items-start gap-3">
                    <div class="avtar bg-brand-color-3 text-white me-3">
                        <i class="ph-duotone ph-users-four f-26"></i>
                    </div>
                    <div>
                        <p class="text-muted mb-0">Total horas dictadas</p>
                        <div class="d-flex align-items-end">
                            <h2 class="mb-0 f-w-500"><?= $totalHoras ?></h2>
                            <span class="badge bg-light-success ms-2"><i class="ti ti-calendar"></i> <?php echo date('d/m/Y'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="card table-card">
        <div class="card-header">
            <h5><i class="ti ti-calendar"></i> Clases de Hoy</h5>
        </div>
        <div class="card-body py-3 px-0">
            <div class="table-responsive affiliate-table">
                <table class="table table-hover table-borderless mb-0">
                    <tbody>
                        <?php if (!empty($listadoClasesHoy)): ?>
                            <?php foreach ($listadoClasesHoy as $clase): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-start gap-3">
                                            <img src="<?= htmlspecialchars($estudiantePhotoPath) ?>"
                                                alt="Foto"
                                                style="width: 48px; height: 48px; object-fit: cover;"
                                                class="rounded-circle border">

                                            <div class="text-start">
                                                <div class="fw-bold"><?= htmlspecialchars($clase['estudiante_nombres']) ?></div>
                                                <div class="fw-bold"><?= htmlspecialchars($clase['estudiante_apellidos']) ?></div>
                                                <div class="text-muted small">
                                                    <i class="ti ti-phone"></i> <?= htmlspecialchars($clase['telefono']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td><span class="badge bg-primary"><?= htmlspecialchars($clase['clase_nombre']) ?>
                                            <br><?= date('H:i', strtotime($clase['clase_hora'])) ?> - <?= date('H:i', strtotime($clase['clase_fin'])) ?>
                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= htmlspecialchars(strtoupper($clase['vehiculo_placa'])) ?></span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-info btn-historial"
                                            data-matricula-id="<?= htmlspecialchars($clase['matricula_id'] ?? '') ?>"
                                            data-estudiante="<?= htmlspecialchars(($clase['estudiante_nombres'] ?? '') . ' ' . ($clase['estudiante_apellidos'] ?? '')) ?>">
                                            <i class="ti ti-list"></i> Historial
                                        </button>
                                    </td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No hay clases programadas para hoy.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalHistorial" tabindex="-1" aria-labelledby="modalHistorialLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistorialLabel">
                    Historial de clases de <span id="nombreEstudianteHistorial"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered align-middle" id="tablaHistorial">
                        <thead class="table-light">
                            <tr>
                                <th>FECHA</th>
                                <th>HORARIO</th>
                                <th>CLASE</th>
                                <th>INSTRUCTOR</th>
                                <th>CALIFICACIÓN (INSTR.)</th>
                                <th>OBSERVACIONES (INSTR.)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">Cargando...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $(document).on('click', '.btn-historial', function() {
        const matriculaId = $(this).data('matricula-id');
        const nombreEstudiante = $(this).data('estudiante');

        // Título del modal con el nombre del estudiante
        $('#nombreEstudianteHistorial').text(nombreEstudiante);

        // Mensaje inicial mientras carga
        $('#tablaHistorial tbody').html('<tr><td colspan="6">Cargando...</td></tr>');

        $.ajax({
            url: '/F5G6h7I8Jk/', // 🔹 Ahora no pasamos la matrícula en la URL
            method: 'POST',
            data: {
                matricula_id: matriculaId
            },
            dataType: 'json',
            success: function(data) {
                try {
                    let filas = '';

                    if (data.length > 0) {
                        data.forEach(c => {
                            filas += `
                          <tr>
                            <td>${c.fecha}</td>
                            <td>${c.hora_inicio} - ${c.hora_fin}</td>
                            <td>${c.clase_nombre}</td>
                            <td>${c.instructor_nombre || ''}</td>
                            <td>${c.instructor_calificacion || 'Sin calificar'}</td>
                            <td>${c.instructor_observaciones || ''}</td>
                          </tr>
                        `;
                        });
                    } else {
                        filas = '<tr><td colspan="6" class="text-center">Sin historial</td></tr>';
                    }

                    $('#tablaHistorial tbody').html(filas);
                    $('#modalHistorial').modal('show');
                } catch (e) {
                    console.error("❌ Error procesando historial:", e, data);
                    alert('Error al procesar los datos del historial');
                }
            },
            error: function(xhr, status, error) {
                console.error("❌ Error AJAX:", status, error);
                alert('Error al cargar el historial.');
            }
        });
    });
</script>