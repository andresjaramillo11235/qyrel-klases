<?php $routes = include '../config/Routes.php'; ?>
<?php include_once '../shared/utils/ObtenerCalificacionBadge.php'; ?>
<?php include_once '../shared/utils/AjustarImagen.php'; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Calificaciones</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color:rgb(245, 223, 155); color: #333;">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Listado de calificaciones, clases prácticas</h5>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-striped data-table" id="tablaCalificaciones">
                        <thead>
                            <tr>
                                <th>Fecha<br>Clase</th>
                                <th>Hora<br>Inicio</th>
                                <th>Hora<br>Fin</th>
                                <th>Clase</th>
                                <th></th>
                                <th>Estudiante</th>
                                <th>Calificación<br>Estudiante</th>
                                <th>Instructor</th>
                                <th>Calificación<br>Instructor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($calificaciones)) : ?>
                                <?php foreach ($calificaciones as $index => $calificacion) : ?>
                                    <tr>
                                        <td><?= $calificacion['clase_fecha'] ?></td>
                                        <td><?= substr($calificacion['clase_hora_inicio'], 0, 5) ?></td>
                                        <td><?= substr($calificacion['clase_hora_fin'], 0, 5) ?></td>
                                        <td><?= htmlspecialchars(strtoupper($calificacion['clase_nombre'])) ?></td>
                                        <td>
                                            <?php if (!empty($calificacion['estudiante_foto'])) : ?>
                                                <?php
                                                $rutaImagen = "../files/fotos_estudiantes/" . $calificacion['estudiante_foto'];
                                                list($imgWidth, $imgHeight) = ajustarImagen($rutaImagen, 60, 60); // Ajustar a 150x150 máx
                                                ?>
                                                <img src="<?= htmlspecialchars($rutaImagen) ?>"
                                                    alt="Foto del estudiante"
                                                    class="rounded shadow-sm"
                                                    width="<?= $imgWidth ?>"
                                                    height="<?= $imgHeight ?>"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#fotoModal"
                                                    onclick="verFotoModal(this)">
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($calificacion['estudiante_nombres']) ?> <?= htmlspecialchars($calificacion['estudiante_apellidos']) ?></td>
                                        <td><?php echo obtenerCalificacionBadge($calificacion['estudiante_calificacion']); ?></td>
                                        <td><?= htmlspecialchars(strtoupper($calificacion['instructor_nombres'])) ?> <?= htmlspecialchars(strtoupper($calificacion['instructor_apellidos'])) ?></td>
                                        <td><?php echo obtenerCalificacionBadge($calificacion['instructor_calificacion']); ?></td>
                                        <td>
                                            <a href="<?= $routes['calificaciones_detail']; ?><?= $calificacion['id'] ?>"
                                                class="avtar avtar-xs btn-link-secondary"
                                                title="Ver detalles de la calificación">
                                                <i class="ti ti ti-info-circle f-20"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="7" class="text-center">No hay calificaciones disponibles.</td>
                                </tr>
                            <?php endif; ?>
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
                <?php
                // Tamaño máximo permitido en el modal
                $maxModalWidth = 400;
                $maxModalHeight = 400;
                ?>
                <img src="" id="modalFoto" class="img-fluid rounded shadow-lg"
                    style="max-width: <?= $maxModalWidth ?>px; max-height: <?= $maxModalHeight ?>px; object-fit: cover;"
                    alt="Foto del Estudiante">
            </div>
        </div>
    </div>
</div>


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
    $(document).ready(function() {
        $('#tablaCalificaciones').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success', // Estilo Bootstrap
                title: 'Listado de calificaciones, clases prácticas ',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7] // Índices de las columnas que quieres exportar
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
                [0, "desc"]
            ],
            pageLength: 30
        });
    });
</script>

<!-- JavaScript para actualizar la foto en el modal -->
<script>
    function verFotoModal(imgElement) {
        var imgSrc = imgElement.src;
        var imgModal = document.getElementById('modalFoto');

        imgModal.src = imgSrc;

        // Asegurar el tamaño de la imagen en el modal
        imgModal.style.width = "100%"; // Ocupa el máximo dentro del modal
        imgModal.style.maxWidth = "400px"; // Tamaño máximo permitido
        imgModal.style.height = "auto"; // Mantiene la proporción
    }
</script>