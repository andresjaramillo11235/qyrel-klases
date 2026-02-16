<?php
require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class NovedadesController
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

        if (!$permissionController->hasPermission($currentUserId, 'view_novedades')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        // Consultar las novedades registradas
        $query = "SELECT 
                    cn.id, 
                    cn.tiempo, 
                    cn.fecha, 
                    cn.hora_inicio, 
                    cn.hora_fin, 
                    cn.novedad_estado, 
                    cn.observaciones, 
                    e.nombres AS estudiante_nombre, 
                    e.apellidos AS estudiante_apellido, 
                    e.foto AS estudiante_foto, 
                    e.numero_documento AS numero_documento,
                    i.nombres AS instructor_nombre, 
                    i.apellidos AS instructor_apellido, 
                    v.placa AS vehiculo_placa, 
                    cn.lugar, 
                    cn.clase_nombre AS clase_nombre 
                  FROM clases_novedades cn 
                  LEFT JOIN clases_practicas cp ON cn.clase_practica_id = cp.id 
                  LEFT JOIN matriculas m ON cn.matricula_id = m.id 
                  LEFT JOIN estudiantes e ON m.estudiante_id = e.id 
                  LEFT JOIN instructores i ON cn.instructor_id = i.id 
                  LEFT JOIN vehiculos v ON cn.vehiculo_id = v.id 
                  WHERE cn.empresa_id = :empresa_id 
                  ORDER BY cn.tiempo DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $novedades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/novedades/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update()
    {
        // Recibir datos en formato JSON
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['novedad_id']) || !isset($data['observaciones'])) {
            echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
            return;
        }

        $novedadId = $data['novedad_id'];
        $observaciones = $data['observaciones'];

        // Consultar los datos de la novedad para auditoría
        $queryDatos = "SELECT 
                        cn.tiempo, cn.fecha, cn.hora_inicio, cn.hora_fin, cn.clase_nombre,
                        e.nombres AS estudiante_nombres, e.apellidos AS estudiante_apellidos,
                        i.nombres AS instructor_nombres, i.apellidos AS instructor_apellidos,
                        v.placa AS vehiculo_placa
                    FROM clases_novedades cn
                    LEFT JOIN matriculas m ON cn.matricula_id = m.id
                    LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                    LEFT JOIN instructores i ON cn.instructor_id = i.id
                    LEFT JOIN vehiculos v ON cn.vehiculo_id = v.id
                    WHERE cn.id = :novedad_id";

        $stmtDatos = $this->conn->prepare($queryDatos);
        $stmtDatos->bindParam(':novedad_id', $novedadId, PDO::PARAM_INT);
        $stmtDatos->execute();
        $novedad = $stmtDatos->fetch(PDO::FETCH_ASSOC);

        $query = "UPDATE clases_novedades SET novedad_estado = 'FINALIZADA', observaciones = :observaciones WHERE id = :novedad_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
        $stmt->bindParam(':novedad_id', $novedadId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            // 🔎 Registrar auditoría detallada
            $estudiante = strtoupper(trim($novedad['estudiante_nombres'] . ' ' . $novedad['estudiante_apellidos']));
            $instructor = strtoupper(trim($novedad['instructor_nombres'] . ' ' . $novedad['instructor_apellidos']));
            $vehiculo = $novedad['vehiculo_placa'] ? strtoupper($novedad['vehiculo_placa']) : 'NO ASIGNADO';
            $clase = strtoupper($novedad['clase_nombre']);
            $fecha = $novedad['fecha'];
            $horario = $novedad['hora_inicio'] . ' - ' . $novedad['hora_fin'];

            $descripcion = "Se finalizó la novedad con ID {$novedadId} para la clase '{$clase}' "
                . "programada el {$fecha} de {$horario}. "
                . "Estudiante: {$estudiante}, Instructor: {$instructor}, Vehículo: {$vehiculo}. "
                . "Observaciones: {$observaciones}";

            $auditoriaController = new AuditoriaController();
            $auditoriaController->registrar($_SESSION['user_id'], 'Actualizar', 'Clases Novedades', $descripcion, $_SESSION['empresa_id']);

            echo json_encode(['success' => true, 'message' => 'Novedad finalizada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar la novedad.']);
        }
    }
}
