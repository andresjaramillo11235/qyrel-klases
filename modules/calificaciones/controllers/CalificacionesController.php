<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class CalificacionesController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }
    /**
     * Método para listar todas las calificaciones ordenadas por la más reciente.
     */
    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        $query = "
            SELECT 
                ccp.*, 
                cp.nombre AS clase_nombre, 
                cp.fecha AS clase_fecha,
                cp.hora_inicio AS clase_hora_inicio,
                cp.hora_fin AS clase_hora_fin,
                e.nombres AS estudiante_nombres, 
                e.apellidos AS estudiante_apellidos,
                e.numero_documento AS estudiante_documento,
                e.foto AS estudiante_foto,
                i.nombres AS instructor_nombres,
                i.apellidos AS instructor_apellidos
            FROM control_clases_practicas ccp
            LEFT JOIN clases_practicas cp ON ccp.clase_practica_id = cp.id
            LEFT JOIN matriculas m ON cp.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN instructores i ON cp.instructor_id = i.id
            WHERE (ccp.estudiante_fecha_calificacion IS NOT NULL 
                OR ccp.instructor_fecha_calificacion IS NOT NULL)
            AND cp.empresa_id = :empresa_id
            ORDER BY ccp.created_at DESC";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);

        // Ejecutar la consulta
        $stmt->execute();
        $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renderizar la vista de lista de calificaciones
        ob_start();
        include '../modules/calificaciones/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function detail($id)
    {
        $query = "
            SELECT 
                ccp.*, 
                cp.nombre AS clase_nombre, 
                cp.fecha AS clase_fecha,
                cp.hora_inicio AS clase_hora_inicio,
                cp.hora_fin AS clase_hora_fin,
                cp.lugar AS clase_lugar,
                cp.estado_id AS clase_estado_id,
                e.nombres AS estudiante_nombres,
                e.apellidos AS estudiante_apellidos,
                e.numero_documento AS estudiante_documento,
                e.foto AS estudiante_foto,
                i.nombres AS instructor_nombres,
                i.apellidos AS instructor_apellidos,
                i.foto AS instructor_foto,
                v.placa AS vehiculo_placa,
                v.foto AS vehiculo_foto
            FROM control_clases_practicas ccp
            LEFT JOIN clases_practicas cp ON ccp.clase_practica_id = cp.id
            LEFT JOIN matriculas m ON cp.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN instructores i ON cp.instructor_id = i.id
            LEFT JOIN vehiculos v ON cp.vehiculo_id = v.id
            WHERE ccp.id = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $calificacion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$calificacion) {
            // Manejar caso de no encontrar calificación
            header('Location: /calificaciones/');
            exit;
        }

        ob_start();
        include '../modules/calificaciones/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
