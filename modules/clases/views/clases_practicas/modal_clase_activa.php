<!-- Modal para visualizar clase activa -->
<div class="modal fade" id="modalClaseActiva" tabindex="-1" aria-labelledby="modalClaseActivaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalClaseActivaLabel"><span id="claseId" style="display: none;"></span>Clase Práctica
                    <p><strong>Estado:</strong> <span id="iconoEstadoClase"></span> <span id="nombreEstadoClase"></span></p>
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
                                    <img id="fotoEstudianteActiva" src="" alt="Foto del Estudiante" class="img-fluid rounded" style="max-width: 100px;">
                                </div>
                                <div class="col-md-8">
                                    <p><strong>Nombre:</strong> <span id="nombreCompletoEstudianteActiva"></span></p>
                                    <p><strong>Cédula:</strong> <span id="cedulaEstudianteActiva"></span></p>
                                    <p><strong>Matrícula:</strong> <span id="codigoMatriculaActiva"></span></p>
                                </div>
                            </div>
                            <div class="mb-3 mt-3">
                                <p><strong>Programa:</strong> <span id="nombreProgramaActiva"></span></p>
                            </div>
                            <div class="mb-3">
                                <p><strong>Clase:</strong></p> <p><span id="nombreClaseActiva"></span></p>
                            </div>
                        </div>

                        <!-- Información de la clase -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p><strong>Instructor:</strong> <span id="nombreInstructorActiva"></span></p>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Fecha:</strong></p> <p><span id="modalFechaActiva"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Hora de Inicio y Fin:</strong></p> <p><span id="modalHoraActiva"></span></p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <p><strong>Vehículo:</strong> <span id="vehiculoActiva"></span></p>
                            </div>
                            <div class="mb-3">
                                <p><strong>Observaciones:</strong></p>
                                <p id="modalObservacionesActiva"></p>
                            </div>
                        </div>
                    </div>

                    <!-- 🔥 Formulario para cambiar el estado de la clase -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="estadoClase" class="form-label"><strong>Estado de la Clase</strong></label>
                            <select class="form-control" id="estadoClase" name="estadoClase" required>
                                <option value="" disabled selected>Seleccione un estado</option>
                                <?php foreach ($estadosClases as $estado): ?>
                                    <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="observacionesClase" class="form-label"><strong>Observaciones</strong></label>
                            <textarea class="form-control" id="observacionesClase" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botón para actualizar el estado de la clase -->
            <div class="modal-footer">
                <button id="btnActualizarEstadoClase" class="btn btn-primary">
                    <i class="ti ti-check"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>
