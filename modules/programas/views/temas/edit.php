<?php $routes = include '../config/Routes.php'; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/programas/">Programas</a></li>
                    <li class="breadcrumb-item">
                        <a href="<?= $routes['programas_temas_index'] ?><?= $tema['programa_id'] ?>">Temas</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Tema</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- Formulario de edición -->
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header bg-primary text-white">
                <h5>
                    <i class="ti ti-pencil"></i> Editar Tema: <span class="fw-bold"></span>
                </h5>
            </div>

            <div class="card-body">
                <form action="<?= $routes['programas_temas_update'] ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $tema['id'] ?>">
                    <input type="hidden" name="programa_id" value="<?= $tema['programa_id'] ?>">

                    <!-- Campo: Nombre del Tema -->
                    <div class="mb-4">
                        <label for="nombre_clase" class="form-label">
                            Nombre del Tema <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control"
                            id="nombre_clase"
                            name="nombre_clase"
                            value="<?= htmlspecialchars($tema['nombre_clase']) ?>"
                            required>
                    </div>

                    <!-- Campo: Número de Horas -->
                    <div class="mb-4">
                        <label for="numero_horas" class="form-label">
                            Número de Horas <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="numero_horas"
                            name="numero_horas"
                            value="<?= $tema['numero_horas'] ?>"
                            min="1"
                            required>
                    </div>

                    <!-- Campo: Orden -->
                    <div class="mb-4">
                        <label for="orden" class="form-label">
                            Orden <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control"
                            id="orden"
                            name="orden"
                            value="<?= $tema['orden'] ?>"
                            min="1"
                            required>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-between">
                        <a href="<?= $routes['programas_temas_index'] ?><?= $tema['programa_id'] ?>" class="btn btn-secondary">
                            <i class="ti ti-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check"></i> Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>