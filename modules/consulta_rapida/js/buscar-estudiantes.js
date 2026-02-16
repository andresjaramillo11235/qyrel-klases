$(document).ready(function () {
    // Buscar estudiantes automáticamente mientras el usuario escribe
    $('#termino_busqueda').on('input', function () {
        const termino = $(this).val().trim();

        if (termino.length >= 3) {
            $.post('/k1L2m3N4Op/', { termino: termino }, function (data) {
                try {
                    const estudiantes = JSON.parse(data);
                    const $select = $('#selectEstudiante');
                    $select.empty().append('<option value="" disabled selected>Seleccione un estudiante</option>');

                    if (Array.isArray(estudiantes) && estudiantes.length > 0) {
                        estudiantes.forEach(function (est) {
                            const nombre = `${est.nombres} ${est.apellidos}`;
                            $select.append(`<option value="${est.id}">${nombre}</option>`);
                        });
                    }

                } catch (error) {
                    console.error('Error al procesar los datos del estudiante:', error);
                }
            }).fail(function () {
                console.error('Error al traer los datos del estudiante.');
            });
        } else {
            $('#selectEstudiante').empty().append('<option value="" disabled selected>Seleccione un estudiante</option>');
        }
    });

    // Redireccionar al seleccionar un estudiante
    $('#selectEstudiante').on('change', function () {
        const estudianteId = $(this).val();
        if (estudianteId) {
            window.location.href = `/estudiantesdetail/${estudianteId}`;
        }
    });
});
