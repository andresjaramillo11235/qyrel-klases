<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Vehículos</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANwWzrI2QLwog9qdKMCKy7lB2cL1paL6c&callback=initMap" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 100%;
            width: 100%;
        }
    </style>
</head>
<body>
    <div id="map"></div>
    
    <script>
        let map;
        let markers = {};
        
        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 4.60971, lng: -74.08175 }, // Centro en Bogotá
                zoom: 12
            });
            fetchVehiculos();
            setInterval(fetchVehiculos, 60000); // Actualizar cada minuto
        }

        function fetchVehiculos() {
            console.log("Solicitando datos de vehículos...");
            $.getJSON("getVehiculos.php?empresa_id=17", function(data) {
                console.log("Datos recibidos:", data);
                if (!Array.isArray(data)) {
                    console.error("Error: La API no devolvió un array", data);
                    return;
                }
                
                data.forEach(vehiculo => {
                    let position = { lat: parseFloat(vehiculo.latitud), lng: parseFloat(vehiculo.longitud) };
                    
                    if (markers[vehiculo.api_device_id]) {
                        markers[vehiculo.api_device_id].setPosition(position);
                    } else {
                        markers[vehiculo.api_device_id] = new google.maps.Marker({
                            position: position,
                            map: map,
                            title: vehiculo.placa
                        });
                    }
                });
            }).fail(function(jqxhr, textStatus, error) {
                console.error("Error al obtener los datos de vehículos:", textStatus, error);
            });
        }
    </script>
</body>
</html>
