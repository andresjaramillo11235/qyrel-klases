<!-- Modal para editar clase -->
<div class="modal fade" id="modalEditarClase" tabindex="-1" aria-labelledby="modalEditarClaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    Clase Práctica
                    <!-- Botón de seguimiento, oculto por defecto -->
                    <button id="btnSeguimiento" class="btn btn-outline-secondary ms-2" style="display: none;">
                        <i class="fas fa-map-marker-alt"></i> Seguimiento
                    </button>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Información del estudiante -->
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <img id="fotoEstudianteEditar" src="" alt="Foto del Estudiante" class="img-fluid rounded" style="max-width: 100px;">
                                </div>
                                <div class="col-md-8">
                                    <p><strong>Nombre:</strong> <span id="nombreCompletoEstudianteEditar"></span></p>
                                    <p><strong>Cédula:</strong> <span id="cedulaEstudianteEditar"></span></p>
                                    <p><strong>Matrícula:</strong> <span id="codigoMatriculaEditar"></span></p>
                                </div>
                            </div>

                            <!-- Programa del estudiante -->
                            <div class="mb-3 mt-3">
                                <p><strong>Programa:</strong> <span id="nombreProgramaEditar"></span></p>
                            </div>

                            <!-- Clase del programa -->
                            <div class="mb-3">
                                <label for="selectClaseProgramadaEditar">Clase Programada:</label>
                                <select class="form-select" id="selectClaseProgramadaEditar" name="clase_programa_id">
                                    <option value="">Seleccione una clase</option>
                                    <!-- Las opciones serán cargadas dinámicamente usando JavaScript -->
                                </select>
                            </div>
                        </div>

                        <!-- Información de la clase -->
                        <div class="col-md-6">
                            <!-- Información del instructor -->
                            <div class="mb-3">
                                <p><strong>INSTRUCTOR:</strong> <span id="nombreInstructorEditar" style="text-transform: uppercase;"></span></p>
                                <input type="hidden" id="modalInstructorIdEditar" name="instructorId">
                            </div>
                            <div class="mb-3">
                                <select id="selectInstructorEditar" class="form-select" name="instructor_id" required>
                                    <option value="" disabled selected>Seleccione un instructor</option>
                                    <!-- Las opciones se llenarán dinámicamente -->
                                </select>
                            </div>
                            <!-- Información de la clase: fecha, hora y duración -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="modalFechaEditar" class="form-label">Fecha</label>
                                    <input type="text" class="form-control" id="modalFechaEditar" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="modalHoraEditar" class="form-label">Hora de Inicio y Fin</label>
                                    <input type="text" class="form-control" id="modalHoraEditar" readonly>
                                </div>
                            </div>
                            <!-- Campo para Seleccionar Vehículo -->
                            <div class="mb-3">
                                <label for="selectVehiculoEditar">Vehículo</label>
                                <div class="mb-3">
                                    <select class="form-select" id="selectVehiculoEditar" name="vehiculo_id">
                                        <option value="" disabled selected>Seleccione un Vehículo</option>
                                    </select>
                                </div>
                            </div>
                            <!-- Campo para Observaciones -->
                            <div class="mb-3">
                                <label for="modalObservacionesEditar">Observaciones</label>
                                <textarea class="form-control" id="modalObservacionesEditar" name="observaciones" rows="3"></textarea>
                            </div>

                            <input type="hidden" id="modalClaseId" value="">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Botones para guardar cambios o eliminar la clase -->
                <button type="button" class="btn btn-danger" id="btnEliminarClase">Eliminar Clase</button>
                <button type="button" class="btn btn-primary" id="btnGuardarClaseEditar">Guardar Cambios</button>
            </div>
        </div>
    </div>
</div>

<script>
    const eliminarClaseUrl = "<?= $routes['clases_practicas_eliminar_clase'] ?>";
</script>