// assets/js/gestion_modal_clases_instructor.js
$(document).ready(function() {
    console.log("Script gestion_modal_clases_instructor.js cargado correctamente.");

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

    $('#btnMostrarCronograma').on('click', function() {
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
    });

    $(document).on('click', '.clase', function() {
        var claseId = $(this).data('clase-id');
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
    });

    $('#formActualizarClase').on('submit', function(event) {
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
    });

    $('#modalDetalleClase').on('show.bs.modal', function() {
        cargarEstados();
    });
});
