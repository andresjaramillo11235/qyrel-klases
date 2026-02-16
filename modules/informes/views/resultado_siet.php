<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5><i class="ph-duotone ph-clipboard-text"></i> Resultados Informe SIET</h5>
        <a href="<?= $routes['informes_siet_excel'] ?>?programa_id=<?= $programaId ?>&fecha_desde=<?= $desde ?>&fecha_hasta=<?= $hasta ?>"
            class="btn btn-outline-success">
            <i class="ph-duotone ph-file-xls"></i> Exportar a Excel
        </a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>Consecutivo</th>
                <th>Tipo de Identificación</th>
                <th>Número de Identificación</th>
                <th>Fecha de Obtención</th>
                <th>No. Acta</th>
                <th>Folio</th>
            </tr>
        </thead>
        <tbody>
            <?php $n = 1;
            foreach ($resultados as $r): ?>
                <tr>
                    <td><?= $n++ ?></td>
                    <td>1) CÉDULA DE CIUDADANÍA</td>
                    <td><?= htmlspecialchars($r['numero_documento']) ?></td>
                    <td><?= htmlspecialchars($r['fecha_obtencion'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['numero_acta']) ?></td>
                    <td> </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>