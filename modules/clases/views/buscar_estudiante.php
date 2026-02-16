<div class="container mt-5">
    <h2>Buscar Estudiante</h2>
    <form id="formBuscarEstudiante" class="mb-3">
        <div class="row">
            <div class="col">
                <input type="text" class="form-control" id="termino_busqueda" name="termino_busqueda" placeholder="Cédula o Nombre" required>
            </div>
            <div class="col">
                <button type="button" class="btn btn-primary" id="btnBuscarEstudiante">Buscar</button>
            </div>
        </div>
    </form>

    <div id="resultadoBusqueda" style="display: none;">
        <select class="form-select" id="selectEstudiante">
            <option value="">Seleccione un estudiante</option>
        </select>
        <button type="button" class="btn btn-success mt-2" id="btnSeleccionarEstudiante">Seleccionar</button>
    </div>

    <div id="detalleEstudiante" style="display: none;" class="mt-3">
        <h3>Detalle del Estudiante</h3>
        <div class="row">
            <div class="col-md-4">
                <img id="fotoEstudiante" src="" alt="Foto del Estudiante" class="img-fluid">
            </div>
            <div class="col-md-8">
                <p><strong>Cédula:</strong> <span id="cedulaEstudiante"></span></p>
                <p><strong>Nombre:</strong> <span id="nombreCompletoEstudiante"></span></p>
                <p><strong>Código de Matrícula:</strong> <span id="codigoMatricula"></span></p>
                <div class="mb-3">
                    <label for="programas" class="form-label">Programas</label>
                    <select class="form-select" id="selectProgramas"></select>
                    <button type="button" class="btn btn-info mt-2" id="btnSeleccionarPrograma">Seleccionar Programa</button>
                </div>
                <div id="detallePrograma" style="display: none;">
                    <p><strong>Horas Prácticas Requeridas:</strong> <span id="horasRequeridas"></span></p>
                    <p><strong>Horas Prácticas Cursadas:</strong> <span id="horasCursadas"></span></p>
                </div>
            </div>
        </div>
    </div>
</div>
