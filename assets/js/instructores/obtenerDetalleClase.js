// assets/js/instructores/obtenerDetalleClase.js
// Este archivo contiene la función obtenerDetalleClase, que realiza una solicitud AJAX
// para obtener los detalles de una clase específica desde el servidor y muestra dicha
// información en un modal, además de llenar el formulario de actualización con los datos obtenidos.
function obtenerDetalleClase(claseId) {
    console.log('Clase ID:', claseId);
    $.ajax({
        url: '/instructores/obtener_detalle_clase/',
        method: 'POST',
        data: { clase_id: claseId },
        success: function(data) {
            console.log('Datos recibidos para la clase:', data);
            try {
                var clase = JSON.parse(data);
                if (clase.error) {
                    $('#detalleClaseContenido').html('Error: ' + clase.error);
                } else {
                    var contenido = '<p><strong>Nombre de la clase:</strong> ' + clase.nombre + '</p>';
                    contenido += '<p><strong>Descripción:</strong> ' + clase.descripcion + '</p>';
                    contenido += '<p><strong>Fecha:</strong> ' + clase.fecha + '</p>';
                    contenido += '<p><strong>Hora:</strong> ' + clase.hora_inicio + ' - ' + clase.hora_fin + '</p>';
                    contenido += '<p><strong>Estudiante:</strong> ' + clase.estudiante_nombres + ' ' + clase.estudiante_apellidos + '</p>';
                    if (clase.vehiculo_placa) {
                        contenido += '<p><strong>Vehículo:</strong> Placa ' + clase.vehiculo_placa + '</p>';
                    }
                    contenido += '<p><strong>Observaciones:</strong> ' + clase.observaciones + '</p>';
                    $('#detalleClaseContenido').html(contenido);

                    // Llenar el formulario con los datos actuales de la clase
                    $('#claseId').val(clase.id);
                    $('#estadoClase').val(clase.estado_id);
                    $('#observacionesClase').val(clase.observaciones);
                }
            } catch (e) {
                console.error('Error al parsear los datos de la clase:', e);
                $('#detalleClaseContenido').html('Error al procesar los datos de la clase.');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener los detalles de la clase:', status, error);
            $('#detalleClaseContenido').html('Error al obtener los detalles de la clase.');
        }
    });
}
