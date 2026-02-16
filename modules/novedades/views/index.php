<?php $routes = include '../config/Routes.php'; ?>
<?php include_once '../shared/utils/AjustarImagen.php'; ?>
<?php include_once '../shared/utils/InsertarSaltosDeLinea.php'; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Novedades</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header" style="background-color:rgb(245, 235, 156); color: #333;">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Novedades, clases prácticas</h5>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-striped data-table" id="tablaNovedades">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Fecha novedad</th>
                                <th>Estudiante</th>
                                <th>Documento</th>
                                <th>Clase</th>
                                <th>Instructor</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Horas</th>
                                <th>Observaciones</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($novedades as $novedad) : ?>
                                <tr>
                                    <td><?php if (!empty($novedad['estudiante_foto'])) : ?>
                                            <?php
                                            $rutaImagen = "../files/fotos_estudiantes/" . $novedad['estudiante_foto'];
                                            list($imgWidth, $imgHeight) = ajustarImagen($rutaImagen, 60, 60);
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
                                    <td>
                                        <?php
                                        date_default_timezone_set('America/Bogota'); // Establecer zona horaria
                                        $fechaOriginal = $novedad['tiempo'];
                                        $fechaFormateada = date('Y-m-d H:i', strtotime($fechaOriginal));
                                        echo htmlspecialchars($fechaFormateada);
                                        ?>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($novedad['estudiante_nombre'] . ' ' . $novedad['estudiante_apellido']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($novedad['numero_documento']); ?></td>
                                    <td><?php echo strtoupper(insertarSaltosDeLinea($novedad['clase_nombre'], 6)); ?></td>
                                    <td><?php echo htmlspecialchars($novedad['instructor_nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($novedad['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars($novedad['hora_inicio'] . ' - ' . $novedad['hora_fin']); ?></td>
                                    <td>
                                        <?php
                                        $horaInicio = new DateTime($novedad['hora_inicio']);
                                        $horaFin = new DateTime($novedad['hora_fin']);
                                        $intervalo = $horaInicio->diff($horaFin);
                                        echo $intervalo->h + ($intervalo->i / 60); // horas + minutos convertidos a fracción
                                        ?>
                                    </td>
                                    <td><?php echo insertarSaltosDeLinea($novedad['observaciones'], 6); ?></td>
                                    <td>
                                        <span class="badge <?php echo ($novedad['novedad_estado'] == 'activa') ? 'bg-danger' : 'bg-success'; ?>">
                                            <?php echo strtoupper($novedad['novedad_estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#verNovedadModal"
                                            onclick="cargarNovedad(<?php echo htmlspecialchars(json_encode($novedad)); ?>)">
                                            <i class="fas fa-eye"></i>
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

<!-- Modal para ver novedad -->
<div class="modal fade" id="verNovedadModal" tabindex="-1" aria-labelledby="verNovedadLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="verNovedadLabel">Detalles de la Novedad</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p><strong>ESTUDIANTE:</strong> <span id="novedadEstudiante"></span></p>
                <p><strong>INSTRUCTOR:</strong> <span id="novedadInstructor"></span></p>
                <p><strong>VEHÍCULO:</strong> <span id="novedadVehiculo"></span></p>
                <p><strong>CLASE:</strong> <span id="novedadClase"></span></p>
                <p><strong>FECHA:</strong> <span id="novedadFecha"></span></p>
                <p><strong>HORARIO:</strong> <span id="novedadHorario"></span></p>
                <p><strong>OBSERVACIONES:</strong> <textarea id="novedadObservaciones" class="form-control"></textarea></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" onclick="finalizarNovedad()">Finalizar Novedad</button>
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

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Configuración de DataTables -->
<script>
    $(document).ready(function() {
        $('#tablaNovedades').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-success', // Estilo Bootstrap
                title: 'Listado de novedades, clases prácticas ',
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
                [0, "desc"]
            ],
            pageLength: 30
        });
    });
</script>

<script>
    let novedadIdSeleccionada = null;

    function cargarNovedad(novedad) {
        novedadIdSeleccionada = novedad.id;
        document.getElementById('novedadEstudiante').textContent = novedad.estudiante_nombre + ' ' + novedad.estudiante_apellido;
        document.getElementById('novedadInstructor').textContent = (novedad.instructor_nombre + ' ' + novedad.instructor_apellido).toUpperCase();
        document.getElementById('novedadVehiculo').textContent = (novedad.vehiculo_placa || 'No asignado').toUpperCase();
        document.getElementById('novedadClase').textContent = novedad.clase_nombre;
        document.getElementById('novedadFecha').textContent = novedad.fecha;
        document.getElementById('novedadHorario').textContent = novedad.hora_inicio + ' - ' + novedad.hora_fin;
        document.getElementById('novedadObservaciones').value = novedad.observaciones;
    }

    function finalizarNovedad() {
        if (!novedadIdSeleccionada) {
            Swal.fire({
                icon: 'warning',
                title: 'Atención',
                text: 'No se ha seleccionado una novedad.'
            });
            return;
        }

        let observaciones = document.getElementById('novedadObservaciones').value;

        fetch('/d3E4f5G6Hj/', { // Asegúrate de que esta es la URL correcta
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    novedad_id: novedadIdSeleccionada,
                    observaciones: observaciones
                })
            })
            .then(response => response.text()) // Mostrar la respuesta en texto para debug
            .then(text => {
                console.log("Respuesta del servidor:", text); // Ver respuesta en consola
                return JSON.parse(text); // Convertir a JSON después de verificar
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: 'Novedad finalizada correctamente.'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error al finalizar la novedad: ' + data.message
                    });
                }
            })
            .catch(error => {
                console.error("Error en la solicitud:", error);
            });
    }
</script>