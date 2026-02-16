<?php
// Mensajes globales
if (!empty($_SESSION['success_message'])) {
    echo "<script>Swal.fire({ icon: 'success', html: " . json_encode($_SESSION['success_message']) . " });</script>";
    unset($_SESSION['success_message']);
}

if (!empty($_SESSION['error_message'])) {
    echo "<script>Swal.fire({ icon: 'error', html: " . json_encode($_SESSION['error_message']) . " });</script>";
    unset($_SESSION['error_message']);
}
?>

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cajas Configuradas</h5>

                <a href="<?= $routes['cajas_create'] ?>" class="btn btn-sm btn-success">
                    <i class="fas fa-plus-circle"></i> Nueva Caja
                </a>
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-light">
                            <tr>
                                <th style="width:80px;">ID</th>
                                <th>Nombre</th>
                                <th style="width:140px;">Tipo</th>
                                <th>Descripción</th>
                                <th style="width:120px;">Estado</th>
                                <th style="width:140px;">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (!empty($cajas)): ?>
                                <?php foreach ($cajas as $c): ?>
                                    <tr>
                                        <td><?= (int)$c['id'] ?></td>
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>

                                        <td>
                                            <?php
                                            $badge = match ($c['tipo']) {
                                                'EFECTIVO' => 'success',
                                                'BANCO' => 'primary',
                                                'BILLETERA_DIGITAL' => 'info',
                                                'DATÁFONO' => 'warning',
                                                'QR' => 'secondary',
                                                default => 'dark'
                                            };
                                            ?>
                                            <span class="badge bg-<?= $badge ?>"><?= $c['tipo'] ?></span>
                                        </td>

                                        <td><?= htmlspecialchars($c['descripcion'] ?? '—') ?></td>

                                        <td>
                                            <?php if ((int)$c['estado'] === 1): ?>
                                                <span class="badge bg-success">Activa</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Inactiva</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm">

                                                <!-- Editar -->
                                                <a href="<?= $routes['cajas_edit'] . urlencode($c['id']) ?>"
                                                    class="btn btn-outline-primary"
                                                    title="Editar">
                                                    <i class="ti ti-edit"></i>
                                                </a>

                                                <!-- Eliminar -->
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-eliminar-caja"
                                                    data-id="<?= $c['id'] ?>"
                                                    data-nombre="<?= htmlspecialchars($c['nombre']) ?>"
                                                    title="Eliminar">
                                                    <i class="ti ti-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-3">
                                        No hay cajas configuradas aún.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </div>
</div>

<form id="formEliminarCaja" action="<?= $routes['cajas_delete'] ?>" method="post" style="display:none;">
    <input type="hidden" name="id" id="inputEliminarCajaId">
</form>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-eliminar-caja').forEach(btn => {
        btn.addEventListener('click', function () {

            const id = this.dataset.id;
            const nombre = this.dataset.nombre;

            Swal.fire({
                title: "¿Eliminar la caja?",
                html: `
                    <strong>${nombre}</strong><br>
                    <small class="text-muted">Esta acción no se puede deshacer.</small>
                `,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Sí, eliminar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#d33",
                cancelButtonColor: "#6c757d"
            }).then(result => {

                if (result.isConfirmed) {
                    document.querySelector('#inputEliminarCajaId').value = id;
                    document.querySelector('#formEliminarCaja').submit();
                }

            });

        });
    });

});
</script>
