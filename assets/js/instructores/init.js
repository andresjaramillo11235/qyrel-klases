// assets/js/instructores/init.js
// Este archivo contiene el código de inicialización de eventos y lógica necesaria para
// gestionar la funcionalidad de la página del cronograma semanal de los instructores.
// Se encarga de configurar los eventos de clic y envío de formularios, y muestra una alerta
// de éxito si la actualización de una clase se realizó correctamente.
$(document).ready(function() {
    console.log("Script init.js cargado correctamente.");

    $('#btnMostrarCronograma').on('click', mostrarCronograma);
    $(document).on('click', '.clase', function() {
        var claseId = $(this).data('clase-id');
        obtenerDetalleClase(claseId);
    });

    $('#formActualizarClase').on('submit', actualizarClase);

    $('#modalDetalleClase').on('show.bs.modal', function() {
        cargarEstados();
    });

    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('updated')) {
        Swal.fire({
            title: 'Éxito',
            text: 'La clase se ha actualizado correctamente.',
            icon: 'success'
        });
    }
});
