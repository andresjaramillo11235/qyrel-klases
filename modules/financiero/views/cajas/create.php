<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Crear Nueva Caja</h5>
        <a href="<?= htmlspecialchars($routes['cajas_index']) ?>" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card-body">

        <?php if (!empty($_SESSION['error_message'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <form id="formCrearCaja" action="<?= htmlspecialchars($routes['cajas_store']) ?>" method="post">

            <div class="row">

                <!-- Nombre -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Caja <span class="text-danger">*</span></label>
                        <input type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control"
                            required>
                    </div>
                </div>


                <!-- Tipo -->
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Caja <span class="text-danger">*</span></label>
                        <select name="tipo" id="tipo" class="form-select" required>
                            <option value="EFECTIVO">EFECTIVO</option>
                            <option value="BANCO">BANCO</option>
                            <option value="BILLETERA_DIGITAL">BILLETERA DIGITAL</option>
                            <option value="DATFONO">DATÁFONO / POS</option>
                            <option value="QR">QR / BOTÓN DE PAGO</option>
                            <option value="OTRO">OTRO</option>
                        </select>
                    </div>
                </div>


                <!-- Descripción -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea name="descripcion"
                            id="descripcion"
                            class="form-control"
                            rows="3"></textarea>
                    </div>
                </div>

                <!-- Estado -->
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado" class="form-select" required>
                            <option value="1">ACTIVA</option>
                            <option value="0">INACTIVA</option>
                        </select>
                    </div>
                </div>

            </div>

            <button type="submit" class="btn btn-primary mt-3">
                <i class="fas fa-save"></i> Guardar Caja
            </button>

        </form>
    </div>
</div>