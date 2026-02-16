<?php $routes = include '../config/Routes.php'; ?>

<style>
    input[type="text"],
    textarea {
        text-transform: uppercase;
    }
</style>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/vehiculos/">Vehículos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear Vehículo</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5><i class="ti ti-car"></i> Crear Nuevo Vehículo</h5>
    </div>
    <div class="card-body">
        <form action="<?= $routes['vehiculos_store'] ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row">

                <div class="col-md-6 mb-3">
                    <label for="placa" class="form-label">Placa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="placa" name="placa" required>
                    <div class="invalid-feedback" id="errorPlaca">La placa es obligatoria.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="numero_licencia" class="form-label">Número de Licencia</label>
                    <input type="text" class="form-control" id="numero_licencia" name="numero_licencia">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="modelo" class="form-label">Modelo <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="modelo" name="modelo" min="1900" max="<?= date('Y') ?>" required>
                    <div class="invalid-feedback">El modelo es obligatorio y debe estar entre 1900 y el año actual.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="fecha_matricula" class="form-label">Fecha de Matrícula</label>
                    <input type="date" class="form-control" id="fecha_matricula" name="fecha_matricula">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="vin" class="form-label">VIN</label>
                    <input type="text" class="form-control" id="vin" name="vin">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="cilindrada" class="form-label">Cilindrada</label>
                    <input type="text" class="form-control" id="cilindrada" name="cilindrada">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="capacidad" class="form-label">Capacidad</label>
                    <input type="text" class="form-control" id="capacidad" name="capacidad">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="carroceria" class="form-label">Carrocería</label>
                    <input type="text" class="form-control" id="carroceria" name="carroceria">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="numero_motor" class="form-label">Número de Motor</label>
                    <input type="text" class="form-control" id="numero_motor" name="numero_motor">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="numero_chasis" class="form-label">Número de Chasis</label>
                    <input type="text" class="form-control" id="numero_chasis" name="numero_chasis">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="numero_serie" class="form-label">Número de Serie</label>
                    <input type="text" class="form-control" id="numero_serie" name="numero_serie">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="propietario" class="form-label">Propietario</label>
                    <input type="text" class="form-control" id="propietario" name="propietario">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="identificacion" class="form-label">Identificación del Propietario</label>
                    <input type="text" class="form-control" id="identificacion" name="identificacion">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="id_traccar" class="form-label">ID GPS</label>
                    <input type="number" class="form-control" id="id_traccar" name="id_traccar">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="endpoint" class="form-label">Endpoint</label>
                    <input type="text" class="form-control" id="endpoint" name="endpoint">
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipo_vehiculo_id" class="form-label">Tipo de Vehículo <span class="text-danger">*</span></label>
                    <select class="form-control" id="tipo_vehiculo_id" name="tipo_vehiculo_id" required>
                        <option value="">Seleccione un tipo</option>
                        <?php foreach ($tiposVehiculo as $tipo): ?>
                            <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">Debe seleccionar un tipo de vehículo.</div>
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tipo_combustible_id" class="form-label">Tipo de Combustible</label>
                    <select class="form-control" id="tipo_combustible_id" name="tipo_combustible_id">
                        <option value="">Seleccione un tipo</option>
                        <?php foreach ($tiposCombustible as $combustible): ?>
                            <option value="<?= $combustible['id'] ?>"><?= htmlspecialchars($combustible['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Campo para cargar la foto -->
                <div class="col-md-6 mb-3">
                    <label for="foto" class="form-label">Foto del Vehículo</label>
                    <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                </div>

                <!-- Botones de acción -->
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-primary"><i class="ti ti-check"></i> Guardar</button>
                    <a href="/vehiculos/" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Script para validación -->
<script>
    (function() {
        'use strict';

        // Validación general de formularios
        window.addEventListener('load', function() {
            const forms = document.getElementsByClassName('needs-validation');

            // Iterar por cada formulario con needs-validation
            Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    // Verificar la validación nativa del formulario
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    // Verificar validaciones personalizadas (ejemplo: placa única)
                    const placaField = document.getElementById('placa');
                    if (placaField) {
                        verificarPlacaUnica(placaField.value).then(isUnique => {
                            if (!isUnique) {
                                event.preventDefault();
                                event.stopPropagation();
                                placaField.setCustomValidity('La placa ya existe.');
                                document.getElementById('errorPlaca').textContent = 'La placa ya está registrada.';
                                form.classList.add('was-validated');
                            } else {
                                placaField.setCustomValidity('');
                            }
                        });
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        }, false);

        // Función reutilizable para verificar unicidad de campo en el servidor
        async function verificarPlacaUnica(placa) {
            try {
                const response = await fetch('<?= $routes['vehiculos_verificar_placa_unica'] ?>' + encodeURIComponent(placa));
                const data = await response.text(); // Cambiar a .text() temporalmente
                const result = JSON.parse(data);
                return !result.exists; // Devuelve true si la placa es única
            } catch (error) {
                console.error('Error al verificar la placa:', error);
                return false; // Asumir que no es única si ocurre un error
            }
        }

    })();
</script>