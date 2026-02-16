<?php
$routes = include '../config/Routes.php';
include_once '../shared/utils/InsertarSaltosDeLinea.php';
require_once '../config/DatabaseConfig.php';
$config = new DatabaseConfig();
$conn = $config->getConnection();
?>

<?php

function evaluarDiligenciamientoModal(array $inspeccion, int $index)
{
    // Campos tipo checkbox que deben estar marcados (1)
    $camposCheckbox = [
        'estado_general',
        'faros_delanteros',
        'luces_traseras',
        'luces_direccionales',
        'luz_freno',
        'luz_placa',
        'manillar',
        'asiento',
        'controles',
        'limpiar_ajustar',
        'ruidos_inusuales',
        'motor_arranca',
        'interruptor_encendido',
        'inspeccion_visual',
        'sistema_frenos',
        'sistema_suspension',
        'sistema_escape',
        'cadena_transmision',
        'nivel_aceite',
        'nivel_refrigerante',
        'nivel_frenos',
        'fugas_fluidos',
        'cascos',
        'protecciones',
        'herramientas',
        'llave_encendido',
        'caja_herramientas',
        'tarjeta_servicio',
        'licencia_transito',
        'seguro_obligatorio',
        'tecnico_mecanica',
        'licencia_conduccion',
        'cedula_ciudadania'
    ];

    // Opcional: nombres más amigables para mostrar en el modal
    $nombresAmigables = [
        'estado_general' => 'Estado general de la motocicleta',
        'faros_delanteros' => 'Faros delanteros',
        'luces_traseras' => 'Luces traseras',
        'luces_direccionales' => 'Luces direccionales (intermitentes)',
        'luz_freno' => 'Luz de freno trasero',
        'luz_placa' => 'Luz de la placa',
        'manillar' => 'Manillar',
        'asiento' => 'Asiento',
        'controles' => 'Controles',
        'limpiar_ajustar' => 'Limpiar y ajustar',
        'ruidos_inusuales' => 'Ruidos inusuales',
        'motor_arranca' => 'Motor arranca',
        'interruptor_encendido' => 'Interruptor de encendido',
        'inspeccion_visual' => 'Inspección visual',
        'sistema_frenos' => 'Sistema de frenos',
        'sistema_suspension' => 'Sistema de suspensión',
        'sistema_escape' => 'Sistema de escape',
        'cadena_transmision' => 'Cadena de transmisión',
        'nivel_aceite' => 'Nivel de aceite',
        'nivel_refrigerante' => 'Nivel de refrigerante',
        'nivel_frenos' => 'Nivel de frenos',
        'fugas_fluidos' => 'Fugas de fluidos',
        'cascos' => 'Cascos',
        'protecciones' => 'Protecciones',
        'herramientas' => 'Herramientas',
        'llave_encendido' => 'Llave de encendido',
        'caja_herramientas' => 'Caja de herramientas',
        'tarjeta_servicio' => 'Tarjeta de servicio',
        'licencia_transito' => 'Licencia de tránsito',
        'seguro_obligatorio' => 'Seguro obligatorio',
        'tecnico_mecanica' => 'Técnico en mecánica',
        'licencia_conduccion' => 'Licencia de conducción',
        'cedula_ciudadania' => 'Cédula de ciudadanía'
    ];

    $camposFaltantes = [];

    foreach ($camposCheckbox as $campo) {
        if (!isset($inspeccion[$campo]) || $inspeccion[$campo] != 1) {
            $camposFaltantes[] = $campo;
        }
    }

    if (count($camposFaltantes) === 0) {
        return '<span class="badge bg-success">Todo diligenciado</span>';
    }

    // ID único del modal por fila
    $modalId = "modalCamposFaltantes_$index";

    // Botón para abrir el modal
    $html = '<button type="button" class="badge bg-warning text-dark border-0" data-bs-toggle="modal" data-bs-target="#' . $modalId . '">';
    $html .= count($camposFaltantes) . ' sin diligenciar';
    $html .= '</button>';

    // Modal con lista de campos
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
        $nombre = $nombresAmigables[$campo] ?? ucwords(str_replace('_', ' ', $campo));
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


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="ph-duotone ph-funnel me-1"></i> Filtro de Inspecciones de Motos</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo $routes['inspecciones_motos_index'] ?>" class="row g-3 mb-4">
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

<div class="row">
    <div class="col-12">
        <div class="card border-primary">

            <div class="card-header bg-primary text-white">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5><i class="fas fa-motorcycle"></i> Listado de inspecciones Motocicletas.</h5>
                    <div>
                        <a href="<?php echo $routes['inspecciones_motos_create'] ?>" class="btn btn-light">
                            <i class="fas fa-motorcycle me-1"></i> Crear nueva inspección Motocicleta
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <?php if (empty($inspecciones)) : ?>
                    <div class="alert alert-info">No hay inspecciones registradas.</div>
                <?php else : ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>MOTO</th>
                                    <th>USUARIO</th>
                                    <th>FECHA INSPECCION</th>
                                    <th>KILOMETRAJE</th>
                                    <th>OBSERVACIONES</th>
                                    <th>CAMPOS SIN MARCAR</th>
                                    <th> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($inspecciones as $i => $inspeccion) : ?>
                                    <tr>
                                        <td><?= htmlspecialchars($inspeccion['id']) ?></td>
                                        <td><?= htmlspecialchars($inspeccion['placa']) ?></td>
                                        <td><?= htmlspecialchars($inspeccion['usuario_nombre']) ?></td>
                                        <td><?= date('Y-m-d H:i', strtotime($inspeccion['fecha_hora'])) ?></td>
                                        <td><?= htmlspecialchars($inspeccion['kilometraje']) ?> km</td>
                                        <td><?php echo insertarSaltosDeLinea($inspeccion['observaciones'], 6); ?></td>
                                        <td><?php echo evaluarDiligenciamientoModal($inspeccion, $i); ?></td>
                                        <td>
                                            <a href="<?php echo $routes['inspecciones_motos_view'] . $inspeccion['id'] ?>" class="btn btn-primary">
                                                <i class="fas fa-eye"></i> Ver
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>