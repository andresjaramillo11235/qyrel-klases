<div class="row">
    <div class="col-md-12">

        <form method="post" action="/estudiantes_agendar_teorica/">

            <input type="hidden" name="clase_teorica_id" id="clase_teorica_id">
            <input type="hidden" name="programa_id" id="programa_id">
            <input type="hidden" name="calendario_fecha" id="calendario_fecha">

            <div class="row">

                <div class="col-md-6">
                    <div class="mb-3">
                        <img src="" id="instructor_foto" alt="Foto del Instructor" style="width: 100px; height: auto;">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="instructor_nombre" class="form-label">Instructor:</label>
                        <input type="text" class="form-control" id="instructor_nombre" name="instructor_nombre" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="programa_nombre" class="form-label">Programa:</label>
                        <input type="text" class="form-control" id="programa_nombre" name="programa_nombre" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="clase_nombre" class="form-label">Clase:</label>
                        <input type="text" class="form-control" id="tema_nombre" name="tema_nombre" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="aula_nombre" class="form-label">Aula:</label>
                        <input type="text" class="form-control" id="aula_nombre" name="aula_nombre" readonly>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="fecha_hora" class="form-label">Fecha y Hora:</label>
                        <input type="text" class="form-control" id="fecha_hora" name="fecha_hora" readonly>
                    </div>
                </div>

                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Agendar</button>
                </div>

            </div>

        </form>

    </div>
    
</div>