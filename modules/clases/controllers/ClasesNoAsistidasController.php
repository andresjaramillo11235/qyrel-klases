<?php

require_once '../config/DatabaseConfig.php';

class ClasesNoAsistidasController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function index()
    {
        $empresaId = $_SESSION['empresa_id'] ?? null;

        if (!$empresaId) {
            header('Location: /login/');
            exit;
        }

        $sql = "SELECT 
                cte.id AS cte_id,
                ct.id AS clase_id,
                ct.fecha,
                ct.hora_inicio,
                ct.hora_fin,
                COALESCE(ctt.nombre, '') AS tema_nombre,
                COALESCE(p.nombre, '')   AS programa_nombre,
                CONCAT(COALESCE(e.nombres,''), ' ', COALESCE(e.apellidos,'')) AS estudiante_nombre,
                e.numero_documento,
                CONCAT(COALESCE(i.nombres,''), ' ', COALESCE(i.apellidos,'')) AS instructor_nombre,
                COALESCE(a.nombre, '') AS aula_nombre,
                cte.asistencia
            FROM clases_teoricas_estudiantes cte
            INNER JOIN clases_teoricas ct ON ct.id = cte.clase_teorica_id
            INNER JOIN estudiantes e ON e.id = (SELECT estudiante_id FROM matriculas m WHERE m.id = cte.matricula_id LIMIT 1)
            LEFT JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
            LEFT JOIN programas p ON ct.programa_id = p.id
            LEFT JOIN instructores i ON ct.instructor_id = i.id
            LEFT JOIN aulas a ON ct.aula_id = a.id
            WHERE cte.asistencia = 2
              AND ct.empresa_id = :emp
            ORDER BY ct.fecha DESC, ct.hora_inicio";

        $st = $this->conn->prepare($sql);
        $st->bindValue(':emp', $empresaId, PDO::PARAM_INT);
        $st->execute();
        $clasesNoAsistidas = $st->fetchAll(PDO::FETCH_ASSOC);

        // Render
        ob_start();
        include '../modules/clases/views/clases_teoricas/clases_no_asistidas/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Acción para cambiar "NO ASISTIÓ" (2) a "NO VISTO" (0)
    // public function marcarComoNoVisto()
    // {
    //     $routes = include '../config/Routes.php';

    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         header('Location: ' . $routes['clases_teoricas_no_asistidas']);
    //         exit;
    //     }

    //     $cteId = $_POST['cte_id'] ?? null;
    //     if (!$cteId) {
    //         $_SESSION['error_message'] = 'Solicitud incompleta.';
    //         header('Location: ' . $routes['clases_teoricas_no_asistidas']);
    //         exit;
    //     }

    //     $upd = $this->conn->prepare("UPDATE clases_teoricas_estudiantes SET asistencia = 0 WHERE id = :cte");
    //     $upd->bindValue(':cte', (int)$cteId, PDO::PARAM_INT);
    //     if ($upd->execute()) {
    //         $_SESSION['success_message'] = 'Asistencia actualizada: el estudiante podrá volver a inscribirse en la clase.';
    //     } else {
    //         $_SESSION['error_message'] = 'No fue posible actualizar la asistencia.';
    //     }

    //     header('Location: ' . $routes['clases_teoricas_no_asistidas']);
    //     exit;
    // }

    // ----------------------------------------------------------
    // 🔹 Acción para cambiar "NO ASISTIÓ" (2) a "NO VISTO" (0)
    // ----------------------------------------------------------
    public function marcarComoNoVisto()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $routes['clases_teoricas_no_asistidas']);
            exit;
        }

        $cteId = $_POST['cte_id'] ?? null;

        if (!$cteId) {
            $_SESSION['error_message'] = 'Solicitud incompleta.';
            header('Location: ' . $routes['clases_teoricas_no_asistidas']);
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 Validar estado actual de la asistencia
        // ----------------------------------------------------------
        $val = $this->conn->prepare("
                SELECT asistencia
                FROM clases_teoricas_estudiantes
                WHERE id = :cte
                LIMIT 1
            ");
        $val->bindValue(':cte', (int)$cteId, PDO::PARAM_INT);
        $val->execute();

        $asistenciaActual = $val->fetchColumn();

        if ($asistenciaActual === false) {
            $_SESSION['error_message'] = 'Registro de asistencia no encontrado.';
            header('Location: ' . $routes['clases_teoricas_no_asistidas']);
            exit;
        }

        if ((int)$asistenciaActual !== 2) {
            $_SESSION['error_message'] =
                'Solo se pueden marcar como "No visto" clases con estado "No asistió".';
            header('Location: ' . $routes['clases_teoricas_no_asistidas']);
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 Actualizar asistencia a "NO VISTO" (0)
        // ----------------------------------------------------------
        $upd = $this->conn->prepare("
                UPDATE clases_teoricas_estudiantes
                SET asistencia = 0
                WHERE id = :cte
            ");
        $upd->bindValue(':cte', (int)$cteId, PDO::PARAM_INT);

        if ($upd->execute()) {
            $_SESSION['success_message'] =
                'Asistencia actualizada: el estudiante podrá volver a inscribirse en la clase.';
        } else {
            $_SESSION['error_message'] =
                'No fue posible actualizar la asistencia.';
        }

        header('Location: ' . $routes['clases_teoricas_no_asistidas']);
        exit;
    }
}
