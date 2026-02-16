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

// Función para cargar el cronograma mediante AJAX
function cargarCronograma(fecha) {

    $('#tituloCronograma').html(`Cronograma de Clases Prácticas - ${fecha}`);

    var url = '/clasespracticasajax/' + fecha;

    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            try {

                // Lógica para generar el cronograma con colores según el estado de la clase
                if (Array.isArray(data)) {

                    // Limpiar el contenido del cronograma
                    $('#tablaInstructores').empty();

                    // Obtener la fecha y hora actual
                    const now = new Date();

                    // Agrupar las clases por instructor
                    const cronogramaPorInstructor = {};

                    data.forEach((registro) => {
                        if (!cronogramaPorInstructor[registro.instructor_id]) {
                            cronogramaPorInstructor[registro.instructor_id] = {
                                nombre: registro.nombres.toUpperCase() + ' <br> ' + registro.apellidos.toUpperCase(),
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
                        fila += `<td class="align-middle instructor-columna" style="background-color: #F5F5F5; font-size: 11px;">${instructor.nombre}</td>`;

                        // Horas de inicio y fin del cronograma
                        let horaActual = 6;
                        const horaFinDia = 21;

                        // Ordenar las clases por hora de inicio
                        instructor.clases.sort((a, b) => a.hora_inicio.localeCompare(b.hora_inicio));

                        const nombreInstructor = instructor.nombre ? instructor.nombre.trim() : 'Instructor';

                        // Añadir celdas para las horas con o sin clases
                        instructor.clases.forEach((clase) => {

                            const fechaClase = fecha ? fecha.trim() : '';
                            const horaInicio = clase.hora_inicio ? clase.hora_inicio.trim() : '';
                            const horaFin = clase.hora_fin ? clase.hora_fin.trim() : '';

                            // Crear objetos Date para comparar con la hora actual
                            const inicioClase = new Date(`${fechaClase}T${horaInicio}`);
                            const finClase = new Date(`${fechaClase}T${horaFin}`);

                            // ✅ Calcular la duración en horas
                            const horaInicioNum = parseInt(horaInicio.split(':')[0]);
                            console.log('hora inicio num' + horaInicioNum);

                            const horaFinNum = parseInt(horaFin.split(':')[0]);
                            const duracion = horaFinNum - horaInicioNum;

                            // ✅ Crear celdas vacías antes de la hora de inicio de la clase
                            while (horaActual < horaInicioNum) {
                                fila += `<td class="hora-celda" style="width: 100px; height: 100px; position: relative;" data-hora="${horaActual}" data-instructor-id="${instructorId}">
                                            <div style="position: absolute; top: 5px; left: 5px; font-size: 10px; color: #aaa; text-align: center; width: 100%;">
                                                ${horaActual}:00 <br> <span style="color: #aaa;">${nombreInstructor}</span>
                                            </div>
                                        </td>`;
                                horaActual++;
                            }

                            let claseColor = 'bg-primary text-white'; // 🔹 Por defecto, clase programada en el futuro

                            // ✅ -----------------------------------------------------------------------------------------------------------

                            if (!isNaN(inicioClase) && !isNaN(finClase)) {

                                const tiempoRestante = finClase - now;
                                const minutosRestantes = tiempoRestante / (1000 * 60);

                                // ✅ VERIFICAR SI LA CLASE ES RESERVA
                                if (clase.estado_id === 10) {
                                    claseColor = 'bg-verde-reserva'; // 🎨 color especial para reservas
                                } else {
                                    // 🔵 Lógica normal para clases programadas
                                    if (now < inicioClase) {
                                        claseColor = 'bg-primary text-white';

                                    } else if (now >= inicioClase && now <= finClase) {
                                        if (clase.estado_id === 1 || clase.estado_id === 2) {
                                            claseColor = 'bg-warning text-dark';
                                        } else if (clase.estado_id === 3) {
                                            if (minutosRestantes <= 30) {
                                                claseColor = 'bg-success text-black';
                                            } else {
                                                claseColor = 'bg-warning text-dark';
                                            }
                                        } else if (clase.estado_id === 4 || clase.estado_id === 5) {
                                            claseColor = 'bg-danger text-white';
                                        }
                                    } else if (now > finClase) {
                                        if (clase.estado_id === 3 && minutosRestantes > -30) {
                                            claseColor = 'bg-success text-black';
                                        } else if (clase.estado_id === 1 || clase.estado_id === 2) {
                                            claseColor = 'bg-secondary text-white';
                                        } else if (clase.estado_id === 3) {
                                            claseColor = 'bg-success text-black';
                                        } else if (clase.estado_id === 4 || clase.estado_id === 5) {
                                            claseColor = 'bg-danger text-black';
                                        }
                                    }
                                }
                            } else {
                                console.error('⚠️ Fecha inválida:', fechaClase, horaInicio, horaFin);
                                claseColor = 'bg-secondary text-white'; // Clase con error
                            }

                            fila += `<td class="hora-celda clase-asignada ${claseColor}" 
                                colspan="${duracion}" 
                                data-clase-id="${clase.clase_id}" 
                                data-instructor-id="${instructorId}" 
                                data-hora="${horaInicio}">`;

                            if (clase.estado_id === 10) {
                                // ✅ Es una RESERVA → mostrar solo observaciones
                                fila += `📌 <strong>RESERVA</strong><br>`;
                                if (clase.observaciones) {
                                    fila += `${clase.observaciones}<br>`;
                                }
                            } else {
                                // 📘 Clase normal → mostrar datos del estudiante
                                fila += `${clase.estudiante_nombre} ${clase.estudiante_apellidos}<br>`;
                                fila += `${clase.clase_nombre}<br>`;
                                fila += `${clase.programa_nombre.toUpperCase()}<br>`;
                                fila += `${clase.vehiculo_placa ? clase.vehiculo_placa.toUpperCase() : ''}<br>`;
                            }

                            fila += `</td>`;

                            // Actualizar hora actual después de la clase
                            horaActual += duracion;
                        });

                        while (horaActual <= horaFinDia) {
                            fila += `<td class="hora-celda" style="width: 100px; height: 100px; position: relative;" data-hora="${horaActual}" data-instructor-id="${instructorId}">
                                        <div style="position: absolute; top: 5px; left: 5px; font-size: 10px; color: #aaa; text-align: center; width: 100%;">
                                            ${horaActual}:00 <br> <span style="color: #ccc;">${nombreInstructor}</span>
                                        </div>
                                    </td>`;
                            horaActual++;
                        }

                        fila += '</tr>';
                        $('#tablaInstructores').append(fila);
                    });

                    // Reaplicar los eventos a las celdas recién cargadas
                    aplicarEventosCeldas();

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