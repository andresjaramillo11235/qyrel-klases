<!-- modules/instructores/views/modal_detalle_clase.php -->
<div class="modal fade" id="modalDetalleClase" tabindex="-1" aria-labelledby="modalDetalleClaseLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDetalleClaseLabel">Detalle de la Clase</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <!-- Información de la clase -->
                        <p id="detalleClaseContenido">Cargando...</p>
                    </div>
                    <div class="col-md-6">
                        <!-- Formulario para actualizar la clase -->
                        <form id="formActualizarClase">
                            <div class="mb-3">
                                <label for="estadoClase" class="form-label">Estado de la Clase</label>
                                <select class="form-select" id="estadoClase" name="estado_id" required>
                                    <!-- Opciones del select serán cargadas dinámicamente -->
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="observacionesClase" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observacionesClase" name="observaciones" rows="3" required></textarea>
                            </div>
                            <input type="hidden" id="claseId" name="clase_id">
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
