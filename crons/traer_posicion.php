<?php
date_default_timezone_set('America/Bogota');
require_once '../config/DatabaseConfig.php';

// Ruta del archivo de log
define("LOG_FILE", "/var/log/ceacloud/cron_posiciones.log");

/**
 * Función para escribir en el log con diferentes niveles de mensaje
 * @param string $mensaje El mensaje a registrar
 * @param string $nivel El nivel de log (INFO, ERROR, DEBUG)
 */
function escribirLog($mensaje, $nivel = "INFO")
{
    $fechaHora = date("Y-m-d H:i:s");
    $logMensaje = "[$fechaHora] [$nivel] $mensaje" . PHP_EOL;
    file_put_contents(LOG_FILE, $logMensaje, FILE_APPEND);
}

// Crear instancia de la base de datos
$database = new DatabaseConfig();
$db = $database->getConnection();

if (!$db) {
    escribirLog("Error al conectar a la base de datos.", "ERROR");
    exit();
}

escribirLog("=== Inicio de ejecución del cron ===", "INFO");

// Paso 1: Consultar clases activas que estén dentro del rango de horario
$query_clases = "
        SELECT id, vehiculo_id, hora_inicio, hora_fin 
        FROM clases_practicas 
        WHERE estado = 'activa' 
        AND TIME(NOW()) BETWEEN hora_inicio AND hora_fin
    ";
$stmt_clases = $db->prepare($query_clases);
$stmt_clases->execute();
$result_clases = $stmt_clases->fetchAll(PDO::FETCH_ASSOC);

// Verificar si hay clases activas dentro del horario permitido
if (!$result_clases) {
    escribirLog("No hay clases activas dentro del horario permitido.", "INFO");
}

// Marcar clases como inactivas si ya pasaron su horario
$query_update_clases = "
        UPDATE clases_practicas 
        SET estado = 'inactiva' 
        WHERE estado = 'activa' 
        AND TIME(NOW()) > hora_fin
    ";
$stmt_update_clases = $db->prepare($query_update_clases);
$stmt_update_clases->execute();

if ($stmt_update_clases->rowCount() > 0) {
    escribirLog("Se actualizaron {$stmt_update_clases->rowCount()} clases a estado 'inactiva' porque superaron su horario.", "INFO");
}

foreach ($result_clases as $clase) {
    $vehiculo_id = $clase['vehiculo_id'];
    escribirLog("Procesando vehículo ID: {$vehiculo_id}", "DEBUG");

    // Paso 2: Obtener el id_traccar del vehículo
    $query_vehiculo = "SELECT id_traccar FROM vehiculos WHERE id = :vehiculo_id";
    $stmt_vehiculo = $db->prepare($query_vehiculo);
    $stmt_vehiculo->bindParam(":vehiculo_id", $vehiculo_id, PDO::PARAM_INT);
    $stmt_vehiculo->execute();
    $vehiculo = $stmt_vehiculo->fetch(PDO::FETCH_ASSOC);

    if (!$vehiculo || !$vehiculo['id_traccar']) {
        escribirLog("Vehículo ID {$vehiculo_id} no tiene un id_traccar asociado.", "WARNING");
        continue;
    }

    $id_traccar = $vehiculo['id_traccar'];
    escribirLog("Obteniendo posición de Traccar para ID {$id_traccar}", "DEBUG");

    // Paso 3: Consultar el servicio web de Traccar
    $url = "http://wsapisamael.lat/get_vehicle.php?id=" . $id_traccar;
    $response = @file_get_contents($url); // Se usa @ para evitar warnings en caso de fallo

    if ($response === FALSE) {
        escribirLog("Fallo al obtener datos del servicio web para ID {$id_traccar}.", "ERROR");
        continue;
    }

    $data = json_decode($response, true);

    if (!$data || !isset($data['success']) || !$data['success']) {
        escribirLog("Error en la respuesta del servicio para ID {$id_traccar}: " . json_encode($data), "ERROR");
        continue;
    }

    $position = $data['data'];
    escribirLog("Datos obtenidos: " . json_encode($position), "DEBUG");

    // Paso 4: Verificar si el `positionId` es nuevo
    $query_check_position = "SELECT positionId FROM posiciones_temporales WHERE vehiculo_id = :vehiculo_id ORDER BY id DESC LIMIT 1";
    $stmt_check_position = $db->prepare($query_check_position);
    $stmt_check_position->bindParam(":vehiculo_id", $vehiculo_id, PDO::PARAM_INT);
    $stmt_check_position->execute();
    $last_position = $stmt_check_position->fetch(PDO::FETCH_ASSOC);

    if ($last_position && $last_position['positionId'] == $position['positionId']) {
        escribirLog("Posición no insertada para vehículo ID {$vehiculo_id}: ya existe el mismo positionId.", "INFO");
        continue;
    }

    // Paso 5: Insertar la nueva posición en `posiciones_temporales`
    $query_insert_position = "
        INSERT INTO posiciones_temporales (vehiculo_id, status, lastUpdate, positionId, latitude, longitude, speed, course)
        VALUES (:vehiculo_id, :status, :lastUpdate, :positionId, :latitude, :longitude, :speed, :course)
    ";

    try {
        $stmt_insert_position = $db->prepare($query_insert_position);

        // Convertir la fecha a la zona horaria de Colombia
        $fechaOriginal = new DateTime($position['lastUpdate'], new DateTimeZone('UTC'));
        $fechaOriginal->setTimezone(new DateTimeZone('America/Bogota'));
        $lastUpdateColombia = $fechaOriginal->format('Y-m-d H:i:s');

        // Vincular los parámetros
        $stmt_insert_position->bindParam(":vehiculo_id", $vehiculo_id, PDO::PARAM_INT);
        $stmt_insert_position->bindParam(":status", $position['status']);
        $stmt_insert_position->bindParam(":lastUpdate", $lastUpdateColombia);
        $stmt_insert_position->bindParam(":positionId", $position['positionId'], PDO::PARAM_INT);
        $stmt_insert_position->bindParam(":latitude", $position['latitude']);
        $stmt_insert_position->bindParam(":longitude", $position['longitude']);
        $stmt_insert_position->bindParam(":speed", $position['speed']);
        $stmt_insert_position->bindParam(":course", $position['course']);

        if ($stmt_insert_position->execute()) {
            escribirLog("Posición insertada para vehículo ID {$vehiculo_id} con positionId {$position['positionId']}", "INFO");
        } else {
            escribirLog("Error al insertar posición para vehículo ID {$vehiculo_id}: " . implode(", ", $stmt_insert_position->errorInfo()), "ERROR");
        }
    } catch (Exception $e) {
        escribirLog("Excepción al insertar posición para vehículo ID {$vehiculo_id}: " . $e->getMessage(), "ERROR");
    }
}

escribirLog("=== Fin de ejecución del cron ===", "INFO");
