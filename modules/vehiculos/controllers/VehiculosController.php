<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class VehiculosController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }


    public function traerVehiculosDisponibles()
    {


        header('Content-Type: application/json');

        try {
            $empresaId = $_SESSION['empresa_id'] ?? null;

            // Leer el JSON del cuerpo de la solicitud
            $datos = json_decode(file_get_contents("php://input"), true);





            $fecha = $datos['fecha'] ?? null;
            $horaInicio = $datos['hora_inicio'] ?? null;
            $horaFin = $datos['hora_fin'] ?? null;
            $tipoVehiculoId = $datos['tipo_vehiculo_id'] ?? null;
            $vehiculoActualId = $datos['vehiculo_actual_id'] ?? null;


            error_log("Fecha recibida: " . $fecha);
            error_log("Hora inicio: " . $horaInicio);
            error_log("Hora fin: " . $horaFin);
            error_log("Tipo Vehiculo ID: " . $tipoVehiculoId);
            error_log("Vehiculo actual ID: " . $vehiculoActualId);




            if (!$fecha || !$horaInicio || !$horaFin || !$tipoVehiculoId) {
                throw new Exception("Parámetros faltantes o inválidos.");
            }

            // Formatear horas si vienen sin segundos
            //if (strlen($horaInicio) === 5) $horaInicio .= ':00';
            //if (strlen($horaFin) === 5) $horaFin .= ':00';

            // 1. Obtener todos los vehículos del tipo especificado
            $queryVehiculos = "
                SELECT id, placa 
                FROM vehiculos 
                WHERE empresa_id = :empresa_id
                AND tipo_vehiculo_id = :tipo_vehiculo_id
            ";
            $stmtVehiculos = $this->conn->prepare($queryVehiculos);
            $stmtVehiculos->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
            $stmtVehiculos->bindParam(':tipo_vehiculo_id', $tipoVehiculoId, PDO::PARAM_INT);
            $stmtVehiculos->execute();
            $vehiculos = $stmtVehiculos->fetchAll(PDO::FETCH_ASSOC);

            error_log("Vehículos obtenidos: " . json_encode($vehiculos));


            $vehiculosDisponibles = [];

            // 2. Verificar disponibilidad de cada vehículo
            foreach ($vehiculos as $vehiculo) {

                $vehiculoId = $vehiculo['id'];

                $queryConflictos = "
                    SELECT COUNT(*) AS conflictos
                    FROM clases_practicas
                    WHERE vehiculo_id = :vehiculo_id
                    AND fecha = :fecha
                    AND (
                        (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
                        OR (hora_inicio = :hora_inicio AND hora_fin = :hora_fin)
                    )

                ";




                $stmtConflictos = $this->conn->prepare($queryConflictos);
                $stmtConflictos->bindParam(':vehiculo_id', $vehiculoId, PDO::PARAM_INT);
                $stmtConflictos->bindParam(':fecha', $fecha);
                $stmtConflictos->bindParam(':hora_inicio', $horaInicio);
                $stmtConflictos->bindParam(':hora_fin', $horaFin);
                $stmtConflictos->execute();
                $conflicto = $stmtConflictos->fetch(PDO::FETCH_ASSOC);

                error_log("Conflictos para vehículo ID $vehiculoId: " . json_encode($conflicto));


                // Permitir si no hay conflicto o si es el vehículo actual
                if ($conflicto['conflictos'] == 0 || $vehiculoId == $vehiculoActualId) {
                    $vehiculosDisponibles[] = $vehiculo;
                }
            }

            echo json_encode($vehiculosDisponibles, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }



    public function getVehiculosDisponibles($fechaISO, $tipoVehiculoId)
    {
        try {
            $empresaId = $_SESSION['empresa_id'];

            // Validar el tipo de vehículo
            if (!$tipoVehiculoId || !is_numeric($tipoVehiculoId)) {
                throw new Exception("El tipo de vehículo proporcionado no es válido.");
            }

            // Consulta para obtener los vehículos
            $query = "
                SELECT id, placa 
                FROM vehiculos 
                WHERE empresa_id = :empresa_id
                AND tipo_vehiculo_id = :tipo_vehiculo_id
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
            $stmt->bindParam(':tipo_vehiculo_id', $tipoVehiculoId, PDO::PARAM_INT);
            $stmt->execute();
            $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Validar que se encontraron vehículos
            if (!$vehiculos) {
                $vehiculos = [];
            }

            // Asegurar una respuesta JSON válida
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($vehiculos, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        } catch (Exception $e) {
            // Manejar errores y enviar mensaje en JSON
            header('Content-Type: application/json; charset=utf-8', true, 500);
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        // Validar si el usuario tiene permiso para ver los vehículos
        if (!$permissionController->hasPermission($currentUserId, 'view_vehiculos')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        // Obtener el ID de la empresa del usuario actual
        $empresaId = $_SESSION['empresa_id'];

        // Consultar los vehículos de la empresa
        $query = "
            SELECT v.*, 
                ptv.nombre AS tipo_vehiculo
            FROM vehiculos v
            LEFT JOIN param_tipo_vehiculo ptv ON v.tipo_vehiculo_id = ptv.id
            WHERE v.empresa_id = :empresa_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->execute();
        $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cargar la vista de listado
        ob_start();
        include '../modules/vehiculos/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        // Validar permiso para crear vehículos
        if (!$permissionController->hasPermission($currentUserId, 'create_vehiculos')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        // Obtener los datos necesarios para el formulario
        $empresaId = $_SESSION['empresa_id'];
        $tiposVehiculo = $this->getTiposVehiculo();
        $tiposCombustible = $this->getTiposCombustible();

        ob_start();
        include '../modules/vehiculos/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        // Verificar permisos
        if (!$permissionController->hasPermission($currentUserId, 'create_vehiculos')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $empresaId = $_SESSION['empresa_id'];
        $fotoPath = null;

        if (!empty($_FILES['foto']['name'])) {
            $targetDir = "../files/fotos_vehiculos/";
            $fileExtension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION)); // Obtener la extensión del archivo
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // Tipos de archivos permitidos

            // Validar tipo de archivo
            if (!in_array($fileExtension, $allowedTypes)) {
                echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                return;
            }

            // Generar un nombre único para el archivo con la extensión original
            $fileName = uniqid('', true) . '.' . $fileExtension;
            $targetFile = $targetDir . $fileName;

            // Intentar mover el archivo cargado a la ubicación deseada
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
                $fotoPath = $fileName; // Guardar el nombre del archivo para almacenarlo en la base de datos
            } else {
                echo "Hubo un error al subir la imagen.";
                return;
            }
        }

        // Insertar datos en la base de datos
        $query = "
            INSERT INTO vehiculos (
                placa, numero_licencia, modelo, fecha_matricula, vin, cilindrada, capacidad, 
                carroceria, numero_motor, numero_chasis, numero_serie, tipo_combustible_id, 
                propietario, identificacion, id_traccar, tipo_vehiculo_id, foto, endpoint, empresa_id
            ) VALUES (
                :placa, :numero_licencia, :modelo, :fecha_matricula, :vin, :cilindrada, :capacidad, 
                :carroceria, :numero_motor, :numero_chasis, :numero_serie, :tipo_combustible_id, 
                :propietario, :identificacion, :id_traccar, :tipo_vehiculo_id, :foto, :endpoint, :empresa_id
            )
        ";

        // Validar si viene el dato o asignar NULL
        $placa = isset($_POST['placa']) && $_POST['placa'] !== '' ? $_POST['placa'] : null;
        $numero_licencia = isset($_POST['numero_licencia']) && $_POST['numero_licencia'] !== '' ? $_POST['numero_licencia'] : null;
        $modelo = isset($_POST['modelo']) && $_POST['modelo'] !== '' ? $_POST['modelo'] : null;
        $fecha_matricula = isset($_POST['fecha_matricula']) && $_POST['fecha_matricula'] !== '' ? $_POST['fecha_matricula'] : null;
        $vin = isset($_POST['vin']) && $_POST['vin'] !== '' ? $_POST['vin'] : null;
        $cilindrada = isset($_POST['cilindrada']) && $_POST['cilindrada'] !== '' ? $_POST['cilindrada'] : null;
        $capacidad = isset($_POST['capacidad']) && $_POST['capacidad'] !== '' ? $_POST['capacidad'] : null;
        $carroceria = isset($_POST['carroceria']) && $_POST['carroceria'] !== '' ? $_POST['carroceria'] : null;
        $numero_motor = isset($_POST['numero_motor']) && $_POST['numero_motor'] !== '' ? $_POST['numero_motor'] : null;
        $numero_chasis = isset($_POST['numero_chasis']) && $_POST['numero_chasis'] !== '' ? $_POST['numero_chasis'] : null;
        $numero_serie = isset($_POST['numero_serie']) && $_POST['numero_serie'] !== '' ? $_POST['numero_serie'] : null;
        $tipo_combustible_id = isset($_POST['tipo_combustible_id']) && $_POST['tipo_combustible_id'] !== '' ? $_POST['tipo_combustible_id'] : null;
        $propietario = isset($_POST['propietario']) && $_POST['propietario'] !== '' ? $_POST['propietario'] : null;
        $identificacion = isset($_POST['identificacion']) && $_POST['identificacion'] !== '' ? $_POST['identificacion'] : null;
        $id_traccar = isset($_POST['id_traccar']) && $_POST['id_traccar'] !== '' ? $_POST['id_traccar'] : null;
        $tipo_vehiculo_id = isset($_POST['tipo_vehiculo_id']) && $_POST['tipo_vehiculo_id'] !== '' ? $_POST['tipo_vehiculo_id'] : null;
        $endpoint = isset($_POST['endpoint']) && $_POST['endpoint'] !== '' ? $_POST['endpoint'] : null;

        // Bind de parámetros
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':placa', $placa);
        $stmt->bindParam(':numero_licencia', $numero_licencia);
        $stmt->bindParam(':modelo', $modelo);
        $stmt->bindParam(':fecha_matricula', $fecha_matricula);
        $stmt->bindParam(':vin', $vin);
        $stmt->bindParam(':cilindrada', $cilindrada);
        $stmt->bindParam(':capacidad', $capacidad);
        $stmt->bindParam(':carroceria', $carroceria);
        $stmt->bindParam(':numero_motor', $numero_motor);
        $stmt->bindParam(':numero_chasis', $numero_chasis);
        $stmt->bindParam(':numero_serie', $numero_serie);
        $stmt->bindParam(':tipo_combustible_id', $tipo_combustible_id);
        $stmt->bindParam(':propietario', $propietario);
        $stmt->bindParam(':identificacion', $identificacion);
        $stmt->bindParam(':id_traccar', $id_traccar);
        $stmt->bindParam(':tipo_vehiculo_id', $tipo_vehiculo_id);
        $stmt->bindParam(':foto', $fotoPath); // Asumo que $fotoPath ya está definido
        $stmt->bindParam(':endpoint', $endpoint);
        $stmt->bindParam(':empresa_id', $empresaId); // Asumo que $empresaId ya está definido

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Vehículo creado correctamente.';
            header('Location: ' . $routes['vehiculos_index']);
        } else {
            echo "Hubo un error al guardar los datos.";
        }
    }

    public function edit($id)
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_vehiculos')) {
            echo "No tienes permiso para editar este vehículo.";
            return;
        }

        $empresaId = $_SESSION['empresa_id'];

        // Obtener los datos del vehículo
        $query = "SELECT * FROM vehiculos WHERE id = :id AND empresa_id = :empresa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->execute();
        $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vehiculo) {
            echo "Vehículo no encontrado.";
            return;
        }

        // Obtener datos para menús desplegables
        $tiposCombustible = $this->getTiposCombustible();
        $tiposVehiculo = $this->getTiposVehiculo();

        ob_start();
        include '../modules/vehiculos/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update()
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_vehiculos')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $id = $_POST['id'];
        $empresaId = $_SESSION['empresa_id'];

        // Validar y actualizar los datos
        $query = "
            UPDATE vehiculos 
            SET 
                placa = :placa,
                numero_licencia = :numero_licencia,
                modelo = :modelo,
                fecha_matricula = :fecha_matricula,
                vin = :vin,
                cilindrada = :cilindrada,
                capacidad = :capacidad,
                carroceria = :carroceria,
                numero_motor = :numero_motor,
                numero_chasis = :numero_chasis,
                numero_serie = :numero_serie,
                tipo_combustible_id = :tipo_combustible_id,
                propietario = :propietario,
                identificacion = :identificacion,
                id_traccar = :id_traccar,
                tipo_vehiculo_id = :tipo_vehiculo_id,
                endpoint = :endpoint,
                foto = :foto
            WHERE id = :id AND empresa_id = :empresa_id
        ";

        $stmt = $this->conn->prepare($query);

        // Vincular los parámetros
        $stmt->bindParam(':placa', $_POST['placa']);
        $stmt->bindParam(':numero_licencia', $_POST['numero_licencia']);
        $stmt->bindParam(':modelo', $_POST['modelo']);
        $stmt->bindParam(':fecha_matricula', $_POST['fecha_matricula']);
        $stmt->bindParam(':vin', $_POST['vin']);
        $stmt->bindParam(':cilindrada', $_POST['cilindrada']);
        $stmt->bindParam(':capacidad', $_POST['capacidad']);
        $stmt->bindParam(':carroceria', $_POST['carroceria']);
        $stmt->bindParam(':numero_motor', $_POST['numero_motor']);
        $stmt->bindParam(':numero_chasis', $_POST['numero_chasis']);
        $stmt->bindParam(':numero_serie', $_POST['numero_serie']);
        $stmt->bindParam(':tipo_combustible_id', $_POST['tipo_combustible_id']);
        $stmt->bindParam(':propietario', $_POST['propietario']);
        $stmt->bindParam(':identificacion', $_POST['identificacion']);
        $stmt->bindParam(':id_traccar', $_POST['id_traccar']);
        $stmt->bindParam(':tipo_vehiculo_id', $_POST['tipo_vehiculo_id']);
        $stmt->bindParam(':endpoint', $_POST['endpoint']);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empresa_id', $empresaId);

        // Manejo de la foto
        $fotoPath = null;
        if (!empty($_FILES['foto']['name'])) {
            $targetDir = "../files/fotos_vehiculos/";
            $fileName = uniqid() . '.' . pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $targetFile = $targetDir . $fileName;
            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validar tipo de archivo
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $allowedTypes)) {
                echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
                return;
            }

            // Subir archivo
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $targetFile)) {
                $fotoPath = $fileName;
            } else {
                echo "Hubo un error al subir la imagen.";
                return;
            }
        }

        // Usar la nueva foto si se subió, de lo contrario, mantener la existente
        if ($fotoPath) {
            $stmt->bindParam(':foto', $fotoPath);
        } else {
            $currentPhotoQuery = "SELECT foto FROM vehiculos WHERE id = :id AND empresa_id = :empresa_id";
            $currentPhotoStmt = $this->conn->prepare($currentPhotoQuery);
            $currentPhotoStmt->bindParam(':id', $id);
            $currentPhotoStmt->bindParam(':empresa_id', $empresaId);
            $currentPhotoStmt->execute();
            $currentPhoto = $currentPhotoStmt->fetchColumn();
            $stmt->bindParam(':foto', $currentPhoto);
        }

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Vehículo actualizado correctamente.';
            header('Location: ' . $routes['vehiculos_index']);
        } else {
            echo "Hubo un error al guardar los datos.";
        }
        exit;
    }

    public function detail($id)
    {
        // Verificar permisos
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_vehiculos')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $empresaId = $_SESSION['empresa_id'];

        // Consulta para obtener los datos del vehículo
        $query = "
            SELECT v.*, 
                   ptc.nombre AS tipo_combustible,
                   ptv.nombre AS tipo_vehiculo
            FROM vehiculos v
            LEFT JOIN param_tipo_combustible ptc ON v.tipo_combustible_id = ptc.id
            LEFT JOIN param_tipo_vehiculo ptv ON v.tipo_vehiculo_id = ptv.id
            WHERE v.id = :id AND v.empresa_id = :empresa_id
        ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $vehiculo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$vehiculo) {
            echo "Vehículo no encontrado.";
            return;
        }

        // Renderizar la vista
        ob_start();
        include '../modules/vehiculos/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Obtener tipos de combustible
    private function getTiposCombustible()
    {
        $query = "SELECT id, nombre FROM param_tipo_combustible";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener tipos de vehículo
    private function getTiposVehiculo()
    {
        $query = "SELECT id, nombre FROM param_tipo_vehiculo WHERE estado = 1";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function verificarPlaca($placa)
    {
        try {
            $empresaId = $_SESSION['empresa_id'];
            $query = "SELECT COUNT(*) as count FROM vehiculos WHERE placa = :placa AND empresa_id = :empresa_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
            $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Asegurar que solo se devuelve JSON
            header('Content-Type: application/json');
            echo json_encode(['exists' => $result['count'] > 0]);
            exit;
        } catch (Exception $e) {
            // Manejar errores en formato JSON
            header('Content-Type: application/json', true, 500);
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }

    /**
     * Obtiene la información completa de un vehículo a partir de su ID.
     *
     * @param int $vehiculoId ID del vehículo.
     * @return array|null Información del vehículo (array asociativo) o null si no se encuentra.
     */
    public function getVehiculoById($vehiculoId)
    {
        // Consulta para obtener toda la información del vehículo
        $query = " SELECT * 
                FROM vehiculos
                WHERE id = :vehiculo_id
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':vehiculo_id', $vehiculoId, PDO::PARAM_INT);
        $stmt->execute();

        // Retornar los datos del vehículo o null si no se encuentra
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
