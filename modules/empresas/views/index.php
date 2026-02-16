<?php if (isset($_SESSION['empresa_success'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['empresa_success'];
                    unset($_SESSION['empresa_success']); ?>'
        });
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['success_edit'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['success_edit'];
                    unset($_SESSION['success_edit']); ?>'
        });
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['empresa_error'])) : ?>
    <script>
        Swal.fire({
            icon: 'error', // Cambiado de 'success' a 'error'
            text: '<?php echo $_SESSION['empresa_error'];
                    unset($_SESSION['empresa_error']); ?>'
        });
    </script>
<?php endif; ?>


<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="/matriculas/">Empresas</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->
<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5><i class="ph-duotone ph-factory"></i> Listado de empresas.</h5>
                <div>
                    <a href="/empresas-create/" class="btn btn-primary">Nueva Empresa</a>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">

                    <table id="datatable" class="table table-striped table-bordered data-table nowrap">
                        <thead>
                            <tr>
                                <th>Empresa</th>
                                <th>Identificación</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th>Estado</th>
                                <th>Fecha de Ingreso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($empresas as $empresa) : ?>
                                <tr>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <?php if (!empty($empresa['logo'])) : ?>
                                                    <img src="/files/logos_empresas/<?php echo htmlspecialchars($empresa['logo']); ?>"
                                                        alt="<?php echo htmlspecialchars($empresa['nombre']); ?>"
                                                        class="img-thumbnail mt-2 wid-40">
                                                <?php else : ?>
                                                    <img src="/assets/images/user/avatar-1.jpg"
                                                        alt="<?php echo htmlspecialchars($empresa['nombre']); ?>"
                                                        class="img-radius wid-40">
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0"><?= htmlspecialchars($empresa['nombre']) ?></h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $empresa['identificacion'] ?></td>
                                    <td><?= $empresa['correo'] ?></td>
                                    <td><?= $empresa['telefono'] ?></td>
                                    <td><?= htmlspecialchars($empresa['estado_nombre']) ?></td>
                                    <td><?= $empresa['fecha_ingreso'] ?></td>
                                    <td>
                                        <a href="/empresas-edit/<?= $empresa['id'] ?>" class="avtar avtar-xs btn-link-secondary" title="Editar Empresa">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <a href="/empresas-detail/<?= $empresa['id'] ?>" class="avtar avtar-xs btn-link-secondary" title="Ver Detalle Empresa">
                                            <i class="ti ti-eye f-20"></i> <!-- Icono de "eye" para ver detalles -->
                                        </a>
                                        <a href="/empresas-admin/<?= $empresa['id'] ?>" class="avtar avtar-xs btn-link-secondary" title="Ver Administrador">
                                            <i class="ti ti-user f-20"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/datatables-config.js"></script>