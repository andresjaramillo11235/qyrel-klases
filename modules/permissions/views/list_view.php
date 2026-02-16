<?php if (isset($_SESSION['success_create'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['success_create'];
                    unset($_SESSION['success_create']); ?>'
        });
    </script>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Listado de Permisos</h5>
                    <div>
                        <a href="/permissionscreate/" class="btn btn-primary">Crear Nuevo Permiso</a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover data-table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre del Permiso</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($permissions as $permission) : ?>
                                <tr>
                                    <td><?php echo $permission['id']; ?></td>
                                    <td><?php echo $permission['name']; ?></td>
                                    <td><?php echo $permission['description']; ?></td>
                                    <td>
                                        <a href="/permissionupdate/<?php echo $permission['id']; ?>" class="avtar avtar-xs btn-link-secondary" title="Editar">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <a href="/permissiondelete/<?php echo $permission['id']; ?>" class="avtar avtar-xs btn-link-secondary" title="Eliminar">
                                            <i class="ti ti-trash f-20"></i>
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