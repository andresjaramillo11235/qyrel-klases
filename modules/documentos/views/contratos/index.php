<?php $routes = include '../config/Routes.php'; ?>

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

<h4>Gestión de Contrato</h4>
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#crearSeccionModal">
    <i class="ti ti-plus"></i> Nueva Sección
</button>

<p>La sección con orden 0 será tratada como el encabezado del contrato.</p>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Orden</th>
            <th>Título</th>
            <th>Contenido</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($contratos as $contrato): ?>
            <tr>
                <td><?= $contrato['orden'] ?></td>
                <td><?= $contrato['titulo_seccion'] ?></td>
                <td><?= substr($contrato['contenido'], 0, 50) . '...' ?></td>
                <td>
                    <a href="#" class="avtar avtar-xs btn-link-warning" title="Editar Sección" data-bs-toggle="modal" data-bs-target="#editarSeccionModal<?= $contrato['id'] ?>">
                        <i class="ti ti-edit f-20"></i>
                    </a>
                    <a href="#" class="avtar avtar-xs btn-link-danger" title="Eliminar Sección" onclick="confirmarEliminar('<?= $routes['documento_contrato_delete'] . $contrato['id'] ?>');">
                        <i class="ti ti-trash f-20"></i>
                    </a>
                </td>
            </tr>

            <!-- Modal para Editar Sección -->
            <div class="modal fade" id="editarSeccionModal<?= $contrato['id'] ?>" tabindex="-1" aria-labelledby="editarSeccionLabel<?= $contrato['id'] ?>" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form action="<?= $routes['documento_contrato_update'] ?>" method="POST">
                            <div class="modal-header">
                                <h5 class="modal-title" id="editarSeccionLabel<?= $contrato['id'] ?>">Editar Sección</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="id" value="<?= $contrato['id'] ?>">
                                <div class="mb-3">
                                    <label for="titulo" class="form-label">Título</label>
                                    <input type="text" class="form-control" name="titulo" value="<?= $contrato['titulo_seccion'] ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contenido" class="form-label">Contenido</label>
                                    <textarea class="form-control" name="contenido" rows="4" required><?= $contrato['contenido'] ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="orden" class="form-label">Orden</label>
                                    <input type="number" class="form-control" name="orden" value="<?= $contrato['orden'] ?>" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Modal para Crear Nueva Sección -->
<div class="modal fade" id="crearSeccionModal" tabindex="-1" aria-labelledby="crearSeccionLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= $routes['documento_contrato_store'] ?>" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="crearSeccionLabel">Nueva Sección</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="titulo" class="form-label">Título</label>
                        <input type="text" class="form-control" name="titulo">
                    </div>
                    <div class="mb-3">
                        <label for="contenido" class="form-label">Contenido</label>
                        <textarea class="form-control" name="contenido" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="orden" class="form-label">Orden</label>
                        <input type="number" class="form-control" name="orden" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script para Confirmación de Eliminación -->
<script>
    function confirmarEliminar(url) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "No podrás revertir esto",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }
</script>