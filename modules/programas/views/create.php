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
                    <li class="breadcrumb-item"><i class="ti ti-home"></i> <a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= $routes['programas_index']; ?>">Programas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Crear Programa</li>
                </ul>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-md-12">
        <div class="card">


            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-white">
                    <i class="ti ti-plus text-white"></i> Crear Programa <br>
                    <small>los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</small>

                </h5>
            </div>

            <div class="card-body">
                <form method="post" action="/programas/store" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del programa</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                                <div class="invalid-feedback">
                                    Ingrese el nombre del programa.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="descripcion" name="descripcion"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="horas_practicas" class="form-label">Horas prácticas</label>
                                <input type="number" class="form-control" id="horas_practicas" name="horas_practicas" required>
                                <div class="invalid-feedback">
                                    Ingrese el número de horas prácticas.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="horas_teoricas" class="form-label">Horas Teóricas</label>
                                <input type="number" class="form-control" id="horas_teoricas" name="horas_teoricas" required>
                                <div class="invalid-feedback">
                                    Ingrese el número de horas teóricas..
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_servicio" class="form-label">Tipo de Servicio</label>
                                <select class="form-control" id="tipo_servicio" name="tipo_servicio" required>
                                    <option value="" disabled selected>Seleccione el tipo de servicio</option>
                                    <option value="Particular">PARTICULAR</option>
                                    <option value="Público">PÚBLICO</option>
                                </select>
                                <div class="invalid-feedback">
                                    Seleccione el tipo de servicio.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categoria" class="form-label">Categoría</label>
                                <select class="form-control" id="categoria" name="categoria" required>
                                    <option value="" disabled selected>Seleccione la categoría</option>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Seleccione la categoría.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_vehiculo_id" class="form-label">Tipo de Vehículo</label>
                                <select class="form-control" id="tipo_vehiculo_id" name="tipo_vehiculo_id" required>
                                    <option value="" disabled selected>Seleccione el tipo de vehículo</option>
                                    <?php foreach ($tiposVehiculo as $vehiculo): ?>
                                        <option value="<?= $vehiculo['id'] ?>"><?= $vehiculo['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">
                                    Seleccione el tipo de vehículo.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Crear</button>
                        </div>

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