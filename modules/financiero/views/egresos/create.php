<?php $routes = include '../config/Routes.php'; ?>

<div class="container my-3">
    <h4 class="mb-3">Registrar egreso</h4>

    <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']);
                                        unset($_SESSION['flash_error']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']);
                                            unset($_SESSION['flash_success']); ?></div>
    <?php endif; ?>

    <form action="<?php echo $routes['egresos_store'] ?>" method="post" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tipo de documento</label>
                <select name="tipo_documento_id" class="form-select" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tiposDocumento as $td): ?>
                        <option value="<?= (int)$td['id'] ?>"><?= htmlspecialchars($td['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Documento</label>
                <input type="text" name="documento" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Nombre de la entidad/persona</label>
                <input type="text" name="nombre_tercero" class="form-control" required>
            </div>

            <!-- Tipo de egreso -->
            <div class="col-md-4">
                <select name="tipo_egreso_id" id="cr_tipo" class="form-select" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($tiposEgreso as $t): ?>
                        <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Cuenta (cada opción indica a qué tipo pertenece) -->
            <div class="col-md-4">
                <select name="cuenta_egreso_id" id="cr_cuenta" class="form-select">
                    <option value="">—</option>
                    <?php foreach ($cuentasAll as $c): ?>
                        <option
                            value="<?= (int)$c['id'] ?>"
                            data-tipo="<?= (int)$c['tipo_egreso_id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Subcuenta (cada opción indica a qué cuenta pertenece) -->
            <div class="col-md-4">
                <select name="sub_cuenta_egreso_id" id="cr_subcuenta" class="form-select">
                    <option value="">—</option>
                    <?php foreach ($subcuentasAll as $sc): ?>
                        <option
                            value="<?= (int)$sc['id'] ?>"
                            data-cuenta="<?= (int)$sc['cuenta_egreso_id'] ?>"><?= htmlspecialchars($sc['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Valor</label>
                <input type="number" step="0.01" min="0" name="valor" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Observaciones</label>
                <input type="text" name="observaciones" class="form-control">
            </div>

            <div class="col-md-6">
                <label for="caja_id" class="form-label">Caja</label>
                <select id="caja_id" name="caja_id" class="form-select" required>
                    <option value="" disabled selected>Seleccione una caja</option>
                    <?php foreach ($cajas as $caja): ?>
                        <option value="<?php echo $caja['id']; ?>">
                            <?php echo htmlspecialchars($caja['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-text">Seleccione de cuál caja saldrá el dinero para este egreso.</div>
            </div>

            <div class="col-md-6">
                <label class="form-label">Soporte (PDF/JPG/PNG/WEBP)</label>
                <input type="file" name="soporte" class="form-control" accept=".pdf,image/*">
                <div class="form-text">Máx. 10MB.</div>
            </div>
        </div>

        <div class="mt-3">
            <button type="submit" class="btn btn-primary">Guardar egreso</button>
            <a href="/egresos/" class="btn btn-secondary">Cancelar</a>
        </div>
    </form>
</div>

<script>
document.getElementById("formIngreso").addEventListener("submit", function(e) {
    const caja = document.getElementById("caja_id").value;
    if (!caja) {
        e.preventDefault();
        alert("Debe seleccionar una caja para registrar el egreso.");
    }
});
</script>

<script>
    (function() {
        // Helpers genéricos:
        function cloneOptions(select) {
            return Array.from(select.options).map(o => o.cloneNode(true));
        }

        function resetSelect(select, placeholderText) {
            select.innerHTML = '';
            select.appendChild(new Option(placeholderText || '—', ''));
        }

        function filterAccountsByTipo(selectTipo, selectCuenta, originalAccountOptions) {
            resetSelect(selectCuenta, '—');
            const tipo = String(selectTipo.value || '');
            originalAccountOptions.forEach(opt => {
                const t = opt.getAttribute('data-tipo');
                if (t && String(t) === tipo) {
                    selectCuenta.appendChild(opt.cloneNode(true));
                }
            });
        }

        function filterSubsByCuenta(selectCuenta, selectSub, originalSubOptions) {
            resetSelect(selectSub, '—');
            const cuenta = String(selectCuenta.value || '');
            originalSubOptions.forEach(opt => {
                const c = opt.getAttribute('data-cuenta');
                if (c && String(c) === cuenta) {
                    selectSub.appendChild(opt.cloneNode(true));
                }
            });
        }

        // ========== CREATE ==========
        const crTipo = document.getElementById('cr_tipo');
        const crCuenta = document.getElementById('cr_cuenta');
        const crSub = document.getElementById('cr_subcuenta');

        if (crTipo && crCuenta && crSub) {
            const crOrigCtas = cloneOptions(crCuenta);
            const crOrigSubs = cloneOptions(crSub);

            crTipo.addEventListener('change', function() {
                filterAccountsByTipo(crTipo, crCuenta, crOrigCtas);
                // al cambiar tipo, limpiamos subcuenta
                resetSelect(crSub, '—');
            });

            crCuenta.addEventListener('change', function() {
                filterSubsByCuenta(crCuenta, crSub, crOrigSubs);
            });

            // Inicializar si ya viene preseleccionado (por validaciones servidor, etc.)
            if (crTipo.value) {
                filterAccountsByTipo(crTipo, crCuenta, crOrigCtas);
                if (crCuenta.value) {
                    filterSubsByCuenta(crCuenta, crSub, crOrigSubs);
                }
            }
        }
    })();
</script>