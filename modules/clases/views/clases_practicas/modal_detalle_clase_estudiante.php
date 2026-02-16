<!-- Modal para visualizar clase activa -->
<div class="modal fade" id="modalDetalleClaseEstudiante" tabindex="-1" aria-labelledby="modalDetalleClaseEstudianteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleClaseEstudianteLabel">Clase Práctica Activa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="col-md-8">
                                <p><strong>Nombre:</strong><br> <span id="nombreCompletoEstudianteActiva"></span></p>
                                <p><strong>Cédula:</strong> <span id="cedulaEstudianteActiva"></span></p>
                                <p><strong>Matrícula:</strong> <span id="codigoMatriculaActiva"></span></p>
                            </div>
                            <div class="mb-3 mt-3">
                                <p><strong>Programa:</strong> <span id="nombreProgramaActiva"></span></p>
                            </div>
                            <div class="mb-3">
                                <p><strong>Clase Programada:</strong> <span id="nombreClaseActiva"></span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <p><strong>Instructor:</strong> <span id="nombreInstructorActiva"></span></p>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Fecha:</strong> <span id="modalFechaActiva"></span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Hora de Inicio y Fin:</strong> <span id="modalHoraActiva"></span></p>
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
                </div>
            </div>
            <div class="modal-footer">
                <button id="btnSeguimientoActiva" class="btn btn-outline-secondary">
                    <i class="fas fa-map-marker-alt"></i> Seguimiento
                </button>
            </div>
        </div>
    </div>
</div>