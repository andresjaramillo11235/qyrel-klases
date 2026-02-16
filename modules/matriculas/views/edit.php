<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/matriculas/">Matrículas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Modificar matrícula</li>
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
                <h5>Datos de la matrícula: los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</h5>
            </div>

            <div class="card-body">

                <form action="/matriculasupdate/" method="post" class="needs-validation" novalidate>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inscripcion" class="form-label">Fecha Inscripción</label>
                                <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="<?= $matricula['fecha_inscripcion'] ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_vencimiento" class="form-label">Fecha Vencimiento</label>
                                <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="<?= $matricula['fecha_vencimiento'] ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estudiante_id" class="form-label">Estudiante</label>
                                <select class="form-control" id="estudiante_id" name="estudiante_id" readonly>
                                    <?php foreach ($estudiantes as $estudiante) : ?>
                                        <option value="<?= $estudiante['id'] ?>" <?= $matricula['estudiante_id'] == $estudiante['id'] ? 'selected' : '' ?>><?= $estudiante['nombres'] . ' ' . $estudiante['apellidos'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="programa_id" class="form-label">Programas</label>
                                <select class="form-control" id="programa_id" name="programa_id[]" multiple>
                                    <?php foreach ($programas as $programa) : ?>
                                        <option value="<?= $programa['id'] ?>" <?= in_array($programa['id'], $matriculaProgramas) ? 'selected' : '' ?>><?= $programa['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div> -->

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="programa_id" class="form-label">Programa</label>
                                <select class="form-control" id="programa_id" name="programa_id">
                                    <option value="">Seleccione un programa</option>
                                    <?php foreach ($programas as $programa) : ?>
                                        <option value="<?= $programa['id'] ?>" <?= $programa['id'] == $programaSeleccionado ? 'selected' : '' ?>>
                                            <?= $programa['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_enrolamiento" class="form-label">Fecha Enrolamiento</label>
                                <input type="date" class="form-control" id="fecha_enrolamiento" name="fecha_enrolamiento" value="<?= $matricula['fecha_enrolamiento'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_aprovacion_teorico" class="form-label">Fecha Aprobación Teórico</label>
                                <input type="date" class="form-control" id="fecha_aprovacion_teorico" name="fecha_aprovacion_teorico" value="<?= $matricula['fecha_aprovacion_teorico'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_aprovacion_practico" class="form-label">Fecha Aprobación Práctico</label>
                                <input type="date" class="form-control" id="fecha_aprovacion_practico" name="fecha_aprovacion_practico" value="<?= $matricula['fecha_aprovacion_practico'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_certificacion" class="form-label">Fecha Certificación</label>
                                <input type="date" class="form-control" id="fecha_certificacion" name="fecha_certificacion" value="<?= $matricula['fecha_certificacion'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_solicitud_id" class="form-label">Tipo de Solicitud <span class="text-danger">*</span></label>
                                <select class="form-control" id="tipo_solicitud_id" name="tipo_solicitud_id" required>
                                    <?php foreach ($tiposSolicitud as $tipo) : ?>
                                        <option value="<?= $tipo['id'] ?>" <?= $matricula['tipo_solicitud_id'] == $tipo['id'] ? 'selected' : '' ?>><?= $tipo['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado <span class="text-danger">*</span></label>
                                <select class="form-control" id="estado" name="estado_id" required>
                                    <?php foreach ($estadosMatricula as $estado) : ?>
                                        <option value="<?= $estado['id'] ?>" <?= $matricula['estado'] == $estado['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="convenio_id" class="form-label">Convenio <span class="text-danger">*</span></label>
                                <select class="form-control" id="convenio_id" name="convenio_id" required>
                                    <?php foreach ($convenios as $convenio) : ?>
                                        <option value="<?= $convenio['id'] ?>" <?= $matricula['convenio_id'] == $convenio['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($convenio['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valor_matricula" class="form-label">Valor de Matrícula <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="valor_matricula" name="valor_matricula" value="<?= htmlspecialchars($matricula['valor_matricula']) ?>" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"><?= $matricula['observaciones'] ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="hidden" name="empresa_id" value="<?= $_SESSION['empresa_id'] ?>">
                            <input type="hidden" name="id" value="<?= $matricula['id'] ?>">
                            <button type="submit" id="submit_button" class="btn btn-primary">Enviar</button>
                        </div>

                    </div>

                </form>

            </div>


        </div>
    </div>
</div>
<!-- [ Main Content ] end -->


<script>
    (function() {
        'use strict';
        var forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>