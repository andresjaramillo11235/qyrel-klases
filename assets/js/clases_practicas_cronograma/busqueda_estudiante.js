$(document).ready(function () {

 $('#termino_busqueda').on('input', function () {
    var termino = $(this).val().trim();

    if (termino.length >= 3) {

        $.ajax({
            url: '/estudiantesbuscar',
            method: 'POST',
            data: {
                termino: termino
            },
            success: function (data) {
                try {
                    var estudiantes = JSON.parse(data);
                    $('#selectEstudiante').empty();

                    $('#selectEstudiante').append(
                        '<option value="" disabled selected>Seleccione un estudiante</option>'
                    );

                    if (Array.isArray(estudiantes) && estudiantes.length > 0) {
                        estudiantes.forEach(function (estudiante) {
                            var nombreCompleto = estudiante.nombres + ' ' + estudiante.apellidos;
                            $('#selectEstudiante').append(
                                '<option value="' + estudiante.id + '">' + nombreCompleto + '</option>'
                            );
                        });
                        $('#resultadoBusqueda').show();
                    } else {
                        $('#resultadoBusqueda').hide();
                    }

                } catch (e) {
                    console.error('Error parseando estudiantes', e);
                }
            },
            error: function () {
                console.error('Error al buscar estudiantes');
            }
        });

    } else {
        $('#resultadoBusqueda').hide();
    }
});


    // Seleccionar el estudiante del select automáticamente al cambiar la opción
    $('#selectEstudiante').on('change', function () {
        var estudianteId = $(this).val();
        var selectedOption = $('#selectEstudiante option:selected');
        var foto = selectedOption.data('foto');
        var cedula = selectedOption.data('cedula');
        var nombre = selectedOption.data('nombre');

        if (estudianteId) {
            // Realizar la solicitud AJAX para obtener detalles del estudiante
            $.ajax({
                url: '/estudiantesdetalle/' + estudianteId,
                method: 'GET',
                success: function (data) {
                    try {
                        var estudiante = JSON.parse(data);

                        $('#cedulaEstudiante').text(estudiante.cedula);
                        $('#fotoEstudiante').attr('src', '/files/fotos_estudiantes/' + estudiante.foto);
                        $('#nombreCompletoEstudiante').text(estudiante.nombres + ' ' + estudiante.apellidos);

                        $('#selectProgramas').empty();
                        $('#selectProgramas').append('<option value="" disabled selected>Seleccione un programa del estudiante</option>');

                        estudiante.matriculas.forEach(function (matricula) {
                            // Guardamos en el value el programa_id y matricula_id separados por un "|"
                            $('#selectProgramas').append('<option value="' + matricula.programa_id + '|' + matricula.matricula_id + '">' + matricula.matricula_id + ' - ' + matricula.programa.toUpperCase() + '</option>');
                        });


                        // Mostrar el detalle del estudiante
                        $('#detalleEstudiante').show();

                    } catch (e) {
                        alert('Error al procesar los datos del estudiante.');
                    }

                },
                error: function () {
                    alert('Error al traer los datos del estudiante.');
                }
            });
        } else {
            $('#detalleEstudiante').hide();
        }
    });

    // Seleccionar automáticamente el programa y mostrar detalles al cambiar el select
    $(document).on('change', '#selectProgramas', function () {

        var selectedValue = $(this).val();  // Obtener el valor seleccionado
        var values = selectedValue.split("|"); // Dividir el valor en programaId y matriculaId
        var programaId = values[0];  // Extraer programa_id
        var matriculaId = values[1]; // Extraer matricula_id

        if (programaId && matriculaId) {

            var url = '/programasdetalle/' + programaId + '/' + matriculaId;
            $('#codigoMatricula').text(matriculaId); // 🔹 Actualizar el campo oculto con matricula_id

            $.ajax({
                url: url,
                method: 'GET',
                success: function (data) {

                    try {
                        var programa = JSON.parse(data);

                        // Actualizar los detalles del programa en la interfaz
                        $('#horasRequeridas').text(programa.horas_practicas);
                        $('#horasCursadas').text(programa.horas_cursadas);
                        $('#detallePrograma').show();

                        // Hace que la barra de progreso sea clickeable
                        $('#progressHoras').css('cursor', 'pointer');

                        // Evento para abrir el modal al hacer clic en la barra de progreso
                        $('#progressHoras').on('click', function () {
                            if (programa.clases_cursadas.length > 0) {
                                $('#tablaClasesCursadas').empty(); // Limpiar la tabla antes de llenarla

                                programa.clases_cursadas.forEach((clase) => {
                                    let estadoTexto = obtenerTextoEstado(clase.estado_id); // Obtener el estado de la clase

                                    let fila = `
                                        <tr>
                                            <td>${clase.fecha}</td>
                                            <td>${clase.nombre}</td>
                                            <td>${clase.numero_horas}</td>
                                            <td>${clase.hora_inicio.substring(0, 5)}</td>
                                            <td>${clase.hora_fin.substring(0, 5)}</td>
                                            <td class="text-center">${estadoTexto}</td>
                                            <td>${clase.observaciones ? clase.observaciones : 'Sin observaciones'}</td>
                                        </tr>
                                    `;
                                    $('#tablaClasesCursadas').append(fila);
                                });

                                // Mostrar el modal con el detalle de las clases cursadas
                                $('#modalClasesCursadas').modal('show');
                                
                            } else {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Sin Clases Cursadas',
                                    text: 'Este estudiante aún no ha tomado ninguna clase práctica.'
                                });
                            }
                        });

                        $('#modalClasesCursadas').on('show.bs.modal', function () {
                            $('.modal-backdrop').not(':last').remove(); // 🔹 Mantiene visible el fondo original
                        });

                        // Calcular el porcentaje de progreso
                        let porcentaje = (programa.horas_cursadas / programa.horas_practicas) * 100;
                        porcentaje = porcentaje > 100 ? 100 : porcentaje; // Evitar valores mayores al 100%

                        // Actualizar la barra de progreso
                        $('#progressHoras').css('width', porcentaje + '%').attr('aria-valuenow', porcentaje);
                        $('#progressHoras').text(Math.round(porcentaje) + '%');
                        $('#infoHoras').text(`${programa.horas_cursadas} de ${programa.horas_practicas} horas completadas`);

                        // Obtener las clases del programa
                        //obtenerClasesPrograma(programaId);
                        obtenerClasesPrograma(matriculaId);

                        // Recuperar valores de las variables almacenadas en los campos ocultos
                        var fechaSeleccionada = $('#modalFechaOculta').val() || '0000-00-00'; // Valor predeterminado
                        var horaSeleccionada = $('#modalHoraInicio').val() || '00:00:00';    // Valor predeterminado

                        // Obtener vehículos disponibles
                        var tipoVehiculoId = programa.tipo_vehiculo_id;

                        if (tipoVehiculoId) {
                            obtenerVehiculosDisponibles(fechaSeleccionada, horaSeleccionada + ":00:00", tipoVehiculoId);
                            $('#selectVehiculo').prop('disabled', false); // Habilitar el campo
                        }

                    } catch (e) {
                        console.error('Error al procesar los datos del programa:', e); // DEPURACIÓN
                        alert('Error al procesar los datos del programa.');
                    }
                },
                error: function () {
                    console.error('Error al realizar la solicitud AJAX para detalles del programa.'); // DEPURACIÓN
                    alert('Error al traer los datos del programa.');
                }
            });
        } else {
            $('#detallePrograma').hide();
            $('#selectVehiculo').prop('disabled', true); // Deshabilitar el campo si no hay programa
        }
    });

    // Función para obtener el estado de la clase según su ID con etiquetas de Bootstrap
    function obtenerTextoEstado(estadoId) {
        switch (estadoId) {
            case 1: return '<span class="badge bg-primary">Programada</span>';
            case 2: return '<span class="badge bg-warning text-dark">En Progreso</span>';
            case 3: return '<span class="badge bg-success">Finalizada</span>';
            case 4: return '<span class="badge bg-danger">Cancelada con reposición</span>';
            case 5: return '<span class="badge bg-danger">Cancelada sin reposición</span>';
            default: return '<span class="badge bg-secondary">Desconocido</span>';
        }
    }

});




function obtenerClasesPrograma(matriculaId) {
    $.ajax({
        url: '/clasesPendientesPorMatricula/' + matriculaId,
        method: 'GET',
        success: function (data) {
            try {
                var clases = JSON.parse(data);

                // Limpiar y llenar el select de clases del programa
                $('#selectClasePrograma').empty();
                $('#selectClasePrograma').append('<option value="" disabled selected>Seleccione una Clase del Programa</option>');

                if (Array.isArray(clases) && clases.length > 0) {
                    clases.forEach(function (clase) {
                        $('#selectClasePrograma').append('<option value="' + clase.id + '">' + clase.nombre_clase + ' (' + clase.numero_horas + ' horas)</option>');
                    });
                    $('#selectClasePrograma').show();
                } else {
                    $('#selectClasePrograma').append('<option value="" disabled>No se encontraron clases para este programa</option>');
                }
            } catch (e) {
                alert('Error al procesar los datos de las clases.');
            }
        },
        error: function () {
            alert('Error al traer los datos de las clases.');
        }
    });
}













// Función para obtener las clases del programa
// function obtenerClasesPrograma(programaId) {
//     $.ajax({
//         url: '/clasesporprograma/' + programaId,
//         method: 'GET',
//         success: function (data) {
//             try {
//                 var clases = JSON.parse(data);

//                 // Limpiar y llenar el select de clases del programa
//                 $('#selectClasePrograma').empty();
//                 $('#selectClasePrograma').append('<option value="" disabled selected>Seleccione una Clase del Programa</option>');

//                 if (Array.isArray(clases) && clases.length > 0) {
//                     clases.forEach(function (clase) {
//                         $('#selectClasePrograma').append('<option value="' + clase.id + '">' + clase.nombre_clase + ' (' + clase.numero_horas + ' horas)</option>');
//                     });
//                     $('#selectClasePrograma').show();
//                 } else {
//                     $('#selectClasePrograma').append('<option value="" disabled>No se encontraron clases para este programa</option>');
//                 }
//             } catch (e) {
//                 alert('Error al procesar los datos de las clases.');
//             }
//         },
//         error: function () {
//             alert('Error al traer los datos de las clases.');
//         }
//     });
// }
