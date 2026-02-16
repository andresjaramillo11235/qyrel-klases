<?php $routes = include '../config/Routes.php'; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= $routes['ingresos_index']; ?>">Ingresos Financieros</a></li>
                    <li class="breadcrumb-item" aria-current="page">Editar Ingreso</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- Formulario de Edición -->
<div class="row">
    <!-- Columna izquierda: Datos del estudiante -->
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="ti ti-user"></i> Datos del Estudiante</h5>
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> <?= $ingreso['estudiante_nombre'] ?> <?= $ingreso['estudiante_apellidos'] ?></p>
                <p><strong>Cédula:</strong> <?= $ingreso['estudiante_cedula'] ?></p>
                <div class="text-center">
                    <img src="../files/fotos_estudiantes/<?= $ingreso['estudiante_foto'] ?>" alt="Foto del estudiante" class="img-fluid rounded-circle" style="max-width: 150px;">
                </div>
            </div>
        </div>
    </div>

    <!-- Columna derecha: Formulario de ingreso -->
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5><i class="ti ti-edit"></i> Editar Datos del Ingreso</h5>
            </div>
            <div class="card-body">
                <form action="<?= $routes['ingresos_update']; ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $ingreso['id'] ?>">
                    <input type="hidden" name="matricula_id" value="<?= $ingreso['matricula_id'] ?>">

                    <!-- Campo: Valor -->
                    <div class="mb-3">
                        <label for="valor" class="form-label">Valor <span class="text-danger">*</span></label>
                        <input type="number" class="form-control form-control-lg" id="valor" name="valor" value="<?= $ingreso['valor'] ?>" required>
                    </div>

                    <!-- Campo: Motivo -->
                    <div class="mb-3">
                        <label for="motivo_ingreso_id" class="form-label">Motivo <span class="text-danger">*</span></label>
                        <select id="motivo_ingreso_id" name="motivo_ingreso_id" class="form-select form-select-lg" required>
                            <?php foreach ($motivos as $motivo): ?>
                                <option value="<?= $motivo['id']; ?>" <?= $ingreso['motivo_ingreso_id'] == $motivo['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($motivo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campo: Tipo -->
                    <div class="mb-3">
                        <label for="tipo_ingreso_id" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select id="tipo_ingreso_id" name="tipo_ingreso_id" class="form-select form-select-lg" required>
                            <?php foreach ($tipos as $tipo): ?>
                                <option value="<?= $tipo['id']; ?>" <?= $ingreso['tipo_ingreso_id'] == $tipo['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($tipo['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Campo: Observaciones -->
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea class="form-control form-control-lg" id="observaciones" name="observaciones" rows="4"><?= htmlspecialchars($ingreso['observaciones']) ?></textarea>
                    </div>

                    <!-- Campo: Fecha -->
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                        <input type="date" class="form-control form-control-lg" id="fecha" name="fecha" value="<?= $ingreso['fecha'] ?>" required>
                    </div>

                    <!-- Botones -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= $routes['ingresos_index']; ?>" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Volver
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check"></i> Actualizar Ingreso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<br><br>













<!-- Incluir jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>