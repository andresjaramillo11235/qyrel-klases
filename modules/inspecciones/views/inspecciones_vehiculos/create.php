<?php $routes = include '../config/Routes.php'; ?>

<style>
    /* Estilos para los contenedores de las fotos */
    .foto-container {
        display: inline-block;
        width: 300px;
        /* Ancho fijo de cada contenedor */
        margin-right: 10px;
        /* Espacio entre las columnas */
    }

    /* Estilos para las imágenes */
    .foto-container img {
        max-width: 100%;
        /* Asegura que las imágenes no sean más anchas que su contenedor */
        height: auto;
        /* Mantiene la proporción de aspecto de las imágenes */
    }
</style>

<?php
function pintarCheckBox($id, $label, $valor = null)
{
    $htm = '<div class="custom-control custom-checkbox">';
    $htm .= '<input class="custom-control-input custom-control-input-success" type="checkbox" id="' . $id . '" name="' . $id . '" ' . ($valor == 1 ? "checked disabled" : "") . ' onclick="mostrarConforme(this)">&nbsp;&nbsp;';
    $htm .= '<label class="custom-control-label" style="margin-right: 50px" for="' . $id . '"> <strong id="' . $id . '_label">' . $label . '</strong> </label>';
    $htm .= '</div>';
    $htm .= '<script>
                function mostrarConforme(checkbox) {
                    var labelId = checkbox.id + "_label";
                    var label = document.getElementById(labelId);

                    if (checkbox.checked) {
                        if (!label.innerHTML.includes("Conforme. ")) {
                            label.innerHTML = "Conforme. " + label.innerHTML;
                        }
                        label.classList.add("text-primary"); // Aplica color azul
                    } else {
                        label.innerHTML = label.innerHTML.replace("Conforme. ", "");
                        label.classList.remove("text-primary"); // Corrige aquí
                    }
                }
            </script>
            ';
    return $htm;
}
?>

<?php /** [ breadcrumb ] start */  ?>
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $routes['inspecciones_vehiculos_index'] ?>"> Inspecciones Automóviles</a></li>
                    <li class="breadcrumb-item" aria-current="page"> Inspección Automóvil</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /** [ breadcrumb ] end */  ?>

<div class="row">
    <div class="col-md-12">

        <form action="<?php echo $routes['inspecciones_vehiculos_store'] ?>" method="post" enctype="multipart/form-data" onsubmit="desabilitaBoton()">

            <div class="card">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <?php if (isset($inspeccion)): ?>
                            Datos de la inspección <?= htmlspecialchars($inspeccion['fecha_hora']) ?>
                        <?php else: ?>
                            <i class="ti ti-plus text-white"></i> Crear inspección Automóvil<br>
                            <small>los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</small>
                        <?php endif; ?>
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label for="selectVehiculos" class="form-label"><i class="ph-duotone ph-asterisk"></i> Seleccionar Automóvil: </label>
                                <?php if (isset($inspeccion)) { ?>
                                    <input type="text" class="form-control" id="placa" name="placa" value="<?php echo strtoupper($inspeccion['placa']) ?>" readonly>
                                <?php } else { ?>
                                    <select class="custom-select" id="selectVehiculos" name="id_vehiculo">
                                        <?php foreach ($vehiculos as $item) { ?>
                                            <option value="<?php echo $item["vehiculos_id"] ?>">
                                                <?php echo $item["vehiculos_placa"] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Kilometraje -->
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label for="kilometraje" class="form-label">Kilometraje</label>
                                <?php if (isset($inspeccion)) { ?>
                                    <input type="number" class="custom-select" id="kilometraje" value="<?php echo $inspeccion['kilometraje'] ?>" readonly>
                                <?php } else { ?>
                                    <input type="number" class="form-control" id="kilometraje" name="kilometraje" required>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </div><!-- /.card -->


            <div class="card">
                <div class="card-header text-white" style="background-color: #0074D9;">
                    <h5 class="mb-0 text-white">
                        Inspección exterior
                    </h5>

                    <small>
                        Comience observando el exterior del automóvil para detectar daños visibles,
                        como abolladuras, rasguños o desprendimientos de pintura. También, verifique el estado y
                        funcionamiento de lo siguiente:
                    </small>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php
                                if (isset($inspeccion)) {
                                    echo pintarCheckBox('estado_carroceria', ' Estado de la carrocería.', $inspeccion['estado_carroceria']);
                                } else {
                                    echo pintarCheckBox('estado_carroceria', ' Estado de la carrocería.');
                                }
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php
                                if (isset($inspeccion)) {
                                    echo pintarCheckBox('faros_delanteros', 'Faros delanteros.', $inspeccion['faros_delanteros']);
                                } else {
                                    echo pintarCheckBox('faros_delanteros', 'Faros delanteros.');
                                }
                                ?>
                            </div>
                        </div>

                            <div class="col-sm-6">
                                <div class="mb-3">
                                <?php echo pintarCheckBox('luces_traseras', 'Luces traseras.', isset($inspeccion) ? $inspeccion['luces_traseras'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luces_freno', 'Luces de freno.', isset($inspeccion) ? $inspeccion['luces_freno'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luces_direccionales', 'Luces direccionales.', isset($inspeccion) ? $inspeccion['luces_direccionales'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luces_reversa', 'Luces de reversa.', isset($inspeccion) ? $inspeccion['luces_reversa'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luces_parqueo', 'Luces de Parqueo.', isset($inspeccion) ? $inspeccion['luces_parqueo'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luces_placa', 'Luces de la placa.', isset($inspeccion) ? $inspeccion['luces_placa'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('espejos_retrovisores', 'Espejos retrovisores (ajuste y limpieza).', isset($inspeccion) ? $inspeccion['espejos_retrovisores'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('parabrisas', 'Parabrisas (sin daños, limpio y sin obstrucciones).', isset($inspeccion) ? $inspeccion['parabrisas'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('ventanas_laterales', 'Limpieza de ventanas laterales.', isset($inspeccion) ? $inspeccion['ventanas_laterales'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('llantas', 'Estado de las llantas (desgaste, cortes, protuberancias, presión de aire adecuada).', isset($inspeccion) ? $inspeccion['llantas'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('tapa_tanque', 'Tapa del tanque de combustible (si está bien cerrada).', isset($inspeccion) ? $inspeccion['tapa_tanque'] : null) ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('alineacion_ruedas', 'Alineación de las ruedas.', isset($inspeccion) ? $inspeccion['alineacion_ruedas'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div><!-- /.card-body -->
            </div><!-- /.card -->


            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #6f42c1;">
                    <h5 class="mb-0 text-white">Inspección del Interior del Vehículo</h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('cinturones_seguridad', 'Cinturones de seguridad (sin daños y funcionando correctamente).', isset($inspeccion) ? $inspeccion['cinturones_seguridad'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('ajuste_asientos', 'Ajuste de los asientos y espejos (para una buena visibilidad).', isset($inspeccion) ? $inspeccion['ajuste_asientos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('ajuste_espejos', 'Limpiar y ajustar los espejos interiores y exteriores.', isset($inspeccion) ? $inspeccion['ajuste_espejos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('panel_instrumentos', 'Panel de instrumentos (verificación de luces de advertencia y errores).', isset($inspeccion) ? $inspeccion['panel_instrumentos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('freno_estacionamiento', 'Funcionamiento del freno de estacionamiento.', isset($inspeccion) ? $inspeccion['freno_estacionamiento'] : null) ?>
                            </div>
                        </div>

                    </div>
                </div>
            </div><!-- /.card -->


            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #17a2b8;">
                    Arranque del Motor
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('ruidos_motor', 'Escuchar ruidos inusuales en el motor.', isset($inspeccion) ? $inspeccion['ruidos_motor'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('arranque_motor', 'Comprobar que el motor arranca sin problemas.', isset($inspeccion) ? $inspeccion['arranque_motor'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('palanca_cambios', 'Asegúrese de que la palanca de cambios (si es automática) esté en la posición adecuada.', isset($inspeccion) ? $inspeccion['palanca_cambios'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.card -->

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #20c997;">
                    Componentes Mecánicos
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('inspeccion_motor', 'Inspección visual del motor (sin fugas ni daños).', isset($inspeccion) ? $inspeccion['inspeccion_motor'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('transmision', 'Transmisión (sin fugas ni daños).', isset($inspeccion) ? $inspeccion['transmision'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('frenos', 'Sistema de frenos (Altura pedal de freno y fugas).', isset($inspeccion) ? $inspeccion['frenos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('direccion', 'Sistema de dirección (sin holguras excesivas).', isset($inspeccion) ? $inspeccion['direccion'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('escape', 'Sistema de escape (sin fugas o daños).', isset($inspeccion) ? $inspeccion['escape'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('correas_motor', 'Correas del motor (sin daños ni desgaste excesivo).', isset($inspeccion) ? $inspeccion['correas_motor'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('estado_bateria', 'Estado Batería "no sulfatada".', isset($inspeccion) ? $inspeccion['estado_bateria'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.card -->

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #138496;">
                    Fluidos del Vehículo
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('aceite_motor', 'Verificación de los niveles de aceite del motor.', isset($inspeccion) ? $inspeccion['aceite_motor'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('refrigerante_radiador', 'Nivel de refrigerante del radiador.', isset($inspeccion) ? $inspeccion['refrigerante_radiador'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('liquido_frenos', 'Nivel del líquido de frenos.', isset($inspeccion) ? $inspeccion['liquido_frenos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('direccion_asistida', 'Nivel de líquido de dirección hidráulica "Si aplica".', isset($inspeccion) ? $inspeccion['direccion_asistida'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.card -->

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #fd7e14;">
                    Kit de Carretera
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('botiquin', 'Botiquín de primeros auxilios', isset($inspeccion) ? $inspeccion['botiquin'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('llanta_repuesto', 'Llanta de repuesto', isset($inspeccion) ? $inspeccion['llanta_repuesto'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('gato', 'Gato', isset($inspeccion) ? $inspeccion['gato'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('conos', 'Triángulos o conos', isset($inspeccion) ? $inspeccion['conos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('linterna', 'Linterna', isset($inspeccion) ? $inspeccion['linterna'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('extintor', 'Extintor', isset($inspeccion) ? $inspeccion['extintor'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('tacos', 'Tacos', isset($inspeccion) ? $inspeccion['tacos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('llave_cruz', 'Llave cruz', isset($inspeccion) ? $inspeccion['llave_cruz'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('kit_herramientas', 'Caja o kit de herramientas', isset($inspeccion) ? $inspeccion['kit_herramientas'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.card -->


            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    Documentos del automóvil - Vencimientos
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('tarjeta_servicio', 'Documentación Vigente', isset($inspeccion) ? $inspeccion['tarjeta_servicio'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('licencia_transito', 'Licencia de tránsito', isset($inspeccion) ? $inspeccion['licencia_transito'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('seguro_obligatorio', 'Seguro obligatorio vigente', isset($inspeccion) ? $inspeccion['seguro_obligatorio'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('tecnico_mecanica', 'Revisión técnico-mecánica', isset($inspeccion) ? $inspeccion['tecnico_mecanica'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('seguro', 'Seguro Contractual y Extracontractual “Si aplica”.', isset($inspeccion) ? $inspeccion['seguro'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('revision_preventiva', 'Revisión Preventiva “si aplica”.', isset($inspeccion) ? $inspeccion['revision_preventiva'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #6c757d;">
                    Documentos del Conductor (Portar)
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('licencia_conduccion', 'Licencia de conducción', isset($inspeccion) ? $inspeccion['licencia_conduccion'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('cedula_ciudadania', 'Cédula de ciudadanía', isset($inspeccion) ? $inspeccion['cedula_ciudadania'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.card -->

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #dc3545;">
                    Declaraciones del Conductor
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <?php echo pintarCheckBox(
                                    'declaracion_responsabilidad',
                                    'Realizo esta inspección bajo mi total responsabilidad y declaro que los ítems marcados serán con base en lo observado en el vehículo.',
                                    isset($inspeccion) ? $inspeccion['declaracion_responsabilidad'] : null
                                ) ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <?php echo pintarCheckBox(
                                    'declaracion_consumo',
                                    'Declaro el no consumo de sustancias psicoactivas y/o bebidas embriagantes.',
                                    isset($inspeccion) ? $inspeccion['declaracion_consumo'] : null
                                ) ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <?php echo pintarCheckBox(
                                    'declaracion_optimo',
                                    'Manifiesto que mi estado es óptimo para ejecutar la conducción del automóvil asignado.',
                                    isset($inspeccion) ? $inspeccion['declaracion_optimo'] : null
                                ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.card -->


            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #343a40;">
                    Evidencias Fotográficas
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label for="foto1" class="form-label">Foto 1</label>

                                <?php if (isset($inspeccion) && !empty($inspeccion['foto1'])): ?>
                                    <div class="mb-2">
                                        <img src="/files/fotos_inspecciones_vehiculos/<?php echo htmlspecialchars($inspeccion['foto1']); ?>" alt="Foto 1" class="img-fluid rounded border" style="max-width: 200px;">
                                    </div>
                                <?php else: ?>
                                    <input class="form-control" type="file" id="foto1" name="foto1" accept="image/*">
                                <?php endif; ?>
                            </div>
                        </div>


                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label for="foto2" class="form-label">Foto 2</label>

                                <?php if (isset($inspeccion) && !empty($inspeccion['foto2'])): ?>
                                    <div class="mb-2">
                                        <img src="/files/fotos_inspecciones_vehiculos/<?php echo htmlspecialchars($inspeccion['foto2']); ?>" alt="Foto 2" class="img-fluid rounded border" style="max-width: 200px;">
                                    </div>
                                <?php else: ?>
                                    <input class="form-control" type="file" id="foto2" name="foto2" accept="image/*">
                                <?php endif; ?>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="mb-3">
                                <label for="foto3" class="form-label">Foto 3</label>

                                <?php if (isset($inspeccion) && !empty($inspeccion['foto3'])): ?>
                                    <div class="mb-2">
                                        <img src="/files/fotos_inspecciones_vehiculos/<?php echo htmlspecialchars($inspeccion['foto3']); ?>" alt="Foto 3" class="img-fluid rounded border" style="max-width: 200px;">
                                    </div>
                                <?php else: ?>
                                    <input class="form-control" type="file" id="foto3" name="foto3" accept="image/*">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- /.card -->

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #004085;">
                    Observaciones
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones" rows="4"><?php
                                                                                                        echo isset($inspeccion) ? htmlspecialchars($inspeccion['observaciones']) : "";
                                                                                                        ?></textarea>
                    </div>
                </div>
            </div>

            <?php if (!isset($inspeccion)) { ?>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" id="boton-enviar">
                        <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span id="texto-boton">Enviar</span>
                    </button>
                </div>
                <br>
            <?php } ?>

        </form>

    </div>
</div>



<script>
    function desabilitaBoton() {
        const botonEnviar = document.getElementById("boton-enviar");
        const spinner = document.getElementById("spinner");
        const textoBoton = document.getElementById("texto-boton");

        // Deshabilitar el botón para evitar múltiples clics
        botonEnviar.disabled = true;

        // Mostrar el spinner y ocultar el texto del botón
        spinner.classList.remove('d-none');
        textoBoton.style.display = 'none';
    }
</script>