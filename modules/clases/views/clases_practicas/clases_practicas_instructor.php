<?php
if (!function_exists('e')) {
    function e($v)
    {
        return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
if (!function_exists('up')) {
    function up($v)
    {
        $s = (string)($v ?? '');
        return function_exists('mb_strtoupper') ? mb_strtoupper($s, 'UTF-8') : strtoupper($s);
    }
}
?>
<?php include_once '../shared/utils/ObtenerClaseColor.php'; ?>
<?php include_once '../shared/utils/InsertarSaltosDeLinea.php'; ?>

<style>
    .btn-responsive {
        width: 100%;
        /* Ocupa todo el ancho en móvil */
    }

    @media (min-width: 768px) {
        .btn-responsive {
            width: auto;
            /* En pantallas grandes, ocupa solo el tamaño del contenido */
        }
    }
</style>

<?php $routes = include '../config/Routes.php'; ?>
<?php include '../shared/utils/AjustarImagen.php' ?>

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

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page"> Clases prácticas</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="container mt-3">
    <div class="row justify-content-center">
        <!-- Formulario con el mismo ancho que la tabla -->
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title text-center">Filtrar Clases Prácticas</h5>
                    <form id="filtroClasesForm" method="POST" action="/OdLNgQkmAi/">
                        <div class="row g-2">
                            <div class="col-6">
                                <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" id="fechaInicio" name="fecha_inicio" class="form-control"
                                    value="<?= isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-6">
                                <label for="fechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" id="fechaFin" name="fecha_fin" class="form-control"
                                    value="<?= isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-responsive">
                                    <i class="ti ti-search"></i> Filtrar Clases
                                </button>
                                <button type="button" id="filtrarHoy" class="btn btn-secondary btn-responsive">
                                    <i class="ti ti-calendar"></i> Hoy
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="ph-duotone ph-car"></i> Clases prácticas programadas.
                        <small class="text-muted">
                            <?php
                            if (isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) {
                                echo "Mostrando clases del " . date('d/m/Y', strtotime($_POST['fecha_inicio'])) .
                                    " al " . date('d/m/Y', strtotime($_POST['fecha_fin']));
                            } else {
                                echo "Mostrando todas las clases";
                            }
                            ?>
                        </small>
                    </h5>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped data-table" id="tablaClasesInstructor">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Clase</th>
                                    <th>Programa</th>
                                    <th>Vehículo</th>
                                    <th>Foto</th>
                                    <th>Alumno</th>
                                    <th>Documento</th>
                                    <th>Teléfono</th>
                                    <th>Estado</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($clases)): ?>
                                    <?php foreach ($clases as $clase): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($clase['fecha']) ?></td>
                                            <td><?= substr($clase['hora_inicio'], 0, 5) ?></td>
                                            <td><?= substr($clase['hora_fin'], 0, 5) ?></td>
                                            <td><?= insertarSaltosDeLinea(htmlspecialchars($clase['clase_nombre']), 2) ?></td>
                                            <td><?= htmlspecialchars(strtoupper($clase['programa_nombre'])) ?></td>
                                            <td>
                                                <?= htmlspecialchars(strtoupper($clase['vehiculo_placa'])) ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($clase['estudiante_foto'])) : ?>
                                                    <img src="/files/fotos_estudiantes/<?php echo $clase['estudiante_foto']; ?>"
                                                        alt="<?php echo htmlspecialchars(strtoupper($clase['estudiante_nombre'])); ?>"
                                                        class="img-thumbnail mt-2 wid-40"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#fotoModal"
                                                        onclick="verFotoModal(this)">
                                                <?php else : ?>
                                                    <img src="/files/fotos_estudiantes/img-defecto-estudiante.webp"
                                                        alt="<?php echo htmlspecialchars(strtoupper($clase['estudiante_nombre'])); ?>"
                                                        class="img-thumbnail mt-2 wid-40"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#fotoModal"
                                                        onclick="verFotoModal(this)">
                                                <?php endif; ?>

                                            </td>
                                            <td>
                                                <?= insertarSaltosDeLinea(e(up($clase['estudiante_nombre'] ?? '')), 2) ?><br>
                                            </td>

                                            <td>
                                                <?= e($clase['numero_documento'] ?? '') ?><br>
                                            </td>

                                            <td>
                                                <?= e($clase['telefono'] ?? '') ?><br>
                                            </td>


                                            <td>
                                                <?php list($color, $nombreEstado) = obtenerClaseEstado($clase['estado_id'], $clase['fecha'], $clase['hora_inicio'], $clase['hora_fin']); ?>
                                                <span class="badge <?= $color ?>">
                                                    <?= htmlspecialchars($nombreEstado) ?>
                                                </span>
                                            </td>

                                            <td>
                                                <?php if ($clase['clase_terminada'] && empty($clase['instructor_fecha_calificacion'])): ?>
                                                    <a href="<?= $routes['clases_practicas_instructor_calificar'] ?><?= $clase['clase_id'] ?>" class="btn btn-primary btn-sm">
                                                        Gestionar
                                                    </a>
                                                <?php elseif ($clase['clase_terminada'] && !empty($clase['instructor_fecha_calificacion'])): ?>
                                                    <button class="btn btn-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#calificacionModal<?= $clase['clase_id'] ?>">
                                                        Ver Calificación
                                                    </button>

                                                    <div class="modal fade" id="calificacionModal<?= $clase['clase_id'] ?>"
                                                        tabindex="-1"
                                                        aria-labelledby="modalLabel<?= $clase['clase_id'] ?>"
                                                        aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="modalLabel<?= $clase['clase_id'] ?>">Calificación de la Clase</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p><strong>Calificación:</strong>
                                                                    <div id="stars-container-<?= $clase['clase_id'] ?>">
                                                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                            <i class="fa<?= $i <= $clase['instructor_calificacion'] ? 's' : 'r' ?> fa-star text-warning"></i>
                                                                        <?php endfor; ?>
                                                                    </div>
                                                                    <?= htmlspecialchars($clase['instructor_calificacion']) ?> estrellas</p>
                                                                    <p><strong>Fecha de Calificación:</strong><br>
                                                                        <?= !empty($clase['instructor_fecha_calificacion']) ? date('d/m/Y H:i', strtotime($clase['instructor_fecha_calificacion'])) : 'Sin calificación' ?>
                                                                    </p>
                                                                    <p><strong>Observaciones:</strong><br> <?= htmlspecialchars($clase['instructor_observaciones']) ?: 'Sin observaciones' ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="badge bg-info text-white">Clase no finalizada</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center">No tienes clases programadas.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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

<script>
    function verFotoModal(imgElement) {
        var imgSrc = imgElement.src;
        document.getElementById('modalFoto').src = imgSrc;
    }
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>



<script>
    document.getElementById('filtrarHoy').addEventListener('click', function() {
        const fechaHoy = new Intl.DateTimeFormat('sv-SE', {
            timeZone: 'America/Bogota',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(new Date());

        // La fecha viene en formato YYYY-MM-DD gracias al locale 'sv-SE'
        document.getElementById('fechaInicio').value = fechaHoy;
        document.getElementById('fechaFin').value = fechaHoy;
        document.getElementById('filtroClasesForm').submit();
    });
</script>



<script>
    $(document).ready(function() {
        // Obtener el nombre del instructor desde PHP y convertirlo a mayúsculas
        var instructorNombre = "<?php echo strtoupper($_SESSION['instructor_nombres'] . ' ' . $_SESSION['instructor_apellidos']); ?>";

        $('#tablaClasesInstructor').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf"></i> Exportar a PDF',
                className: 'btn btn-danger', // Estilo Bootstrap
                title: 'LISTADO DE CALIFICACIONES, CLASES PRÁCTICAS - INSTRUCTOR: ' + instructorNombre,
                orientation: 'landscape', // Opcional: Puedes cambiar a 'portrait' para vertical
                pageSize: 'A4', // Opcional: Puedes cambiar a 'LEGAL', 'LETTER', etc.
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8] // Índices de las columnas que quieres exportar
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
                [0, "desc"],
                [1, "desc"]
            ],
            pageLength: 10 // ✅ Mostrar solo 10 registros por página
        });
    });
</script>