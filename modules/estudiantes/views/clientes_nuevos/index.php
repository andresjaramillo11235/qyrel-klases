<?php $routes = include '../config/Routes.php'; ?>

<?php if (!empty($_SESSION['flash_success'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'success',
                title: '¡Operación exitosa!',
                text: <?= json_encode($_SESSION['flash_success'], JSON_UNESCAPED_UNICODE) ?>,
                timer: 2500,
                showConfirmButton: true,
            });
        });
    </script>
<?php unset($_SESSION['flash_success']);
endif; ?>


<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Nuevos clientes</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-3 mb-sm-0">Nuevos clientes (pendientes)</h5>

                <div>
                    <a href="<?= htmlspecialchars($routes['clientes_nuevos_create'] ?? '#') ?>" class="btn btn-primary">
                        <i class="ph-duotone ph-user-plus me-1"></i> Crear nuevo cliente
                    </a>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">

                    <table class="table table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:140px;">Creado</th>
                                <th style="width:150px;">Documento</th>
                                <th>Nombre</th>
                                <th style="width:160px;">Teléfono</th>
                                <th style="width:120px;">Estado</th>
                                <th style="width:1px;" class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rows)): ?>
                                <?php foreach ($rows as $r): ?>
                                    <tr>
                                        <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($r['created_at'] ?? 'now'))) ?></td>
                                        <td><?= htmlspecialchars($r['numero_documento'] ?? '') ?></td>
                                        <td><?= strtoupper(htmlspecialchars(trim(($r['nombres'] ?? '') . ' ' . ($r['apellidos'] ?? '')))) ?></td>
                                        <td><?= htmlspecialchars($r['telefono'] ?? '') ?></td>
                                        <td>
                                            <?php if ((int)($r['estado'] ?? 1) === 1): ?>
                                                <span class="badge bg-warning text-dark">Pendiente</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Completado</span>
                                            <?php endif; ?>
                                        </td>

                                        <td class="text-end">
                                            <div class="btn-group">
                                                <form action="<?= htmlspecialchars($routes['clientes_nuevos_send_wa'] ?? '') ?>"
                                                    method="post" class="m-0 p-0 d-inline" target="_blank">
                                                    <input type="hidden" name="id" value="<?= (int)$r['estudiante_id'] ?>">
                                                    <button type="submit"
                                                        class="btn btn-success btn-sm d-inline-flex align-items-center gap-1"
                                                        data-bs-toggle="tooltip"
                                                        title="Enviar WhatsApp">
                                                        <i class="ph-duotone ph-whatsapp-logo"></i>
                                                        <span>WhatsApp</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">No hay clientes nuevos pendientes.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>