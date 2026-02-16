<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            html: <?= json_encode($_SESSION['success_message']) ?>
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            html: <?= json_encode($_SESSION['error_message']) ?>,
            confirmButtonText: 'Entendido'
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="container py-4">

    <!-- 🔹 Título -->
    <h4 class="mb-4">Progreso Teórico del Estudiante</h4>

    <!-- 🔹 Selección de matrícula -->
    <form method="POST" class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label fw-bold">Seleccione una matrícula:</label>
            <select name="matricula_id" class="form-select" onchange="this.form.submit()">
                <option value="">Seleccione...</option>

                <?php foreach ($matriculas as $m): ?>
                    <option value="<?= $m['matricula_id'] ?>"
                        <?= ($m['matricula_id'] == ($matriculaId ?? '')) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($m['programa_nombre']) ?>
                        (ID: <?= $m['matricula_id'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>

    <?php if (empty($matriculaId)): ?>
        <div class="alert alert-info">Seleccione una matrícula para ver el progreso.</div>
        <?php return; ?>
    <?php endif; ?>

    <!-- 🔹 Nombre del programa -->
    <h5 class="text-primary mb-4">
        Programa seleccionado: <b><?= htmlspecialchars($programaSeleccionado) ?></b>
    </h5>

    <!-- -------------------------------------------------------------- -->
    <!-- 🔹 BARRA DE PROGRESO -->
    <!-- -------------------------------------------------------------- -->
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-body">

            <h5 class="card-title mb-3">Avance General</h5>

            <div class="mb-2">
                <small class="text-muted">Progreso de Inscripción (temas agendados)</small>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-info"
                        role="progressbar"
                        style="width: <?= $porcentajeInscrito ?>%;">
                        <?= $porcentajeInscrito ?>%
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <small class="text-muted">Progreso de Asistencia (temas completados)</small>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success"
                        role="progressbar"
                        style="width: <?= $porcentajeAsistido ?>%;">
                        <?= $porcentajeAsistido ?>%
                    </div>
                </div>
            </div>

            <div class="row text-center">
                <div class="col">
                    <span class="badge bg-success">Asistidos: <?= $asistidos ?></span>
                </div>
                <div class="col">
                    <span class="badge bg-primary">Inscritos: <?= $inscritos ?></span>
                </div>
                <div class="col">
                    <span class="badge bg-danger">No asistió: <?= $noAsistio ?></span>
                </div>
                <div class="col">
                    <span class="badge bg-secondary">No visto: <?= $noVistos ?></span>
                </div>
                <div class="col">
                    <span class="badge bg-dark">Total temas: <?= $totalTemas ?></span>
                </div>
            </div>

        </div>
    </div>

    <!-- -------------------------------------------------------------- -->
    <!-- 🔹 TABLA DE PROGRESO POR TEMA -->
    <!-- -------------------------------------------------------------- -->
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-striped table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th style="width:60px;">#</th>
                    <th>Tema</th>
                    <th>Fecha Clase</th>
                    <th>Hora Inicio</th>
                    <th>Hora Fin</th>
                    <th style="width:150px;">Estado</th>
                    <th style="width:120px;">Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($progresoPorTema)): ?>
                    <?php $n = 1; ?>
                    <?php foreach ($progresoPorTema as $item): ?>

                        <?php
                        // Badge según estado
                        $badgeClass = 'badge bg-secondary';
                        switch ($item['estado']) {
                            case 'ASISTIÓ':
                                $badgeClass = 'badge bg-success';
                                break;
                            case 'NO ASISTIÓ':
                                $badgeClass = 'badge bg-danger';
                                break;
                            case 'INSCRITO':
                                $badgeClass = 'badge bg-primary';
                                break;
                        }
                        ?>

                        <tr>
                            <td><?= $n++ ?></td>

                            <td><?= htmlspecialchars($item['tema']) ?></td>

                            <td><?= $item['fecha'] ?: '-' ?></td>
                            <td><?= $item['hora_inicio'] ?: '-' ?></td>
                            <td><?= $item['hora_fin'] ?: '-' ?></td>

                            <td>
                                <span class="<?= $badgeClass ?>">
                                    <?= $item['estado'] ?>
                                </span>
                            </td>

                            <td>
                                <?php if ($item['estado'] === 'INSCRITO'): ?>
                                    <!-- Desagendar -->
                                    <form method="post"
                                        action="<?= htmlspecialchars($routes['clases_teoricas_estudiante_unsuscribe']) ?>"
                                        class="d-inline">

                                        <!-- en este modelo, necesitas pasar la clase_id -->
                                        <input type="hidden" name="clase_teorica_id" value="<?= $item['clase_id'] ?>">
                                        <input type="hidden" name="matricula_id" value="<?= $matriculaId ?>">

                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            Desagendar
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-3 text-muted">
                            No hay temas registrados para este programa.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>