<div class="modal fade" id="modalEliminarReserva" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title">Eliminar Reserva</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <p>¿Seguro que deseas eliminar esta reserva?</p>
        <p id="detalleReserva" class="fw-bold text-danger"></p>
        <input type="hidden" id="claseIdEliminar" />
      </div>
      <div class="modal-footer">
         <input type="hidden" id="modalClaseId" value="">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" id="btnEliminarClase" class="btn btn-danger">Eliminar</button>
      </div>
    </div>
  </div>
</div>