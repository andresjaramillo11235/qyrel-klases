document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("formIngreso");

    form.addEventListener("submit", function (event) {
        // Obtener los valores de los campos
        const valor = document.getElementById("valor").value;
        const fecha = document.getElementById("fecha").value;

        // Verificar que los campos no estén vacíos
        if (!valor || !fecha) {
            event.preventDefault(); // Evitar que el formulario se envíe
            alert("Por favor, complete los campos de 'Valor' y 'Fecha' antes de guardar el ingreso.");
        }
    });
});
