<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/matriculas/">Matrículas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Detalle matrícula</li>
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
                <h5>Datos de la matrícula</h5>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="id" class="form-label">ID</label>
                            <input type="text" class="form-control" id="id" name="id" value="<?= $matricula['id'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_inscripcion" class="form-label">Fecha Inscripción</label>
                            <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" value="<?= $matricula['fecha_inscripcion'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_inscripcion" class="form-label">Fecha Vencimiento</label>
                            <input type="date" class="form-control" id="fecha_vencimiento" name="fecha_vencimiento" value="<?= $matricula['fecha_vencimiento'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_enrolamiento" class="form-label">Fecha Enrolamiento</label>
                            <input type="date" class="form-control" id="fecha_enrolamiento" name="fecha_enrolamiento" value="<?= $matricula['fecha_enrolamiento'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_aprovacion_teorico" class="form-label">Fecha Aprobación Teórico</label>
                            <input type="date" class="form-control" id="fecha_aprovacion_teorico" name="fecha_aprovacion_teorico" value="<?= $matricula['fecha_aprovacion_teorico'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_aprovacion_practico" class="form-label">Fecha Aprobación Práctico</label>
                            <input type="date" class="form-control" id="fecha_aprovacion_practico" name="fecha_aprovacion_practico" value="<?= $matricula['fecha_aprovacion_practico'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="fecha_certificacion" class="form-label">Fecha Certificación</label>
                            <input type="date" class="form-control" id="fecha_certificacion" name="fecha_certificacion" value="<?= $matricula['fecha_certificacion'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="programas" class="form-label">Programa</label>
                            <input type="text" class="form-control" id="programas" name="programas" value="<?= $matricula['programas'][0]['programa_nombre'] ?? 'Sin programa' ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estudiante_id" class="form-label">Estudiante</label>
                            <input type="text" class="form-control" id="estudiante_id" name="estudiante_id" value="<?= $matricula['estudiante_nombres'] . ' ' . $matricula['estudiante_apellidos'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="tipo_solicitud" class="form-label">Tipo de Solicitud</label>
                            <input type="text" class="form-control" id="tipo_solicitud" name="tipo_solicitud" value="<?= $matricula['tipo_solicitud_nombre'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <input type="text" class="form-control" id="estado" name="estado" value="<?= $matricula['estado_nombre'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Convenio</label>
                            <input type="text" class="form-control" id="convenio" name="convenio" value="<?= $matricula['convenio_nombre'] ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="valor_matricula" class="form-label">Valor Matrícula</label>
                            <input type="text" class="form-control" id="valor_matricula" name="valor_matricula" value="<?= '$' . number_format($matricula['valor_matricula'], 0, ',', '.') ?>" disabled>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" disabled><?= $matricula['observaciones'] ?></textarea>
                        </div>
                    </div>

                    <div class="col-md-12 text-end">
                        <a href="/matriculas/" class="btn btn-secondary">Volver</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>