<?php $routes = include '../config/Routes.php'; ?>

<!-- Mostrar mensajes de éxito o error -->
<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: "<?php echo $_SESSION['success_message']; ?>",
            });
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="/home/" class="text-primary"></a>
                    <i class="ti ti-home"></i> Inicio
                </li>

                <li class="breadcrumb-item">
                    <a href="<?= $routes['programas_index'] ?>" class="text-primary">Programas</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Temas</li>
            </ol>
        </nav>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- Botón para crear un nuevo tema -->
<div class="container mb-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0">Gestión de Temas del Programa</h5>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearTemaModal">
        <i class="ti ti-plus"></i> Crear Nuevo Tema
    </button>
</div>

<!-- Tabla de temas -->

<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <h6 class="mb-0">Listado de Temas</h6>
        <span class="badge bg-light text-dark">Total: <?= count($temas) ?> temas</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center" id="pc-dt-simple">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nombre del Tema</th>
                        <th>Número de Horas</th>
                        <th>Orden</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($temas as $index => $tema): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($tema['nombre_clase']) ?></td>
                            <td><?= htmlspecialchars($tema['numero_horas']) ?></td>
                            <td><?= htmlspecialchars($tema['orden']) ?></td>
                            <td>
                                <a href="<?= $routes['programas_temas_edit'] . $tema['id'] ?>" class="btn btn-sm btn-warning" title="Modificar Tema">
                                    <i class="ti ti-edit"></i> Editar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>




<!-- Modal para crear un nuevo tema -->
<div class="modal fade" id="crearTemaModal" tabindex="-1" aria-labelledby="crearTemaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> <!-- Modal más amplio -->
        <div class="modal-content">

            <!-- Encabezado del modal -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="crearTemaModalLabel">
                    <i class="ti ti-bookmark"></i> Crear Nuevo Tema
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            <!-- Cuerpo del modal -->
            <div class="modal-body">
                <form action="<?= $routes['programas_temas_store'] ?>" method="POST">

                    <input type="hidden" name="programa_id" value="<?= $programaId ?>">

                    <!-- Campo: Nombre del Tema -->
                    <div class="mb-4">
                        <label for="nombre_clase" class="form-label">
                            Nombre del Tema <span class="text-danger">*</span>
                        </label>
                        <input
                            type="text"
                            class="form-control form-control-lg"
                            id="nombre_clase"
                            name="nombre_clase"
                            placeholder="Ingrese el nombre del tema"
                            required>
                    </div>

                    <!-- Campo: Número de Horas -->
                    <div class="mb-4">
                        <label for="numero_horas" class="form-label">
                            Número de Horas <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control form-control-lg"
                            id="numero_horas"
                            name="numero_horas"
                            placeholder="Ingrese el número de horas"
                            min="1"
                            required>
                    </div>

                    <!-- Campo: Orden -->
                    <div class="mb-4">
                        <label for="orden" class="form-label">
                            Orden <span class="text-danger">*</span>
                        </label>
                        <input
                            type="number"
                            class="form-control form-control-lg"
                            id="orden"
                            name="orden"
                            placeholder="Ingrese el orden del tema"
                            min="1"
                            required>
                    </div>


                    <!-- Notas -->
                    <p class="text-muted">
                        <i class="ph-duotone ph-asterisk"></i> Los campos marcados con * son obligatorios.
                    </p>

                    <!-- Pie del modal (dentro del formulario) -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="ti ti-x"></i> Cerrar
                        </button>
                        <button type="submit" class="btn btn-success">
                            <i class="ti ti-check"></i> Guardar Tema
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/datatables-config.js"></script>