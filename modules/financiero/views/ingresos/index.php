<?php $routes = include '../config/Routes.php'; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "<?php echo $_SESSION['success_message']; ?>"
            });
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "<?php echo $_SESSION['error_message']; ?>"
            });
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Ingresos Financieros</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Formulario de Filtro de Fechas -->
<div class="card">
    <div class="card-header bg-light text-dark border-bottom">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="mb-3 mb-sm-0">
                <i class="ti ti-filter me-2"></i> Filtro de ingresos por intervalo de tiempo
            </h5>
        </div>
    </div>
    <div class="card-body">
        <form id="form-filtros" method="GET">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label for="fecha_inicial" class="form-label">
                            <i class="ti ti-calendar"></i> Fecha Inicial:
                        </label>
                        <input type="date" id="fecha_inicial" name="fecha_inicial"
                            class="form-control form-control-sm"
                            value="<?php echo isset($fecha_inicial) ? $fecha_inicial : ''; ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-2">
                        <label for="fecha_final" class="form-label">
                            <i class="ti ti-calendar"></i> Fecha Final:
                        </label>
                        <input type="date" id="fecha_final" name="fecha_final"
                            class="form-control form-control-sm"
                            value="<?php echo isset($fecha_final) ? $fecha_final : ''; ?>" required>
                    </div>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ti ti-filter"></i> Filtrar
                    </button>
                    <a href="<?php echo $routes['ingresos_index']; ?>" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-refresh"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Lista de Ingresos Financieros</h5>
                    <div>
                        <a href="<?php echo $routes['ingresos_create']; ?>" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Agregar Nuevo Ingreso
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-striped data-table" id="tablaIngresos">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Matrícula</th>
                                <th>Programa</th>
                                <th>Categoría</th>
                                <th>Estudiante</th>
                                <th>Cédula</th>
                                <th>Convenio</th>
                                <th>Valor</th>
                                <th>Motivo</th>
                                <th>Tipo</th>
                                <th>Fecha</th>
                                <th># Recibo</th>
                                <th>Observaciones</th>
                                <th><i class="fas fa-print" title="Imprimir"></i></th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ingresos as $ingreso): ?>
                                <tr>
                                    <td><?= $ingreso['id'] ?></td>
                                    <td><?= $ingreso['matricula_id'] ?></td>
                                    <td><?= $ingreso['programa_nombre'] ?></td>
                                    <td><?= $ingreso['categoria_licencia'] ?></td>
                                    <td><?= $ingreso['estudiante_nombres'] ?> <?= $ingreso['estudiante_apellidos'] ?></td>
                                    <td><?= $ingreso['estudiante_cedula'] ?></td>
                                    <td><?= $ingreso['convenio_nombre'] ?></td>
                                    <td><?= "$" . number_format($ingreso['valor'], 0, ',', '.') ?></td>
                                    <td><?= $ingreso['motivo_ingreso'] ?></td>
                                    <td><?= $ingreso['tipo_ingreso'] ?></td>
                                    <td><?= $ingreso['fecha'] ?></td>
                                    <td><?= $ingreso['numero_recibo'] ?></td>
                                    <td><?= $ingreso['observaciones'] ?></td>
                                    <td>
                                        <a href="<?= $routes['pdf_generar_recibo_pago'] ?><?= $ingreso['id'] ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
                                            <i class="ti ti-printer"></i> Recibo
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= $routes['ingresos_edit']; ?><?= $ingreso['id'] ?>" class="btn btn-link btn-sm text-warning" title="Editar">
                                            <i class="ti ti-edit"></i>
                                        </a>
                                        <a href="#" data-url="<?= $routes['ingresos_delete'] . $ingreso['id'] ?>" class="btn btn-link btn-sm text-danger eliminar-ingreso" title="Eliminar">
                                            <i class="ti ti-trash"></i>
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

<!-- Script para confirmar eliminación -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.eliminar-ingreso').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.dataset.url;
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = url;
                    }
                });
            });
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
    <?php if ($_SESSION['rol_nombre'] == 'AUDITOR') : ?>
        $('#tablaIngresos').DataTable({
            dom: 'frtip', // quitamos la "B" para no mostrar botones
            columnDefs: [{
                targets: 0, // Ajusta según la posición real de la columna de fecha
                type: 'int'
            }],
            order: [
                [0, 'desc']
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
    <?php else : ?>
        $('#tablaIngresos').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    className: 'btn btn-success',

                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                        format: {
                            body: function(data, row, column, node) {

                                // 🔹 Columna VALOR (ajusta el índice si cambia)
                                if (column === 7) {

                                    // Limpia HTML
                                    const cleaned = $('<div>').html(data).text();

                                    // Quita formato monetario
                                    const numericValue = cleaned
                                        .replace(/\./g, '')
                                        .replace(/\$/g, '')
                                        .replace(/\s/g, '')
                                        .trim();

                                    // Retorna número entero real
                                    return numericValue ? parseInt(numericValue, 10) : 0;
                                }

                                return $('<div>').html(data).text();
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
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
                    }
                }
            ],
            columnDefs: [{
                targets: 0, // Suponiendo que la columna "Fecha" es la primera
                type: 'int' // Indica que esta columna debe tratarse como fecha
            }],
            order: [
                [0, 'desc']
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
    <?php endif; ?>
</script>

<script>
    function redirigirFiltro() {
        const fi = document.getElementById('fecha_inicial').value;
        const ff = document.getElementById('fecha_final').value;

        if (fi && ff) {
            window.location.href = `/mngtAFsdx48/${fi}|${ff}`;
            return false;
        }

        return true;
    }
</script>

<script src="../assets/js/helpers/token.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const form = document.getElementById('form-filtros');

        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // ❌ evita el envío tradicional GET con ?fecha_inicial

                const fi = document.getElementById('fecha_inicial').value;
                const ff = document.getElementById('fecha_final').value;

                if (!fi || !ff) return;

                if (fi > ff) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Rango de fechas inválido',
                        text: 'La fecha inicial no puede ser posterior a la fecha final.',
                        confirmButtonText: 'Entendido'
                    });
                    return;
                }

                const filtros = {
                    fecha_inicial: fi,
                    fecha_final: ff,
                    pagina: 1,
                    orden: 'fecha_desc'
                };

                const token = generarTokenURL(filtros); // Usa tu helper
                window.location.href = `/mngtAFsdx48/${token}`;
            });
        }
    });
</script>