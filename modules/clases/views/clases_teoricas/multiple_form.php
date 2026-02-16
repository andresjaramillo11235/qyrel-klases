

<div class="container mt-4">
    <h4>📘 Cargar múltiples clases teóricas</h4>
    <hr>

    <!-- 🔹 Fecha -->
    <div class="mb-3">
        <label>Fecha:</label>
        <input type="date" id="fecha" class="form-control" required>
    </div>

    <!-- 🔹 Contenedor dinámico -->
    <table class="table table-bordered" id="tablaClases">
        <thead>
            <tr>
                <th>Hora inicio</th>
                <th>Hora fin</th>
                <th>Programa</th>
                <th>Tema</th>
                <th>Instructor</th>
                <th>Aula</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody id="tbodyClases"></tbody>
    </table>

    <button class="btn btn-primary" id="btnAgregar">+ Agregar clase</button>
    <button class="btn btn-info" id="btnRevisar">🧐 Revisar clases</button>
</div>


<!-- 🔹 Modal revisión -->
<div class="modal fade" id="modalRevisar" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Revisar clases creadas</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="revisarContenido"></div>
        <div class="modal-footer">
            <button id="btnGuardar" class="btn btn-success">💾 Guardar todas</button>
            <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        </div>
    </div>
  </div>
</div>


<script src="../assets/js/clases_teoricas_multiple.js"></script>