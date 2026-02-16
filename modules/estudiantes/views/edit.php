<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/estudiantes/">Estudiantes</a></li>
                    <li class="breadcrumb-item" aria-current="page">Modificar estudiante</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h5>Modificar datos del estudiante.</h5>
            </div>

            <div class="card-body">

                <form action="/estudiantesupdate/<?php echo $estudiante['id']; ?>" method="POST" enctype="multipart/form-data" class="validate-me" data-validate>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <?php if (!empty($estudiante['foto'])) : ?>
                                    <img src="../files/fotos_estudiantes/<?= $estudiante['foto'] ?>" alt="Foto del instructor" class="img-thumbnail mt-2" style="max-width: 150px;">
                                    <input type="hidden" name="existing_foto" value="<?= $estudiante['foto'] ?>">
                                <?php endif; ?>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombres" class="form-label">Nombres <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control text-uppercase" id="nombres" name="nombres" value="<?php echo htmlspecialchars($estudiante['nombres']); ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese los nombres.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control text-uppercase" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($estudiante['apellidos']); ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese los apellidos.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_documento" class="form-label">Tipo de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                                    <?php foreach ($parametricTables['tipo_documento'] as $tipo_documento) : ?>
                                        <option value="<?php echo $tipo_documento['id']; ?>" <?php echo $estudiante['tipo_documento'] == $tipo_documento['id'] ? 'selected' : ''; ?>><?php echo $tipo_documento['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione el tipo de documento.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_documento" class="form-label">Número de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="<?php echo htmlspecialchars($estudiante['numero_documento']); ?>" readonly>
                                <input type="hidden" name="numero_documento" value="<?php echo htmlspecialchars($estudiante['numero_documento']); ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expedicion_departamento" class="form-label">Departamento de Expedición <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control text-uppercase" id="expedicion_departamento" name="expedicion_departamento"
                                    value="<?= htmlspecialchars($estudiante['expedicion_departamento'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese el departamento de expedición.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expedicion_ciudad" class="form-label">Ciudad de Expedición <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control text-uppercase" id="expedicion_ciudad" name="expedicion_ciudad"
                                    value="<?= htmlspecialchars($estudiante['expedicion_ciudad'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese la ciudad de expedición.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_expedicion" class="form-label">Fecha de Expedición <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" value="<?php echo htmlspecialchars($estudiante['fecha_expedicion']); ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese la fecha de expedición.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grupo_sanguineo" class="form-label">Grupo Sanguíneo <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="grupo_sanguineo" name="grupo_sanguineo" required>
                                    <?php foreach ($parametricTables['grupo_sanguineo'] as $grupo_sanguineo) : ?>
                                        <option value="<?php echo $grupo_sanguineo['id']; ?>" <?php echo $estudiante['grupo_sanguineo'] == $grupo_sanguineo['id'] ? 'selected' : ''; ?>><?php echo $grupo_sanguineo['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione el grupo sanguíneo.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="genero" class="form-label">Género </label>
                                <select class="form-control" id="genero" name="genero" required>
                                    <?php foreach ($parametricTables['genero'] as $genero) : ?>
                                        <option value="<?php echo $genero['id']; ?>" <?php echo $estudiante['genero'] == $genero['id'] ? 'selected' : ''; ?>><?php echo $genero['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione el género.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($estudiante['fecha_nacimiento']); ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese la fecha de nacimiento.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="email" class="form-control text-uppercase" id="correo" name="correo"
                                    value="<?= htmlspecialchars($estudiante['correo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese el correo.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="celular" class="form-label">Celular <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="number" class="form-control" id <input type="text" class="form-control" id="celular" name="celular" value="<?php echo htmlspecialchars($estudiante['celular']); ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese el número de celular.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="estado" name="estado" <?= $estudiante['estado'] == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" id="estado-label" for="estado"><?= $estudiante['estado'] == '1' ? 'Activo' : 'Inactivo' ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="direccion_residencia" class="form-label">Dirección de Residencia</label>
                                <input type="text" class="form-control text-uppercase" id="direccion_residencia" name="direccion_residencia"
                                    value="<?= htmlspecialchars($estudiante['direccion_residencia'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="barrio" class="form-label">Barrio</label>
                                <input type="text" class="form-control text-uppercase" id="barrio" name="barrio"
                                    value="<?= htmlspecialchars($estudiante['barrio'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="direccion_oficina" class="form-label">Dirección de Oficina</label>
                                <input type="text" class="form-control text-uppercase" id="direccion_oficina" name="direccion_oficina"
                                    value="<?= htmlspecialchars($estudiante['direccion_oficina'] ?? '', ENT_QUOTES, 'UTF-8') ?>">


                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono_oficina" class="form-label">Teléfono de Oficina</label>
                                <input type="number" class="form-control" id="telefono_oficina" name="telefono_oficina" value="<?php echo htmlspecialchars($estudiante['telefono_oficina']); ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado_civil" class="form-label">Estado Civil</label>
                                <select class="form-control" id="estado_civil" name="estado_civil">
                                    <?php foreach ($parametricTables['estado_civil'] as $estado_civil) : ?>
                                        <option value="<?php echo $estado_civil['id']; ?>" <?php echo $estudiante['estado_civil'] == $estado_civil['id'] ? 'selected' : ''; ?>><?php echo $estado_civil['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ocupacion" class="form-label">Ocupación</label>
                                <select class="form-control" id="ocupacion" name="ocupacion">
                                    <?php foreach ($parametricTables['ocupacion'] as $ocupacion) : ?>
                                        <option value="<?php echo $ocupacion['id']; ?>" <?php echo $estudiante['ocupacion'] == $ocupacion['id'] ? 'selected' : ''; ?>><?php echo $ocupacion['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="jornada" class="form-label">Jornada</label>
                                <select class="form-control" id="jornada" name="jornada">
                                    <?php foreach ($parametricTables['jornada'] as $jornada) : ?>
                                        <option value="<?php echo $jornada['id']; ?>" <?php echo $estudiante['jornada'] == $jornada['id'] ? 'selected' : ''; ?>><?php echo $jornada['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estrato" class="form-label">Estrato</label>
                                <select class="form-control" id="estrato" name="estrato">
                                    <?php foreach ($parametricTables['estrato'] as $estrato) : ?>
                                        <option value="<?php echo $estrato['id']; ?>" <?php echo $estudiante['estrato'] == $estrato['id'] ? 'selected' : ''; ?>><?php echo $estrato['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="seguridad_social" class="form-label">Seguridad Social</label>
                                <select class="form-control" id="seguridad_social" name="seguridad_social">
                                    <?php foreach ($parametricTables['seguridad_social'] as $seguridad_social) : ?>
                                        <option value="<?php echo $seguridad_social['id']; ?>" <?php echo $estudiante['seguridad_social'] == $seguridad_social['id'] ? 'selected' : ''; ?>><?php echo $seguridad_social['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nivel_educacion" class="form-label">Nivel de Educación</label>
                                <select class="form-control" id="nivel_educacion" name="nivel_educacion">
                                    <?php foreach ($parametricTables['nivel_educacion'] as $nivel_educacion) : ?>
                                        <option value="<?php echo $nivel_educacion['id']; ?>" <?php echo $estudiante['nivel_educacion'] == $nivel_educacion['id'] ? 'selected' : ''; ?>><?php echo $nivel_educacion['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ciudad_origen" class="form-label">Ciudad de Origen</label>
                                <input type="text" class="form-control text-uppercase" id="ciudad_origen" name="ciudad_origen"
                                    value="<?= htmlspecialchars($estudiante['ciudad_origen'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="discapacidad" class="form-label">Discapacidad</label>
                                <select class="form-control" id="discapacidad" name="discapacidad">
                                    <?php foreach ($parametricTables['discapacidad'] as $discapacidad) : ?>
                                        <option value="<?php echo $discapacidad['id']; ?>" <?php echo $estudiante['discapacidad'] == $discapacidad['id'] ? 'selected' : ''; ?>><?php echo $discapacidad['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_contacto" class="form-label">Nombre del Contacto</label>
                                <input type="text" class="form-control text-uppercase" id="nombre_contacto" name="nombre_contacto"
                                    value="<?= htmlspecialchars($estudiante['nombre_contacto'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono_contacto" class="form-label">Teléfono del Contacto</label>
                                <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto"
                                    value="<?= htmlspecialchars($estudiante['telefono_contacto'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>

                                <?php
                                if (!function_exists('h')) {
                                    function h($v): string
                                    {
                                        return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
                                    }
                                }
                                ?>

                                <textarea class="form-control text-uppercase" id="observaciones" name="observaciones">
                                    <?= h($estudiante['observaciones'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <?php if ($this->userUtils->isSuperAdmin($currentUserId)) : ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="empresa_id" class="form-label">Empresa</label>
                                    <select class="form-control" id="empresa_id" name="empresa_id">
                                        <?php foreach ($parametricTables['empresa'] as $empresa) : ?>
                                            <option value="<?php echo $empresa['id']; ?>" <?php echo $estudiante['empresa_id'] == $empresa['id'] ? 'selected' : ''; ?>><?php echo $empresa['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-primary">Enviar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/plugins/bouncer.min.js"></script>
<script src="../assets/js/pages/form-validation.js"></script>

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


    $(document).ready(function() {
        $('#estado').on('change', function() {
            if ($(this).is(':checked')) {
                $('#estado-label').text('Activo');
            } else {
                $('#estado-label').text('Inactivo');
            }
        });
    });
</script>