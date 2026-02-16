// assets/js/instructores/cargarEstados.js
// Este archivo contiene la función cargarEstados, que realiza una solicitud AJAX
// para obtener los estados de las clases desde el servidor y poblar el select
// correspondiente en el formulario de detalle de la clase.

function cargarEstados() {
    $.ajax({
        url: '/instructores/obtener_estados_clase/',
        method: 'GET',
        success: function(data) {
            var estados = JSON.parse(data);
            var selectEstado = $('#estadoClase');
            selectEstado.empty();
            estados.forEach(function(estado) {
                selectEstado.append('<option value="' + estado.id + '">' + estado.nombre + '</option>');
            });
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener los estados de las clases:', status, error);
            alert('Error al obtener los estados de las clases.');
        }
    });
}
