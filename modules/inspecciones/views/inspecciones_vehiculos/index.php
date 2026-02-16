<?php
$routes = include '../config/Routes.php';
include_once '../shared/utils/InsertarSaltosDeLinea.php';
require_once '../config/DatabaseConfig.php';
$config = new DatabaseConfig();
$conn = $config->getConnection();
?>

<?php

function evaluarDiligenciamientoModal(PDO $conn, array $inspeccion, int $index)
{
    static $camposCheckbox = null;

    if ($camposCheckbox === null) {
        $camposCheckbox = [];

        $stmt = $conn->query("SHOW COLUMNS FROM inspeccion_vehiculos");
        $columnas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($columnas as $col) {
            if (preg_match('/^tinyint\(1\)$/i', $col['Type'])) {
                $camposCheckbox[] = $col['Field'];
            }
        }
    }

    $camposFaltantes = [];

    foreach ($camposCheckbox as $campo) {
        if (array_key_exists($campo, $inspeccion)) {
            if ($inspeccion[$campo] !== 1 && $inspeccion[$campo] !== '1') {
                $camposFaltantes[] = $campo;
            }
        }
    }

    if (count($camposFaltantes) === 0) {
        return '<span class="badge bg-success">Todo diligenciado</span>';
    }

    // ID único del modal por fila
    $modalId = "modalCamposFaltantes_$index";

    // Botón que abre el modal
    $html = '<button type="button" class="badge bg-warning text-dark border-0" data-bs-toggle="modal" data-bs-target="#' . $modalId . '">';
    $html .= count($camposFaltantes) . ' sin diligenciar';
    $html .= '</button>';

    // Contenido del modal
    $html .= '
    <div class="modal fade" id="' . $modalId . '" tabindex="-1" aria-labelledby="' . $modalId . 'Label" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="' . $modalId . 'Label">Campos no diligenciados</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <ul class="list-group">';

    foreach ($camposFaltantes as $campo) {
        $nombre = ucwords(str_replace('_', ' ', $campo));
        $html .= '<li class="list-group-item">' . htmlspecialchars($nombre) . '</li>';
    }

    $html .= '
            </ul>
          </div>
        </div>
      </div>
    </div>';

    return $html;
}

?>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['success_message']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php /** [ breadcrumb ] start */  ?>
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page"> Inspecciones Automóviles</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /** [ breadcrumb ] end */  ?>

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5><i class="fas fa-car"></i> Listado de inspecciones Automóviles.</h5>
                    <div>
                        <a href="<?php echo $routes['inspecciones_vehiculos_create'] ?>" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Crear nueva inspección Automóvil
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-3">
                <div class="table-responsive">
                    <?php
                    if (count($inspecciones) > 0) { ?>

                        <table id="tablaInspecciones" class="table table-striped data-table">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Automóvil</th>
                                    <th>Usuario</th>
                                    <th>Fecha inspección</th>
                                    <th>Kilometraje</th>
                                    <th>Observaciones</th>
                                    <th>Campos sin marcar</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php foreach ($inspecciones as $i => $item) { ?>

                                    <tr>
                                        <td><?php echo $item['id'] ?></td>
                                        <td><?php echo strtoupper($item['placa']) ?></td>
                                        <td><?php echo strtoupper($item['usuario_nombre']) ?></td>
                                        <td><?php echo $item['fecha_hora'] ?></td>
                                        <td><?php echo $item['kilometraje'] ?></td>
                                        <td><?php echo insertarSaltosDeLinea($item['observaciones'], 6); ?></td>
                                        <td><?php echo evaluarDiligenciamientoModal($conn, $item, $i); ?></td>
                                        <td><a href="<?php echo $routes['inspecciones_vehiculos_view'] . $item['id'] ?>" class="btn btn-primary">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php } ?>

                            </tbody>
                        </table>
                    <?php } else { ?>
                        <h4>No hay inspecciones registradas.</h4>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>






<!-- Formulario de Filtro de Fechas -->
<!-- <div class="card">
        <div class="card-body">
          <form method="GET" action="">
            <div class="row">
              <div class="col-md-4">
                <div class="form-group">
                  <label for="fecha_inicial">Fecha Inicial:</label>
                  <input type="date" id="fecha_inicial" name="fecha_inicial" class="form-control" required>
                </div>
              </div>
              <div class="col-md-4">
                <div class="form-group">
                  <label for="fecha_final">Fecha Final:</label>
                  <input type="date" id="fecha_final" name="fecha_final" class="form-control" required>
                </div>
              </div>
              <div class="col-md-4 align-self-end">
                <button type="submit" class="btn btn-primary">Filtrar</button>
              </div>
            </div>
          </form>
        </div>
      </div> -->



<script>
    $(function() {
        $("#inspecciones").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#inspecciones_wrapper .col-md-6:eq(0)');
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
        $('#tablaInspecciones').DataTable({
            dom: 'frtip', // quitamos la "B" de Buttons
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