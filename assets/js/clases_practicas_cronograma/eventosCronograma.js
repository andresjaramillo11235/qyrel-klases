$(document).ready(function () {
    // Aplicar eventos a las celdas del cronograma
    aplicarEventosCeldas();

    $('#fechaForm').on('submit', function (event) {
        event.preventDefault();

        // ✅ Obtener directamente la fecha en `YYYY-MM-DD` sin modificarla
        var fechaFormateada = $('#fechaInput').val();

        console.warn("Fecha enviada a `cargarCronograma()` sin desfase:", fechaFormateada);

        if (fechaFormateada) {
            cargarCronograma(fechaFormateada);
        }
    });

    $('#hoyBtn').on('click', function () {
        // Obtener la fecha actual en la zona horaria de Colombia
        var hoy = new Date();

        // Extraer los componentes de la fecha en formato `YYYY-MM-DD`
        var dia = hoy.getDate().toString().padStart(2, '0');  // Asegurar dos dígitos
        var mes = (hoy.getMonth() + 1).toString().padStart(2, '0');  // Asegurar dos dígitos
        var anio = hoy.getFullYear();

        var fechaColombia = `${anio}-${mes}-${dia}`;

        // Asignar la fecha al input y cargar el cronograma
        $('#fechaInput').val(fechaColombia);
        cargarCronograma(fechaColombia);
    });

    $('#anteriorBtn').on('click', function () {
        // ✅ Obtener la fecha actual del input
        var fechaSeleccionada = $('#fechaInput').val();

        // ✅ Extraer los componentes de la fecha manualmente
        var partes = fechaSeleccionada.split('-'); // Formato YYYY-MM-DD
        var anio = parseInt(partes[0]);
        var mes = parseInt(partes[1]) - 1; // Los meses en JS van de 0 a 11
        var dia = parseInt(partes[2]);

        // ✅ Crear el objeto Date con la fecha exacta (sin desfase)
        var fecha = new Date(anio, mes, dia);

        // ✅ Restar 1 día sin modificar la zona horaria
        fecha.setDate(fecha.getDate() - 1);

        // ✅ Extraer la nueva fecha en formato `YYYY-MM-DD`
        var nuevoDia = fecha.getDate().toString().padStart(2, '0');
        var nuevoMes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // Se suma 1 porque getMonth() va de 0 a 11
        var nuevoAnio = fecha.getFullYear();

        var nuevaFecha = `${nuevoAnio}-${nuevoMes}-${nuevoDia}`;

        // ✅ Establecer la nueva fecha en el input y cargar el cronograma
        $('#fechaInput').val(nuevaFecha);
        cargarCronograma(nuevaFecha);
    });


    $('#siguienteBtn').on('click', function () {
        // ✅ Obtener la fecha actual del input
        var fechaSeleccionada = $('#fechaInput').val();

        // ✅ Extraer los componentes de la fecha manualmente
        var partes = fechaSeleccionada.split('-'); // Formato YYYY-MM-DD
        var anio = parseInt(partes[0]);
        var mes = parseInt(partes[1]) - 1; // Los meses en JS van de 0 a 11
        var dia = parseInt(partes[2]);

        // ✅ Crear el objeto Date con la fecha exacta (sin desfase)
        var fecha = new Date(anio, mes, dia);

        // ✅ Sumar 1 día sin modificar la zona horaria
        fecha.setDate(fecha.getDate() + 1);

        // ✅ Extraer la nueva fecha en formato `YYYY-MM-DD`
        var nuevoDia = fecha.getDate().toString().padStart(2, '0');
        var nuevoMes = (fecha.getMonth() + 1).toString().padStart(2, '0'); // Se suma 1 porque getMonth() va de 0 a 11
        var nuevoAnio = fecha.getFullYear();
        var nuevaFecha = `${nuevoAnio}-${nuevoMes}-${nuevoDia}`;

        // ✅ Establecer la nueva fecha en el input y cargar el cronograma
        $('#fechaInput').val(nuevaFecha);
        cargarCronograma(nuevaFecha);
    });

    // Evento para calcular la hora de fin al seleccionar la duración de la clase
    $('#duracionClase').on('change', function () {
        calcularHoraFin();
    });
});

// Función para aplicar los eventos a las celdas del cronograma
function aplicarEventosCeldas() {

    $('.hora-celda').off('click').on('click', function () {

        var instructorId = $(this).data('instructor-id');
        var instructorNombre = $(this).closest('tr').find('td:first').text();  // Obtener el nombre del instructor

        // ✅ Obtener la fecha y hora seleccionada correctamente en Colombia
        let fechaSeleccionada = $('#fechaInput').val();
        let horaSeleccionada = $(this).data('hora'); // Obtener la hora seleccionada

        // ✅ Validar si `horaSeleccionada` es undefined y asignar 12 por defecto
        if (horaSeleccionada === undefined || horaSeleccionada === null) {
            console.warn("Hora no definida, asignando valor por defecto: 12");
            horaSeleccionada = 12;
        }

        // ✅ Asegurar que la hora tenga dos dígitos
        horaSeleccionada = horaSeleccionada.toString().padStart(2, '0');

        // ✅ Extraer la fecha en componentes manualmente
        var partes = fechaSeleccionada.split('-'); // `YYYY-MM-DD`
        var anio = parseInt(partes[0]);
        var mes = parseInt(partes[1]) - 1; // Los meses van de 0 a 11
        var dia = parseInt(partes[2]);

        // ✅ Crear la fecha en zona horaria de Colombia
        var fechaHoraSeleccionada = new Date(anio, mes, dia, horaSeleccionada, 0, 0);

        // ✅ Obtener timestamp en zona horaria de Colombia
        var timestampSeleccionado = fechaHoraSeleccionada.getTime();

        // ✅ Obtener la fecha actual correctamente en Colombia
        const fechaActual = new Date();
        var fechaHoraActual = fechaActual.getTime(); // ✅ Definir fechaHoraActual aquí

        // ✅ Definir `horaActual` correctamente
        var horaActual = fechaActual.getHours().toString().padStart(2, '0'); // Asegurar dos dígitos

        // ✅ Definir `fechaActualFormatted` correctamente en formato `YYYY-MM-DD`
        var diaActual = fechaActual.getDate().toString().padStart(2, '0');
        var mesActual = (fechaActual.getMonth() + 1).toString().padStart(2, '0');
        var anioActual = fechaActual.getFullYear();

        var fechaActualFormatted = `${anioActual}-${mesActual}-${diaActual}`;

        if ($(this).hasClass('clase-asignada')) {
            // La celda tiene una clase asignada, obtener el ID de la clase
            var claseId = $(this).data('clase-id');

            // Solicitar detalles de la clase para verificar el estado
            $.ajax({
                url: '/clasespracticasdetalle/' + claseId,
                method: 'GET',
                success: function (data) {

                    if (typeof data === 'string') {
                        data = JSON.parse(data);
                    }

                    // Obtener fechas y horas para comparar
                    var claseFecha = data.fecha; // Formato esperado: 'YYYY-MM-DD'
                    var horaInicio = data.hora_inicio; // Formato esperado: 'HH:MM:SS'
                    var horaFin = data.hora_fin; // Formato esperado: 'HH:MM:SS'

                    var inicioClase = new Date(`${claseFecha}T${horaInicio}`);
                    var finClase = new Date(`${claseFecha}T${horaFin}`);

                    // Verificar el estado de la clase
                    if (fechaActual > finClase) {
                        // ✅ Clase pasada → abrir modal de clase activa
                        abrirModalClaseActiva(claseId);
                    } else if (fechaActual >= inicioClase && fechaActual <= finClase) {
                        // ✅ Clase en marcha
                        abrirModalClaseActiva(claseId);
                    } else {
                        // ✅ Clase futura
                        abrirModalEditarClase(claseId);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al obtener los detalles de la clase:', status, error);
                }
            });
        }
        else {
            // Validación: no permitir clases en el pasado
            if (fechaSeleccionada === fechaActualFormatted) {
                const horaActualInt = parseInt(horaActual);
                const horaSeleccionadaInt = parseInt(horaSeleccionada);
                const minutosActual = fechaActual.getMinutes();

                if (horaSeleccionadaInt < horaActualInt) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hora pasada',
                        text: 'No se pueden crear clases en horas anteriores.',
                    });
                    return;
                }

                // Si es la misma hora actual, pero ya han pasado minutos, también permitir
                // Pero si no ha pasado la hora aún, bloquear (ej: hora actual = 15:00, seleccionas 15:00 a las 14:59)
                if (horaSeleccionadaInt === horaActualInt && minutosActual > 59) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Hora pasada',
                        text: 'No se pueden crear clases en horas anteriores.',
                    });
                    return;
                }
            }

            // ✅ Mostrar modal para crear nueva clase
            mostrarModalCrearClase(fechaSeleccionada, horaSeleccionada, instructorId, instructorNombre);
        }
    });
}


/**
 * Abre el modal para editar una clase práctica existente.
 * Recupera los detalles de la clase desde el servidor y llena el formulario en el modal.
 * También carga las opciones relacionadas, como vehículos disponibles y clases programadas.
 *
 * @param {number} claseId - ID de la clase a editar.
 */
function abrirModalEditarClase(claseId) {

    const url = '/clasespracticasdetalle/' + claseId;

    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {

            try {
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }

                if (data && data.clase_id) {

                    // 🔹 Si es una RESERVA → abrir solo modal de eliminar
                    if (data.estado_id === 10) {
                        $('#modalEliminarReserva #claseIdEliminar').val(data.clase_id);
                        $('#modalClaseId').val(data.clase_id);
                        $('#modalEliminarReserva #detalleReserva').text(
                            `${data.fecha} ${data.hora_inicio} - ${data.hora_fin} (${data.instructor_nombre} ${data.instructor_apellidos})`
                        );
                        $('#modalEliminarReserva').modal('show');
                        return; // ⛔ salir, no abrir el modal normal
                    }

                    // Llenar el modal con los valores obtenidos de la clase
                    $('#fotoEstudianteEditar').attr('src', data.foto_estudiante_url);
                    $('#nombreCompletoEstudianteEditar').text(data.estudiante_nombre + ' ' + data.estudiante_apellidos);
                    $('#cedulaEstudianteEditar').text(data.cedula_estudiante);
                    $('#codigoMatriculaEditar').text(data.matricula_id);
                    $('#nombreProgramaEditar').text('Programa: ' + data.programa_nombre);
                    $('#modalInstructorIdEditar').val(data.instructor_id);
                    $('#nombreInstructorEditar').text(data.instructor_nombre + ' ' + data.instructor_apellidos);
                    $('#modalFechaEditar').val(data.fecha);
                    $('#modalHoraEditar').val(data.hora_inicio + ' - ' + data.hora_fin);
                    $('#selectVehiculoEditar').val(data.vehiculo_id);
                    $('#modalObservacionesEditar').val(data.observaciones);
                    $('#modalClaseId').val(data.clase_id);

                    // Configurar el select de clases programadas
                    const selectClasesProgramadas = $('#selectClaseProgramadaEditar');
                    selectClasesProgramadas.html('<option value="">Seleccione una clase</option>');


                    // Obtener las clases programadas para el programa actual
                    const clasesUrl = '/clasesprogramaslistado/' + data.programa_id;
                    $.ajax({
                        url: clasesUrl,
                        method: 'GET',
                        success: function (clases) {
                            try {
                                if (typeof clases === 'string') {
                                    clases = JSON.parse(clases);
                                }

                                clases.forEach(function (clase) {
                                    const option = $('<option>')
                                        .val(clase.id)
                                        .text(clase.nombre_clase);

                                    if (clase.id === data.clase_programa_id) {
                                        option.prop('selected', true);
                                    }

                                    selectClasesProgramadas.append(option);
                                });
                            } catch (error) {
                                alert('Hubo un problema al cargar las clases del programa.');
                            }
                        },
                        error: function () {
                            alert('Error al obtener las clases del programa.');
                        }
                    });

                    // Obtener vehículos disponibles
                    //obtenerVehiculosDisponiblesParaEditar(data.fecha, data.hora_inicio, data.tipo_vehiculo_id, data.vehiculo_id);

                    obtenerVehiculosDisponiblesParaEditar(
                        data.fecha,
                        data.hora_inicio,
                        data.hora_fin,
                        data.tipo_vehiculo_id,
                        data.vehiculo_id
                    );


                    cargarInstructoresDisponibles(data.fecha, data.hora_inicio, data.hora_fin, data.instructor_id);

                    // Mostrar el modal
                    $('#modalEditarClase').modal('show');
                }

            } catch (error) {
                alert('Hubo un problema al procesar los detalles de la clase.');
            }
        },
        error: function () {
            alert('Error al obtener los detalles de la clase.');
        }
    });
}

function cargarInstructoresDisponibles(fecha, horaInicio, horaFin, instructorAsignadoId) {
    $.ajax({
        url: '/obtenerInstructoresDisponibles/',
        method: 'POST',
        data: {
            fecha: fecha,
            hora_inicio: horaInicio,
            hora_fin: horaFin
        },
        success: function (response) {
            if (Array.isArray(response)) {
                let $select = $('#selectInstructorEditar');
                $select.empty(); // Limpiar opciones anteriores

                // Agregar opción por defecto
                $select.append('<option value="">Seleccione un instructor</option>');

                // Agregar instructores disponibles
                response.forEach(instructor => {
                    const selected = (instructor.id === parseInt(instructorAsignadoId)) ? 'selected' : '';
                    $select.append(`<option value="${instructor.id}" ${selected}>${instructor.nombre}</option>`);
                });

                $select.prop('disabled', false);
            } else {
                console.warn('⚠️ Respuesta inesperada:', response);
            }
        },
        error: function (xhr, status, error) {
            console.error('❌ Error al cargar instructores disponibles:', error);
        }
    });
}

function abrirModalClaseActiva(claseId) {

    var url = '/clasespracticasdetalle/' + claseId;

    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            if (typeof data === 'string') {
                data = JSON.parse(data);
            }

            if (data && data.clase_id) {

                const estadoNombre = data.estado_nombre; // Estado en mayúsculas (ejemplo: "FINALIZADA")

                // Diccionario de íconos según el estado
                const iconosEstados = {
                    "PROGRAMADA": '<i class="ti ti-calendar-event text-white"></i>',
                    "EN PROGRESO": '<i class="ti ti-play text-white"></i>',
                    "FINALIZADA": '<i class="ti ti-check text-white"></i>',
                    "CANCELADA CON REPOSICIÓN": '<i class="ti ti-refresh text-white"></i>',
                    "CANCELADA SIN REPOSICIÓN": '<i class="ti ti-ban text-white"></i>'
                };

                // Rellenar los datos en el modal
                $('#fotoEstudianteActiva').attr('src', data.foto_estudiante_url);
                $('#nombreCompletoEstudianteActiva').text(data.estudiante_nombre + ' ' + data.estudiante_apellidos);
                $('#cedulaEstudianteActiva').text(data.cedula_estudiante);
                $('#codigoMatriculaActiva').text(data.matricula_id);
                $('#nombreProgramaActiva').text(data.programa_nombre);
                $('#nombreClaseActiva').text(data.clase_nombre);
                $('#nombreInstructorActiva').text((data.instructor_nombre + ' ' + data.instructor_apellidos).toUpperCase());
                $('#modalFechaActiva').text(data.fecha);
                $('#modalHoraActiva').text(
                    data.hora_inicio.substring(0, 5) + ' - ' + data.hora_fin.substring(0, 5)
                );
                $('#vehiculoActiva').text(data.vehiculo_placa.toUpperCase());
                $('#modalObservacionesActiva').text(data.observaciones);
                $('#claseId').text(data.clase_id);
                $('#iconoEstadoClase').html(iconosEstados[estadoNombre] || '<i class="ti ti-alert-triangle text-secondary"></i>');
                $('#nombreEstadoClase').text(estadoNombre);

                // ✅ Cargar el estado actual de la clase en el select
                $('#estadoClase').val(data.estado_id);

                // Mostrar el modal
                $('#modalClaseActiva').modal('show');
            }
        },
        error: function (xhr, status, error) {
            console.error('Error al obtener los detalles de la clase:', status, error);
        }
    });
}

$(document).ready(function () {

    $('#btnActualizarEstadoClase').on('click', function () {

        var claseId = $('#claseId').text();
        var estadoId = $('#estadoClase').val();
        var observaciones = $('#observacionesClase').val();

        if (!estadoId) {
            Swal.fire({
                icon: 'warning',
                title: 'Seleccione un estado',
                text: 'Debe seleccionar un estado antes de guardar los cambios.'
            });
            return;
        }

        $.ajax({
            url: '/Op0CvBnmQ/',
            method: 'POST',
            data: {
                clase_id: claseId,
                estado_id: estadoId,
                observaciones: observaciones
            },
            dataType: 'json', // 🔹 Asegurar que la respuesta se interpreta como JSON
            success: function (response) {

                if (response.success) {
                    // ✅ Si el servidor responde con éxito
                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: response.message // 🔹 Muestra el mensaje del servidor
                    }).then(() => {
                        $('#modalClaseActiva').modal('hide');
                        location.reload();
                    });
                } else {
                    // ❌ Si la respuesta tiene un error desde el backend
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Ocurrió un problema desconocido.'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.error('❌ Error en AJAX:', status, error);
                console.error('🔴 Respuesta completa del servidor:', xhr.responseText);

                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Hubo un problema al actualizar el estado de la clase. Verifique la conexión o contacte al soporte.'
                });
            }
        });

    });
});

function validarFormulario() {
    const esReserva = $('#chkReserva').is(':checked');
    let valido = true;

    // Siempre validar la fecha
    if (!$('#modalFechaOculta').val()) valido = false;

    // Si no es reserva, validar también los demás campos
    if (!esReserva) {
        if (!$('#selectEstudiante').val()) valido = false;
        if (!$('#selectProgramas').val()) valido = false;
        if (!$('#selectClasePrograma').val()) valido = false;
        if (!$('#duracionClase').val()) valido = false;
        if (!$('#selectVehiculo').val()) valido = false;
        if (!$('#modalHoraInicio').val()) valido = false;
        if (!$('#modalHoraFin').val()) valido = false;
    }

    $('#btnGuardarClase').prop('disabled', !valido);
}


// Deshabilitar el botón de guardar al abrir el modal
$('#modalAgregarClase').on('show.bs.modal', function () {
    $('#btnGuardarClase').prop('disabled', true);
});

$('#selectEstudiante, #selectProgramas, #selectClasePrograma, #duracionClase, #selectVehiculo, #modalFechaOculta, #modalHoraInicio, #modalHoraFin, #chkReserva')
    .on('change input', function () {
        validarFormulario();
    });

// Función para verificar si la fecha y hora seleccionada es pasada
function esFechaPasada(fechaSeleccionada, horaSeleccionada) {
    var fechaActual = new Date();
    var horaActual = fechaActual.getHours();
    var fechaActualFormatted = fechaActual.toISOString().split('T')[0];

    return fechaSeleccionada < fechaActualFormatted || (fechaSeleccionada === fechaActualFormatted && horaSeleccionada < horaActual);
}

// Función para mostrar mensaje de error cuando se selecciona una fecha pasada
function mostrarMensajeFechaPasada() {
    Swal.fire({
        icon: 'error',
        title: 'Hora pasada',
        text: 'No se pueden crear clases prácticas con fechas y horas pasadas.',
    });
}

// Función para cargar los detalles de una clase asignada y mostrarlos en el modal
function cargarDetallesClase(claseId) {
    $.ajax({
        url: '/clasespracticasdetalle/' + claseId,
        method: 'GET',
        success: function (data) {

            try {
                var clase = JSON.parse(data);
                // Llenar los campos del modal con la información de la clase
                $('#modalInstructorId').val(clase.instructor_id);
                $('#nombreInstructor').text(clase.instructor_nombre.toUpperCase());
                $('#modalFechaTexto').text(clase.fecha);
                $('#modalFechaOculta').val(clase.fecha);
                $('#modalHoraInicio').val(clase.hora_inicio);
                $('#modalHoraFin').val(clase.hora_fin);
                $('#selectVehiculo').val(clase.vehiculo_id);
                $('#modalLugar').val(clase.lugar);
                $('#modalObservaciones').val(clase.observaciones);
                $('#nombreProgramaEditar').text(data.programa_nombre); // Mostrar el nombre del programa

                // Abrir el modal con la información de la clase existente
                $('#modalAgregarClase').modal('show');
            } catch (e) {
                alert('Error al procesar los detalles de la clase.');
            }
        },
        error: function () {
            alert('Error al traer los detalles de la clase.');
        }
    });
}

function mostrarModalCrearClase(fechaSeleccionada, horaSeleccionada, instructorId, instructorNombre) {
    // 🔄 Resetear la barra de progreso
    $('#progressHoras').css('width', '0%').attr('aria-valuenow', '0');
    $('#progressHoras').text('0%');
    $('#infoHoras').text('0 de 0 horas completadas');

    // Limpiar todos los campos del modal para evitar residuos de datos anteriores
    $('#modalInstructorId').val('');
    $('#nombreInstructor').text('');
    $('#modalFechaTexto').text('');
    $('#modalFechaOculta').val('');
    $('#modalHoraInicio').val('');
    $('#modalHoraFin').val('');
    $('#modalLugar').val('');
    $('#modalObservaciones').val('');
    $('#termino_busqueda').val(''); // Limpiar el campo de búsqueda del estudiante
    $('#selectEstudiante').val('');
    $('#selectProgramas').empty().append('<option value="" disabled selected>Seleccione un programa del estudiante</option>');
    $('#detallePrograma').hide();
    $('#selectClasePrograma').empty().append('<option value="" disabled selected>Seleccione una Clase del Programa</option>');
    $('#selectVehiculo').empty().append('<option value="" disabled selected>Seleccione un Vehículo</option>');
    $('#duracionClase').val('');
    $('#detalleEstudiante').hide();

    // Formatear la fecha seleccionada para el modal
    var fechaSeleccionadaObj = new Date(fechaSeleccionada + 'T00:00:00'); // Asegurar que la fecha se interprete correctamente como fecha local
    var opcionesFormato = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    var fechaFormateada = fechaSeleccionadaObj.toLocaleDateString('es-ES', opcionesFormato);

    // Llenar el modal con los valores iniciales
    $('#modalInstructorId').val(instructorId);
    $('#nombreInstructor').text(instructorNombre.toUpperCase());
    $('#modalFechaTexto').text(fechaFormateada);
    $('#modalFechaOculta').val(fechaSeleccionada); // Almacena la fecha en un campo oculto
    $('#modalHoraInicio').val(horaSeleccionada);   // Almacena la hora en un campo oculto
    $('#modalHoraFin').val(''); // Limpiar la hora de fin al abrir el modal
    $('#modalLugar').val(''); // Limpiar el campo de lugar
    $('#modalObservaciones').val(''); // Limpiar el campo de observaciones

    // Mostrar el modal
    $('#modalAgregarClase').modal('show');
}

// Código para abrir el modal de edición de clase
$('.hora-celda.clase-asignada').on('click', function () {

    let claseId = $(this).data('clase-id');

    // Llamada AJAX para obtener los detalles de la clase
    $.ajax({
        url: '/clasespracticasdetalle/' + claseId,
        method: 'GET',
        dataType: 'json', // 👈 ya llega como objeto
        success: function (clase) {
            try {
                //let clase = JSON.parse(data);

                console.log("✅ Objeto JSON recibido:", clase);

                if (clase.estado_id === 10) {
                    alert('Es una reserva');
                    // ✅ Es una RESERVA → abrir modal solo de eliminar
                    $('#modalEliminarReserva #claseIdEliminar').val(clase.clase.id);
                    $('#modalEliminarReserva #detalleReserva').text(
                        `${clase.fecha} ${clase.hora_inicio} - ${clase.hora_fin} (${clase.instructor_nombre} ${clase.instructor_apellidos})`
                    );
                    $('#modalEliminarReserva').modal('show');
                } else {
                    // 📘 Clase normal → llenar el modal de edición
                    $('#modalEditarClase #instructorNombreEditar').text(clase.instructor_nombre + ' ' + clase.instructor_apellidos);
                    $('#modalEditarClase #modalFechaTextoEditar').text(clase.fecha);
                    $('#modalEditarClase #horaInicioEditar').val(clase.hora_inicio);
                    $('#modalEditarClase #horaFinEditar').val(clase.hora_fin);
                    $('#modalEditarClase #programaEditar').val(clase.programa_id);
                    // ... otros campos

                    //$('#modalEditarClase').modal('show');
                }
            } catch (error) {
                console.error('Error al analizar los datos JSON:', error);
            }
        },



        // success: function (data) {
        //     try {
        //         let clase = JSON.parse(data);

        //         // Llenar los campos del modal de edición
        //         $('#modalEditarClase #instructorNombreEditar').text(clase.instructor_nombre + ' ' + clase.instructor_apellidos);
        //         $('#modalEditarClase #modalFechaTextoEditar').text(clase.fecha);
        //         $('#modalEditarClase #horaInicioEditar').val(clase.hora_inicio);
        //         $('#modalEditarClase #horaFinEditar').val(clase.hora_fin);
        //         $('#modalEditarClase #programaEditar').val(clase.programa_id);
        //         // ... otros campos

        //         // Mostrar el modal de edición
        //         $('#modalEditarClase').modal('show');
        //     } catch (error) {
        //         console.error('Error al analizar los datos JSON:', error);
        //     }
        // },
        error: function (xhr, status, error) {
            console.error('Error al obtener los detalles de la clase:', status, error);
        }
    });
});

function limpiarModalCrearClase() {
    $('#modalCrearClase input, #modalCrearClase select').val('');
    $('#modalCrearClase textarea').val('');
    // Si hay algún campo específico que necesita limpiarse de otra manera, asegúrate de hacerlo aquí
}

function calcularHoraFin() {
    var horaInicio = $('#modalHoraInicio').val(); // Obtener la hora de inicio
    var duracion = $('#duracionClase').val(); // Obtener la duración seleccionada

    // Validar la duración
    if (!duracion) {
        console.error('Duración de la clase no seleccionada.');
        $('#modalHoraFin').val('');
        return;
    }

    duracion = parseInt(duracion, 10); // Convertir la duración a número
    if (isNaN(duracion)) {
        console.error('Duración no es un número válido:', duracion);
        $('#modalHoraFin').val('');
        return;
    }

    // Validar y formatear la hora de inicio
    horaInicio = parseInt(horaInicio, 10); // Convertir horaInicio a número
    if (isNaN(horaInicio)) {
        console.error('Hora de inicio no es válida:', horaInicio);
        $('#modalHoraFin').val('');
        return;
    }

    // Calcular la hora final (asumiendo que los minutos son 00)
    var horaFin = horaInicio + duracion; // Sumar la duración en horas
    if (horaFin >= 24) {
        horaFin = horaFin % 24; // Ajustar si la hora pasa de las 24
    }

    // Formatear las horas al formato HH:mm:ss
    var horaInicioFormatted = `${horaInicio.toString().padStart(2, '0')}:00:00`;
    var horaFinFormatted = `${horaFin.toString().padStart(2, '0')}:00:00`;

    // Mostrar los resultados
    $('#modalHoraInicio').val(horaInicioFormatted); // Actualizar la hora de inicio en el campo (si es necesario)
    $('#modalHoraFin').val(horaFinFormatted); // Mostrar la hora final en el campo
}

function obtenerVehiculosDisponiblesParaEditar(fecha, horaInicio, horaFin, tipoVehiculoId, vehiculoActualId) {
    const url = '/vehiculosdisponibles/';

    const datos = {
        fecha: fecha,
        hora_inicio: horaInicio,
        hora_fin: horaFin,
        tipo_vehiculo_id: tipoVehiculoId,
        vehiculo_actual_id: vehiculoActualId
    };

    $.ajax({
        url: url,
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        success: function (data) {
            try {
                const vehiculos = typeof data === "string" ? JSON.parse(data) : data;

                const selectVehiculo = $('#selectVehiculoEditar');
                selectVehiculo.empty();

                selectVehiculo.append('<option value="" disabled selected>Seleccione un vehículo</option>');

                if (Array.isArray(vehiculos) && vehiculos.length > 0) {
                    vehiculos.forEach(function (vehiculo) {
                        const opcion = $('<option>')
                            .val(vehiculo.id)
                            .text(vehiculo.placa.toUpperCase());
                        selectVehiculo.append(opcion);
                    });

                    if (vehiculoActualId) {
                        selectVehiculo.val(vehiculoActualId);
                    }
                } else {
                    selectVehiculo.append('<option value="" disabled>No hay vehículos disponibles</option>');
                }
            } catch (e) {
                console.error('❌ Error procesando respuesta:', e);
                alert('Error al procesar los datos de los vehículos.');
            }
        },
        error: function (xhr, status, error) {
            console.error('❌ Error en la solicitud:', status, error);
            alert('Error al consultar los vehículos disponibles.');
        }
    });
}

// Función para formatear la hora con ceros a la izquierda
function formatHora(hora) {
    var partes = hora.split(':'); // Divide en [hora, minutos, segundos]
    var horaFormateada = partes[0].padStart(2, '0'); // Asegura dos dígitos en la hora
    return horaFormateada + ':' + partes[1] + ':' + partes[2]; // Vuelve a unir las partes
}

function obtenerVehiculosDisponibles(fechaSeleccionada, horaSeleccionada, tipoVehiculoId) {

    var horaFormateada = formatHora(horaSeleccionada);
    var fechaHoraISO = fechaSeleccionada + 'T' + horaFormateada;

    $.ajax({
        url: '/vehiculosdisponibles/' + fechaHoraISO + '/' + tipoVehiculoId,
        method: 'GET',

        success: function (data) {

            try {
                // Validar si la respuesta ya es un objeto JSON
                var vehiculos = typeof data === "string" ? JSON.parse(data) : data;
                $('#selectVehiculo').empty();
                $('#selectVehiculo').append('<option value="" disabled selected>Seleccione un vehículo</option>');

                if (Array.isArray(vehiculos) && vehiculos.length > 0) {
                    vehiculos.forEach(function (vehiculo) {
                        $('#selectVehiculo').append('<option value="' + vehiculo.id + '">' + vehiculo.placa.toUpperCase() + '</option>');
                    });
                } else {
                    $('#selectVehiculo').append('<option value="" disabled>No hay vehículos disponibles</option>');
                }

            } catch (e) {
                console.error('Error al procesar los datos de los vehículos:', e); // DEPURACIÓN
                alert('Error al procesar los datos de los vehículos.');
            }
        },
        error: function () {
            console.error('Error al realizar la solicitud AJAX para vehículos.'); // DEPURACIÓN
            alert('Error al traer los datos de los vehículos.');
        }
    });
}

$('#btnGuardarClase').on('click', function () {

    let claseData = {};
    if ($('#chkReserva').is(':checked')) {
        console.log("🔒 Modo RESERVA activado");

        // Calcular hora_fin automáticamente (+2 horas si no se selecciona)
        let horaInicio = $('#modalHoraInicio').val();
        let horaFin = $('#modalHoraFin').val();

        if (!horaFin && horaInicio) {
            let h = parseInt(horaInicio, 10);
            let nuevaHora = (h + 2).toString().padStart(2, '0') + ":00:00";
            horaFin = nuevaHora;
        }

        claseData = {
            nombre_clase: 'Reserva',
            estado_id: 10,
            fecha_clase: $('#modalFechaOculta').val(),
            hora_inicio: horaInicio,
            hora_fin: horaFin,   // ✅ ya con valor calculado
            matricula_id: null,
            programa_id: 999,
            clase_programa_id: 999,
            lugar_recogida: null,
            vehiculo_id: null,
            instructor_id: $('#modalInstructorId').val(),
            observaciones: $('#modalObservaciones').val(),
            empresa_id: $('#empresaId').val()
        };
    }
    else {
        var programaSeleccionado = $('#selectProgramas').val() || '';
        var valores = programaSeleccionado.split("|");
        var programaId = valores[0];
        var matriculaId = valores[1];

        // Construir claseData en modo NORMAL
        claseData = {
            nombre_clase: $('#selectClasePrograma option:selected').text(),
            estado_id: 1,
            fecha_clase: $('#modalFechaOculta').val(),
            hora_inicio: $('#modalHoraInicio').val(),
            hora_fin: $('#modalHoraFin').val(),
            matricula_id: matriculaId,
            programa_id: programaId,
            clase_programa_id: $('#selectClasePrograma').val(),
            lugar_recogida: $('#modalLugar').val(),
            vehiculo_id: $('#selectVehiculo').val(),
            instructor_id: $('#modalInstructorId').val(),
            observaciones: $('#modalObservaciones').val(),
            empresa_id: $('#empresaId').val()
        };
    }

    $.ajax({
        url: '/clasespracticasguardar',
        method: 'POST',
        data: claseData,
        dataType: 'json', // Asegura que la respuesta se procese como JSON
        success: function (response) {
            // Verificar si la respuesta indica éxito
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Clase creada exitosamente',
                    text: response.message || 'La clase práctica ha sido registrada correctamente.'
                }).then(() => {
                    // Cerrar el modal
                    $('#modalAgregarClase').modal('hide');

                    // Recargar el cronograma con la fecha actual
                    cargarCronograma(claseData.fecha_clase);
                });
            } else {
                // Manejar errores específicos del servidor
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'No se pudo guardar la clase. Inténtelo de nuevo.'
                });
            }
        },
        error: function (xhr, status, error) {
            // Manejo de errores no controlados o problemas de conexión
            Swal.fire({
                icon: 'error',
                title: 'Error de conexión',
                text: 'Hubo un problema al comunicarse con el servidor. Inténtelo de nuevo.'
            });
        }
    });
});

/**
 * Función para actualizar el cronograma visualmente tras la creación de una nueva clase
 * @param {object} claseData - Datos de la clase recién creada
 */
function actualizarCronogramaConNuevaClase(claseData) {
    var horaInicio = parseInt(claseData.hora_inicio.split(':')[0]);
    var horaFin = parseInt(claseData.hora_fin.split(':')[0]);
    var duracion = horaFin - horaInicio;
    var instructorId = claseData.instructor_id;

    // Buscar la fila del instructor en el cronograma
    var filaInstructor = $('#tablaInstructores').find(`[data-instructor-id="${instructorId}"]`).closest('tr');

    // Remover celdas en el rango de horas de la clase (ya que serán reemplazadas por una sola celda combinada)
    filaInstructor.find(`[data-hora]`).each(function () {
        var horaCelda = parseInt($(this).data('hora'));
        if (horaCelda >= horaInicio && horaCelda < horaFin) {
            $(this).remove();
        }
    });

    // Crear una celda combinada para la clase recién creada
    var celdaClase = '<td class="hora-celda clase-asignada bg-primary text-white" colspan="' + duracion + '" data-clase-id="' + claseData.clase_id + '" data-instructor-id="' + instructorId + '">';
    celdaClase += claseData.nombre + '<br>' + claseData.matricula_id + '<br>' + claseData.programa_id + '<br>' + $('#selectVehiculo option:selected').text();
    celdaClase += '</td>';

    // Añadir la celda combinada en la posición correcta de la fila del instructor
    filaInstructor.find(`[data-hora="${horaInicio}"]`).before(celdaClase);
}

// Remover 'aria-hidden' al abrir el modal y enfocar el primer campo interactivo
$('#modalAgregarClase').on('shown.bs.modal', function () {
    $('#modalAgregarClase').removeAttr('aria-hidden');
    $('#nombreClase').focus(); // Mover el foco al primer campo dentro del modal
});

// Agregar 'aria-hidden' nuevamente al cerrar el modal
$('#modalAgregarClase').on('hidden.bs.modal', function () {
    $('#modalAgregarClase').attr('aria-hidden', 'true');
});

/**
 * Maneja la eliminación de una clase práctica cuando el usuario confirma la acción.
 * Incluye la interacción con el backend mediante una solicitud AJAX para eliminar la clase.
 * Actualiza el cronograma y muestra mensajes al usuario según el resultado de la operación.
 */
$(document).on('click', '#btnEliminarClase', function () {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            const claseId = $('#modalClaseId').val(); // Obtener el ID de la clase

            if (!claseId) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo obtener el ID de la clase para eliminar.'
                });
                return;
            }

            $.ajax({
                url: `/qweRtYuiO/${claseId}`, // Ruta al backend con el ID dinámico
                method: 'GET',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Clase eliminada',
                            text: response.message
                        }).then(() => {
                            // Cerrar el modal correcto
                            if ($('#modalEditarClase').hasClass('show')) {
                                $('#modalEditarClase').modal('hide');
                            }
                            if ($('#modalEliminarReserva').hasClass('show')) {
                                $('#modalEliminarReserva').modal('hide');
                            }

                            // Recargar el cronograma
                            const fecha = $('#fechaInput').val();
                            cargarCronograma(fecha);
                        });
                        // Swal.fire({
                        //     icon: 'success',
                        //     title: 'Clase eliminada',
                        //     text: response.message
                        // }).then(() => {
                        //     $('#modalEditarClase').modal('hide'); // Cerrar el modal
                        //     const fecha = $('#fechaInput').val(); // Obtener fecha actual
                        //     cargarCronograma(fecha); // Actualizar el cronograma
                        // });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar la clase. Inténtelo nuevamente.'
                    });
                }
            });

        } else {
            Swal.fire({
                icon: 'info',
                title: 'Cancelado',
                text: 'La clase no fue eliminada.'
            });
        }
    });
});


/** MODIFICAR CLASE */
$(document).on('click', '#btnGuardarClaseEditar', function () {

    // Recoger datos del formulario
    const claseId = $('#modalClaseId').val();
    const claseProgramadaId = $('#selectClaseProgramadaEditar').val();
    const vehiculoId = $('#selectVehiculoEditar').val();
    const observaciones = $('#modalObservacionesEditar').val();
    const nuevoInstructorId = $('#selectInstructorEditar').val();

    if (!claseId) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo obtener el ID de la clase.'
        });
        return;
    }

    // Validar que los campos requeridos no estén vacíos
    if (!claseProgramadaId || !vehiculoId) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos obligatorios',
            text: 'Asegúrese de seleccionar la clase programada y el vehículo.'
        });
        return;
    }

    // Enviar los datos al backend
    $.ajax({
        url: `/zxcVbnmQw/${claseId}`, // Cambia a tu ruta de edición en el backend
        method: 'POST',
        contentType: 'application/json',

        data: JSON.stringify({
            clase_programa_id: claseProgramadaId,
            vehiculo_id: vehiculoId,
            observaciones: observaciones,
            instructor_id: nuevoInstructorId !== '' ? parseInt(nuevoInstructorId) : null
        }),

        success: function (response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Guardado',
                    text: response.message
                }).then(() => {
                    $('#modalEditarClase').modal('hide'); // Cerrar el modal
                    const fecha = $('#fechaInput').val(); // Recargar el cronograma
                    cargarCronograma(fecha);
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo guardar la clase. Inténtelo nuevamente.'
            });
        }
    });
});

