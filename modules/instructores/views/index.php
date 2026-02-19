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

            <div class="col-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/home/"><i class="ti ti-home"></i> Inicio</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="/instructores/">
                            <?= LabelHelper::get('menu_instructores') ?>
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
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">
                    <i class="ph-duotone ph-chalkboard-teacher"></i> <?php echo LabelHelper::get('menu_instructores'); ?>
                </h5>
                <a href="/instructorescreate/" class="btn btn-light">
                    <i class="ti ti-plus"></i> Nuevo <?php echo LabelHelper::get('menu_instructor'); ?>
                </a>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover data-table" id="tablaInstructores">
                        <thead>
                            <tr>
                                <th> </th>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Correo</th>
                                <th>Celular</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($instructores as $instructor) : ?>
                                <tr>
                                    <td class="text-nowrap">

                                        <div class="d-flex align-items-center w-100">
                                            <!-- 🔹 Imagen -->
                                            <div class="flex-shrink-0">
                                                <img src="/files/fotos_instructores/<?php echo $instructor['foto']; ?>"
                                                    alt="<?php echo htmlspecialchars($instructor['nombres']); ?>"
                                                    class="img-thumbnail mt-2 wid-40"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#fotoModal"
                                                    onclick="verFotoModal(this)">
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-nowrap">
                                        <div class="d-flex align-items-center w-100">
                                            <!-- 🔹 Nombre -->
                                            <div class="flex-grow-1 ms-3" >
                                                
                                                    <?= $instructor['nombres'] ?><br>
                                                    <?= $instructor['apellidos'] ?>
                                            </div>
                                        </div>
                                    </td>

                                    <td><?= $instructor['numero_documento'] ?></td>
                                    <td><?= $instructor['correo'] ?></td>
                                    <td><?= $instructor['celular'] ?></td>
                                    <td>
                                        <a href="/instructoresedit/<?= $instructor['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <a href="/instructoresdetail/<?= $instructor['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-info-circle f-20"></i>
                                        </a>
                                        <a href="/instructores/delete/<?= $instructor['id'] ?>" class="avtar avtar-xs btn-link-secondary">
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

<!-- JavaScript para actualizar la foto en el modal -->
<script>
    function verFotoModal(imgElement) {
        var imgSrc = imgElement.src;
        document.getElementById('modalFoto').src = imgSrc;
    }
</script>


<!-- <script>
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
</script> -->


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


<!-- Modal HTML (al final del archivo) -->
<div class="modal fade" id="fotoModal" tabindex="-1" aria-labelledby="fotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fotoModalLabel">Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalFoto" class="img-fluid" alt="Foto">
            </div>
        </div>
    </div>
</div>




<!-- Configuración de DataTables -->
<script>
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
            pageLength: 10
        });
    });
</script>