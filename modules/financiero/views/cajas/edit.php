<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Editar Caja</h5>

        <a href="<?= htmlspecialchars($routes['cajas_index']) ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card-body">

        <?php if (!empty($_SESSION['error_message'])): ?>
            <script>
                Swal.fire({
                    icon: 'error',
                    html: <?= json_encode($_SESSION['error_message']) ?>
                });
            </script>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <form id="formEditarCaja" action="<?= htmlspecialchars($routes['cajas_update']) ?>" method="post">

            <input type="hidden" name="id" value="<?= (int)$caja['id'] ?>">

            <div class="row">

                <!-- Nombre -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Caja <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control"
                            value="<?= htmlspecialchars($caja['nombre']) ?>"
                            required>
                    </div>
                </div>

                <!-- Tipo -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Caja <span class="text-danger">*</span></label>
                        <?php
                        $tipos = [
                            'EFECTIVO',
                            'BANCO',
                            'BILLETERA_DIGITAL',
                            'DATÁFONO',
                            'QR',
                            'OTRO'
                        ];
                        ?>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t ?>" <?= ($caja['tipo'] == $t ? 'selected' : '') ?>>
                                    <?= $t ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                    </div>
                </div>

                <!-- Descripción -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea
                            name="descripcion"
                            id="descripcion"
                            class="form-control"
                            rows="3"><?= htmlspecialchars($caja['descripcion'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Estado -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="1" <?= $caja['estado'] == 1 ? 'selected' : '' ?>>ACTIVA</option>
                            <option value="0" <?= $caja['estado'] == 0 ? 'selected' : '' ?>>INACTIVA</option>
                        </select>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-save"></i> Actualizar Caja
            </button>

        </form>
    </div>
</div>