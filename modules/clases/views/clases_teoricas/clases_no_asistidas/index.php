<?php $routes = include '../config/Routes.php'; ?>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: '<?php echo $_SESSION['success_message']; ?>',
            confirmButtonText: 'Aceptar'
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Listado de Clases Teóricas No Asistidas</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Listado de Clases Teóricas No Asistidas</h5>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">

                    <table class="table table-striped data-table" id="tableNoAsistidas">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Estudiante</th>
                                <th>Documento</th>
                                <th>Programa</th>
                                <th>Tema</th>
                                <th>Instructor</th>
                                <th>Aula</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clasesNoAsistidas as $c): ?>
                                <tr>
                                    <td><?= $c['fecha'] ?></td>
                                    <td><?= substr($c['hora_inicio'], 0, 5) ?> - <?= substr($c['hora_fin'], 0, 5) ?></td>
                                    <td><?= $c['estudiante_nombre'] ?></td>
                                    <td><?= $c['numero_documento'] ?></td>
                                    <td><?= $c['programa_nombre'] ?></td>
                                    <td><?= $c['tema_nombre'] ?></td>
                                    <td><?= $c['instructor_nombre'] ?></td>
                                    <td><?= $c['aula_nombre'] ?></td>

                                    <td>
                                        <button class="btn btn-sm btn-outline-warning btn-mark"
                                            data-cte="<?= $c['cte_id'] ?>"
                                            data-estudiante="<?= $c['estudiante_nombre'] ?>"
                                            data-tema="<?= $c['tema_nombre'] ?>">
                                            Marcar como NO VISTO
                                        </button>
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

<!-- Formulario oculto -->
<form id="formMark" method="post" action="<?php echo $routes['clases_teoricas_marcar_no_visto'] ?>">
    <input type="hidden" name="cte_id" id="inputCteId">
</form>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-mark');
        if (!btn) return;

        const cteId = btn.dataset.cte;
        const est = btn.dataset.estudiante;
        const tema = btn.dataset.tema;

        Swal.fire({
            icon: 'warning',
            title: 'Cambiar asistencia',
            text: `¿Seguro quieres marcar como NO VISTO a ${est} en el tema "${tema}"?`,
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar'
        }).then(r => {
            if (r.isConfirmed) {
                document.getElementById('inputCteId').value = cteId;
                document.getElementById('formMark').submit();
            }
        });
    });
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
    $(document).ready(function() {
        $('#tableNoAsistidas').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success', // Estilo Bootstrap
                title: 'Listado de Clases Teóricas No Asistidas',
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

            // ✅ Ordenar por la columna 0 en orden descendente por defecto
            order: [
                [0, "desc"],
                [1, "desc"]
            ],

            // ✅ Mostrar 10 registros por defecto
            pageLength: 10
        });
    });
</script>