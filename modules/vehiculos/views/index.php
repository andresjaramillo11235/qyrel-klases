<?php
$routes = include '../config/Routes.php';
include '../shared/utils/AjustarImagen.php';
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

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Vehículos</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0"><i class="ti ti-car"></i> Listado de Vehículos</h5>
                    <div>
                        <a href="<?= $routes['vehiculos_create']; ?>" class="btn btn-primary">
                            <i class="ti ti-plus"></i> Crear Vehículo
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body pt-3">
                <div class="table-responsive">
                    <table class="table table-hover data-table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th> </th>
                                <th>Placa</th>
                                <th>Modelo</th>
                                <th>Tipo de Vehículo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($vehiculos as $vehiculo) : ?>
                                <tr>
                                    <td>
                                        <?php
                                        // Ruta de la imagen
                                        $fotoPath = "../files/fotos_vehiculos/" . ($vehiculo['foto'] ?? '');

                                        // Comprobar si la imagen existe
                                        if (!empty($vehiculo['foto']) && file_exists($fotoPath)) {
                                            // Ajustar las dimensiones de la imagen
                                            list($imgWidth, $imgHeight) = ajustarImagen($fotoPath, 50, 50);
                                        } else {
                                            // Usar imagen por defecto
                                            $fotoPath = "../files/fotos_vehiculos/default-vehicle.png";
                                            $imgWidth = 50;
                                            $imgHeight = 50;
                                        }
                                        ?>
                                        <img src="<?= htmlspecialchars($fotoPath) ?>" 
                                            alt="Foto del vehículo" 
                                            style="width: <?= $imgWidth ?>px; height: <?= $imgHeight ?>px; 
                                            object-fit: cover; " class="img-thumbnail mt-2 wid-40">

                                    </td>
                                    <td><?= htmlspecialchars($vehiculo['placa'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($vehiculo['modelo'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($vehiculo['tipo_vehiculo'] ?? '') ?></td>
                                    <td>
                                        <a href="<?= $routes['vehiculos_detail'] ?><?= $vehiculo['id'] ?>" class="avtar avtar-xs btn-link-primary" title="Ver Detalle">
                                            <i class="ti ti-eye f-20"></i>
                                        </a>
                                        <!-- Botón de editar -->
                                        <a href="<?= $routes['vehiculos_edit'] ?><?= $vehiculo['id'] ?>" class="avtar avtar-xs btn-link-warning" title="Editar">
                                            <i class="ti ti-edit f-20"></i>
                                        </a>
                                        <!-- Botón de eliminar -->
                                        <a href="/vehiculos/delete/<?= $vehiculo['id'] ?>" class="avtar avtar-xs btn-link-danger eliminar-vehiculo" title="Eliminar">
                                            <i class="ti ti-trash f-20"></i>
                                        </a>
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