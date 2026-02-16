<?php $routes = include '../config/Routes.php'; ?>

<h2>Crear Nuevo Ingreso</h2>

<div class="row">
    <!-- Columna izquierda: Búsqueda y datos del estudiante -->
    <div class="col-md-5">
        <!-- Formulario de búsqueda de estudiantes -->
        <div class="mb-3">
            <label for="termino_busqueda" class="form-label">Buscar Estudiante</label>
            <input type="text" class="form-control" id="termino_busqueda" placeholder="Ingrese nombre o cédula del estudiante">
        </div>
        <div class="mb-3">
            <label for="selectEstudiante" class="form-label">Seleccionar Estudiante</label>
            <select class="form-select" id="selectEstudiante"></select>
        </div>

        <!-- Datos del estudiante, oculto hasta que se seleccione un estudiante -->
        <div id="datosEstudiante" style="display: none;">
            <h4>Datos del Estudiante</h4>
            <div id="detalleEstudiante">
                <!-- Aquí se mostrarán el nombre, cédula y foto del estudiante -->
            </div>
        </div>

        <div id="finanzasMatricula" class="mt-3"></div>

    </div>

    <!-- Columna derecha: Formulario de ingreso -->
    <div class="col-md-7">
        <!-- Formulario de ingreso, oculto hasta que se seleccione un estudiante -->
        <div id="formularioIngreso" style="display: none;">
            <h4>Datos del Ingreso</h4>
            <form id="formIngreso" action="<?php echo $routes['ingresos_store']; ?>" method="POST">

                <input type="hidden" id="matricula_id" name="matricula_id">

                <div class="mb-3">
                    <label for="valor" class="form-label">
                        Valor
                        <i class="fas fa-info-circle text-primary ms-1"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            title=""
                            id="tooltipValor">
                        </i>
                    </label>

                    <input type="text" class="form-control" id="valor" required>

                    <div class="form-text text-muted">
                        Saldo pendiente: <strong id="saldoDisponibleTexto">$0</strong>
                    </div>
                    <input type="hidden" id="valor_real" name="valor">
                </div>

                <div class="mb-3">
                    <label for="motivo_ingreso_id" class="form-label">Motivo</label>
                    <select id="motivo_ingreso_id" name="motivo_ingreso_id" class="form-select" required>
                        <?php foreach ($motivos as $motivo): ?>
                            <option value="<?php echo $motivo['id']; ?>"><?php echo $motivo['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="tipo_ingreso_id" class="form-label">Tipo</label>
                    <select id="tipo_ingreso_id" name="tipo_ingreso_id" class="form-select" required>
                        <?php foreach ($tipos as $tipo): ?>
                            <option value="<?php echo $tipo['id']; ?>"><?php echo $tipo['nombre']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="caja_id" class="form-label">Caja</label>
                    <select id="caja_id" name="caja_id" class="form-select" required>
                        <option value="">Seleccione una caja</option>
                        <?php foreach ($cajas as $caja): ?>
                            <option value="<?php echo $caja['id']; ?>">
                                <?php echo htmlspecialchars($caja['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="observaciones" class="form-label">Observaciones</label>
                    <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                </div>

                <?php
                date_default_timezone_set('America/Bogota');
                $fechaColombia = date('d/m/Y');
                ?>

                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="text" class="form-control" id="fecha" name="fecha" value="<?php echo $fechaColombia; ?>" readonly>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Ingreso</button>
            </form>
        </div>
    </div>
</div>

<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Incluir el script de búsqueda de estudiantes -->
<script src="../modules/financiero/views/js/busqueda_estudiante_ingresos.js"></script>

<!-- Incluir el script de validación de formulario -->
<script src="../modules/financiero/views/js/validar_ingreso.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let today = new Date().toISOString().split("T")[0]; // Obtiene la fecha en formato YYYY-MM-DD
        document.getElementById("fecha").value = today; // Asigna la fecha actual al input
    });
</script>

<script>
    let saldoDisponible = 0;

    $(document).on('change', '#selectMatricula', function() {
        const matriculaId = $(this).val();

        if (!matriculaId) return;

        $.post('/QWErtyuioP/', {
            matricula_id: matriculaId
        }, function(response) {
            if (response.success) {

                const valor = response.valor_matricula;
                const abonos = response.total_abonos;
                const saldo = response.saldo;

                saldoDisponible = response.saldo;

                $('#saldoDisponibleTexto').text(`$${saldoDisponible.toLocaleString()}`);

                // También mostrar info general
                $('#finanzasMatricula').html(`
                <p><strong>Valor matrícula:</strong> $${response.valor_matricula.toLocaleString()}</p>
                <p><strong>Total abonado:</strong> $${response.total_abonos.toLocaleString()}</p>
                <p><strong>Saldo:</strong> $${saldoDisponible.toLocaleString()}</p>
            `);

                actualizarTooltipFinanciero(valor, abonos, saldo);
            }
        }, 'json');
    });

    $(document).on('input', '#valor', function() {
        const valorIngresado = parseInt($(this).val().replace(/\D/g, '')); // Limpia y convierte a número

        if (isNaN(valorIngresado) || valorIngresado <= 0 || valorIngresado > saldoDisponible) {
            $(this).addClass('is-invalid');
            $('#btnEnviar').attr('disabled', true); // Desactiva el botón
        } else {
            $(this).removeClass('is-invalid');
            $('#btnEnviar').attr('disabled', false); // Reactiva el botón
        }
    });

    // Valida que el ingreso del monto sea mayor a 0
    $(document).ready(function() {
        $('#formIngreso').on('submit', function(e) {
            const valorReal = parseInt($('#valor_real').val());

            if (isNaN(valorReal) || valorReal <= 0) {
                e.preventDefault(); // Cancela el envío del formulario

                // Marcar visualmente como inválido
                $('#valor').addClass('is-invalid');

                // Opcional: mensaje personalizado (si usas bootstrap)
                if ($('#mensajeValorInvalido').length === 0) {
                    $('<div id="mensajeValorInvalido" class="invalid-feedback d-block">El valor debe ser mayor que 0.</div>').insertAfter('#valor');
                }

                return false;
            } else {
                $('#valor').removeClass('is-invalid');
                $('#mensajeValorInvalido').remove(); // Elimina mensaje si ya existe
            }
        });
    });




    // Validar cuando el usuario escribe en el campo de valor
    // $(document).on('input', '#valor', function() {
    //     const valorIngresado = parseInt($(this).val());

    //     if (valorIngresado > saldoDisponible) {
    //         $(this).addClass('is-invalid');
    //         $('#btnEnviar').attr('disabled', true); // Desactiva el botón
    //     } else {
    //         $(this).removeClass('is-invalid');
    //         $('#btnEnviar').attr('disabled', false); // Reactiva el botón
    //     }
    // });

    // Validación final con Swal al enviar el formulario
    $(document).on('submit', '#formularioIngreso', function(e) {
        const valor = parseInt($('#valor').val());

        if (valor > saldoDisponible) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Valor excedido',
                text: `El valor ingresado supera el saldo pendiente ($${saldoDisponible.toLocaleString()})`,
            });

            return false;
        }
    });

    // Función para formatear en formato moneda colombiana
    function formatearMonedaCOP(numero) {
        return numero.toLocaleString('es-CO', {
            style: 'currency',
            currency: 'COP',
            minimumFractionDigits: 0
        });
    }

    // Evento al salir del input de valor
    $(document).on('blur', '#valor', function() {
        let valorIngresado = parseInt($(this).val().replace(/\D/g, '')); // Eliminar todo excepto números

        if (!isNaN(valorIngresado)) {
            // Mostrar formato moneda
            $(this).val(formatearMonedaCOP(valorIngresado));

            // Guardar el valor real sin formato en el campo oculto
            $('#valor_real').val(valorIngresado);
        } else {
            $(this).val('');
            $('#valor_real').val('');
        }
    });

    // Validación con saldo también debe usar #valor_real
    $(document).on('input', '#valor', function() {
        let valorIngresado = parseInt($(this).val().replace(/\D/g, ''));
        if (valorIngresado > saldoDisponible) {
            $(this).addClass('is-invalid');
            $('#btnEnviar').attr('disabled', true);
        } else {
            $(this).removeClass('is-invalid');
            $('#btnEnviar').attr('disabled', false);
        }
    });

    // Validación final en submit
    $(document).on('submit', '#formularioIngreso', function(e) {
        let valor = parseInt($('#valor_real').val());

        if (valor > saldoDisponible) {
            e.preventDefault();

            Swal.fire({
                icon: 'warning',
                title: 'Valor excedido',
                text: `El valor ingresado supera el saldo pendiente ($${saldoDisponible.toLocaleString()})`,
            });

            return false;
        }
    });

    // Activar el tooltip con Bootstrap 5
    function actualizarTooltipFinanciero(matricula, abonos, saldo) {
        const tooltip = bootstrap.Tooltip.getInstance(document.getElementById('tooltipValor'));
        if (tooltip) tooltip.dispose(); // eliminar anterior

        const texto = `Valor matrícula: $${matricula.toLocaleString('es-CO')}\n` +
            `Total abonado: $${abonos.toLocaleString('es-CO')}\n` +
            `Saldo: $${saldo.toLocaleString('es-CO')}`;

        document.getElementById('tooltipValor').setAttribute('title', texto);

        new bootstrap.Tooltip(document.getElementById('tooltipValor'));
    }
</script>