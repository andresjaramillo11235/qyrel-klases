// Esperar a que el DOM esté cargado
document.addEventListener("DOMContentLoaded", function () {
    // Inicializar el mapa
    var map = L.map('map').setView([4.6097, -74.0817], 13); // Bogotá como ejemplo

    // Cargar el mapa base
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    console.log("Mapa cargado correctamente.");

    // Función para recargar datos sin recargar toda la página
    function actualizarMapa() {
        console.log("Actualizando mapa...");
        location.reload(); // Recargar toda la página (temporalmente)
    }

    // Recargar cada 3 minutos (180,000 milisegundos)
    setInterval(actualizarMapa, 180000); 
});
