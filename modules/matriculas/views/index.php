<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['matricula_error'])) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error al eliminar',
            text: '<?= addslashes($_SESSION['matricula_error']) ?>',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Cerrar'
        });
    </script>
    <?php unset($_SESSION['matricula_error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['matricula_eliminada'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Eliminación exitosa!',
            text: '<?= addslashes($_SESSION['matricula_eliminada']) ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    </script>
    <?php unset($_SESSION['matricula_eliminada']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['matricula_modificada'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?= addslashes($_SESSION['matricula_modificada']) ?>'
        });
    </script>
    <?php unset($_SESSION['matricula_modificada']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['matricula_creada'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?= addslashes($_SESSION['matricula_creada']) ?>'
        });
    </script>
    <?php unset($_SESSION['matricula_creada']); ?>
<?php endif; ?>



<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">

            <div class="col-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/home/"><i class="ti ti-home"></i> Inicio</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/matriculas/">
                            <?= LabelHelper::get('menu_matriculas') ?>
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</div>










<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ph-duotone ph-funnel me-1"></i> Filtro </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/matriculas/" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha inicial:</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha final:</label>
                        <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($_POST['fecha_fin'] ?? '') ?>">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-duotone ph-funnel me-1"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">

                <h5 class="mb-0 text-white">
                    <i class="ph-duotone ph-subtitles"></i> <?php echo LabelHelper::get('menu_matriculas'); ?>
                </h5>

                <a href="/matriculascreate/" class="btn btn-light">
                    <i class="ti ti-plus"></i> Crear <?php echo LabelHelper::get('menu_matricula'); ?>
                </a>

            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table id="tablaMatriculas" class="table table-striped table-bordered data-table nowrap">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Inscripción</th>
                                <th>Programa</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>IDENTIFICACIÓN</th>
                                <th>VALOR</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            foreach ($matriculas as $matricula) : ?>
                                <tr>
                                    <td><?= $matricula['codigo'] ?></td>
                                    <td><?= $matricula['fecha_inscripcion'] ?></td>
                                    <td><?= strtoupper($matricula['programa_nombre']) ?? 'Sin programa' ?></td>
                                    <td><?= $matricula['estudiante_nombres'] ?></td>
                                    <td><?= $matricula['estudiante_apellidos'] ?></td>
                                    <td><?= $matricula['numero_documento'] ?></td>
                                    <td><?= '$' . number_format($matricula['valor_matricula'], 0, ',', '.') ?></td>
                                    <td>
                                        <a href="/matriculasdetail/<?= $matricula['codigo'] ?>" class="avtar avtar-xs btn-link-secondary" title="Ver Detalle">
                                            <i class="ti ti-info-circle f-20"></i>
                                        </a>
                                        <a href="/matriculasedit/<?= $matricula['codigo'] ?>" class="avtar avtar-xs btn-link-secondary" title="Editar Matrícula">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="avtar avtar-xs btn-link-danger" title="Eliminar Matrícula"
                                            onclick="confirmarEliminacion('<?= $matricula['codigo'] ?>')">
                                            <i class="ti ti-trash f-20"></i>
                                        </a>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <?= $paginator->summary() ?>
                        </div>
                        <div>
                            <?= $paginationHtml ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function confirmarEliminacion(matriculaId) {
        Swal.fire({
            title: '¿Eliminar Matrícula ' + matriculaId + '?',
            text: "Esta acción eliminará la matrícula seleccionada.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Segunda advertencia con más detalles
                Swal.fire({
                    title: '¡Advertencia!',
                    html: '<strong>Matrícula ' + matriculaId + '</strong><br><br>' +
                        'Se eliminarán también:<br>- Clases prácticas<br>- Calificaciones<br>- Abonos a la matrícula<br><br>' +
                        '<strong>Esta acción <u>NO</u> puede ser reversada.</strong>',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar todo',
                    cancelButtonText: 'Cancelar'
                }).then((secondResult) => {
                    if (secondResult.isConfirmed) {
                        // Redireccionar al backend para eliminar
                        window.location.href = '/8sTuVwXyZa/' + matriculaId;
                    }
                });
            }
        });
    }
</script>