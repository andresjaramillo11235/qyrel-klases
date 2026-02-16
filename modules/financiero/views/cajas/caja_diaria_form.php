<?php $routes = include '../config/Routes.php'; ?>

<style>
    /* ----------------------------------------------------------
   🔹 Header estándar para cards financieras / administrativas
    ---------------------------------------------------------- */
    .cc-card-header {
        background-color: #e4e6eb;
        /* gris oscuro suave */
        border-bottom: 1px solid #d0d4da;
        font-weight: 600;
        color: #1f2937;
        /* gris oscuro texto */
    }
</style>


<!-- ===================== -->
<!-- FORMULARIO - CAJA DIARIA -->
<!-- ===================== -->
<div class="card shadow-sm mb-4">

    <!-- HEADER -->
    <div class="card-header cc-card-header">
        <div class="d-flex align-items-center">
            <i class="ti ti-filter me-2 text-secondary"></i>
            <h6 class="mb-0 fw-semibold text-dark">
                Consulta de caja diaria
            </h6>
        </div>
    </div>

    <!-- BODY -->
    <div class="card-body">
        <form action="<?= $routes['procesar_caja_diaria'] ?>" method="POST" class="row g-3 align-items-end">

            <div class="col-md-4 col-lg-3">
                <label class="form-label fw-semibold">Fecha</label>
                <input
                    type="date"
                    name="fecha"
                    class="form-control"
                    value="<?= htmlspecialchars($fecha ?? date('Y-m-d')) ?>"
                    required>
            </div>

            <div class="col-md-3">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="ti ti-search me-1"></i>
                    Consultar
                </button>
            </div>

        </form>
    </div>

</div>






<div class="card shadow-sm mb-4">



    <!-- ===================== -->
    <!-- RESULTADOS -->
    <!-- ===================== -->
    <?php if (!empty($mostrarResultados)): ?>

        <!-- CONSOLIDADO -->

        <div class="card-header cc-card-header">
            Consolidado por caja – <?= htmlspecialchars($fecha) ?>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0">
                <thead>
                    <tr>
                        <th>Caja</th>
                        <th class="text-end">Ingresos</th>
                        <th class="text-end">Egresos</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($consolidado as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['caja']) ?></td>
                            <td class="text-end">
                                <?= number_format($row['total_ingresos'], 0, ',', '.') ?>
                            </td>
                            <td class="text-end">
                                <?= number_format($row['total_egresos'], 0, ',', '.') ?>
                            </td>
                            <td class="text-end fw-bold">
                                <?= number_format($row['saldo'], 0, ',', '.') ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>TOTAL</th>
                        <th class="text-end"><?= number_format($totalIngresos, 0, ',', '.') ?></th>
                        <th class="text-end"><?= number_format($totalEgresos, 0, ',', '.') ?></th>
                        <th class="text-end"><?= number_format($saldoGeneral, 0, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

    <?php endif; ?>

</div>


<div class="card shadow-sm mb-4">

    <!-- ===================== -->
    <!-- RESULTADOS -->
    <!-- ===================== -->
    <?php if (!empty($mostrarResultados)): ?>

        <div class="row">

            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header cc-card-header">
                        <h6 class="mb-0 fw-semibold">
                            <i class="ti ti-arrow-down-left me-1"></i> Ingresos
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Caja</th>
                                    <th class="text-end">Valor</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalle as $mov): ?>
                                    <?php if ($mov['tipo'] === 'INGRESO'): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($mov['caja']) ?></td>
                                            <td class="text-end">
                                                <?= number_format($mov['valor'], 0, ',', '.') ?>
                                            </td>
                                            <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                                            <td><?= htmlspecialchars($mov['username'] ?? '-') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header cc-card-header">
                        <h6 class="mb-0 fw-semibold">
                            <i class="ti ti-arrow-up-right me-1"></i> Egresos
                        </h6>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-sm table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Caja</th>
                                    <th class="text-end">Valor</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalle as $mov): ?>
                                    <?php if ($mov['tipo'] === 'EGRESO'): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($mov['caja']) ?></td>
                                            <td class="text-end">
                                                <?= number_format($mov['valor'], 0, ',', '.') ?>
                                            </td>
                                            <td><?= htmlspecialchars($mov['descripcion']) ?></td>
                                            <td><?= htmlspecialchars($mov['username'] ?? '-') ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>






    <?php endif; ?>

</div>