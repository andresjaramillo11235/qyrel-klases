<!-- Modal para visualizar clase activa del instructor -->
<div class="modal fade" id="modalDetalleClaseInstructor" tabindex="-1" aria-labelledby="modalDetalleClaseInstructorLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleClaseInstructorLabel">Detalle de la Clase Práctica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Información del estudiante -->
                        <div class="col-md-6">
                            <p><strong>Nombre estudiante:</strong><br> <span id="nombreCompletoEstudianteActiva"></span></p>
                            <p><strong>Documento:</strong><br> <span id="cedulaEstudianteActiva"></span></p>
                            <p><strong>Matrícula:</strong><br> <span id="codigoMatriculaActiva"></span></p>
                            <p><strong>Programa:</strong><br> <span id="nombreProgramaActiva"></span></p>
                            <p><strong>Clase Programada:</strong><br> <span id="nombreClaseActiva"></span></p>
                        </div>

                        <!-- Información adicional -->
                        <div class="col-md-6">
                            <p><strong>Teléfono:</strong><br> <span id="telefonoEstudianteActiva"></span></p>
                            <p><strong>Fecha:</strong><br> <span id="modalFechaActiva"></span></p>
                            <p><strong>Hora de Inicio y Fin:</strong><br> <span id="modalHoraActiva"></span></p>
                            <p><strong>Vehículo:</strong><br> <span id="vehiculoActiva"></span></p>
                            <p><strong>Observaciones:</strong></p>
                            <p id="modalObservacionesActiva"></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button id="btnSeguimientoActiva" class="btn btn-outline-secondary">
                    <i class="fas fa-map-marker-alt"></i> Seguimiento
                </button>
            </div> -->
        </div>
    </div>
</div>
