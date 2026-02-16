<?php if (isset($_SESSION['success_create'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['success_create']; unset($_SESSION['success_create']); ?>'
        });
    </script>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Listado de Asignaciones de Permisos a Roles</h5>
                </div>
            </div>
            <div class="card-body pt-3">
                <?php if (!empty($error_message)) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)) : ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Rol</th>
                                <th>Permiso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rolePermissions as $rolePermission) : ?>
                                <tr>
                                    <td><?php echo $rolePermission['role_name']; ?></td>
                                    <td><?php echo $rolePermission['permission_name']; ?></td>
                                    <td>
                                        <a href="/rolepermissionsdelete/<?php echo $rolePermission['id']; ?>" class="avtar avtar-xs btn-link-secondary" title="Eliminar" onclick="return confirm('¿Está seguro de que desea eliminar esta asignación?');">
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

<?php
if (isset($_SESSION['error_message'])) {
    unset($_SESSION['error_message']);
}
if (isset($_SESSION['success_message'])) {
    unset($_SESSION['success_message']);
}
?>
