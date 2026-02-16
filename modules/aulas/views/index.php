<?php
// Asegura rutas disponibles
$routes = $routes ?? include '../config/Routes.php';

// Helpers de mensajes (opcionales)
if (isset($_SESSION['success_message'])) : ?>
    <script>
        const msg = <?php echo json_encode($_SESSION['success_message']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: msg
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])) : ?>
    <script>
        const msg = <?php echo json_encode($_SESSION['error_message']); ?>;
        Swal.fire({
            icon: 'error',
            title: 'Oops…',
            text: msg
        });
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Aulas</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-sm-12">
        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>Listado de aulas</h5>
                <div>
                    <a href="<?= $routes['aulas_create'] ?>" class="btn btn-primary">Nueva aula</a>
                </div>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table class="table table-hover data-table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th style="width:120px;">Capacidad</th>
                                <th style="width:120px;">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($aulas as $aula): ?>
                                <tr>
                                    <td><?= htmlspecialchars($aula['nombre'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($aula['descripcion'] ?? '') ?></td>
                                    <td><span class="badge text-bg-secondary"><?= (int)($aula['capacidad'] ?? 0) ?></span></td>
                                    <td>
                                        <a href="<?= ($routes['aulas_edit'] ?? '/aulas/edit/') . urlencode($aula['id']) ?>"
                                            class="avtar avtar-xs btn-link-secondary" title="Editar aula">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>

                                        <!-- Botón eliminar -->
                                        <a href="#"
                                            class="avtar avtar-xs btn-link-secondary ms-2 btn-delete-aula"
                                            data-id="<?= (int)$aula['id'] ?>"
                                            data-name="<?= htmlspecialchars($aula['nombre'] ?? '', ENT_QUOTES) ?>"
                                            title="Eliminar aula">
                                            <i class="ti ti-trash f-20"></i>
                                        </a>

                                        <!-- Form de eliminación (POST) -->
                                        <form id="form-delete-<?= (int)$aula['id'] ?>"
                                            action="<?= ($routes['aulas_delete'] ?? '/aulas/delete/') . urlencode($aula['id']) ?>"
                                            method="post" class="d-none">
                                        </form>

                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-delete-aula');
        if (!btn) return;

        e.preventDefault();

        const id = btn.dataset.id;
        const name = btn.dataset.name || '';
        const submit = () => {
            const form = document.getElementById('form-delete-' + id);
            if (form) form.submit();
        };

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Eliminar aula',
                html: '¿Seguro que deseas eliminar el aula <b>' + name + '</b>? Esta acción no se puede deshacer.',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) submit();
            });
        } else {
            if (confirm('¿Seguro que deseas eliminar el aula "' + name + '"?')) submit();
        }
    });
</script>