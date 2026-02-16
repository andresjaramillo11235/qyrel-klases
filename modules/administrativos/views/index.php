<?php if (isset($_SESSION['administrativo_creado'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['administrativo_creado'];
                    unset($_SESSION['administrativo_creado']); ?>'
        });
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['administrativo_modificado'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['administrativo_modificado'];
                    unset($_SESSION['administrativo_modificado']); ?>'
        });
    </script>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Home</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="/administrativos/">Administrativos</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-12">
        <div class="card table-card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Listado de administrativos</h5>
                    <div>
                        <a href="/administrativos-create/" class="btn btn-primary">Nuevo administrativo</a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Nombre Usuario</th>
                                <th>Documento</th>
                                <th>Correo</th>
                                <th>Celular</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($administrativos as $administrativo) : ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($administrativo['foto'])) : ?>
                                            <img src="/files/fotos_administrativos/<?php echo $administrativo['foto']; ?>"
                                                alt="<?php echo htmlspecialchars($administrativo['nombres']); ?>"
                                                class="img-thumbnail mt-2 wid-40">
                                        <?php else : ?>
                                            <img src="/assets/images/user/avatar-1.jpg" alt="<?php echo htmlspecialchars($administrativo['nombres']); ?>" class="img-radius wid-40">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $administrativo['nombres'] . '<br>' . $administrativo['apellidos']; ?></td>
                                    <td><?= $administrativo['username'] ?></td>
                                    <td><?= $administrativo['numero_documento'] ?></td>
                                    <td><?= $administrativo['correo'] ?></td>
                                    <td><?= $administrativo['celular'] ?></td>
                                    <td><?= htmlspecialchars($administrativo['rol_description']) . '<br> (' . htmlspecialchars($administrativo['rol_name']) . ')'; ?></td> <!-- Mostrar el rol -->
                                    <td><?= $administrativo['estado'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                                    <td>
                                        <a href="/administrativos-edit/<?= $administrativo['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <a href="/administrativos-detail/<?= $administrativo['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-info-circle f-20"></i>
                                        </a>
                                        <a href="/administrativos/delete/<?= $administrativo['id'] ?>" class="avtar avtar-xs btn-link-secondary">
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

<!-- Modal for Administrativo -->
<div class="modal fade" id="administrativoModal" tabindex="-1" aria-labelledby="administrativoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="administrativoModalLabel">Detalles del Administrativo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="administrativoModalBody">
                <!-- Content will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        console.log('Document ready, attaching event handlers.');

        $('.administrativo-detail').on('click', function() {
            var administrativoId = $(this).data('administrativo-id');
            console.log('Administrativo ID:', administrativoId);
            $.ajax({
                url: '/administrativos/detail/' + administrativoId,
                method: 'GET',
                success: function(data) {
                    $('#administrativoModalBody').html(data);
                    $('#administrativoModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching administrativo details:', status, error);
                }
            });
        });
    });
</script>