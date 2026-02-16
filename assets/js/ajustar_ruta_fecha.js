$('form[action="/clases_practicas/cronograma/"]').on('submit', function (e) {
    e.preventDefault();
    var fechaSeleccionada = $('input[name="fecha"]').val();
    window.location.href = '/clases_practicas/cronograma/' + fechaSeleccionada;
});
