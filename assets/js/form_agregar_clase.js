console.log("###########################form_agregar_clase.js cargado");



// Evento de cambio en el select de nombre de clase
$('#nombre_clase').on('change', function () {
    var selectedOption = $(this).find('option:selected');
    var duracion = selectedOption.data('duracion');
    var horaInicio = $('#hora').val().split(':')[0];

    if (horaInicio !== undefined && horaInicio !== null) {
        var horaFin = parseInt(horaInicio) + parseInt(duracion);
        horaFin = horaFin < 10 ? '0' + horaFin : horaFin;
        $('#hora').val(horaInicio + ':00 - ' + horaFin + ':00');
        $('#duracion_oculto').val(duracion);
    }
});

// Función para abrir el modal con los datos del estudiante y programa
function abrirModalConDatos(estudiante, programa) {
    $('#cedula').val(estudiante.cedula);
    $('#nombre_estudiante').val(estudiante.nombreCompleto);
    $('#programa').val(programa.nombre);
    $('#programa_id').val(programa.id);
    $('#codigo_matricula').val(estudiante.codigoMatricula);
    $('#matricula_id').val(estudiante.codigoMatricula);

    $('#modalAgregarClase').modal('show');
}
