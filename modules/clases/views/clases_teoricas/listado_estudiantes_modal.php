<?php $routes = include '../config/Routes.php'; ?>

<?php if (isset($_SESSION['success'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '<?php echo $_SESSION['success']; ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Listado de Estudiantes -
                        <small class="text-muted">
                            Clase: <?= $clase['tema_nombre'] ?> |
                            Fecha: <?= date('d/m/Y', strtotime($clase['fecha'])) ?> |
                            Aula: <?= $clase['aula_nombre'] ?> |
                            Instructor: <?= strtoupper($clase['instructor_nombre_completo']) ?> |
                            Hora: <?= date('H:i', strtotime($clase['hora_inicio'])) ?> - <?= date('H:i', strtotime($clase['hora_fin'])) ?>
                        </small>
                    </h5>
                </div>
            </div>
            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <form method="post" action="<?= $routes['clases_teoricas_guardar_asistencia'] ?>">
                        <table class="table table-striped data-table" id="pc-dt-simple">
                            <thead>
                                <tr>
                                    <th>Foto</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Teléfono</th>
                                    <th>Programa</th>
                                    <th>Asistencia</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($estudiantes as $est): ?>
                                    <tr>
                                        <td><img src="../files/fotos_estudiantes/<?= htmlspecialchars($est['foto']) ?>" alt="foto" width="50" height="50"></td>
                                        <td><?= htmlspecialchars($est['nombre_completo']) ?></td>
                                        <td><?= htmlspecialchars($est['numero_documento']) ?></td>
                                        <td><?= htmlspecialchars($est['celular']) ?></td>
                                        <td>
                                            <?= htmlspecialchars($est['programa_nombre']) ?>
                                        </td>
                                        <td>
                                            <input
                                                type="checkbox"
                                                name="asistencia[<?= (int)$est['clase_teorica_estudiante_id'] ?>]"
                                                <?= ((int)$est['asistencia'] === 1) ? 'checked' : '' ?>>

                                            <?php if ((int)$est['asistencia'] === 2): ?>
                                                <span class="badge bg-danger ms-2">No asistió</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="#"
                                                class="btn-link-secondary btn-unsubscribe-student"
                                                title="Desagendar alumno de esta clase"
                                                data-cte-id="<?= (int)$est['clase_teorica_estudiante_id'] ?>"
                                                data-clase-id="<?= (int)$idClaseTeorica ?>"
                                                data-alumno="<?= htmlspecialchars($est['nombre_completo']) ?>">
                                                <i class="ti ti-user-x f-20"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <input type="hidden" name="clase_id" value="<?= htmlspecialchars($idClaseTeorica) ?>">

                        <button type="submit"
                            class="btn btn-primary"
                            id="btnGuardarAsistencia"
                            data-fecha="<?= $clase['fecha'] ?>"
                            data-hora-inicio="<?= $clase['hora_inicio'] ?>"
                            data-hora-fin="<?= $clase['hora_fin'] ?>">
                            Guardar asistencia
                        </button>
                    </form>

                    <!-- Form oculto para enviar el POST desagendar-->
                    <form id="form-unsubscribe-student" method="POST"
                        action="<?= htmlspecialchars($routes['clases_teoricas_unsubscribe_estudiante_admin']) ?>"
                        style="display:none;">
                        <input type="hidden" name="cte_id" value="">
                        <input type="hidden" name="clase_id" value="<?= (int)$idClaseTeorica ?>">
                        <?php /* CSRF token aquí si lo usas */ ?>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('btnGuardarAsistencia');

        const fechaClase = btn.dataset.fecha; // YYYY-MM-DD
        const horaInicio = btn.dataset.horaInicio; // HH:MM:SS
        const horaFin = btn.dataset.horaFin; // HH:MM:SS

        // Combinar fecha y hora en objetos Date
        const inicioClase = new Date(`${fechaClase}T${horaInicio}`);
        const finClase = new Date(`${fechaClase}T${horaFin}`);

        // Sumar una hora al fin de la clase
        const finConTolerancia = new Date(finClase.getTime() + 60 * 60 * 1000);

        const ahora = new Date();

        // Verificar si estamos dentro del rango permitido
        const mostrarBoton = ahora >= inicioClase && ahora <= finConTolerancia;

        if (!mostrarBoton) {
            btn.disabled = true;
            btn.classList.add('btn-secondary');
            btn.classList.remove('btn-primary');
            btn.textContent = 'Fuera de horario para marcar asistencia';
        }
    });
</script>

<script>
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-unsubscribe-student');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const cteId = btn.dataset.cteId;
        const alumno = btn.dataset.alumno || '';
        const form = document.getElementById('form-unsubscribe-student');

        const submit = () => {
            form.querySelector('input[name="cte_id"]').value = cteId;
            form.submit();
        };

        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: '¿Desagendar alumno?',
                html: `Vas a desagendar a <b>${alumno}</b> de esta clase.`,
                showCancelButton: true,
                confirmButtonText: 'Sí, desagendar',
                cancelButtonText: 'Cancelar'
            }).then(r => {
                if (r.isConfirmed) submit();
            });
        } else {
            if (confirm(`¿Desagendar a ${alumno} de esta clase?`)) submit();
        }
    });
</script>