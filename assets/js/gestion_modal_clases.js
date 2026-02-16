$(document).ready(function () {
    $(document).on('click', '.hora-celda', function () {
        // Verificar si el estudiante y el programa han sido seleccionados
        var cedula = $('#cedulaEstudiante').text();
        var nombreEstudiante = $('#nombreCompletoEstudiante').text();
        var codigoMatricula = $('#codigoMatricula').text();
        var programaId = $('#selectProgramas').val(); // Obtener el ID del programa seleccionado
        var programaNombre = $('#selectProgramas option:selected').text(); // Obtener el nombre del programa seleccionado

        if (!cedula || !nombreEstudiante || !codigoMatricula || !programaId || !programaNombre) { // Verificar también si programaNombre está vacío
            // ========== Cambios Realizados ==========
            Swal.fire({
                icon: 'error',
                title: 'Datos incompletos',
                text: 'Debe seleccionar un usuario y un programa antes de agregar una clase.',
            });
            // ========== Cambios Realizados ==========
            return;
        }

        var instructorId = $(this).data('instructor-id');
        var hora = $(this).data('hora');
        var instructorNombre = $(this).closest('tr').find('td:first').text();

        console.log('Cedula:', cedula); // Verificar el valor de la cédula
        console.log('Nombre del Estudiante:', nombreEstudiante); // Verificar el valor del nombre del estudiante
        console.log('Programa:', programaNombre); // Verificar el valor del nombre del programa
        console.log('Código de Matrícula:', codigoMatricula); // Verificar el valor del código de matrícula

        $('#modalInstructorId').val(instructorId);
        $('#nombre_instructor').val(instructorNombre);
        $('#hora').val(hora + ':00 - ' + (hora + 1) + ':00');
        $('#fecha_clase').val(fechaSeleccionada);
        $('#cedula').val(cedula); // Asignar el valor de la cédula
        $('#nombre_estudiante').val(nombreEstudiante); // Asignar el valor del nombre del estudiante
        $('#programa').val(programaNombre); // Asignar el valor del nombre del programa
        $('#codigo_matricula').val(codigoMatricula); // Asignar el valor del código de matrícula
        $('#modalAgregarClase').modal('show');
        $('#programa_id').val(programaId);

    });
});
