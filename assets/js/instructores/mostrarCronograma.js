// assets/js/instructores/mostrarCronograma.js
// Este archivo contiene la función mostrarCronograma, que realiza una solicitud AJAX
// para obtener el cronograma semanal de clases según una fecha de inicio proporcionada
// y muestra las clases en una tabla en la vista del cronograma semanal.

function mostrarCronograma() {
    var fechaInicio = $('#fecha_inicio').val();
    if (fechaInicio) {
        $.ajax({
            url: '/instructores/cronograma_semanal/',
            method: 'POST',
            data: { fecha_inicio: fechaInicio },
            success: function(data) {
                console.log('Respuesta del servidor:', data);

                try {
                    var clases = JSON.parse(data);
                    if (clases.error) {
                        alert('Error: ' + clases.error);
                        return;
                    }

                    $('#cronogramaSemanal tbody td').empty();
                    var diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    for (var i = 0; i < 7; i++) {
                        var fecha = new Date(fechaInicio);
                        fecha.setDate(fecha.getDate() + i);
                        var fechaFormateada = fecha.toLocaleDateString('es-ES');
                        $('#dia-' + i).text(diasSemana[i] + ' (' + fechaFormateada + ')');
                    }

                    clases.forEach(function(clase) {
                        var fechaClase = new Date(clase.fecha);
                        var diaSemana = fechaClase.getDay() - 1;
                        var horaClase = parseInt(clase.hora_inicio.split(':')[0]);
                        var duracionClase = parseInt(clase.hora_fin.split(':')[0]) - horaClase;
                        var celdaId = '#celda-' + horaClase + '-' + diaSemana;
                        var contenidoClase = '<div class="clase" data-bs-toggle="modal" data-bs-target="#modalDetalleClase" data-clase-id="' + clase.id + '">' + clase.nombre + '<br>' + clase.hora_inicio + '-' + clase.hora_fin + '<br>' + clase.estudiante_nombres + ' ' + clase.estudiante_apellidos + '</div>';
                        
                        $(celdaId).addClass('bg-primary text-white').attr('rowspan', duracionClase).html(contenidoClase);
                        
                        for (var i = 1; i < duracionClase; i++) {
                            $('#celda-' + (horaClase + i) + '-' + diaSemana).remove();
                        }
                    });

                    $('#cronogramaSemanal').show();
                } catch (e) {
                    console.error('Error al parsear los datos del cronograma:', e);
                    alert('Error al procesar los datos del cronograma.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al obtener el cronograma:', status, error);
                alert('Error al obtener el cronograma.');
            }
        });
    } else {
        alert('Por favor, seleccione una fecha.');
    }
}
