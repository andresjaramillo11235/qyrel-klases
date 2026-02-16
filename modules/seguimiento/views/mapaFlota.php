<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mapa de Vehículos</title>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyANwWzrI2QLwog9qdKMCKy7lB2cL1paL6c&callback=initMap" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        #map {
            height: 100%;
            width: 100%;
        }
        .vehicle-panel {
            position: absolute;
            top: 10px;
            left: 10px;
            width: 200px;
            background: white;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            padding: 15px;
            max-height: 80vh;
            overflow-y: auto;
            z-index: 1000;
        }
        .vehicle-panel h3 {
            margin: 0 0 10px;
            text-align: center;
        }
        .vehicle-item {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }
        .vehicle-item:last-child {
            border-bottom: none;
        }
        .online {
            color: green;
        }
        .offline {
            color: red;
        }
    </style>
</head>
<body>
    <div class="vehicle-panel">
        <input type="text" id="search" placeholder="Buscar por placa..." style="width: 80%; padding: 5px;">
        <div id="vehicleContainer"></div>
    </div>
    <div id="map"></div>
    
    <script src="../modules/seguimiento/assets/js/mapa.js"></script>
    <script src="../modules/seguimiento/assets/js/camaras.js"></script>

</body>
</html>
