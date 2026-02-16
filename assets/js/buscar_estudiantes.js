$(document).ready(function () {

    $('#btnBuscarEstudiante').on('click', function () {
        var termino = $('#termino_busqueda').val();

        console.log('Término de búsqueda:', termino);

        $.ajax({
            url: '/estudiantesbuscar/' + termino,
            method: 'GET',
            success: function (data) {
                console.log('Datos recibidos:', data);
                try {
                    var estudiantes = JSON.parse(data);
                    if (Array.isArray(estudiantes) && estudiantes.length > 0) {
                        $('#selectEstudiante').empty();
                        $('#selectEstudiante').append('<option value="">Seleccione un estudiante</option>');
                        estudiantes.forEach(function (estudiante) {
                            var nombreCompleto = estudiante.nombres + ' ' + estudiante.apellidos;
                            $('#selectEstudiante').append('<option value="' + estudiante.id + '" data-foto="' + estudiante.foto + '" data-cedula="' + estudiante.numero_documento + '" data-nombre="' + nombreCompleto + '">' + nombreCompleto + '</option>');
                        });
                        $('#resultadoBusqueda').show();
                    } else {
                        console.error('No se encontraron estudiantes.');
                        alert('No se encontraron estudiantes.');
                    }
                } catch (e) {
                    console.error('Error al parsear los datos del estudiante:', e);
                    alert('Error al procesar los datos del estudiante.');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al traer los datos del estudiante:', status, error);
                alert('Error al traer los datos del estudiante.');
            }
        });
    });

    $('#btnSeleccionarEstudiante').on('click', function () {
        var estudianteId = $('#selectEstudiante').val();
        var selectedOption = $('#selectEstudiante option:selected');
        var foto = selectedOption.data('foto');
        var cedula = selectedOption.data('cedula');
        var nombre = selectedOption.data('nombre');

        if (estudianteId) {
            console.log('Estudiante seleccionado:', estudianteId);

            $.ajax({
                url: '/estudiantesdetalle/' + estudianteId,
                method: 'GET',
                success: function (data) {
                    console.log('Datos del estudiante recibidos:', data);
                    try {
                        var estudiante = JSON.parse(data);
                        $('#cedulaEstudiante').text(cedula);
                        $('#fotoEstudiante').attr('src', '/assets/uploads/' + foto);
                        $('#nombreCompletoEstudiante').text(nombre);
                        $('#codigoMatricula').text(estudiante.matricula_id);

                        $('#cedula').val(estudiante.cedula);
                        $('#nombre_estudiante').val(nombre);
                        $('#codigo_matricula').val(estudiante.matricula_id);

                        $('#selectProgramas').empty();
                        estudiante.matriculas.forEach(function (matricula) {
                            $('#selectProgramas').append('<option value="' + matricula.programa_id + '">' + matricula.programa + '</option>');
                        });

                        $('#detalleEstudiante').show();
                    } catch (e) {
                        console.error('Error al parsear los datos del estudiante:', e);
                        alert('Error al procesar los datos del estudiante.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al traer los datos del estudiante:', status, error);
                    alert('Error al traer los datos del estudiante.');
                }
            });
        } else {
            alert('Por favor, seleccione un estudiante.');
        }
    });



    $('#btnSeleccionarPrograma').on('click', function () {

        console.log('Seleccionar programa');

        var programaId = $('#selectProgramas').val();
        var matriculaId = $('#codigoMatricula').text();

        console.assert('Programa seleccionado:============================================', programaId);

        if (programaId && matriculaId) {

            $.ajax({
                url: '/programasdetalle/' + programaId + '/' + matriculaId,
                method: 'GET',
                success: function (data) {
                    console.log('Datos del programa recibidos:', data);
                    try {
                        var programa = JSON.parse(data);
                        $('#horasRequeridas').text(programa.horas_practicas);
                        $('#horasCursadas').text(programa.horas_cursadas);

                        $('#programa').val(programa.programa);
                        $('#programa_id').val(programa.programa_id);
                        $('#detallePrograma').show();
                    } catch (e) {
                        console.error('Error al parsear los datos del programa:', e);
                        alert('Error al procesar los datos del programa.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al traer los datos del programa:', status, error);
                    alert('Error al traer los datos del programa.');
                }
            });
        } else {
            alert('Por favor, seleccione un programa.');
        }
    });
});
