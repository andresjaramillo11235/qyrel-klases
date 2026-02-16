<div class="card">
    <div class="card-header">
        <h5>Movimientos de Caja</h5>
    </div>
    <div class="card-body">
        <!-- Filtro -->
        <?php $routes = include '../config/Routes.php'; ?>

        <form action="<?= $routes['movimientos_caja_index'] ?>" method="POST" class="row g-3 mb-4">

            <div class="col-md-2">
                <label class="form-label">Fecha inicial</label>
                <input type="date" name="fecha_ini" class="form-control" value="<?= htmlspecialchars($fechaIni) ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label">Fecha final</label>
                <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fechaFin) ?>">
            </div>

            <div class="col-md-2">
                <label class="form-label">Caja</label>
                <select name="caja_id" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach ($cajas as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($selCaja == $c['id'] ? 'selected' : '') ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Tipo</label>
                <select name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="INGRESO" <?= ($selTipo === 'INGRESO' ? 'selected' : '') ?>>Ingreso</option>
                    <option value="EGRESO" <?= ($selTipo === 'EGRESO' ? 'selected' : '') ?>>Egreso</option>
                    <option value="AJUSTE" <?= ($selTipo === 'AJUSTE' ? 'selected' : '') ?>>Ajuste</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Origen</label>
                <select name="origen" class="form-select">
                    <option value="">Todos</option>
                    <option value="ingreso" <?= ($selOrigen === 'ingreso' ? 'selected' : '') ?>>Ingreso</option>
                    <option value="egreso" <?= ($selOrigen === 'egreso' ? 'selected' : '') ?>>Egreso</option>
                    <option value="ajuste" <?= ($selOrigen === 'ajuste' ? 'selected' : '') ?>>Ajuste</option>
                    <option value="cierre" <?= ($selOrigen === 'cierre' ? 'selected' : '') ?>>Cierre</option>
                </select>
            </div>



            <div class="col-md-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary">Filtrar</button>
                <!-- <a href="/movimientos-caja/" class="btn btn-secondary">Limpiar</a> -->
            </div>

        </form>


        <!-- Tabla de Movimientos -->
        <table class="table mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Caja</th>
                    <th>Tipo</th>
                    <th>Valor</th>
                    <th>Descripción</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($movimientos)): ?>
                    <?php foreach ($movimientos as $movimiento): ?>
                        <tr>
                            <td><?= $movimiento['id'] ?></td>
                            <td><?= $movimiento['fecha'] ?></td>
                            <td><?= $movimiento['caja_id'] ?></td>
                            <td><?= $movimiento['tipo'] ?></td>
                            <td>$ <?= number_format($movimiento['valor'], 2, ',', '.') ?></td>
                            <td><?= $movimiento['descripcion'] ?></td>
                            <td>
                                <a href="<?= $routes['movimientos_caja_detalle'] . $movimiento['id'] ?>" class="btn btn-warning btn-sm">Ver</a>
                                <!-- <a href="<?= $routes['movimiento_delete'] . '/' . $movimiento['id'] ?>" class="btn btn-danger btn-sm">Eliminar</a> -->
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No se encontraron movimientos</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>