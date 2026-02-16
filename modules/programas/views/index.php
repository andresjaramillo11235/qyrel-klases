<?php $routes = include '../config/Routes.php'; ?>
<?php include '../shared/utils/InsertarSaltosDeLinea.php'; ?>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['success_message']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>


<?php if (isset($_SESSION['error_message'])) : ?>
    <script>
        const errorMessage = <?php echo json_encode($_SESSION['error_message']); ?>;
        Swal.fire({
            icon: 'error',
            title: 'No se puede eliminar',
            text: errorMessage,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Entendido'
        });
    </script>
    <?php unset($_SESSION['error_message']); // Limpiar mensaje de error 
    ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['success_message']); ?>;
        Swal.fire({
            icon: 'success',
            title: 'Programa eliminado',
            text: successMessage,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Ok'
        });
    </script>
    <?php unset($_SESSION['success_message']); // Limpiar mensaje de éxito 
    ?>
<?php endif; ?>

<?php if (isset($_SESSION['delete_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: '¡Ingreso Eliminado!',
                text: "<?php echo $_SESSION['delete_message']; ?>"
            });
        });
    </script>
    <?php unset($_SESSION['delete_message']); ?>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="/home/"><i class="ti ti-home"></i> Inicio</a>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">Programas</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">
                    <i class="ti ti-book text-white"></i> Listado de Programas
                </h5>
                <a href="<?= $routes['programas_create']; ?>" class="btn btn-light">
                    <i class="ti ti-plus"></i> Crear Programa
                </a>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover data-table" id="pc-dt-simple">
                        <thead class="bg-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Categoría</th>
                                <th>Horas<br>Prácticas</th>
                                <th>Horas<br>Teóricas</th>
                                <th>Tipo<br>vehículo</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th>Temas</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($programas as $index => $programa) : ?>
                                <tr>
                                    <td><?= insertarSaltosDeLinea(htmlspecialchars(strtoupper($programa['nombre'])), 3) ?></td>
                                    <td><?= insertarSaltosDeLinea(strtoupper($programa['descripcion']), 4) ?></td>
                                    <td><?= htmlspecialchars($programa['categoria_nombre']) ?></td>
                                    <td><?= $programa['horas_practicas'] ?></td>
                                    <td><?= $programa['horas_teoricas'] ?></td>
                                    <td><?= $programa['vehiculo_nombre'] ?></td>
                                    <td><?= strtoupper($programa['tipo_servicio']) ?></td>
                                    <td>
                                        <span class="badge <?= $programa['estado'] == 1 ? 'bg-success' : 'bg-secondary' ?>">
                                            <?= $programa['estado'] == 1 ? 'ACTIVO' : 'INACTIVO' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?= $routes['programas_temas_index'] ?><?= $programa['id'] ?>" class="btn btn-info btn-sm">
                                            <i class="ti ti-car"></i> Clases Práctica
                                        </a>

                                        <a href="<?= $routes['clases_teoricas_temas_index'] ?><?= $programa['id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="ti ti-school"></i> Temas Teoría
                                        </a>
                                    </td>
                                    <td>
                                        <a href="<?= $routes['programas_edit'] ?><?= $programa['id'] ?>" class="btn btn-info btn-sm" title="Editar">
                                            <i class="ti ti-edit"></i>
                                        </a>

                                        <button class="btn btn-danger btn-sm btn-eliminar" title="Eliminar" data-id="<?= $programa['id'] ?>">
                                            <i class="ti ti-trash"></i>
                                        </button>

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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/datatables-config.js"></script>

<script>
    $(document).ready(function() {
        $(document).on("click", ".btn-eliminar", function() {
            var programaId = $(this).data("id");

            // ⚠️ Mostrar alerta de confirmación antes de eliminar
            Swal.fire({
                title: "¿Estás seguro?",
                text: "Esta acción eliminará el programa y no se puede deshacer.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Sí, eliminar"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirigir al backend para manejar la eliminación y validación
                    window.location.href = "<?= $routes['programas_delete'] ?>" + programaId;
                }
            });
        });


    });
</script>