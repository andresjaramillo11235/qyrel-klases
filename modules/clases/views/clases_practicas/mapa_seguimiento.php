

<div class="container mt-4">
    <!-- Título del módulo -->
    <div class="text-center mb-4">
        <h3 class="fw-bold">📍 Seguimiento de Vehículo en Tiempo Real</h3>
    </div>

    <!-- Información de la clase -->
    <div class="card shadow-lg">
        <div class="card-body">
            <h5 class="card-title text-primary">Detalles de la Clase</h5>
            <hr>
            <p><strong>📘 Clase:</strong> <?php echo htmlspecialchars($clase['nombre']); ?></p>
            <p><strong>👨‍🏫 Instructor:</strong> <?php echo htmlspecialchars($clase['instructor_nombre'] . ' ' . $clase['instructor_apellidos']); ?></p>
            <p><strong>🚗 Vehículo:</strong> <?php echo htmlspecialchars($clase['vehiculo_placa']); ?></p>
            <p><strong>⏰ Horario:</strong> <?php echo htmlspecialchars($clase['fecha'] . ' de ' . $clase['hora_inicio'] . ' a ' . $clase['hora_fin']); ?></p>
        </div>
    </div>

    <!-- Contenedor del Mapa -->
    <div class="mt-4">
        <h5 class="text-primary text-center">Mapa de Seguimiento</h5>
        <div id="map" class="border rounded shadow-sm" style="height: 500px; width: 100%;"></div>
    </div>
</div>

<!-- Agregar Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<!-- Agregar Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


<!-- Cargar el archivo JavaScript para el seguimiento -->
<script>
    // Variables de PHP para usar en JavaScript
    const posiciones = <?php echo json_encode($posiciones); ?>;
</script>
<script src="/modules/clases/views/js/mapa_seguimiento.js"></script>
