<?php if (isset($_SESSION['success_create'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['success_create'];
                    unset($_SESSION['success_create']); ?>'
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

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">
                        <?php if ($this->userUtils->isSuperAdmin($currentUserId)) : ?>
                            <?php if (!empty($users)) : ?>
                                Usuario administrador <?= $users[0]['empresa_nombre'] ?>
                            <?php else : ?>
                                No hay usuarios administradores.
                            <?php endif; ?>
                        <?php else : ?>
                            Listado de usuarios
                        <?php endif; ?>
                    </h5>
                    <div>
                        <?php if (!$users) : ?>
                            <a href="/users-create/<?= $empresaId ?>" class="btn btn-primary">Nuevo usuario</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover data-table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Teléfono</th>
                                <th>Dirección</th>
                                <th>Status</th>
                                <th>Role</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user) : ?>
                                <tr>
                                    <td><?= $user['username'] ?></td>
                                    <td><?= $user['email'] ?></td>
                                    <td><?= strtoupper($user['first_name']) ?></td>
                                    <td><?= strtoupper($user['last_name']) ?></td>
                                    <td><?= $user['phone'] ?></td>
                                    <td><?= strtoupper($user['address']) ?></td>
                                    <td><?= $user['status'] == 1 ? 'ACTIVO' : 'INACTIVO' ?></td>
                                    <td><?= $user['role_name'] ?></td>

                                     <td>
                                        <?php if ($this->userUtils->isSuperAdmin($currentUserId)) : ?>
                                            <a href="/usersedit/<?= $user['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                                <i class="ti ti-edit f-20"></i>
                                            </a>
                                            <a href="/users/delete/<?= $user['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                                <i class="ti ti-trash f-20"></i>
                                            </a>
                                        <?php else : ?>

                                        <?php endif; ?>
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

