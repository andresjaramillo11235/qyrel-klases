<?php $routes = include '../config/Routes.php'; ?>

<style>
    input,
    textarea {
        text-transform: uppercase;
    }
</style>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><i class="ti ti-home"></i><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= $routes['dispositivos_gps_index']; ?>">Dispositivos GPS</a></li>
                    <li class="breadcrumb-item" aria-current="page">Crear Dispositivo</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" style="background-color: #d6d6d6; color: black;">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">
                        <i class="fas fa-plus-circle"></i> Crear Dispositivo GPS
                        <small>los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</small>
                    </h5>
                </div>
            </div>

            <div class="card-body">
                <form action="<?= $routes['dispositivos_gps_store'] ?>" method="POST" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <div class="invalid-feedback">
                                    Ingrese el nombre del dispositivo.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="imei" class="form-label">IMEI</label>
                                <input type="text" class="form-control" id="imei" name="imei" required>
                                <div class="invalid-feedback">
                                    Ingrese el Imei del dispositivo.
                                </div>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="id_traccar" class="form-label">ID Gps</label>
                                <input type="number" class="form-control" id="id_traccar" name="id_traccar" required>
                                <div class="invalid-feedback">
                                    Ingrese el ID Gps del dispositivo.
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="marca" class="form-label">Marca</label>
                                <input type="text" class="form-control" id="marca" name="marca">
                            </div>
                        </div> -->

                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="linea" class="form-label">Número de línea</label>
                                <input type="text" class="form-control" id="linea" name="linea">
                            </div>
                        </div> -->

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vehiculo_id" class="form-label">Vehículo Asociado</label>
                                <select class="form-control" id="vehiculo_id" name="vehiculo_id">
                                    <option value="">Seleccione un Vehículo</option>
                                    <?php foreach ($vehiculos as $vehiculo): ?>
                                        <option value="<?php echo htmlspecialchars($vehiculo['id']); ?>">
                                            <?php echo htmlspecialchars($vehiculo['placa']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="api_id" class="form-label">Proveedor GPS</label>
                                <select class="form-control" id="api_id" name="api_id">
                                    <option value="">Seleccione una API</option>
                                    <?php foreach ($apis as $api): ?>
                                        <option value="<?php echo htmlspecialchars($api['id']); ?>">
                                            <?php echo htmlspecialchars($api['nombre']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">Crear</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/plugins/bouncer.min.js"></script>
<script src="../assets/js/pages/form-validation.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<script>
    // Bootstrap validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>