<?php $routes = include '../config/Routes.php'; ?>

<div class="container mt-4">

    <h3>Detalle del Movimiento</h3>
    <hr>

    <table class="table table-striped">
        <tr>
            <th>ID</th>
            <td><?php echo $mov['id']; ?></td>
        </tr>
        <tr>
            <th>Caja</th>
            <td><?php echo $mov['caja_nombre']; ?></td>
        </tr>
        <tr>
            <th>Tipo</th>
            <td><?php echo $mov['tipo']; ?></td>
        </tr>
        <tr>
            <th>Origen</th>
            <td><?php echo $mov['origen']; ?></td>
        </tr>
        <tr>
            <th>Valor</th>
            <td>$<?php echo number_format($mov['valor'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <th>Fecha</th>
            <td><?php echo $mov['fecha']; ?></td>
        </tr>
        <tr>
            <th>Usuario</th>
            <td><?php echo $mov['user_nombre']; ?></td>
        </tr>
        <tr>
            <th>Descripción</th>
            <td><?php echo $mov['descripcion']; ?></td>
        </tr>

        <?php if (!empty($mov['ingreso_id'])): ?>
            <tr>
                <th>Ingreso</th>
                <td>
                    <strong>Recibo:</strong> <?= $mov['numero_recibo'] ?><br>
                    <strong>Valor:</strong> $<?= number_format($mov['ingreso_valor'], 0, ',', '.') ?><br>
                    <strong>Fecha:</strong> <?= $mov['ingreso_fecha'] ?><br>
                    <strong>Motivo:</strong> <?= $mov['motivo_nombre'] ?><br>
                    <strong>Tipo:</strong> <?= $mov['tipo_nombre'] ?><br>
                    <strong>Matrícula:</strong> <?= $mov['matricula_id'] ?><br>
                    <strong>Observaciones:</strong> <?= $mov['ingreso_observaciones'] ?>
                </td>
            </tr>
        <?php else: ?>
            <tr>
                <th>Ingreso</th>
                <td>No aplica</td>
            </tr>
        <?php endif; ?>

    </table>

    <a href="<?php echo $routes['movimientos_caja_index']; ?>" class="btn btn-secondary">Volver</a>
</div>