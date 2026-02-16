// assets/js/instructores/actualizarClase.js
// Este archivo contiene la función actualizarClase, que maneja el envío del formulario
// de actualización de clase, realiza una solicitud AJAX para actualizar los datos en el
// servidor y muestra una alerta de éxito o error basada en la respuesta del servidor.
function actualizarClase(event) {
    event.preventDefault();
    var formData = $(this).serialize();
    $.ajax({
        url: '/instructores/actualizar_clase/',
        method: 'POST',
        data: formData,
        success: function(data) {
            console.log('Respuesta del servidor para actualización:', data);
            try {
                var respuesta = JSON.parse(data);
                if (respuesta.error) {
                    Swal.fire('Error', respuesta.error, 'error');
                } else {
                    Swal.fire({
                        title: 'Éxito',
                        text: 'La clase se ha actualizado correctamente.',
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '/instructores/cronograma_semanal/?updated=true';
                    });
                }
            } catch (e) {
                console.error('Error al parsear la respuesta de actualización:', e);
                Swal.fire('Error', 'Error al procesar la respuesta del servidor.', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al actualizar la clase:', status, error);
            Swal.fire('Error', 'Error al actualizar la clase.', 'error');
        }
    });
}
