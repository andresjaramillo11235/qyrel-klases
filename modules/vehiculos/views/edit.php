<div class="card">
    <div class="card-header">
        <h5>Editar Vehículo</h5>
    </div>
    <div class="card-body">
        <form action="<?= $routes['vehiculos_update'] ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $vehiculo['id'] ?>">
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="placa" class="form-label">Placa</label>
                        <input type="text" class="form-control" id="placa" name="placa" value="<?= $vehiculo['placa'] ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="numero_licencia" class="form-label">Número de Licencia</label>
                        <input type="text" class="form-control" id="numero_licencia" name="numero_licencia" value="<?= $vehiculo['numero_licencia'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="number" class="form-control" id="modelo" name="modelo" value="<?= $vehiculo['modelo'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_matricula" class="form-label">Fecha de Matrícula</label>
                        <input type="date" class="form-control" id="fecha_matricula" name="fecha_matricula" value="<?= $vehiculo['fecha_matricula'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="vin" class="form-label">VIN</label>
                        <input type="text" class="form-control" id="vin" name="vin" value="<?= $vehiculo['vin'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="cilindrada" class="form-label">Cilindrada</label>
                        <input type="text" class="form-control" id="cilindrada" name="cilindrada" value="<?= $vehiculo['cilindrada'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="capacidad" class="form-label">Capacidad</label>
                        <input type="text" class="form-control" id="capacidad" name="capacidad" value="<?= $vehiculo['capacidad'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="carroceria" class="form-label">Carrocería</label>
                        <input type="text" class="form-control" id="carroceria" name="carroceria" value="<?= $vehiculo['carroceria'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="numero_motor" class="form-label">Número de Motor</label>
                        <input type="text" class="form-control" id="numero_motor" name="numero_motor" value="<?= $vehiculo['numero_motor'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="numero_chasis" class="form-label">Número de Chasis</label>
                        <input type="text" class="form-control" id="numero_chasis" name="numero_chasis" value="<?= $vehiculo['numero_chasis'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="numero_serie" class="form-label">Número de Serie</label>
                        <input type="text" class="form-control" id="numero_serie" name="numero_serie" value="<?= $vehiculo['numero_serie'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tipo_combustible_id" class="form-label">Tipo de Combustible</label>
                        <select id="tipo_combustible_id" name="tipo_combustible_id" class="form-select">
                            <?php foreach ($tiposCombustible as $tipo): ?>
                                <option value="<?= $tipo['id'] ?>" <?= $vehiculo['tipo_combustible_id'] == $tipo['id'] ? 'selected' : '' ?>><?= $tipo['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="tipo_vehiculo_id" class="form-label">Tipo de Vehículo</label>
                        <select id="tipo_vehiculo_id" name="tipo_vehiculo_id" class="form-select">
                            <?php foreach ($tiposVehiculo as $tipo): ?>
                                <option value="<?= $tipo['id'] ?>" <?= $vehiculo['tipo_vehiculo_id'] == $tipo['id'] ? 'selected' : '' ?>>
                                    <?= $tipo['nombre'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="propietario" class="form-label">Propietario</label>
                        <input type="text" class="form-control" id="propietario" name="propietario" value="<?= $vehiculo['propietario'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="identificacion" class="form-label">Identificación</label>
                        <input type="text" class="form-control" id="identificacion" name="identificacion" value="<?= $vehiculo['identificacion'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="id_traccar" class="form-label">GPS</label>
                        <input type="number" class="form-control" id="id_traccar" name="id_traccar" value="<?= $vehiculo['id_traccar'] ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="endpoint" class="form-label">Endpoint</label>
                        <input type="text" class="form-control" id="endpoint" name="endpoint" value="<?= $vehiculo['endpoint'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="foto" class="form-label">Foto del Vehículo</label>
                        <input type="file" class="form-control" id="foto" name="foto">
                        <small class="text-muted">Formatos permitidos: JPG, PNG, GIF.</small>
                        <?php if (!empty($vehiculo['foto'])): ?>
                            <img src="../files/fotos_vehiculos/<?= $vehiculo['foto'] ?>" alt="Foto del Vehículo" class="img-thumbnail mt-2" style="width: 120px;">
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <a href="<?= $routes['vehiculos_index'] ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </div>
        </form>
    </div>
</div>
