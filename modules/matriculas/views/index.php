<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['matricula_error'])) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Error al eliminar',
            text: '<?= addslashes($_SESSION['matricula_error']) ?>',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Cerrar'
        });
    </script>
    <?php unset($_SESSION['matricula_error']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['matricula_eliminada'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            title: '¡Eliminación exitosa!',
            text: '<?= addslashes($_SESSION['matricula_eliminada']) ?>',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    </script>
    <?php unset($_SESSION['matricula_eliminada']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['matricula_modificada'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?= addslashes($_SESSION['matricula_modificada']) ?>'
        });
    </script>
    <?php unset($_SESSION['matricula_modificada']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['matricula_creada'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?= addslashes($_SESSION['matricula_creada']) ?>'
        });
    </script>
    <?php unset($_SESSION['matricula_creada']); ?>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="/matriculas/">Matrículas</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ph-duotone ph-funnel me-1"></i> Filtro de Matrículas</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="/matriculas/" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha inicial:</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($_POST['fecha_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha final:</label>
                        <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($_POST['fecha_fin'] ?? '') ?>">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ph-duotone ph-funnel me-1"></i> Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php ## Listado de Matrículas ## 
?>
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Listado de matrículas.</h5>
                <div>
                    <a href="/matriculascreate/" class="btn btn-primary"><i class="ti ti-plus"></i> Crear Matrícula</a>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table id="tablaMatriculas" class="table table-striped table-bordered data-table nowrap">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Inscripción</th>
                                <th>Programa</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>Documento</th>
                                <th>Teléfono</th>
                                <th>Sexo</th>
                                <th>Fecha Nacimiento</th>
                                <th>Estado Civil</th>
                                <th>Tipo Solicitud</th>
                                <th>Valor</th>
                                <th>Convenio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            foreach ($matriculas as $matricula) : ?>
                                <tr>
                                    <td><?= $matricula['id'] ?></td>
                                    <td><?= $matricula['fecha_inscripcion'] ?></td>
                                    <td><?= strtoupper($matricula['programa_nombre']) ?? 'Sin programa' ?></td>
                                    <td><?= $matricula['estudiante_nombres'] ?></td>
                                    <td><?= $matricula['estudiante_apellidos'] ?></td>
                                    <td><?= $matricula['estudiante_numero_documento'] ?></td>
                                    <td><?= $matricula['estudiante_celular'] ?></td>
                                    <td><?= $matricula['genero_nombre'] ?></td>
                                    <td><?= $matricula['fecha_nacimiento'] ?></td>
                                    <td><?= $matricula['estado_civil_nombre'] ?></td>
                                    <td><?= $matricula['tipo_solicitud_nombre'] ?></td>
                                    <td><?= '$' . number_format($matricula['valor_matricula'], 0, ',', '.') ?></td>
                                    <td><?= $matricula['convenio_nombre'] ?></td>
                                    <td><?= $matricula['estado_nombre'] ?></td>
                                    <td>
                                        <a href="/matriculasdetail/<?= $matricula['id'] ?>" class="avtar avtar-xs btn-link-secondary" title="Ver Detalle">
                                            <i class="ti ti-info-circle f-20"></i>
                                        </a>
                                        <a href="/matriculasedit/<?= $matricula['id'] ?>" class="avtar avtar-xs btn-link-secondary" title="Editar Matrícula">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <a href="javascript:void(0);" class="avtar avtar-xs btn-link-danger" title="Eliminar Matrícula"
                                            onclick="confirmarEliminacion('<?= $matricula['id'] ?>')">
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

<script>
    $(document).ready(function() {
        $('#tablaMatriculas').DataTable({
            responsive: true,
            ordering: true,
            order: [
                [1, 'desc']
            ], // Ordena por la segunda columna
            dom: 'Bfrtip', // Agrega la sección de botones al DOM
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    className: 'btn btn-success',
                    title: 'Listado de matrículas', // ✅ Título en Excel
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], // ✅ Solo hasta columna "Estado"
                        format: {
                            body: function(data, row, column, node) {
                                if (column === 11) { // ✅ Si "Valor" está en la columna 6
                                    const cleaned = $('<div>').html(data).text();
                                    return cleaned.replace(/\./g, '').replace(/\$/g, '').trim();
                                }
                                return data;
                            }
                        }
                    }
                },
                {
                    extend: 'pdfHtml5',
                    text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                    className: 'btn btn-danger',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    title: 'Listado de matrículas', // ✅ Título principal
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11] // ✅ Igualmente sin "Acciones"
                    }
                },
                {
                    extend: 'csvHtml5',
                    text: '<i class="fas fa-file-csv"></i> Exportar a CSV',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]
                    }
                }
            ],
            language: {
                decimal: ",",
                thousands: ".",
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros por página",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron registros coincidentes",
                emptyTable: "No hay datos disponibles en la tabla",
                aria: {
                    sortAscending: ": activar para ordenar la columna ascendente",
                    sortDescending: ": activar para ordenar la columna descendente"
                },
                buttons: {
                    copy: "Copiar",
                    colvis: "Columnas visibles",
                    print: "Imprimir"
                }
            }
        });
    });
</script>

<script>
    function confirmarEliminacion(matriculaId) {
        Swal.fire({
            title: '¿Eliminar Matrícula ' + matriculaId + '?',
            text: "Esta acción eliminará la matrícula seleccionada.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Segunda advertencia con más detalles
                Swal.fire({
                    title: '¡Advertencia!',
                    html: '<strong>Matrícula ' + matriculaId + '</strong><br><br>' +
                        'Se eliminarán también:<br>- Clases prácticas<br>- Calificaciones<br>- Abonos a la matrícula<br><br>' +
                        '<strong>Esta acción <u>NO</u> puede ser reversada.</strong>',
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar todo',
                    cancelButtonText: 'Cancelar'
                }).then((secondResult) => {
                    if (secondResult.isConfirmed) {
                        // Redireccionar al backend para eliminar
                        window.location.href = '/8sTuVwXyZa/' + matriculaId;
                    }
                });
            }
        });
    }
</script>