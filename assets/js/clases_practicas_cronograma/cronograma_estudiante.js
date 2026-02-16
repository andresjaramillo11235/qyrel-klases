// Este archivo contiene toda la lógica relacionada con la carga del cronograma:

// Función para formatear la fecha de manera amigable
function formatearFecha(fecha) {
    var partesFecha = fecha.split('-');
    var anio = partesFecha[0];
    var mes = partesFecha[1] - 1; // Los meses en JavaScript van de 0 (enero) a 11 (diciembre)
    var dia = partesFecha[2];
    var fechaObjeto = new Date(anio, mes, dia);

    var opciones = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    return fechaObjeto.toLocaleDateString('es-ES', opciones);
}

function cargarCronograma(fecha) {
    var url = '/Ty7CvBnmQ/' + fecha; // Ruta correcta al endpoint

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            try {
                if (Array.isArray(data)) {
                    $('#tablaInstructores').empty();

                    // Agrupar clases por instructor
                    const cronogramaPorInstructor = {};
                    data.forEach((registro) => {
                        if (!cronogramaPorInstructor[registro.instructor_id]) {
                            cronogramaPorInstructor[registro.instructor_id] = {
                                nombre: registro.instructor_nombres.toUpperCase() + ' <br> ' + registro.instructor_apellidos.toUpperCase(),
                                clases: [],
                            };
                        }
                        if (registro.clase_id) {
                            cronogramaPorInstructor[registro.instructor_id].clases.push(registro);
                        }
                    });

                    // Generar el HTML para cada instructor y sus clases
                    Object.keys(cronogramaPorInstructor).forEach((instructorId) => {
                        const instructor = cronogramaPorInstructor[instructorId];
                        let fila = '<tr>';
                        fila += `<td class="align-middle">${instructor.nombre}</td>`;

                        // Horas de inicio y fin del cronograma
                        let horaActual = 6;
                        const horaFinDia = 21;

                        // Ordenar las clases por hora de inicio
                        instructor.clases.sort((a, b) => a.hora_inicio.localeCompare(b.hora_inicio));

                        // Añadir celdas para las horas con o sin clases
                        instructor.clases.forEach((clase) => {
                            const horaInicio = parseInt(clase.hora_inicio.split(':')[0]);
                            const duracion = parseInt(clase.hora_fin.split(':')[0]) - horaInicio;

                            // Determinar el color de la clase según su estado
                            let claseColor = (clase.estado === 'activa') ? 'bg-success text-dark' : 'bg-primary text-white';

                            // Celdas vacías hasta la hora de inicio de la clase
                            while (horaActual < horaInicio) {
                                fila += `<td class="hora-celda" style="width: 100px; height: 100px;" data-hora="${horaActual}" data-instructor-id="${instructorId}"></td>`;
                                horaActual++;
                            }

                            // Celda para la clase asignada (Ahora con evento de clic para abrir el modal)
                            fila += `<td class="hora-celda clase-asignada ${claseColor}" 
                                        colspan="${duracion}" 
                                        data-clase-id="${clase.clase_id}" 
                                        data-estudiante-nombre="${clase.estudiante_nombre} ${clase.estudiante_apellidos}"
                                        data-cedula="${clase.estudiante_documento}"
                                        data-matricula="${clase.matricula_id}"
                                        data-programa="${clase.programa_nombre}"
                                        data-clase-nombre="${clase.clase_nombre}"
                                        data-instructor="${clase.instructor_nombres.toUpperCase()} ${clase.instructor_apellidos.toUpperCase()}"
                                        data-hora="${clase.hora_inicio} - ${clase.hora_fin}"
                                        data-fecha="${fecha}"
                                        data-vehiculo="${clase.vehiculo_placa.toUpperCase()}"
                                        data-observaciones="${clase.observaciones || 'Sin observaciones'}"
                                        data-foto-estudiante="${clase.foto_estudiante || '/ruta/a/foto_default.jpg'}">
                                        ${clase.estudiante_nombre} ${clase.estudiante_apellidos}<br>
                                        ${clase.clase_nombre}<br>
                                        ${clase.programa_nombre.toUpperCase()}<br>
                                        ${clase.vehiculo_placa.toUpperCase()}
                                    </td>`;

                            // Actualizar hora actual después de la clase
                            horaActual += duracion;
                        });

                        // Añadir celdas vacías para el tiempo restante
                        while (horaActual <= horaFinDia) {
                            fila += `<td class="hora-celda" style="width: 100px; height: 100px;" data-hora="${horaActual}" data-instructor-id="${instructorId}"></td>`;
                            horaActual++;
                        }

                        fila += '</tr>';
                        $('#tablaInstructores').append(fila);
                    });

                    $(document).on("click", ".clase-asignada", function () {
                        let estudianteNombre = $(this).data("estudiante-nombre");
                        let cedula = $(this).data("cedula"); // Corregido
                        let matricula = $(this).data("matricula");
                        let programa = $(this).data("programa");
                        let claseNombre = $(this).data("clase-nombre");
                        let instructor = $(this).data("instructor");
                        let horario = $(this).data("hora");
                        let fecha = $(this).data("fecha");
                        let vehiculo = $(this).data("vehiculo");
                        let observaciones = $(this).data("observaciones") || "Sin observaciones";

                        // Llenar los datos en el modal
                        $("#nombreCompletoEstudianteActiva").text(estudianteNombre);
                        $("#cedulaEstudianteActiva").text(cedula);
                        $("#codigoMatriculaActiva").text(matricula);
                        $("#nombreProgramaActiva").text(programa);
                        $("#nombreClaseActiva").text(claseNombre);
                        $("#nombreInstructorActiva").text(instructor);
                        $("#modalFechaActiva").text(fecha);
                        $("#modalHoraActiva").text(horario);
                        $("#vehiculoActiva").text(vehiculo);
                        $("#modalObservacionesActiva").text(observaciones);

                        // Mostrar el modal
                        $("#modalDetalleClaseEstudiante").modal("show");
                    });
                } else {
                    console.error('Error: los datos recibidos no son un array.', data);
                    alert('Hubo un error al cargar el cronograma. Por favor, inténtalo de nuevo más tarde.');
                }
            } catch (e) {
                console.error('Error al procesar los datos recibidos:', e);
                alert('Hubo un error al procesar los datos del cronograma.');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al traer el cronograma:', status, error);
            alert('Error al traer el cronograma.');
        }
    });
}

/**
 * Función auxiliar para crear una celda vacía en el cronograma
 * @param {number} hora - La hora correspondiente a la celda
 * @param {number} instructorId - ID del instructor al que pertenece la celda
 * @returns {string} HTML de la celda vacía
 */
function crearCeldaVacia(hora, instructorId) {
    return '<td class="hora-celda" style="width: 100px; height: 100px;" data-hora="' + hora + '" data-instructor-id="' + instructorId + '"></td>';
}

/**
 * Función auxiliar para crear una celda con una clase asignada en el cronograma
 * @param {object} instructor - Objeto del instructor que contiene la información de la clase
 * @param {number} duracion - Duración de la clase en horas
 * @returns {string} HTML de la celda de clase asignada
 */
function crearCeldaClase(instructor, duracion) {
    var celdaClase = '<td class="hora-celda clase-asignada bg-primary text-white" colspan="' + duracion + '" data-clase-id="' + instructor.clase_id + '" data-instructor-id="' + instructor.instructor_id + '">';
    celdaClase += instructor.estudiante_nombre + ' ' + instructor.estudiante_apellidos + '<br>' + instructor.clase_nombre + '<br>' + instructor.programa_nombre + '<br>' + instructor.vehiculo_placa;
    celdaClase += '</td>';
    return celdaClase;
}

// Función para dividir un nombre completo y mostrar cada parte en líneas separadas
function dividirNombreEnLineas(nombreCompleto) {
    return nombreCompleto.split(' ').join('<br>');
}