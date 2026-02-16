<?php

// Configuración de la base de datos
require_once '/var/www/ceacloud/config/DatabaseConfig.php';

// Definir ruta del archivo de log
define("LOG_FILE", "/var/www/ceacloud/logs/cron_gps.log");

/**
 * Función para registrar logs en el archivo LOG_FILE.
 */
function logMessage($message)
{
    $timestamp = "[" . date('Y-m-d H:i:s') . "] ";
    file_put_contents(LOG_FILE, $timestamp . $message . "\n", FILE_APPEND);
}

/**
 * Función para hacer peticiones a la API con autenticación básica.
 */
function makeApiRequest($url, $usuario, $password)
{
    $url = rtrim($url, '/'); // Eliminar barras adicionales al final
    logMessage("Llamando a la API: $url"); // Registrar URL en el log
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
        CURLOPT_USERPWD => "$usuario:$password",
        CURLOPT_HTTPHEADER => ['Content-Type: application/json']
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [$response, $httpCode];
}

/**
 * Obtener lista de dispositivos desde la base de datos.
 */
function getDispositivos($conn)
{
    $query = "
        SELECT dg.id, dg.imei, dg.id_traccar, dg.vehiculo_id, dg.api_id, dg.empresa_id, a.base_url, a.usuario, a.password
        FROM dispositivos_gps dg
        INNER JOIN apis a ON dg.api_id = a.id
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Obtener positionId de un dispositivo desde la API de Traccar.
 */
function getPositionId($dispositivo)
{
    $apiUrl = rtrim($dispositivo['base_url'], '/') . "/api/devices?id=" . $dispositivo['id_traccar'];
    [$response, $httpCode] = makeApiRequest($apiUrl, $dispositivo['usuario'], $dispositivo['password']);

    if ($httpCode !== 200) {
        logMessage("Error obteniendo device para ID: {$dispositivo['id']}. HTTP Code: $httpCode");
        return null;
    }

    $devices = json_decode($response, true);
    if (!empty($devices) && isset($devices[0]['positionId'])) {
        return $devices[0]['positionId'];
    }
    return null;
}

/**
 * Obtener la última posición del dispositivo.
 */
function getLastPosition($dispositivo, $positionId)
{
    $positionUrl = rtrim($dispositivo['base_url'], '/') . "/api/positions?id=" . $positionId;
    logMessage("Llamando a la API de posiciones: $positionUrl");
    [$positionResponse, $httpCode] = makeApiRequest($positionUrl, $dispositivo['usuario'], $dispositivo['password']);

    if ($httpCode !== 200) {
        logMessage("Error obteniendo posición para ID: {$dispositivo['id']}. HTTP Code: $httpCode");
        return null;
    }

    return json_decode($positionResponse, true);
}

/**
 * Guardar la posición en la base de datos.
 */
function savePosition($conn, $dispositivo, $positionData)
{
    if (!$positionData || empty($positionData[0])) {
        logMessage("No hay datos de posición para el dispositivo ID: {$dispositivo['id']}");
        return;
    }

    $data = $positionData[0];
    $apiDeviceId = "{$dispositivo['api_id']}_{$dispositivo['id_traccar']}";

    // Verificar si api_device_id ya existe en la base de datos
    $query = "SELECT position_id FROM ultima_posicion WHERE api_device_id = :api_device_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':api_device_id', $apiDeviceId);
    $stmt->execute();
    $existingPosition = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si el position_id ya existe en la base de datos, no hacemos nada
    if ($existingPosition && $existingPosition['position_id'] == $data['id']) {
        logMessage("El position_id {$data['id']} ya está registrado para el dispositivo ID: {$dispositivo['id']}. No se realiza inserción.");
        return;
    }

    // Extraer datos para la inserción
    $deviceTime = $data['deviceTime'] ?? null;
    $fixTime = $data['fixTime'] ?? null;
    
    $query = "INSERT INTO ultima_posicion (
        api_device_id, api_id, device_id, position_id, 
        vehiculo_id, empresa_id, protocolo, latitud, 
        longitud, velocidad, server_time, device_time, 
        fix_time, attributes
    ) VALUES (
        :api_device_id, :api_id, :device_id, :position_id, 
        :vehiculo_id, :empresa_id, :protocolo, :latitud, 
        :longitud, :velocidad, :server_time, :device_time, 
        :fix_time, :attributes
    ) 
    ON DUPLICATE KEY UPDATE 
        position_id   = VALUES(position_id), 
        latitud       = VALUES(latitud), 
        longitud      = VALUES(longitud), 
        velocidad     = VALUES(velocidad), 
        server_time   = VALUES(server_time), 
        device_time   = VALUES(device_time), 
        fix_time      = VALUES(fix_time), 
        attributes    = VALUES(attributes)";


    $stmt = $conn->prepare($query);
    $stmt->execute([
        ':api_device_id' => $apiDeviceId,
        ':api_id' => $dispositivo['api_id'], // Se agrega api_id
        ':device_id' => $dispositivo['id_traccar'],
        ':position_id' => $data['id'],
        ':vehiculo_id' => $dispositivo['vehiculo_id'],
        ':empresa_id' => $dispositivo['empresa_id'],
        ':protocolo' => $data['protocol'],
        ':latitud' => $data['latitude'],
        ':longitud' => $data['longitude'],
        ':velocidad' => $data['speed'],
        ':server_time' => $data['serverTime'],
        ':device_time' => $deviceTime,
        ':fix_time' => $fixTime,
        ':attributes' => json_encode($data['attributes'])
    ]);

    logMessage("Posición guardada para el dispositivo ID: {$dispositivo['id']}, Position ID: {$data['id']}");
}


/**
 * Ejecutar el proceso de obtención y almacenamiento de posiciones.
 */
function runCron()
{
    try {
        $config = new DatabaseConfig();
        $conn = $config->getConnection();
        $dispositivos = getDispositivos($conn);

        foreach ($dispositivos as $dispositivo) {
            $positionId = getPositionId($dispositivo);
            if (!$positionId) continue;

            $positionData = getLastPosition($dispositivo, $positionId);
            savePosition($conn, $dispositivo, $positionData);
        }
    } catch (Exception $e) {
        logMessage("Error en el cron: " . $e->getMessage());
    }
}

// Ejecutar el cron
runCron();

?>
