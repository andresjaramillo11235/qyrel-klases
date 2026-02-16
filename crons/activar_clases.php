<?php
// Configurar la zona horaria a la de Colombia
date_default_timezone_set('America/Bogota');

// Incluir la configuración de la base de datos
require_once '/var/www/ceacloud.lat/config/DatabaseConfig.php'; // Conexión a la base de datos de la aplicación

// Ruta del archivo de log
define("LOG_FILE", "/var/log/ceacloud/activar_clases.log");

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

// Instanciar la clase de configuración de la base de datos y obtener la conexión
$database = new DatabaseConfig();
$db = $database->getConnection();

if (!$db) {
    escribirLog("Error de conexión: no se pudo establecer una conexión con la base de datos.", "ERROR");
    exit;
}

// Fecha y hora actual
$fechaActual = date("Y-m-d");
$horaActual = date("H:i:s");

// Consulta para activar clases del día en curso que estén en el horario de inicio y que no estén ya activas
$query = "UPDATE clases_practicas 
          SET estado = 'activa' 
          WHERE fecha = :fechaActual 
          AND hora_inicio <= :horaActual 
          AND hora_fin > :horaActual
          AND estado = 'inactiva'";

$stmt = $db->prepare($query);
$stmt->bindParam(':fechaActual', $fechaActual);
$stmt->bindParam(':horaActual', $horaActual);

// Ejecutar la consulta y verificar si hay clases activadas
if ($stmt->execute()) {
    $clasesActivadas = $stmt->rowCount();
    if ($clasesActivadas > 0) {
        escribirLog("Clases activadas: $clasesActivadas", "INFO");
    } else {
        escribirLog("No hay clases para activar en este momento.", "INFO");
    }
} else {
    // Loguear el error en caso de fallo
    $errorInfo = implode(", ", $stmt->errorInfo());
    escribirLog("Error al activar clases: $errorInfo", "ERROR");
}

// Cerrar la conexión
$db = null;
