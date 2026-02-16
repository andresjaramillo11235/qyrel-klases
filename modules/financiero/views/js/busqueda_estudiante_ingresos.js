$(document).ready(function () {
    // Buscar estudiantes automáticamente mientras el usuario escribe
    $('#termino_busqueda').on('input', function () {
        var termino = $(this).val();

        if (termino.length >= 3) {
            $.ajax({
                url: '/buscar-estudiante-por-nombre/',
                method: 'POST',
                data: { termino: termino },
                success: function (data) {
                    try {
                        var estudiantes = JSON.parse(data);
                        $('#selectEstudiante').empty(); // Limpiar el select
                        $('#selectEstudiante').append('<option value="" disabled selected>Seleccione un estudiante</option>');

                        if (Array.isArray(estudiantes) && estudiantes.length > 0) {
                            estudiantes.forEach(function (estudiante) {
                                var nombreCompleto = estudiante.nombres + ' ' + estudiante.apellidos;
                                $('#selectEstudiante').append('<option value="' + estudiante.id + '" data-estudiante=\'' + JSON.stringify(estudiante) + '\'>' + nombreCompleto + '</option>');
                            });
                        }
                    } catch (e) {
                        console.error('Error al procesar los datos del estudiante:', e);
                    }
                },
                error: function () {
                    console.error('Error al traer los datos del estudiante.');
                }
            });
        } else {
            $('#selectEstudiante').empty().append('<option value="" disabled selected>Seleccione un estudiante</option>');
        }
    });

    // Mostrar los datos del estudiante seleccionado
    $('#selectEstudiante').on('change', function () {
        var selectedOption = $(this).find('option:selected');
        var estudiante = JSON.parse(selectedOption.attr('data-estudiante')); // Obtener el JSON completo del estudiante

        if (estudiante) {
            // Mostrar la información del estudiante
            $('#datosEstudiante').html(`
                <p><strong>Nombre:</strong> ${estudiante.nombres} ${estudiante.apellidos}</p>
                <p><strong>Cédula:</strong> ${estudiante.numero_documento}</p>
                <img src="/files/fotos_estudiantes/${estudiante.foto}" alt="Foto del Estudiante" class="img-fluid" style="max-width: 100px;">
                <h5>Detalles de la Matrícula:</h5>
                <select id="selectMatricula" class="form-control">
                    <option value="" disabled selected>Seleccione una matrícula</option>
                </select>
            `);

            // Llenar el select con las matrículas del estudiante
            estudiante.matriculas.forEach(function (matricula) {
                $('#selectMatricula').append(`
                    <option value="${matricula.id}">${matricula.id} - ${matricula.programa}</option>
                `);
            });

            // Si solo hay una matrícula, seleccionarla automáticamente
            if (estudiante.matriculas.length === 1) {
                $('#selectMatricula').val(estudiante.matriculas[0].id).trigger('change');
            }

            $('#datosEstudiante').show();
            $('#formularioIngreso').show();
        } else {
            $('#datosEstudiante').hide();
            $('#formularioIngreso').hide();
        }
    });

    // Actualizar el campo oculto cuando se seleccione una matrícula
    $(document).on('change', '#selectMatricula', function () {
        var matriculaId = $(this).val();
        $('#matricula_id').val(matriculaId);
    });
});



