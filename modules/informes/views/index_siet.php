<?php $routes = require '../config/Routes.php'; ?>

<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h5><i class="ph-duotone ph-file-text"></i> Informe SIET</h5>
        </div>
        <div class="card-body">
            <form action="<?= $routes['informes_siet_resultado'] ?>" method="POST" class="row g-3">

                <!-- Programa -->
                <div class="col-md-6">
                    <label for="programa_id" class="form-label">Programa</label>
                    <select name="programa_id" id="programa_id" class="form-select" required>
                        <option value="">Seleccione un programa</option>
                        <?php foreach ($programas as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fecha desde -->
                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Fecha desde</label>
                    <input type="date" class="form-control" name="fecha_desde" required>
                </div>

                <!-- Fecha hasta -->
                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Fecha hasta</label>
                    <input type="date" class="form-control" name="fecha_hasta" required>
                </div>

                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="ph-duotone ph-magnifying-glass"></i> Generar Informe
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>