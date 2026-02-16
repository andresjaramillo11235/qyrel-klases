<div class="container mt-5">
    <h2>Editar Clase</h2>
    <form action="/clases/update/<?= $clase['id'] ?>" method="post">
        <input type="hidden" name="matricula_id" value="<?= $clase['matricula_id'] ?>">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $clase['nombre'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion"><?= $clase['descripcion'] ?></textarea>
        </div>
        <div class="mb-3">
            <label for="tipo_id" class="form-label">Tipo</label>
            <select class="form-control" id="tipo_id" name="tipo_id" required>
                <?php foreach ($tipos_clases as $tipo): ?>
                    <option value="<?= $tipo['id'] ?>" <?= $clase['tipo_id'] == $tipo['id'] ? 'selected' : '' ?>><?= $tipo['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="estado_id" class="form-label">Estado</label>
            <select class="form-control" id="estado_id" name="estado_id" required>
                <?php foreach ($estados_clases as $estado): ?>
                    <option value="<?= $estado['id'] ?>" <?= $clase['estado_id'] == $estado['id'] ? 'selected' : '' ?>><?= $estado['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="fecha" class="form-label">Fecha</label>
            <input type="date" class="form-control" id="fecha" name="fecha" value="<?= $clase['fecha'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="hora_inicio" class="form-label">Hora Inicio</label>
            <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="<?= $clase['hora_inicio'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="hora_fin" class="form-label">Hora Fin</label>
            <input type="time" class="form-control" id="hora_fin" name="hora_fin" value="<?= $clase['hora_fin'] ?>" required>
        </div>
        <div class="mb-3">
            <label for="lugar" class="form-label">Lugar</label>
            <input type="text" class="form-control" id="lugar" name="lugar" value="<?= $clase['lugar'] ?>">
        </div>
        <div class="mb-3">
            <label for="vehiculo_id" class="form-label">Vehículo</label>
            <select class="form-control" id="vehiculo_id" name="vehiculo_id">
                <?php foreach ($vehiculos as $vehiculo): ?>
                    <option value="<?= $vehiculo['id'] ?>" <?= $clase['vehiculo_id'] == $vehiculo['id'] ? 'selected' : '' ?>><?= $vehiculo['placa'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="instructor_id" class="form-label">Instructor</label>
            <select class="form-control" id="instructor_id" name="instructor_id" required>
                <?php foreach ($instructores as $instructor): ?>
                    <option value="<?= $instructor['id'] ?>" <?= $clase['instructor_id'] == $instructor['id'] ? 'selected' : '' ?>><?= $instructor['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="observaciones" class="form-label">Observaciones</label>
            <textarea class="form-control" id="observaciones" name="observaciones"><?= $clase['observaciones'] ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Guardar</button>
        <a href="/clases/index/<?= $clase['matricula_id'] ?>" class="btn btn-secondary">Volver</a>
    </form>
</div>
<br><br>
