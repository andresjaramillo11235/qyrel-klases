<h1 class="mt-5">Crear Clase</h1>
<form action="/clases/store/" method="post">
    <div class="mb-3">
        <label for="nombre" class="form-label">Nombre</label>
        <input type="text" class="form-control" id="nombre" name="nombre" required>
    </div>
    <div class="mb-3">
        <label for="descripcion" class="form-label">Descripción</label>
        <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
    </div>
    <div class="mb-3">
        <label for="tipo_id" class="form-label">Tipo</label>
        <select class="form-control" id="tipo_id" name="tipo_id" required>
            <?php foreach ($tiposClases as $tipo) : ?>
                <option value="<?= $tipo['id'] ?>"><?= $tipo['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="estado_id" class="form-label">Estado</label>
        <select class="form-control" id="estado_id" name="estado_id" required>
            <?php foreach ($estadosClases as $estado) : ?>
                <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="fecha" class="form-label">Fecha</label>
        <input type="date" class="form-control" id="fecha" name="fecha" required>
    </div>
    <div class="mb-3">
        <label for="hora_inicio" class="form-label">Hora Inicio</label>
        <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required>
    </div>
    <div class="mb-3">
        <label for="hora_fin" class="form-label">Hora Fin</label>
        <input type="time" class="form-control" id="hora_fin" name="hora_fin" required>
    </div>
    <div class="mb-3">
        <label for="matricula_id" class="form-label">Matrícula</label>
        <input type="text" class="form-control" id="matricula_id" name="matricula_id" value="<?= $matriculaId ?>" readonly>
    </div>
    <div class="mb-3">
        <label for="lugar" class="form-label">Lugar</label>
        <input type="text" class="form-control" id="lugar" name="lugar">
    </div>
    <div class="mb-3">
        <label for="vehiculo_id" class="form-label">Vehículo</label>
        <select class="form-control" id="vehiculo_id" name="vehiculo_id">
            <?php foreach ($vehiculos as $vehiculo) : ?>
                <option value="<?= $vehiculo['id'] ?>"><?= $vehiculo['placa'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="instructor_id" class="form-label">Instructor</label>
        <select class="form-control" id="instructor_id" name="instructor_id" required>
            <?php foreach ($instructores as $instructor) : ?>
                <option value="<?= $instructor['id'] ?>"><?= $instructor['nombre'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="mb-3">
        <label for="observaciones" class="form-label">Observaciones</label>
        <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
    </div>
    <?php if ($userUtils->isSuperAdmin($_SESSION['user_id'])) : ?>
        <div class="mb-3">
            <label for="empresa_id" class="form-label">Empresa</label>
            <select class="form-control" id="empresa_id" name="empresa_id" required>
                <?php foreach ($empresas as $empresa) : ?>
                    <option value="<?= $empresa['id'] ?>"><?= $empresa['nombre'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php else : ?>
        <input type="hidden" name="empresa_id" value="<?= $_SESSION['empresa_id'] ?>">
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Guardar</button>
    <a href="/clases/index/<?= $matriculaId ?>" class="btn btn-secondary">Volver</a>
</form>
<br>
