<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class DispositivosGpsController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_dispositivos_gps')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT d.*, 
                         COALESCE(v.placa, 'Sin Vehículo') AS vehiculo_placa, 
                         a.nombre AS api_nombre 
                  FROM dispositivos_gps d
                  LEFT JOIN vehiculos v ON d.vehiculo_id = v.id
                  LEFT JOIN apis a ON d.api_id = a.id
                  WHERE d.empresa_id = :empresa_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->execute();
        $dispositivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renderizar la vista de lista de ingresos
        ob_start();
        include '../modules/gps/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_dispositivos_gps')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener las APIs disponibles para el select
        $query = "SELECT id, nombre FROM apis WHERE empresa_id = :empresa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $_SESSION['empresa_id']);
        $stmt->execute();
        $apis = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener los vehículos disponibles para el select
        $query = "SELECT id, placa FROM vehiculos WHERE empresa_id = :empresa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $_SESSION['empresa_id']);
        $stmt->execute();
        $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/gps/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $routes = include '../config/Routes.php';
        $empresaId = $_SESSION['empresa_id'];

        $data = [
            'nombre' => $_POST['nombre'] ?? null,
            'imei' => $_POST['imei'] ?? null,
            'id_traccar' => $_POST['id_traccar'] !== "" ? $_POST['id_traccar'] : "",
            'marca' => $_POST['marca'] ?? null,
            'linea' => $_POST['linea'] ?? null,
            'vehiculo_id' => $_POST['vehiculo_id'] !== "" ? $_POST['vehiculo_id'] : null,
            'api_id' => $_POST['api_id'] !== "" ? $_POST['api_id'] : null
        ];

        $query = "INSERT INTO dispositivos_gps (nombre, imei, id_traccar, marca, linea, vehiculo_id, api_id, empresa_id) 
                  VALUES (:nombre, :imei, :id_traccar, :marca, :linea, :vehiculo_id, :api_id, :empresa_id)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':imei', $data['imei']);
        $stmt->bindParam(':id_traccar', $data['id_traccar'], PDO::PARAM_INT);
        $stmt->bindParam(':marca', $data['marca']);
        $stmt->bindParam(':linea', $data['linea']);
        $stmt->bindParam(':vehiculo_id', $data['vehiculo_id'], PDO::PARAM_INT);
        $stmt->bindParam(':api_id', $data['api_id'], PDO::PARAM_INT);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Dispositivo creado exitosamente.';
            header('Location: ' . $routes['dispositivos_gps_index']);
            exit;
        } else {
            $_SESSION['error_message'] = 'Error al crear el dispositivo GPS.';
            header('Location: ' . $routes['dispositivos_gps_index']);
            exit;
        }
    }
}
