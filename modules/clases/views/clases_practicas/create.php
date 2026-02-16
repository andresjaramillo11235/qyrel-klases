<form id="seleccionClaseForm">
    <input type="date" id="fecha" name="fecha" required>
    <select id="instructor" name="instructor" required>
        <!-- Opciones de instructores -->
    </select>
    <button type="button" id="agregarClaseBtn">Agregar</button>
</form>

<div class="modal fade" id="claseModal" tabindex="-1" aria-labelledby="claseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="claseModalLabel">Agregar Clase Práctica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAgregarClase">
                    <div class="mb-3">
                        <label for="matricula_id" class="form-label">Matrícula</label>
                        <select id="matricula_id" name="matricula_id" class="form-control" required>
                            <!-- Opciones de matrículas -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="hora_inicio" class="form-label">Hora Inicio</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="hora_fin" class="form-label">Hora Fin</label>
                        <input type="time" id="hora_fin" name="hora_fin" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="estado_id" class="form-label">Estado</label>
                        <select id="estado_id" name="estado_id" class="form-control" required>
                            <!-- Opciones de estados -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="lugar" class="form-label">Lugar</label>
                        <input type="text" id="lugar" name="lugar" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label for="vehiculo_id" class="form-label">Vehículo</label>
                        <select id="vehiculo_id" name="vehiculo_id" class="form-control">
                            <!-- Opciones de vehículos -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="observaciones" class="form-label">Observaciones</label>
                        <textarea id="observaciones" name="observaciones" class="form-control"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </div>
</div>
