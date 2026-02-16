<?php $routes = include '../config/Routes.php'; ?>
<?php include_once '../shared/utils/InsertarSaltosDeLinea.php'; ?>

<?php /* [ breadcrumb ] start */ ?>
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Auditoria</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /* [ breadcrumb ] end */ ?>

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header" style="background-color: #e0f7fa;">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Control de auditoría</h5>
                </div>
            </div>

            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-striped data-table" id="tablaAuditorias">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Usuario</th>
                                <th>Modulo</th>
                                <th>Accion</th>
                                <th>Descripción</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($auditorias)) : ?>
                                <?php foreach ($auditorias as $index => $auditoria) : ?>
                                    <tr>
                                        <td>
                                        <?php
                                            $date = new DateTime($auditoria['fecha'], new DateTimeZone('UTC'));
                                            $date->setTimezone(new DateTimeZone('America/Bogota'));
                                        ?>
                                        <?= $date->format('Y-m-d H:i:s') ?>
                                        </td>
                                        <td><?= htmlspecialchars(strtoupper($auditoria['usuario_nombre'])) ?> <?= htmlspecialchars(strtoupper($auditoria['usuario_apellido'])) ?></td>
                                        <td><?= htmlspecialchars(strtoupper($auditoria['modulo'])) ?></td>
                                        <td><?= htmlspecialchars(strtoupper($auditoria['accion'])) ?></td>
                                        <td><?= insertarSaltosDeLinea(htmlspecialchars($auditoria['descripcion']), 25) ?></td>
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
        $('#tablaAuditorias').DataTable({

            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success', // Estilo Bootstrap
                title: 'Control Auditoria',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4] // Índices de las columnas que quieres exportar
                }
            }],
            order: [
                [0, "desc"]
            ],
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
            pagingType: "simple_numbers"
        });
    });
</script>