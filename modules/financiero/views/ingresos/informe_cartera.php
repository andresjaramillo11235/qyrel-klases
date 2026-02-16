<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Informe Cartera</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light text-dark border-bottom">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="mb-3 mb-sm-0">
                <i class="ti ti-briefcase"></i> Informe Cartera
            </h5>
        </div>
    </div>

    <div class="card-body pt-3">
        <div class="table-responsive">
            <table class="table table-striped data-table" id="tablaInformeCartera">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Cédula</th>
                        <th>Programa</th>
                        <th>Categoría</th>
                        <th>Fecha de Matrícula</th>
                        <th>Convenio</th>
                        <th>Valor Matrícula</th>
                        <th>Total Pagado</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartera as $fila): ?>
                        <tr>
                            <td><?= htmlspecialchars($fila['estudiante_nombres']) ?></td>
                            <td><?= htmlspecialchars($fila['estudiante_apellidos']) ?></td>
                            <td><?= htmlspecialchars($fila['estudiante_cedula']) ?></td>
                            <td><?= htmlspecialchars($fila['programa_nombre']) ?></td>
                            <td><?= htmlspecialchars($fila['categoria_licencia']) ?></td>
                            <td><?= htmlspecialchars($fila['fecha_inscripcion']) ?></td>
                            <td><?= htmlspecialchars($fila['convenio_nombre']) ?></td>
                            <td>$<?= number_format($fila['valor_matricula'], 0, ',', '.') ?></td>
                            <td>$<?= number_format($fila['total_pagado'], 0, ',', '.') ?></td>
                            <td class="text-danger">$<?= number_format($fila['saldo'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
    $('#tablaInformeCartera').DataTable({
        dom: 'Bfrtip',
        buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 7 || column === 8 || column === 9) {
                                const cleaned = $('<div>').html(data).text();
                                return cleaned.replace(/\./g, '').replace(/\$/, '').trim();
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
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],
                }
            }
        ],
        columnDefs: [{
            targets: 0, // Suponiendo que la columna "Fecha" es la primera
            type: 'int' // Indica que esta columna debe tratarse como fecha
        }],
        order: [
            [5, 'desc']
        ], // Ordena por la columna Fecha en orden descendente
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
</script>