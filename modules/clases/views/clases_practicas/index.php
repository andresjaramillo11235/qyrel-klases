<?php include '../shared/utils/AjustarImagen.php' ?>
<?php $routes = include '../config/Routes.php'; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Clases Prácticas</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php ## FORMULARIO DE FILTRO DE CLASES PRÁCTICAS 
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ph-duotone ph-funnel me-1"></i> Filtro de Clases Prácticas</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php $routes['clases_practicas_listado_admin'] ?>" class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha inicial:</label>
                        <input type="date" class="form-control" name="fecha_inicio" value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? '') ?>">
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha final:</label>
                        <input type="date" class="form-control" name="fecha_fin" value="<?= htmlspecialchars($_GET['fecha_fin'] ?? '') ?>">
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

<?php ## LISTADO DE CLASES PRÁCTICAS 
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ph-duotone ph-car"></i> Listado de Clases Prácticas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped data-table" id="tablaClasesPracticas">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Duración</th>
                                <th>Programa</th>
                                <th>Clase</th>
                                <th>Foto</th>
                                <th>Estudiante</th>
                                <th>Tipo Documento</th>
                                <th>Documento</th>
                                <th>Foto</th>
                                <th>Instructor</th>
                                <th>Foto</th>
                                <th>Vehículo</th>
                                <th>Matrícula</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clasesPracticas as $clase): ?>
                                <tr>
                                    <td><?= htmlspecialchars($clase['fecha']) ?></td>
                                    <td>
                                        <?php
                                        $datetimeInicio = new DateTime($clase['hora_inicio']);
                                        $datetimeFin = new DateTime($clase['hora_fin']);
                                        echo $datetimeInicio->format("H:i") . ' - ' . $datetimeFin->format("H:i");
                                        ?>
                                    </td>

                                    <td class="text-center">
                                        <?php
                                        $hi = $clase['hora_inicio'] ?? null;
                                        $hf = $clase['hora_fin'] ?? null;
                                        $duracion = '—';

                                        if ($hi && $hf) {
                                            // Soporta "H:i" y "H:i:s"
                                            $ini = DateTime::createFromFormat('H:i:s', $hi) ?: DateTime::createFromFormat('H:i', $hi);
                                            $fin = DateTime::createFromFormat('H:i:s', $hf) ?: DateTime::createFromFormat('H:i', $hf);

                                            if ($ini && $fin) {
                                                // Si la hora fin es menor/igual, asumimos que cruza medianoche
                                                if ($fin <= $ini) {
                                                    $fin->modify('+1 day');
                                                }

                                                $diff    = $ini->diff($fin);
                                                $minutes = ($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i;
                                                $hours   = $minutes / 60;

                                                // Si es entero, sin decimales; si no, hasta 2 decimales
                                                $duracion = (fmod($hours, 1.0) == 0.0)
                                                    ? (string) (int) $hours
                                                    : rtrim(rtrim(number_format($hours, 2, '.', ''), '0'), '.');
                                                $duracion .= ' horas';
                                            }
                                        }

                                        echo $duracion;
                                        ?>
                                    </td>




                                    <td><?= htmlspecialchars(strtoupper($clase['programa_nombre'])) ?></td>
                                    <td><?= htmlspecialchars(strtoupper($clase['clase_nombre'])) ?></td>
                                    <td>
                                        <?php
                                        $estudiantePhotoPath = "../files/fotos_estudiantes/" . $clase['estudiante_foto'];
                                        // Verificar si el nombre del archivo existe y si el archivo realmente está presente
                                        if (!empty($clase['estudiante_foto']) && file_exists($estudiantePhotoPath)) {
                                            list($width, $height) = ajustarImagen($estudiantePhotoPath, 50, 50);
                                            echo "<img src=\"$estudiantePhotoPath\" alt=\"Foto Estudiante\" style=\"width: {$width}px; height: {$height}px;\" class=\"img-thumbnail mt-2 wid-40\">";
                                        } else {
                                            // Si no hay foto o el archivo no existe, mostrar imagen por defecto
                                            echo "<img src=\"../assets/images/user/avatar-2.jpg\" alt=\"Sin foto\" style=\"width: 40px; height: 40px;\" class=\"img-thumbnail mt-2 wid-40\">";
                                        }
                                        ?>
                                    </td>

                                    <td><?= htmlspecialchars(strtoupper($clase['estudiante_nombre'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars(strtoupper($clase['tipo_documento'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars(strtoupper($clase['numero_documento'] ?? '')) ?></td>

                                    <td>
                                        <?php
                                        $instructorPhotoPath = "../files/fotos_instructores/" . $clase['instructor_foto'];

                                        // Verificar si el nombre del archivo existe y si el archivo realmente está presente
                                        if (!empty($clase['instructor_foto']) && file_exists($instructorPhotoPath)) {
                                            list($width, $height) = ajustarImagen($instructorPhotoPath, 50, 50);
                                            echo "<img src=\"$instructorPhotoPath\" alt=\"Foto Instructor\" style=\"width: {$width}px; height: {$height}px;\" class=\"img-thumbnail mt-2 wid-40\">";
                                        }
                                        ?>
                                    </td>

                                    <td><?= htmlspecialchars(strtoupper($clase['instructor_nombre'])) ?></td>

                                    <td>
                                        <?php
                                        $vehiculoPhotoPath = "../files/fotos_vehiculos/" . $clase['vehiculo_foto'];
                                        if (is_file($vehiculoPhotoPath)) {
                                            list($width, $height) = ajustarImagen($vehiculoPhotoPath, 50, 50);
                                            echo "<img src=\"$vehiculoPhotoPath\" alt=\"Foto Vehículo\" style=\"width: {$width}px; height: {$height}px;\" class=\"img-thumbnail mt-2 wid-40\">";
                                        }
                                        ?>
                                    </td>

                                    <td><?= htmlspecialchars(strtoupper($clase['vehiculo_placa'] ?? '')) ?></td>
                                    <td><?= htmlspecialchars($clase['matricula_id'] ?? '') ?></td>
                                    <td>
                                        <?php if (!empty($clase['estudiante_nombre'])): ?>
                                            <a href="<?= $routes['clases_practicas_detalle'] ?><?= $clase['clase_id'] ?>"
                                                class="avtar avtar-xs btn-link-secondary">
                                                <i class="ti ti-info-circle f-20"></i>
                                            </a>
                                        <?php endif; ?>
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

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/datatables-config.js"></script> -->


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
            $('#tablaClasesPracticas').DataTable({
                dom: 'frtip', // quitamos la "B" para eliminar botones
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
    <?php else : ?>
        $(document).ready(function() {
            $('#tablaClasesPracticas').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                    className: 'btn btn-success', // Estilo Bootstrap
                    title: 'Listado de clases prácticas ',
                    exportOptions: {
                        columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13] // Índices de las columnas que quieres exportar
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
                    [0, "desc"]
                ],

                // ✅ Mostrar 30 registros por defecto
                pageLength: 30
            });
        });
    <?php endif; ?>
</script>