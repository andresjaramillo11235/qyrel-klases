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

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?php echo $_SESSION['error'];
        unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $routes['programas_index'] ?>">Programas</a></li>
                    <li class="breadcrumb-item" aria-current="page"><?php echo $programaNombre ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5><i class="ti ti-school"></i> Temas Teóricos - Programa <?= htmlspecialchars($programaNombre) ?></h5>
                <a href="<?= $routes['clases_teoricas_temas_create'] ?><?= $programaId ?>" class="btn btn-secondary btn-sm">
                    <i class="ti ti-plus"></i> Crear Tema
                </a>
            </div>

            <div class="card-body pt-3">
                <?php if (!empty($temas)) : ?>
                    <div class="table-responsive">
                        <table class="table table-striped data-table" id="tablaTemasClasesTeoricas">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($temas as $index => $tema) : ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td><?= strtoupper(htmlspecialchars($tema['nombre'])) ?></td>
                                        <td><?= strtoupper(htmlspecialchars($tema['descripcion'] ?? 'Sin descripción')) ?></td>
                                        <td>
                                            <a href="<?= $routes['clases_teoricas_temas_edit'] . $tema['id'] ?>" class="btn btn-primary btn-sm">
                                                <i class="ti ti-pencil"></i> Editar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="text-muted text-center">No hay temas registrados para este programa.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>