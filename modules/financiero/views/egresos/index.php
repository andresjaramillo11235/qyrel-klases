<?php
$routes = include '../config/Routes.php';
include_once '../shared/utils/InsertarSaltosDeLinea.php';
?>

<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "<?php echo $_SESSION['success_message']; ?>"
            });
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: "<?php echo $_SESSION['error_message']; ?>"
            });
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Egresos Financieros</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="card">

    <div class="card-header bg-light text-dark border-bottom">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="mb-3 mb-sm-0">
                <i class="ti ti-filter me-2"></i> Filtro de egresos
            </h5>
        </div>
    </div>


    <div class="card-body">

        <form action="<?php echo $routes['egresos_index'] ?>" method="post">

            <div class="row">

                <div class="col-sm-3">
                    <div class="mb-2">
                        <label class="form-label">Fecha inicio</label>
                        <input type="date" name="fecha_inicio" class="form-control"
                            value="<?= htmlspecialchars($_GET['fecha_inicio'] ?? $_POST['fecha_inicio'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="mb-2">
                        <label class="form-label">Fecha fin</label>
                        <input type="date" name="fecha_fin" class="form-control"
                            value="<?= htmlspecialchars($_GET['fecha_fin'] ?? $_POST['fecha_fin'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="mb-2">
                        <label class="form-label">Tipo de egreso</label>
                        <select name="tipo_egreso_id" class="form-select">
                            <option value="">Todos</option>
                            <?php
                            $selTipo = $_GET['tipo_egreso_id'] ?? $_POST['tipo_egreso_id'] ?? '';
                            foreach ($tiposEgreso as $te):
                                $sel = ((string)$selTipo === (string)$te['id']) ? 'selected' : '';
                            ?>
                                <option value="<?= (int)$te['id'] ?>" <?= $sel ?>><?= htmlspecialchars($te['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="col-sm-3">
                    <div class="mb-2">
                        <label class="form-label">Buscar</label>
                        <input type="text" name="q" class="form-control" placeholder="Documento, tercero u observaciones"
                            value="<?= htmlspecialchars($_GET['q'] ?? $_POST['q'] ?? '') ?>">
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="<?php echo $routes['egresos_index'] ?>" class="btn btn-outline-secondary">Limpiar</a>

                    <a href="<?php echo $routes['egresos_create'] ?>" class="btn btn-success ms-auto">
                        <i class="fas fa-plus-circle"></i> Nuevo egreso
                    </a>

                </div>
            </div>
        </form>
    </div>
</div>


<?php if (!empty($resumenEgresos) || !empty($resumenCuentas)): ?>
<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <strong>Resumen de Egresos</strong>
    </div>

    <div class="card-body">

        <div class="row">

            <!-- RESUMEN POR TIPO -->
            <div class="col-md-6">
                <h6 class="text-primary">Totales por Tipo de Egreso</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Tipo de Egreso</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalTipo = 0;
                        foreach ($resumenEgresos as $r): 
                            $totalTipo += $r['total'];
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($r['tipo_egreso']) ?></td>
                                <td class="text-end">$ <?= number_format($r['total'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        <tr class="table-light">
                            <th>Total</th>
                            <th class="text-end text-primary">$ <?= number_format($totalTipo, 0, ',', '.') ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- RESUMEN POR CUENTA -->
            <div class="col-md-6">
                <h6 class="text-primary">Totales por Cuenta de Egreso</h6>
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Cuenta</th>
                            <th class="text-end">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalCuenta = 0;
                        foreach ($resumenCuentas as $c): 
                            $totalCuenta += $c['total'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($c['cuenta'] ?? 'Sin cuenta') ?></td>
                            <td class="text-end">$ <?= number_format($c['total'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>

                        <tr class="table-light">
                            <th>Total</th>
                            <th class="text-end text-primary">$ <?= number_format($totalCuenta, 0, ',', '.') ?></th>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
<?php endif; ?>





<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-3 mb-sm-0">Lista de Egresos</h5>
                <span class="text-muted small"><?= (int)($totales['cantidad'] ?? 0) ?> registros</span>
                <span class="text-muted small">Total: $ <?= number_format((float)($totales['total_valor'] ?? 0), 0, ',', '.') ?> </span>
            </div>


            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table id="tablaEgresos" class="table table-striped data-table">



                        <thead class="table-light">
                            <tr>
                                <th style="width:80px;">ID</th>
                                <th style="width:110px;">Fecha</th>
                                <th style="width:140px;">Tipo</th>
                                <th style="width:160px;">Cuenta</th>
                                <th style="width:160px;">Subcuenta</th>
                                <th style="width:140px;">Documento</th>
                                <th>Entidad / Persona</th>
                                <th style="width:130px;" class="text-end">Valor</th>
                                <th>Observaciones</th>
                                <th style="width:140px;">Usuario</th>
                                <th style="width:90px;">Soporte</th>
                                <th style="width:1px;white-space:nowrap;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($egresos)): ?>
                                <?php foreach ($egresos as $r): ?>
                                    <tr>
                                        <td><?= (int)$r['id'] ?></td>
                                        <td><?= htmlspecialchars($r['fecha']) ?></td>
                                        <td><?= htmlspecialchars($r['tipo_egreso']) ?></td>
                                        <td><?= htmlspecialchars($r['cuenta_nombre'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($r['subcuenta_nombre'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($r['documento']) ?></td>
                                        <td><?= htmlspecialchars($r['nombre_tercero']) ?></td>
                                        <td class="text-end">$ <?= number_format((float)$r['valor'], 2, ',', '.') ?></td>
                                        <td><?= insertarSaltosDeLinea($r['observaciones'] ?? '', 5) ?></td>
                                        <td><?= insertarSaltosDeLinea(htmlspecialchars(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')), 2) ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($r['soporte_ruta'])): ?>
                                                <a href="<?= htmlspecialchars($r['soporte_ruta']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="<?= htmlspecialchars($r['soporte_nombre'] ?? 'Ver soporte') ?>">
                                                    <i class="fas fa-paperclip"></i>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">


                                                <!-- Editar (modal) -->
                                                <button
                                                    class="btn"
                                                    title="Editar"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#modalEditarEgreso"
                                                    data-id="<?= (int)$r['id'] ?>"
                                                    data-fecha="<?= htmlspecialchars($r['fecha']) ?>"
                                                    data-tipo="<?= (int)$r['tipo_egreso_id'] ?>"
                                                    data-cuenta="<?= (int)($r['cuenta_egreso_id'] ?? 0) ?>"
                                                    data-subcuenta="<?= (int)($r['sub_cuenta_egreso_id'] ?? 0) ?>"
                                                    data-valor="<?= (float)$r['valor'] ?>"
                                                    data-observaciones="<?= htmlspecialchars($r['observaciones'] ?? '') ?>"
                                                    data-tipo-doc="<?= (int)($r['tipo_documento_id'] ?? 0) ?>"
                                                    data-documento="<?= htmlspecialchars($r['documento'] ?? '') ?>"
                                                    data-tercero="<?= htmlspecialchars($r['nombre_tercero'] ?? '') ?>"
                                                    data-soporte-url="<?= htmlspecialchars($r['soporte_ruta'] ?? '') ?>"
                                                    data-soporte-nombre="<?= htmlspecialchars($r['soporte_nombre'] ?? '') ?>">
                                                    <i class="ti ti-edit"></i>
                                                </button>


                                                <!-- Eliminar -->
                                                <form action="<?php echo $routes['egresos_delete'] ?>" method="post" class="d-inline"
                                                    onsubmit="return confirm('¿Eliminar el egreso #<?= (int)$r['id'] ?>? Esta acción no se puede deshacer.');">
                                                    <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                                                    <button type="submit" class="btn" title="Eliminar">
                                                        <i class="ti ti-trash"></i>
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="12" class="text-center text-muted">Sin resultados</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal Editar Egreso -->
<div class="modal fade" id="modalEditarEgreso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="<?php echo $routes['egresos_update'] ?>" method="post" class="modal-content" enctype="multipart/form-data">
            <div class="modal-header">
                <h5 class="modal-title">Editar egreso</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id" id="ed_id">

                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" id="ed_fecha" class="form-control" required>
                    </div>

                    <!-- tercero y documento -->
                    <div class="col-md-3">
                        <label class="form-label">Tipo de documento</label>
                        <select name="tipo_documento_id" id="ed_tipo_doc" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($tiposDocumento as $td): ?>
                                <option value="<?= (int)$td['id'] ?>"><?= htmlspecialchars($td['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Documento</label>
                        <input type="text" name="documento" id="ed_documento" class="form-control" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Nombre del tercero</label>
                        <input type="text" name="nombre_tercero" id="ed_tercero" class="form-control" required>
                    </div>

                    <!-- Tipo de egreso -->
                    <div class="col-md-4">
                        <select name="tipo_egreso_id" id="ed_tipo" class="form-select" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($tiposEgreso as $t): ?>
                                <option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Cuenta -->
                    <div class="col-md-4">
                        <select name="cuenta_egreso_id" id="ed_cuenta" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($cuentasAll as $c): ?>
                                <option
                                    value="<?= (int)$c['id'] ?>"
                                    data-tipo="<?= (int)$c['tipo_egreso_id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Subcuenta -->
                    <div class="col-md-4">
                        <select name="sub_cuenta_egreso_id" id="ed_subcuenta" class="form-select">
                            <option value="">—</option>
                            <?php foreach ($subcuentasAll as $sc): ?>
                                <option
                                    value="<?= (int)$sc['id'] ?>"
                                    data-cuenta="<?= (int)$sc['cuenta_egreso_id'] ?>"><?= htmlspecialchars($sc['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Valor</label>
                        <input type="number" step="0.01" min="0" name="valor" id="ed_valor" class="form-control" required>
                    </div>

                    <div class="col-6">
                        <label class="form-label">Observaciones</label>
                        <input type="text" name="observaciones" id="ed_obs" class="form-control">
                    </div>

                    <!-- SOPORTE -->
                    <div class="row g-3 mt-2">
                        <div class="col-12">
                            <label class="form-label d-block">Soporte actual</label>
                            <a id="ed_soporte_link" href="#" target="_blank" class="btn btn-sm btn-outline-secondary d-none">
                                <i class="fas fa-paperclip"></i> <span id="ed_soporte_name"></span>
                            </a>
                            <span id="ed_soporte_none" class="text-muted">— Sin soporte —</span>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Reemplazar soporte (opcional)</label>
                            <input type="file" name="soporte" id="ed_soporte" class="form-control" accept=".pdf,image/*">
                            <div class="form-text">Admite PDF o imagen. Máx. 10&nbsp;MB.</div>
                        </div>
                        <div class="col-md-4 align-self-end">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="ed_remove_soporte" name="remove_soporte" value="1">
                                <label class="form-check-label" for="ed_remove_soporte">Eliminar soporte actual</label>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </div>
        </form>
    </div>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalEditarEgreso');
        if (!modal) return;

        const selTipo = document.getElementById('ed_tipo');
        const selCuenta = document.getElementById('ed_cuenta');
        const selSub = document.getElementById('ed_subcuenta');

        const originalCtas = Array.from(selCuenta.options);
        const originalSubs = Array.from(selSub.options);

        function filtrarCuentasPorTipo(tipoId) {
            selCuenta.innerHTML = '';
            selCuenta.appendChild(new Option('—', ''));
            originalCtas.forEach(o => {
                const t = o.getAttribute('data-tipo');
                if (t && String(t) === String(tipoId)) selCuenta.appendChild(o.cloneNode(true));
            });
        }

        function filtrarSubPorCuenta(cuentaId) {
            selSub.innerHTML = '';
            selSub.appendChild(new Option('—', ''));
            originalSubs.forEach(o => {
                const c = o.getAttribute('data-cuenta');
                if (c && String(c) === String(cuentaId)) selSub.appendChild(o.cloneNode(true));
            });
        }

        selTipo.addEventListener('change', () => {
            filtrarCuentasPorTipo(selTipo.value);
            selCuenta.value = '';
            filtrarSubPorCuenta('');
        });
        selCuenta.addEventListener('change', () => filtrarSubPorCuenta(selCuenta.value));

        modal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            // Campos existentes
            document.getElementById('ed_id').value = btn.getAttribute('data-id');
            document.getElementById('ed_fecha').value = btn.getAttribute('data-fecha') || '';
            document.getElementById('ed_valor').value = btn.getAttribute('data-valor') || '';
            document.getElementById('ed_obs').value = btn.getAttribute('data-observaciones') || '';

            const tipo = btn.getAttribute('data-tipo') || '';
            const cuenta = btn.getAttribute('data-cuenta') || '';
            const subcuenta = btn.getAttribute('data-subcuenta') || '';

            // 🔹 Nuevos campos
            document.getElementById('ed_tipo_doc').value = btn.getAttribute('data-tipo-doc') || '';
            document.getElementById('ed_documento').value = btn.getAttribute('data-documento') || '';
            document.getElementById('ed_tercero').value = btn.getAttribute('data-tercero') || '';

            selTipo.value = tipo || '';
            filtrarCuentasPorTipo(tipo);
            selCuenta.value = cuenta || '';
            filtrarSubPorCuenta(cuenta);
            selSub.value = subcuenta || '';
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalEditarEgreso');
        if (!modal) return;

        // refs soporte
        const link = document.getElementById('ed_soporte_link');
        const name = document.getElementById('ed_soporte_name');
        const none = document.getElementById('ed_soporte_none');
        const file = document.getElementById('ed_soporte');
        const rmChk = document.getElementById('ed_remove_soporte');

        // si el usuario carga un archivo, desmarcamos "eliminar"
        if (file) file.addEventListener('change', () => {
            if (rmChk) rmChk.checked = false;
        });

        modal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            // ...seteo de todos los demás campos...
            const url = btn.getAttribute('data-soporte-url') || '';
            const fname = btn.getAttribute('data-soporte-nombre') || '';

            if (url) {
                link.classList.remove('d-none');
                link.href = url;
                name.textContent = fname || 'Ver soporte';
                none.classList.add('d-none');
                if (rmChk) rmChk.disabled = false;
            } else {
                link.classList.add('d-none');
                name.textContent = '';
                none.classList.remove('d-none');
                if (rmChk) rmChk.checked = false, rmChk.disabled = true;
            }

            if (file) file.value = '';
        });
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

        // ========== EDIT (modal) ==========
        const modalEd = document.getElementById('modalEditarEgreso');
        if (modalEd) {
            const edTipo = modalEd.querySelector('#ed_tipo');
            const edCuenta = modalEd.querySelector('#ed_cuenta');
            const edSub = modalEd.querySelector('#ed_subcuenta');

            // cachear una sola vez las opciones "completas" del modal
            const edOrigCtas = cloneOptions(edCuenta);
            const edOrigSubs = cloneOptions(edSub);

            // cuando cambia el tipo en el modal, filtrar cuentas y limpiar subcuenta
            edTipo.addEventListener('change', function() {
                filterAccountsByTipo(edTipo, edCuenta, edOrigCtas);
                resetSelect(edSub, '—');
            });

            // cuando cambia la cuenta en el modal, filtrar subcuentas
            edCuenta.addEventListener('change', function() {
                filterSubsByCuenta(edCuenta, edSub, edOrigSubs);
            });

            // al abrir el modal, precargar y filtrar en orden: tipo -> cuenta -> subcuenta
            modalEd.addEventListener('show.bs.modal', function(e) {
                const btn = e.relatedTarget;
                const tipoSel = btn.getAttribute('data-tipo') || '';
                const cuentaSel = btn.getAttribute('data-cuenta') || '';
                const subcuentaSel = btn.getAttribute('data-subcuenta') || '';

                // setear tipo
                edTipo.value = tipoSel || '';
                // filtrar cuentas por tipo
                filterAccountsByTipo(edTipo, edCuenta, edOrigCtas);

                // setear cuenta si pertenece al tipo; si no, limpiar
                if (cuentaSel) {
                    // verificar si la cuenta existe en las opciones filtradas
                    const hasCuenta = Array.from(edCuenta.options).some(o => o.value === String(cuentaSel));
                    edCuenta.value = hasCuenta ? String(cuentaSel) : '';
                } else {
                    edCuenta.value = '';
                }

                // filtrar subcuentas por cuenta
                filterSubsByCuenta(edCuenta, edSub, edOrigSubs);

                // setear subcuenta si pertenece a la cuenta; si no, limpiar
                if (subcuentaSel) {
                    const hasSub = Array.from(edSub.options).some(o => o.value === String(subcuentaSel));
                    edSub.value = hasSub ? String(subcuentaSel) : '';
                } else {
                    edSub.value = '';
                }
            });
        }
    })();
</script>


<!-- Incluir jQuery y DataTables con Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- DataTables y sus extensiones -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>


<!-- Configuración de DataTables -->
<script>
    $(document).ready(function() {
        $('#tablaEgresos').DataTable({
            dom: 'Bfrtip',
            buttons: [{
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel"></i> Exportar a Excel',
                className: 'btn btn-info', // Estilo Bootstrap
                title: 'Listado de egresoss ',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9] // Índices de las columnas que quieres exportar
                }
            }],
            language: {
                "sProcessing": "Procesando...",
                "sLengthMenu": "Mostrar _MENU_ registros",
                "sZeroRecords": "No se encontraron resultados",
                "sEmptyTable": "Ningún dato disponible en esta tabla",
                "sInfo": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered": "(filtrado de un total de _MAX_ registros)",
                "sSearch": "Buscar:",
                "oPaginate": {
                    "sFirst": "Primero",
                    "sLast": "Último",
                    "sNext": "Siguiente",
                    "sPrevious": "Anterior"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad",
                    "print": "Imprimir"
                }
            },
            pagingType: "simple_numbers",
            order: [
                [1, "asc"]
            ],
            pageLength: 30
        });
    });
</script>