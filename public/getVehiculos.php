<?php

require_once '../config/DatabaseConfig.php';

header('Content-Type: application/json');

try {
    $config = new DatabaseConfig();
    $conn = $config->getConnection();
    
    $empresaId = $_GET['empresa_id'] ?? null;
    if (!$empresaId) {
        echo json_encode(["error" => "Empresa ID requerido"]);
        exit;
    }

    $query = "SELECT up.api_device_id, v.placa AS vehiculo_nombre, up.latitud, up.longitud, up.velocidad, up.server_time 
              FROM ultima_posicion up
              INNER JOIN vehiculos v ON up.vehiculo_id = v.id
              WHERE up.empresa_id = :empresa_id";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
    $stmt->execute();
    
    $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Debugging output
    error_log("Consulta ejecutada para empresa_id: $empresaId");
    error_log("Resultados: " . json_encode($vehiculos));
    
    // Asegurar que la salida sea un array
    if (!$vehiculos) {
        echo json_encode([]);
    } else {
        echo json_encode($vehiculos);
    }
} catch (Exception $e) {
    error_log("Error en la consulta: " . $e->getMessage());
    echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
}

?>
