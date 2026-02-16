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
                    <li class="breadcrumb-item"><a href="<?php echo $routes['inspecciones_motos_index'] ?>"> Inspecciones Motos</a></li>
                    <li class="breadcrumb-item" aria-current="page"> Inspección Moto</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php /** [ breadcrumb ] end */  ?>


<div class="row">
    <div class="col-md-12">

        <form action="<?php echo $routes['inspecciones_motos_store'] ?>" method="post" enctype="multipart/form-data" onsubmit="desabilitaBoton()">

            <div class="card mb-4">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-white">
                        <?php if (isset($idInspeccion)): ?>
                            Datos de la inspección <?= htmlspecialchars($datosInspeccion[0]['fecha_hora']) ?>
                        <?php else: ?>
                            <i class="ti ti-plus text-white"></i> Crear inspección Moto<br>
                            <small>los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</small>
                        <?php endif; ?>
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row">

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label for="selectVehiculos" class="form-label">Seleccionar Moto:</label>
                                <?php if (isset($idInspeccion)) { ?>
                                    <input type="text" class="form-control" id="placa" name="placa"
                                        value="<?php echo htmlspecialchars($datosInspeccion[0]['placa']) ?>" readonly>
                                <?php } else { ?>
                                    <select class="custom-select" id="selectVehiculos" name="id_vehiculo">
                                        <?php foreach ($vehiculos as $item) { ?>
                                            <option value="<?php echo $item["vehiculos_id"] ?>"><?php echo $item["vehiculos_placa"] ?></option>
                                        <?php } ?>
                                    </select>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="mb-3">
                                <label for="kilometraje" class="form-label">Kilometraje</label>
                                <?php if (isset($idInspeccion)) { ?>
                                    <input type="number" class="custom-select" id="kilometraje" value="<?php echo $datosInspeccion[0]['kilometraje'] ?>" readonly>
                                <?php } else { ?>
                                    <input type="number" class="form-control" id="kilometraje" name="kilometraje" required>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="card mb-4">

                <div class="card-header text-white" style="background-color: #0074D9;">
                    <h5 class="mb-0 text-white">
                        Inspección exterior
                    </h5>

                    <small>
                        Comience observando el exterior de la motocicleta para detectar daños visibles,
                        como abolladuras, rasguños o desprendimientos de pintura. También, verifique el estado y
                        funcionamiento de lo siguiente:
                    </small>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('estado_general', 'Estado general de la motocicleta.', isset($idInspeccion) ? $datosInspeccion[0]['estado_general'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('estado_general', 'Estado general de la motocicleta.', isset($idInspeccion) ? $datosInspeccion[0]['estado_general'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('faros_delanteros', 'Faros delanteros.', isset($idInspeccion) ? $datosInspeccion[0]['faros_delanteros'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luces_traseras', 'Luces traseras.', isset($idInspeccion) ? $datosInspeccion[0]['luces_traseras'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luces_direccionales', 'Luces direccionales (intermitentes).', isset($idInspeccion) ? $datosInspeccion[0]['luces_direccionales'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luz_freno', 'Luz de freno trasero.', isset($idInspeccion) ? $datosInspeccion[0]['luz_freno'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('luz_placa', 'Luz de la placa.', isset($idInspeccion) ? $datosInspeccion[0]['luz_placa'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #6f42c1;">
                    <h5 class="mb-0 text-white">Inspección motocicleta</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('manillar', 'Manillar (ajuste y comodidad).', isset($idInspeccion) ? $datosInspeccion[0]['manillar'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('asiento', 'Asiento (ajuste y comodidad).', isset($idInspeccion) ? $datosInspeccion[0]['asiento'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('controles', 'Controles (frenos, acelerador, embrague) para verificar su funcionamiento suave.', isset($idInspeccion) ? $datosInspeccion[0]['controles'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('limpiar_ajustar', 'Limpiar y ajustar los espejos retrovisores.', isset($idInspeccion) ? $datosInspeccion[0]['limpiar_ajustar'] : null) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header" style="background-color: #17a2b8;">
                    <h5 class="mb-0 text-white">Arranque del Motor</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('ruidos_inusuales', 'Escuchar ruidos inusuales en el motor.', isset($idInspeccion) ? $datosInspeccion[0]['ruidos_inusuales'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('motor_arranca', 'Comprobar que el motor arranca sin problemas.', isset($idInspeccion) ? $datosInspeccion[0]['motor_arranca'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('interruptor_encendido', 'Asegúrese de que el interruptor de encendido y apagado funcione correctamente.', isset($idInspeccion) ? $datosInspeccion[0]['interruptor_encendido'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header" style="background-color: #20c997;">
                    <h5 class="mb-0 text-white">Componentes mecánicos</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('inspeccion_visual', 'Inspección visual del motor (sin fugas ni daños).', isset($idInspeccion) ? $datosInspeccion[0]['inspeccion_visual'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('sistema_frenos', 'Sistema de frenos (estado de las pastillas y discos en buen estado).', isset($idInspeccion) ? $datosInspeccion[0]['sistema_frenos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('sistema_suspension', 'Sistema de suspensión (horquillas y amortiguadores sin daños).', isset($idInspeccion) ? $datosInspeccion[0]['sistema_suspension'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('sistema_escape', 'Sistema de escape (sin fugas o daños).', isset($idInspeccion) ? $datosInspeccion[0]['sistema_escape'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('cadena_transmision', 'Cadena o correa de transmisión (tensión adecuada y lubricación).', isset($idInspeccion) ? $datosInspeccion[0]['cadena_transmision'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header" style="background-color: #138496;">
                    <h5 class="mb-0 text-white">Fluidos.</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('nivel_aceite', 'Verifique los niveles de aceite del motor (si es aplicable).', isset($idInspeccion) ? $datosInspeccion[0]['nivel_aceite'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('nivel_refrigerante', 'Nivel de refrigerante (si es una motocicleta refrigerada por líquido).', isset($idInspeccion) ? $datosInspeccion[0]['nivel_refrigerante'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('nivel_frenos', 'Verifique el nivel del líquido de frenos.', isset($idInspeccion) ? $datosInspeccion[0]['nivel_frenos'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('fugas_fluidos', 'Buscar posibles fugas de fluidos en el motor.', isset($idInspeccion) ? $datosInspeccion[0]['fugas_fluidos'] : null) ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header" style="background-color: #fd7e14;">
                    <h5 class="mb-0 text-white">Elementos de seguridad.</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <?php echo pintarCheckBox('cascos', 'Cascos', isset($idInspeccion) ? $datosInspeccion[0]['cascos'] : null) ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <?php echo pintarCheckBox('protecciones', 'Protecciones', isset($idInspeccion) ? $datosInspeccion[0]['protecciones'] : null) ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <?php echo pintarCheckBox('herramientas', 'Herramientas', isset($idInspeccion) ? $datosInspeccion[0]['herramientas'] : null) ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <?php echo pintarCheckBox('llave_encendido', 'Llave encendido', isset($idInspeccion) ? $datosInspeccion[0]['llave_encendido'] : null) ?>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="mb-3">
                                    <?php echo pintarCheckBox('caja_herramientas', 'Caja o kit de herramientas', isset($idInspeccion) ? $datosInspeccion[0]['caja_herramientas'] : null) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header" style="background-color: #000000;">
                    <h5 class="mb-0 text-white">Documentos de la motocicleta. Verificar vencimiento</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('tarjeta_servicio', 'Tarjeta de servicio (Si Aplica)', isset($idInspeccion) ? $datosInspeccion[0]['tarjeta_servicio'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('licencia_transito', 'Licencia de tránsito', isset($idInspeccion) ? $datosInspeccion[0]['licencia_transito'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('seguro_obligatorio', 'Seguro obligatorio vigente', isset($idInspeccion) ? $datosInspeccion[0]['seguro_obligatorio'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('tecnico_mecanica', 'Revisión técnico-mecánica', isset($idInspeccion) ? $datosInspeccion[0]['tecnico_mecanica'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header" style="background-color: #003366;">
                    <h5 class="mb-0 text-white">Documentos conductor, portar</h5>
                </div>
                <div class="card-body">

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('licencia_conduccion', 'Licencia de conducción', isset($idInspeccion) ? $datosInspeccion[0]['licencia_conduccion'] : null) ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="mb-3">
                                <?php echo pintarCheckBox('cedula_ciudadania', 'Cédula de ciudadanía', isset($idInspeccion) ? $datosInspeccion[0]['cedula_ciudadania'] : null) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-info">
                <div class="card-header" style="background-color: #004085;">
                    <h5 class="mb-0 text-white">Evidencias</h5>
                </div>

                <div class="card-body">
                    <div class="row">
                        <?php if (isset($idInspeccion) && isset($modoLectura) && $modoLectura): ?>
                            <!-- Modo solo lectura: mostrar imágenes -->
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <?php $foto = $datosInspeccion[0]['foto' . $i] ?? null; ?>
                                <div class="col-sm-4">
                                    <div class="foto-container mb-3 text-center">
                                        <?php if ($foto): ?>
                                            <img src="<?php echo "../files/fotos_inspecciones_motos/" . htmlspecialchars($foto); ?>" alt="Foto <?php echo $i; ?>" class="img-fluid rounded border">
                                        <?php else: ?>
                                            <span class="text-muted">Sin foto <?php echo $i; ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        <?php else: ?>
                            <!-- Modo formulario: cargar archivos -->
                            <?php for ($i = 1; $i <= 3; $i++): ?>
                                <div class="col-sm-4">
                                    <div class="mb-3">
                                        <label for="foto<?php echo $i; ?>" class="form-label">Foto <?php echo $i; ?></label>
                                        <input class="form-control" type="file" id="foto<?php echo $i; ?>" name="foto<?php echo $i; ?>">
                                    </div>
                                </div>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header text-white" style="background-color: #004085;">
                    <h5 class="mb-0 text-white">Observaciones</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observaciones" name="observaciones"><?php
                                                                                                echo isset($idInspeccion) ? $datosInspeccion[0]['observaciones'] : "";
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