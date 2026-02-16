<?php include_once "../shared/utils/CapitalizarPalabras.php"; ?>

<?php if (isset($_SESSION['estudiante_creado'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['estudiante_creado'];
                    unset($_SESSION['estudiante_creado']); ?>'
        });
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['estudiante_modificado'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['estudiante_modificado'];
                    unset($_SESSION['estudiante_modificado']); ?>'
        });
    </script>
<?php endif; ?>


<?php /** [ breadcrumb ] start */  ?>
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Estudiantes</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /** [ breadcrumb ] end */  ?>

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center" style="background-color:rgb(218, 213, 213);">
                <h5>Listado de estudiantes.</h5>
                <div>
                    <a href="/estudiantescreate/" class="btn btn-primary"><i class="ti ti-plus"></i> Crear estudiante</a>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table class="table table-hover data-table" id="tablaEstudiantes">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Usuario</th>
                                <th>Documento</th>
                                <th>Correo</th>
                                <th>Celular</th>
                                <th>Direccion</th>
                                <th>Barrio</th>
                                <th>Contacto</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($estudiantes as $estudiante) : ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <?php if (!empty($estudiante['foto'])) : ?>
                                                    <img src="/files/fotos_estudiantes/<?php echo $estudiante['foto']; ?>"
                                                        alt="<?php echo htmlspecialchars($estudiante['nombres']); ?>"
                                                        class="img-thumbnail mt-2 wid-40"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#fotoModal"
                                                        onclick="verFotoModal(this)">
                                                <?php else : ?>
                                                    <img src="/files/fotos_estudiantes/img-defecto-estudiante.webp"
                                                        alt="<?php echo htmlspecialchars($estudiante['nombres']); ?>"
                                                        class="img-thumbnail mt-2 wid-40"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#fotoModal"
                                                        onclick="verFotoModal(this)">
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0"><?= $estudiante['nombres'] ?><br> <?= $estudiante['apellidos'] ?></h6>
                                            </div>
                                        </div>
                                    </td>

                                    <td><?= $estudiante['nombre_de_usuario'] ?></td>
                                    <td><?= $estudiante['tipo_documento_sigla'] ?> <?= $estudiante['numero_documento'] ?></td>
                                    <td><?= $estudiante['correo'] ?></td>
                                    <td><?= $estudiante['celular'] ?></td>

                                    <td><?= $estudiante['direccion_residencia'] ?></td>
                                    <td><?= $estudiante['barrio'] ?></td>
                                    <td><?= $estudiante['nombre_contacto'] ?> <?= $estudiante['telefono_contacto'] ?></td>

                                    <td>
                                        <?php if ($estudiante['estado'] == 1) : ?>
                                            <span class="badge bg-light-success">ACTIVO</span>
                                        <?php else : ?>
                                            <span class="badge bg-light-danger">INACTIVO</span>
                                        <?php endif; ?>
                                    </td>



                                    <td>
                                        <a href="/estudiantesedit/<?= $estudiante['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <a href="/estudiantesdetail/<?= $estudiante['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti ti-info-circle f-20"></i>
                                        </a>
                                        <!-- <a href="/estudiantes/delete/<?= $estudiante['id'] ?>" class="avtar avtar-xs btn-link-secondary">
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

<!-- Modal HTML (al final del archivo) -->
<div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotoModalLabel">Foto del Estudiante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalFoto" class="img-fluid" alt="Foto del Estudiante">
            </div>
        </div>
    </div>
</div>

<!-- JavaScript para actualizar la foto en el modal -->
<script>
    function verFotoModal(imgElement) {
        var imgSrc = imgElement.src;
        document.getElementById('modalFoto').src = imgSrc;
    }
</script>

<!-- Incluir jQuery y DataTables con Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- DataTables y sus extensiones -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>


<!-- Configuración de DataTables -->
<script>
    <?php if ($_SESSION['rol_nombre'] == 'AUDITOR') : ?>
        $(document).ready(function() {
            $('#tablaEstudiantes').DataTable({
                dom: 'frtip', // quitamos la "B" de Buttons
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "buttons": {
                        "copy": "Copiar",
                        "colvis": "Visibilidad",
                        "print": "Imprimir"
                    }
                },
                pagingType: "simple_numbers",
                order: [
                    [0, "asc"]
                ],
                pageLength: 30
            });
        });
    <?php else : ?>
        $(document).ready(function() {
            $('#tablaEstudiantes').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    className: 'btn btn-info', // Estilo Bootstrap
                    title: 'Listado de estudiantes ',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5] // Índices de las columnas que quieres exportar
                    }
                }],
                language: {
                    "sProcessing": "Procesando...",
                    "sLengthMenu": "Mostrar _MENU_ registros",
                    "sZeroRecords": "No se encontraron resultados",
                    "sEmptyTable": "Ningún dato disponible en esta tabla",
                    "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                    "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                    "sSearch": "Buscar:",
                    "oPaginate": {
                        "sFirst": "Primero",
                        "sLast": "Último",
                        "sNext": "Siguiente",
                        "sPrevious": "Anterior"
                    },
                    "buttons": {
                        "copy": "Copiar",
                        "colvis": "Visibilidad",
                        "print": "Imprimir"
                    }
                },
                pagingType: "simple_numbers",
                order: [
                    [0, "asc"]
                ],
                pageLength: 30
            });
        });
    <?php endif; ?>
</script>