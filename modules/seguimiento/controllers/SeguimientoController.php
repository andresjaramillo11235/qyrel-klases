<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class SeguimientoController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function index()
    {
        try {
            ob_start();
            include '../modules/seguimiento/views/mapaFlota.php';
            $content = ob_get_clean();
            echo $content;
        } catch (Exception $e) {
            echo "Error al cargar la vista: " . $e->getMessage();
        }
    }

    public function getPosicionVehiculos()
    {
        header('Content-Type: application/json');

        try {
            $empresaId = $_SESSION['empresa_id'];

            $query = "SELECT 
                    up.api_device_id, 
                    v.placa AS placa, 
                    up.latitud, 
                    up.longitud, 
                    up.velocidad, 
                    up.device_time,
                    d.imei  -- 🔥 Agregamos el IMEI
                  FROM ultima_posicion up
                  INNER JOIN vehiculos v ON up.vehiculo_id = v.id
                  LEFT JOIN dispositivos_gps d ON v.id = d.vehiculo_id -- 🔥 Unimos con dispositivos_gps
                  WHERE up.empresa_id = :empresa_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
            $stmt->execute();

            $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($vehiculos ?: []);
        } catch (Exception $e) {
            error_log("Error en la consulta: " . $e->getMessage());
            echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
        }
    }

    public function getVehicleApiTracksolid()
    {
        header('Content-Type: application/json');

        try {
            $config = new DatabaseConfig();
            $conn = $config->getConnection();

            $vehiculoId = $_GET['vehiculo_id'] ?? null;
            $empresaId = $_SESSION['empresa_id'];

            if (!$vehiculoId || !$empresaId) {
                echo json_encode(["error" => "vehiculo_id y empresa_id son requeridos"]);
                exit;
            }

            // Obtener API de Tracksolid asociada al vehículo
            $query = "SELECT dg.id, dg.vehiculo_id, dg.empresa_id, dg.plataforma, dg.api_id, 
                             a.base_url, a.usuario, a.password
                      FROM dispositivos_gps dg
                      INNER JOIN apis a ON dg.api_id = a.id
                      WHERE dg.vehiculo_id = :vehiculo_id 
                      AND dg.empresa_id = :empresa_id 
                      AND dg.plataforma = 'tracksolid'";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(':vehiculo_id', $vehiculoId, PDO::PARAM_INT);
            $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
            $stmt->execute();

            $apiData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$apiData) {
                echo json_encode(["error" => "No se encontró API para este vehículo"]);
                exit;
            }

            echo json_encode($apiData);
        } catch (Exception $e) {
            echo json_encode(["error" => "Error en la consulta: " . $e->getMessage()]);
        }
    }
}
