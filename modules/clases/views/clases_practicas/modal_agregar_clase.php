<style>
    #nombreCompletoEstudiante,
    #modalFechaTexto {
        font-size: 1.1em;
        font-weight: bold;
        background-color: #f8f9fa;
        /* Un color suave para destacar */
        padding: 5px;
        border-radius: 4px;
    }

    /* Asterisco para campos obligatorios */
    label.required::after {
        content: "*";
        color: red;
        margin-left: 4px;
    }

    /* Borde rojo para campos con error */
    input.error,
    select.error {
        border-color: red;
    }

    #guardarClaseBtn {
        background-color: #007bff;
        /* Azul con mayor contraste */
        color: white;
        padding: 10px 20px;
        font-size: 1.1em;
    }
</style>

<!-- Modal para agregar clase -->
<div class="modal fade" id="modalAgregarClase" tabindex="-1" aria-labelledby="modalAgregarClaseLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAgregarClaseLabel">Agregar Clase Práctica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <!-- Columna izquierda: Búsqueda y detalles del estudiante -->
                        <div class="col-md-6">

                            <!-- Barra superior: RESERVA -->
                            <div class="mb-3 d-flex align-items-center gap-3">
                                <div class="form-check form-switch">
                                    <!-- <input class="form-check-input" type="checkbox" id="chkReserva" name="es_reserva" value="1"> -->
                                    <input type="checkbox" class="form-check-input" id="chkReserva" name="chkReserva">

                                    <label class="form-check-label fw-semibold" for="chkReserva">
                                        Reserva (bloquear espacio)
                                    </label>
                                </div>

                                <!-- estado_id para reserva (lo setea el JS al activar la reserva) -->
                                <input type="hidden" id="estado_id" name="estado_id" value="">
                            </div>

                            <!-- Campo de búsqueda de estudiante sin título -->
                            <div class="mb-3">
                                <input type="text" class="form-control" id="termino_busqueda" placeholder="Buscar Estudiante (por nombre o cédula)">
                            </div>

                            <!-- Select de estudiantes encontrados -->
                            <div class="mb-3" id="resultadoBusqueda" style="display: none;">
                                <select class="form-control" id="selectEstudiante">
                                    <option value="">Seleccione un Estudiante</option>
                                </select>
                            </div>

                            <!-- Información del estudiante seleccionado -->
                            <div id="detalleEstudiante" style="display: none;">
                                <div class="row">
                                    <!-- Columna izquierda: Foto del estudiante -->
                                    <div class="col-md-4 text-center">
                                        <img id="fotoEstudiante" src="" alt="Foto del Estudiante" class="img-fluid rounded" style="max-width: 80px;">
                                    </div>
                                    <!-- Columna derecha: Información del estudiante -->
                                    <div class="col-md-8">
                                        <p><strong>Estudiante:</strong> <span id="nombreCompletoEstudiante"></span></p>
                                        <p><strong>Cédula:</strong> <span id="cedulaEstudiante"></span></p>
                                    </div>
                                </div>

                                <!-- Programas del estudiante -->
                                <div class="mb-3 mt-3">
                                    <select class="form-control" id="selectProgramas">
                                        <option value="" disabled selected>Seleccione un Programa del Estudiante</option>
                                    </select>
                                </div>

                                <!-- Información del programa seleccionado -->
                                <div id="detallePrograma" style="display: none;">
                                    <p><strong>Horas prácticas requeridas:</strong> <span id="horasRequeridas"></span></p>
                                    <p><strong>Horas cursadas:</strong> <span id="horasCursadas"></span></p>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Progreso de Horas Prácticas</label>
                                    <div class="progress">
                                        <div id="progressHoras" class="progress-bar" role="progressbar"
                                            style="width: 0%;"
                                            aria-valuenow="0"
                                            aria-valuemin="0"
                                            aria-valuemax="100">
                                            0%
                                        </div>
                                    </div>
                                    <small id="infoHoras" class="text-muted">0 de 0 horas completadas</small>
                                </div>

                                <!-- Campo para Seleccionar la Clase del Programa -->
                                <div class="mb-3">
                                    <select class="form-select" id="selectClasePrograma" name="clase_programa_id">
                                        <option value="" disabled selected>Seleccione un tema del Programa</option>
                                        <!-- Opciones serán llenadas dinámicamente -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Columna derecha: Información de la clase -->
                        <div class="col-md-6">
                            <!-- Campos para instructor, fecha y hora -->
                            <div class="mb-3">
                                <span class="input-group-text">
                                    <strong>Instructor:</strong> <span id="nombreInstructor" style="margin-left: 8px;"></span>

                                </span>
                                <input type="hidden" id="modalInstructorId" name="instructorId">
                            </div>

                            <!-- Información de la clase: Fecha -->
                            <div class="mb-3">
                                <span class="input-group-text">
                                    <strong>Fecha:</strong> <span id="modalFechaTexto" style="margin-left: 8px;"></span>
                                </span>
                                <!-- Campo oculto para la fecha en formato 'YYYY-MM-DD' -->
                                <input type="hidden" id="modalFechaOculta" name="fecha_clase">
                            </div>

                            <!-- Información de la clase: Horario y duración -->
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="modalHoraInicio" class="form-label">Hora de Inicio</label>
                                    <input type="text" class="form-control" id="modalHoraInicio" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label for="modalHoraFin" class="form-label">Hora de Fin</label>
                                    <input type="text" class="form-control" id="modalHoraFin" readonly>
                                </div>
                            </div>

                            <!-- Campo para la Duración de la Clase (Select) -->
                            <div class="mb-3">
                                <select class="form-select" id="duracionClase" name="duracion_clase">
                                    <option value="" disabled selected>Duración de la Clase (horas)</option>
                                    <option value="1">1 hora</option>
                                    <option value="2">2 horas</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <select class="form-select" id="selectVehiculo" name="vehiculo_id" disabled>
                                    <option value="" disabled selected>Seleccione un Vehículo</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <input type="text" class="form-control" id="modalLugar" name="lugar_recogida" placeholder="Lugar de recogida del estudiante (Opcional)">
                            </div>

                            <div class="mb-3">
                                <textarea class="form-control" id="modalObservaciones" name="observaciones" rows="3" placeholder="Agregar observaciones relevantes (Opcional)"></textarea>
                            </div>

                            <input type="hidden" id="empresaId" value="<?= $_SESSION['empresa_id']; ?>">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Botón para eliminar la clase -->
                <button type="button" class="btn btn-danger" id="btnEliminarClase" style="display: none;">Eliminar Clase</button>

                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-primary" id="btnGuardarClase">Guardar Clase</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal para Detalle de Clases Cursadas -->
<div class="modal fade" id="modalClasesCursadas" tabindex="-1" aria-labelledby="modalClasesCursadasLabel" aria-hidden="true">

    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalClasesCursadasLabel">Detalle de Clases Cursadas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Clase</th>
                            <th>H. Cursadas</th>
                            <th>Hora Inicio</th>
                            <th>Hora Fin</th>
                            <th>Estado</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody id="tablaClasesCursadas">
                        <!-- Aquí se llenarán los datos dinámicamente -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Validación para mostrar borde rojo si el campo está vacío
    $('#guardarClaseBtn').on('click', function() {
        let inputs = $('#modalAgregarClase input[required], #modalAgregarClase select[required]');
        inputs.each(function() {
            if ($(this).val() === '') {
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
    });
</script>