<?php $routes = include '../config/Routes.php'; ?>

<div class="container mt-4">
    <h2 class="mb-4">Detalle del Vehículo</h2>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5><?= htmlspecialchars($vehiculo['placa']) ?> - <?= htmlspecialchars($vehiculo['modelo']) ?></h5>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Foto del vehículo -->
                <div class="col-md-4 text-center">
                    <?php if (!empty($vehiculo['foto'])): ?>
                        <img src="../files/fotos_vehiculos/<?= htmlspecialchars($vehiculo['foto']) ?>" 
                             alt="Foto del Vehículo" 
                             class="img-fluid rounded" 
                             style="max-width: 100%; height: auto;">
                    <?php else: ?>
                        <img src="../assets/images/default-car.jpg" 
                             alt="Foto no disponible" 
                             class="img-fluid rounded" 
                             style="max-width: 100%; height: auto;">
                    <?php endif; ?>
                </div>

                <!-- Detalles del vehículo -->
                <div class="col-md-8">
                    <table class="table table-striped">
                        <tr>
                            <th>Placa</th>
                            <td><?= htmlspecialchars($vehiculo['placa']) ?></td>
                        </tr>
                        <tr>
                            <th>Tipo de Vehículo</th>
                            <td><?= htmlspecialchars($vehiculo['tipo_vehiculo']) ?></td>
                        </tr>
                        <tr>
                            <th>Tipo de Combustible</th>
                            <td><?= htmlspecialchars($vehiculo['tipo_combustible']) ?></td>
                        </tr>
                        <tr>
                            <th>Modelo</th>
                            <td><?= htmlspecialchars($vehiculo['modelo']) ?></td>
                        </tr>
                        <tr>
                            <th>Fecha de Matrícula</th>
                            <td><?= htmlspecialchars($vehiculo['fecha_matricula']) ?></td>
                        </tr>
                        <tr>
                            <th>VIN</th>
                            <td><?= htmlspecialchars($vehiculo['vin']) ?></td>
                        </tr>
                        <tr>
                            <th>Propietario</th>
                            <td><?= htmlspecialchars($vehiculo['propietario']) ?></td>
                        </tr>
                        <tr>
                            <th>Identificación del Propietario</th>
                            <td><?= htmlspecialchars($vehiculo['identificacion']) ?></td>
                        </tr>
                        <tr>
                            <th>Número de Motor</th>
                            <td><?= htmlspecialchars($vehiculo['numero_motor']) ?></td>
                        </tr>
                        <tr>
                            <th>Número de Chasis</th>
                            <td><?= htmlspecialchars($vehiculo['numero_chasis']) ?></td>
                        </tr>
                        <tr>
                            <th>Número de Serie</th>
                            <td><?= htmlspecialchars($vehiculo['numero_serie']) ?></td>
                        </tr>
                        <tr>
                            <th>Carrocería</th>
                            <td><?= htmlspecialchars($vehiculo['carroceria']) ?></td>
                        </tr>
                        <tr>
                            <th>Cilindrada</th>
                            <td><?= htmlspecialchars($vehiculo['cilindrada']) ?></td>
                        </tr>
                        <tr>
                            <th>Capacidad</th>
                            <td><?= htmlspecialchars($vehiculo['capacidad']) ?></td>
                        </tr>
                        <tr>
                            <th>Gps</th>
                            <td><?= htmlspecialchars($vehiculo['id_traccar']) ?></td>
                        </tr>
                        <tr>
                            <th>Endpoint</th>
                            <td><?= htmlspecialchars($vehiculo['endpoint']) ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <a href="<?= $routes['vehiculos_index'] ?>" class="btn btn-secondary"><i class="ti ti-arrow-left"></i> Volver</a>
</div>
