<?php if (isset($_SESSION['instructor_creado'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['instructor_creado'];
                    unset($_SESSION['instructor_creado']); ?>'
        });
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['instructor_modificado'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['instructor_modificado'];
                    unset($_SESSION['instructor_modificado']); ?>'
        });
    </script>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="/instructores/">Listado Instructores</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Listado de Instructores</h5>
                <div>
                    <a href="/instructorescreate/" class="btn btn-primary"><i class="ti ti-plus"></i> Crear instructor</a>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table class="table table-striped data-table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Correo</th>
                                <th>Celular</th>
                                <th>Estado</th>
                                <th>Categorías<br>Conducción</th>
                                <th>Categorías<br>Instructor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instructores as $instructor) : ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($instructor['foto'])) : ?>
                                            <img src="/files/fotos_instructores/<?php echo $instructor['foto']; ?>"
                                                alt="<?php echo htmlspecialchars($instructor['nombres']); ?>"
                                                class="img-thumbnail mt-2 wid-40">
                                        <?php else : ?>
                                            <img src="/files/fotos_instructores/img-defecto-instructor.webp" 
                                            alt="<?php echo htmlspecialchars($instructor['nombres']); ?>" 
                                            class="img-thumbnail mt-2 wid-40">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo strtoupper($instructor['nombres'] . '<br> ' . $instructor['apellidos']); ?></td>
                                    <td><?= $instructor['numero_documento'] ?></td>
                                    <td><?= $instructor['correo'] ?></td>
                                    <td><?= $instructor['celular'] ?></td>
                                    <td><?= $instructor['estado'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                                    <td><?= $instructor['categorias_conduccion'] ?></td>
                                    <td><?= $instructor['categorias_instructor'] ?></td>
                                    <td>
                                        <a href="/instructoresedit/<?= $instructor['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <!-- <a href="/instructoresdetail/<?= $instructor['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-info-circle f-20"></i>
                                        </a>
                                        <a href="/instructores/delete/<?= $instructor['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-trash f-20"></i>
                                        </a> -->
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

<!-- Modal for Instructor -->
<div class="modal fade" id="instructorModal" tabindex="-1" aria-labelledby="instructorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="instructorModalLabel">Detalles del Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="instructorModalBody">
                <!-- Content will be loaded here via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        console.log('Document ready, attaching event handlers.');

        $('.instructor-detail').on('click', function() {
            var instructorId = $(this).data('instructor-id');
            console.log('Instructor ID:', instructorId);
            $.ajax({
                url: '/instructores/detail/' + instructorId,
                method: 'GET',
                success: function(data) {
                    $('#instructorModalBody').html(data);
                    $('#instructorModal').modal('show');
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching instructor details:', status, error);
                }
            });
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/datatables-config.js"></script>