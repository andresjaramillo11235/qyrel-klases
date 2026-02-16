// Incluir la biblioteca de iconos FontAwesome
const fontAwesomeLink = document.createElement("link");
fontAwesomeLink.rel = "stylesheet";
fontAwesomeLink.href = "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css";
document.head.appendChild(fontAwesomeLink);

/**
 * Archivo: mapa.js
 * Descripción: Controla el mapa de Google Maps, obtiene la posición de los vehículos y maneja eventos de interacción.
 */

let map;
let markers = {};
let activeInfoWindow = null; // Variable para manejar un solo InfoWindow activo

/**
 * Inicializa el mapa de Google Maps con una ubicación centralizada en Bogotá.
 * También configura la actualización automática de datos cada minuto.
 */
function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 4.60971, lng: -74.08175 }, // Centro en Bogotá
        zoom: 12
    });
    fetchVehiculos();
    setInterval(fetchVehiculos, 60000); // Actualizar cada minuto
}

/**
 * Obtiene la lista de vehículos con sus posiciones desde el servidor y los muestra en el mapa.
 * También actualiza la lista de vehículos en el panel lateral.
 */
function fetchVehiculos() {

    console.log("Solicitando datos de vehículos...");
    
    $.getJSON("/7rStUvWxYz/", function (data) {
        console.log("Datos recibidos:", data);
        if (!Array.isArray(data)) {
            console.error("Error: La API no devolvió un array", data);
            return;
        }

        let vehicleContainer = $('#vehicleContainer');
        vehicleContainer.empty();

        data.forEach(vehiculo => {
            let position = { lat: parseFloat(vehiculo.latitud), lng: parseFloat(vehiculo.longitud) };
            let status = vehiculo.velocidad > 0 ? "online" : "offline";
            let localTime = convertToColombianTime(vehiculo.device_time); // Convertir hora GMT a Colombia (-5)

            if (markers[vehiculo.api_device_id]) {
                markers[vehiculo.api_device_id].setPosition(position);
            } else {
                markers[vehiculo.api_device_id] = new google.maps.Marker({
                    position: position,
                    map: map,
                    title: vehiculo.placa
                });
            }

            vehicleContainer.append(`
                <div class="vehicle-item" 
                    onclick="focusOnVehicle('${vehiculo.api_device_id}', ${vehiculo.latitud}, ${vehiculo.longitud}, '${localTime}')"
                    style="cursor: pointer; display: flex; flex-direction: column; align-items: flex-start; gap: 3px;">
                    
                    <span style="text-transform: uppercase; font-weight: bold;">${vehiculo.placa}</span>
                    <span style="font-size: 12px; color: #6c757d;">${localTime}</span>
                    
                    <div style="display: flex; gap: 10px; margin-top: 5px;">

                        <i class="fas fa-info-circle" style="cursor: pointer; font-size: 18px; color: #007bff;" 
                        onclick="showVehicleInfo('${vehiculo.api_device_id}')" title="Información"></i>

                        <!--<i class="fas fa-search" style="cursor: pointer; font-size: 18px; color: #6c757d;"
                        onclick="searchVehicles()" title="Buscar"></i>-->

                        <i class="fas fa-video" style="cursor: pointer; font-size: 18px; color: #28a745;" 
                        onclick="openCamera('${vehiculo.imei}')" title="Cámara"></i>

                    </div>
                </div>
            `);

        });
    }).fail(function (jqxhr, textStatus, error) {
        console.error("Error al obtener los datos de vehículos:", textStatus, error);
    });
}

/**
 * Centra el mapa en el vehículo seleccionado y muestra la última hora de reporte.
 * Cierra cualquier InfoWindow abierto antes de mostrar uno nuevo.
 * @param {string} apiDeviceId - Identificador único del dispositivo GPS.
 * @param {number} lat - Latitud del vehículo.
 * @param {number} lng - Longitud del vehículo.
 * @param {string} lastReport - Fecha y hora del último reporte en hora local.
 */
function focusOnVehicle(apiDeviceId, lat, lng, lastReport) {
    let position = { lat: lat, lng: lng };
    map.setCenter(position);
    map.setZoom(15);

    // Cerrar cualquier InfoWindow abierto
    if (activeInfoWindow) {
        activeInfoWindow.close();
    }

    activeInfoWindow = new google.maps.InfoWindow({
        content: `<div><strong>Último reporte:</strong> ${lastReport}</div>`
    });

    activeInfoWindow.open(map, markers[apiDeviceId]);
}

/**
 * Convierte una hora en formato GMT a hora local de Colombia (-5 GMT).
 * @param {string} gmtTime - Fecha y hora en formato ISO de GMT.
 * @returns {string} - Hora convertida en formato colombiano.
 */
function convertToColombianTime(gmtTime) {
    let date = new Date(gmtTime);
    date.setHours(date.getHours() - 5); // Ajustar a zona horaria de Colombia (-5 GMT)
    return date.toLocaleString('es-CO', { timeZone: 'America/Bogota' });
}
