<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';

class ClasesController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }

    public function index($matriculaId)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();
    
        if (!$permissionController->hasPermission($currentUserId, 'view_clases')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }
    
        // Fetch classes
        $query = "SELECT c.*, 
                         tc.nombre AS tipo_nombre,
                         ec.nombre AS estado_nombre,
                         v.placa AS vehiculo_placa,
                         i.nombres AS instructor_nombre
                  FROM clases c
                  LEFT JOIN param_tipos_clases tc ON c.tipo_id = tc.id
                  LEFT JOIN param_estados_clases ec ON c.estado_id = ec.id
                  LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
                  LEFT JOIN instructores i ON c.instructor_id = i.id
                  WHERE c.matricula_id = :matricula_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (!$clases) {
            $clases = [];
        }
    
        // Fetch matricula details
        $query = "SELECT m.*, 
                         p.nombre AS programa_nombre,
                         e.nombres AS estudiante_nombres,
                         e.apellidos AS estudiante_apellidos
                  FROM matriculas m
                  LEFT JOIN programas p ON m.programa_id = p.id
                  LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                  WHERE m.id = :matricula_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId);
        $stmt->execute();
        $matricula = $stmt->fetch(PDO::FETCH_ASSOC);
    
        ob_start();
        include '../modules/clases/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create($matriculaId)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'create_clases')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $programas = $this->getProgramas();
        $estudiantes = $this->getEstudiantes();
        $vehiculos = $this->getVehiculos();
        $empresas = $this->getEmpresas();
        $tiposClases = $this->getTiposClases();
        $estadosClases = $this->getEstadosClases();
        $instructores = $this->getInstructores();

        ob_start();
        include '../modules/clases/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();
    
        if (!$permissionController->hasPermission($currentUserId, 'create_clases')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }
    
        // Generar un ID único y validar que no exista en la base de datos
        do {
            $id = $this->generateUniqueCode($_POST['matricula_id']);
            $query = "SELECT COUNT(*) FROM clases WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $idExists = $stmt->fetchColumn();
        } while ($idExists > 0);
    
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $tipo_id = $_POST['tipo_id'];
        $estado_id = $_POST['estado_id'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $matricula_id = $_POST['matricula_id'];
        $lugar = $_POST['lugar'];
        $vehiculo_id = $_POST['vehiculo_id'];
        $instructor_id = $_POST['instructor_id'];
        $observaciones = $_POST['observaciones'];
    
        $query = "INSERT INTO clases (id, nombre, descripcion, tipo_id, estado_id, fecha, hora_inicio, hora_fin, 
        matricula_id, lugar, vehiculo_id, instructor_id, observaciones) 
                  VALUES (:id, :nombre, :descripcion, :tipo_id, :estado_id, :fecha, :hora_inicio, :hora_fin, 
                  :matricula_id, :lugar, :vehiculo_id, :instructor_id, :observaciones)";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':tipo_id', $tipo_id);
        $stmt->bindParam(':estado_id', $estado_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':matricula_id', $matricula_id);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':vehiculo_id', $vehiculo_id);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':observaciones', $observaciones);
    
        if ($stmt->execute()) {
            header("Location: /clases/index/$matricula_id");
            exit;
        } else {
            echo "Error al crear la clase.";
        }
    }
    
    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();
    
        if (!$permissionController->hasPermission($currentUserId, 'edit_clases')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }
    
        $programas = $this->getProgramas();
        $estudiantes = $this->getEstudiantes();
        $vehiculos = $this->getVehiculos();
        $instructores = $this->getInstructores(); // Obtener la lista de instructores
        $tipos_clases = $this->getTiposClases(); // Obtener la lista de tipos de clases
        $estados_clases = $this->getEstadosClases(); // Obtener la lista de estados de clases
    
        $query = "SELECT * FROM clases WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $clase = $stmt->fetch(PDO::FETCH_ASSOC);
    
        ob_start();
        include '../modules/clases/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
    
    public function update($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();
    
        if (!$permissionController->hasPermission($currentUserId, 'edit_clases')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }
    
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $tipo_id = $_POST['tipo_id'];
        $estado_id = $_POST['estado_id'];
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $lugar = $_POST['lugar'];
        $vehiculo_id = $_POST['vehiculo_id'];
        $instructor_id = $_POST['instructor_id'];
        $observaciones = $_POST['observaciones'];
        $matricula_id = $_POST['matricula_id'];
    
        $query = "UPDATE clases 
                  SET nombre = :nombre, descripcion = :descripcion, tipo_id = :tipo_id, estado_id = :estado_id, fecha = :fecha, 
                      hora_inicio = :hora_inicio, hora_fin = :hora_fin, lugar = :lugar, vehiculo_id = :vehiculo_id, 
                      instructor_id = :instructor_id, observaciones = :observaciones 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':tipo_id', $tipo_id);
        $stmt->bindParam(':estado_id', $estado_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':vehiculo_id', $vehiculo_id);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':id', $id);
    
        if ($stmt->execute()) {
            header("Location: /clases/index/$matricula_id");
            exit;
        } else {
            echo "Error al actualizar la clase.";
        }
    }

    public function detail($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
    
        if (!$permissionController->hasPermission($currentUserId, 'view_clases')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }
    
        $query = "SELECT c.*, 
                         tc.nombre as tipo_clase_nombre, 
                         ec.nombre as estado_clase_nombre, 
                         m.id as matricula_id, 
                         v.placa as vehiculo_placa, 
                         i.nombre as instructor_nombre
                  FROM clases c
                  LEFT JOIN param_tipos_clases tc ON c.tipo_id = tc.id
                  LEFT JOIN param_estados_clases ec ON c.estado_id = ec.id
                  LEFT JOIN matriculas m ON c.matricula_id = m.id
                  LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
                  LEFT JOIN instructores i ON c.instructor_id = i.id
                  WHERE c.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $clase = $stmt->fetch(PDO::FETCH_ASSOC);
    
        ob_start();
        include '../modules/clases/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    private function generateUniqueCode($matriculaId)
    {
        $datetime = new DateTime();
        $timestamp = $datetime->format('YmdHis');
        $uniqueCode = substr($timestamp . $matriculaId, 0, 10);
        return $uniqueCode;
    }

    private function getProgramas()
    {
        $query = "SELECT * FROM programas";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEstudiantes()
    {
        $query = "SELECT * FROM estudiantes";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getVehiculos()
    {
        $query = "SELECT * FROM vehiculos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEmpresas()
    {
        $query = "SELECT * FROM empresas";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTiposClases()
    {
        $query = "SELECT * FROM param_tipos_clases";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEstadosClases()
    {
        $query = "SELECT * FROM param_estados_clases";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getInstructores()
    {
        $query = "SELECT * FROM instructores";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
