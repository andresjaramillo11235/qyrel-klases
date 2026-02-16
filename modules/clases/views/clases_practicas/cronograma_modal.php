<div class="modal fade" id="modalAgregarClase" tabindex="-1" aria-labelledby="modalAgregarClaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarClaseLabel">Agregar Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <form id="formAgregarClase" method="post" action="/clasespracticasstore/">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_clase" class="form-label">Nombre de la Clase <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="nombre_clase" name="nombre_clase" required>
                                    <!-- Las opciones se cargarán dinámicamente -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="cedula" class="form-label">Cédula del Estudiante</label>
                                <input type="text" class="form-control" id="cedula" name="cedula" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="nombre_estudiante" class="form-label">Nombre del Estudiante</label>
                                <input type="text" class="form-control" id="nombre_estudiante" name="nombre_estudiante" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="programa" class="form-label">Programa</label>
                                <input type="text" class="form-control" id="programa" name="programa" readonly>
                                <input type="hidden" id="programa_id" name="programa_id">
                            </div>
                            <div class="mb-3">
                                <label for="codigo_matricula" class="form-label">Código de Matrícula</label>
                                <input type="text" class="form-control" id="codigo_matricula" name="codigo_matricula" readonly>
                                <input type="hidden" id="matricula_id" name="matricula_id">
                            </div>
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones" 
                                    rows="4" placeholder="Ingrese observaciones aquí"></textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_instructor" class="form-label">Nombre del Instructor</label>
                                <input type="text" class="form-control" id="nombre_instructor" name="nombre_instructor" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="fecha_clase" class="form-label">Fecha</label>
                                <input type="text" class="form-control" id="fecha_clase" name="fecha" readonly value="<?= $fechaSeleccionada ?>">
                            </div>
                            <div class="mb-3">
                                <label for="hora" class="form-label">Hora</label>
                                <input type="text" class="form-control" id="hora" name="hora" readonly>
                            </div>
                            <div class="mb-3">
                                <label for="duracion" class="form-label">Duración <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="duracion" name="duracion" required> 
                                    <option value="0">Seleccione la duración</option>
                                    <option value="1">1 hora</option>
                                    <option value="2">2 horas</option>
                                    <option value="3">3 horas</option>
                                    <option value="4">4 horas</option>
                                    <option value="6">6 horas</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="vehiculo" class="form-label">Vehículo</label>
                                <select class="form-control" id="vehiculo" name="vehiculo_id" required>
                                    <?php foreach ($vehiculos as $vehiculo) : ?>
                                        <option value="<?= $vehiculo['id'] ?>"><?= $vehiculo['placa'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="lugar" class="form-label">Lugar <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="lugar" name="lugar" placeholder="Ingrese donde se recoge el estudiante" required>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="modalInstructorId" name="instructor_id">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>

                <script src="../assets/js/plugins/bouncer.min.js"></script>
                <script src="../assets/js/pages/form-validation.js"></script>
            </div>
        </div>
    </div>
</div>