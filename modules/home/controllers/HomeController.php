<?php

require_once '../config/DatabaseConfig.php';

class HomeController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function index()
    {
        $titulo = 'Inicio';
        $empresaId = $_SESSION['empresa_id'];

        if ($_SESSION['rol_nombre'] == 'INST') {
            $clasesHoy = $this->clasesHoyInstructor();
            $clasesMes = $this->clasesMesInstructor();
            $listadoClasesHoy = $this->listadoClasesHoy();
            $totalHoras = $this->horasDictadasMesInstructor();
        }

        if ($_SESSION['rol_nombre'] == 'ADMIN') {
            $totalClases = $this->obtenerTotalClasesHoy($empresaId);
            $totalInstructores = $this->totalInstructoresHoy($empresaId);
            $clasesMes = $this->obtenerTotalClasesMes($empresaId);
        }

        ob_start();
        include '../modules/home/views/home_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Clases Prácticas programadas para hoy
    public function obtenerTotalClasesHoy($empresaId)
    {
        $query = "
            SELECT COUNT(*) AS total_clases_hoy 
            FROM clases_practicas 
            WHERE empresa_id = :empresa_id 
            AND fecha = CURDATE()
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total_clases_hoy'] ?? 0;
    }

    public function totalInstructoresHoy($empresaId)
    {
        $query = "
            SELECT COUNT(DISTINCT instructor_id) AS total_instructores_hoy 
            FROM clases_practicas 
            WHERE empresa_id = :empresa_id 
              AND fecha = CURDATE()
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total_instructores_hoy'] ?? 0;
    }

    public function obtenerTotalClasesMes($empresaId)
    {
        $query = "
            SELECT COUNT(*) AS total_clases_mes 
            FROM clases_practicas 
            WHERE empresa_id = :empresa_id
            AND MONTH(fecha) = MONTH(CURDATE())
            AND YEAR(fecha) = YEAR(CURDATE())
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total_clases_mes'] ?? 0;
    }

    public function clasesHoyInstructor()
    {
        // Obtener el ID del instructor desde la sesión
        $instructorId = $_SESSION['instructor_id'];
        $empresaId = $_SESSION['empresa_id'];

        // Obtener el número de clases del instructor en el día actual
        $queryClasesHoy = "
                SELECT COUNT(*) AS total_clases_hoy 
                FROM clases_practicas 
                WHERE empresa_id = :empresa_id 
                AND instructor_id = :instructor_id 
                AND fecha = CURDATE()
            ";

        $stmt = $this->conn->prepare($queryClasesHoy);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total_clases_hoy'] ?? 0; // Retornar 0 si no hay clases
    }

    public function clasesMesInstructor()
    {
        // Obtener el ID del instructor desde la sesión
        $instructorId = $_SESSION['instructor_id'];
        $empresaId = $_SESSION['empresa_id'];

        // Consulta para contar las clases del mes actual
        $queryClasesMes = "
                SELECT COUNT(*) AS total_clases_mes 
                FROM clases_practicas 
                WHERE empresa_id = :empresa_id 
                AND instructor_id = :instructor_id 
                AND MONTH(fecha) = MONTH(CURDATE()) 
                AND YEAR(fecha) = YEAR(CURDATE())
            ";

        $stmt = $this->conn->prepare($queryClasesMes);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return $resultado['total_clases_mes'] ?? 0; // Retornar 0 si no hay clases
    }


    public function horasDictadasMesInstructor()
    {
        $instructorId = $_SESSION['instructor_id'];
        $empresaId = $_SESSION['empresa_id'];

        $query = "
            SELECT 
                SUM(TIMESTAMPDIFF(MINUTE, hora_inicio, hora_fin)) AS total_minutos
            FROM clases_practicas 
            WHERE instructor_id = :instructor_id 
            AND empresa_id = :empresa_id
            AND fecha BETWEEN DATE_FORMAT(CURDATE(), '%Y-%m-01') AND CURDATE()
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();

        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalMinutos = $resultado['total_minutos'] ?? 0;

        // Convertir minutos a horas con 2 decimales
        $totalHoras = round($totalMinutos / 60, 2);

        return $totalHoras;
    }



    public function listadoClasesHoy()
    {
        // Obtener el ID del instructor desde la sesión
        $instructorId = $_SESSION['instructor_id'];
        $empresaId = $_SESSION['empresa_id'];

        // Consulta para obtener las clases del día actual
        $queryListadoClases = "SELECT 
                cp.id              AS clase_id,
                cp.matricula_id    AS matricula_id,   -- 🔹 ahora con alias
                cp.fecha,
                cp.hora_inicio     AS clase_hora,
                cp.hora_fin        AS clase_fin,
                cp.nombre          AS clase_nombre,
                v.placa            AS vehiculo_placa,
                e.nombres          AS estudiante_nombres,
                e.apellidos        AS estudiante_apellidos,
                e.celular          AS telefono
            FROM clases_practicas cp
            LEFT JOIN matriculas m   ON cp.matricula_id = m.id
            LEFT JOIN estudiantes e  ON m.estudiante_id = e.id
            LEFT JOIN vehiculos v    ON cp.vehiculo_id = v.id
            WHERE cp.empresa_id   = :empresa_id
            AND cp.instructor_id = :instructor_id
            AND cp.fecha = CURDATE()
            ORDER BY cp.hora_inicio ASC";

        $stmt = $this->conn->prepare($queryListadoClases);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); // 🔥 Retornamos el listado de clases
    }

    public function getHistorialClases()
    {
        $empresaId = $_SESSION['empresa_id'];
        $matriculaId = $_POST['matricula_id'] ?? null;

        if (!$matriculaId) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta matrícula']);
            exit;
        }

        $query = "SELECT 
                cp.id AS clase_practica_id,
                cp.fecha,
                cp.hora_inicio,
                cp.hora_fin,
                cp.nombre AS clase_nombre,
                p.nombre AS programa_nombre,
                v.placa AS vehiculo_placa,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
                ccp.instructor_calificacion,
                ccp.estudiante_calificacion,
                ccp.estado_clase,
                ccp.instructor_observaciones,
                ccp.estudiante_observaciones,
                ccp.hora_inicio_real,
                ccp.hora_fin_real,
                ccp.duracion_clase
            FROM clases_practicas cp
            LEFT JOIN programas p      ON cp.programa_id   = p.id
            LEFT JOIN vehiculos v      ON cp.vehiculo_id   = v.id
            LEFT JOIN instructores i   ON cp.instructor_id = i.id
            LEFT JOIN control_clases_practicas ccp ON ccp.clase_practica_id = cp.id
            WHERE cp.matricula_id = :mid
            AND cp.empresa_id = :eid
            ORDER BY cp.fecha DESC, cp.hora_inicio DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':mid', $matriculaId);
        $stmt->bindParam(':eid', $empresaId);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }
}
