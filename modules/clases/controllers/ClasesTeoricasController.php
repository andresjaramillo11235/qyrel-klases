<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../vendor/autoload.php';
require_once '../shared/utils/LabelHelper.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class ClasesTeoricasController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }

    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'] ?? null;
        $empresa_id    = $_SESSION['empresa_id'] ?? null;

        if (!$currentUserId || !$empresa_id) {
            header('Location: /login/');
            exit;
        }

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 1. Leer filtros desde POST o SESSION
        // ----------------------------------------------------------
        $fecha_inicial = null;
        $fecha_final   = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // 🔹 Caso 1: viene del formulario
            $fecha_inicial = $_POST['fecha_inicial'] ?? null;
            $fecha_final   = $_POST['fecha_final'] ?? null;

            $_SESSION['filtros_clases_teoricas'] = [
                'fecha_inicial' => $fecha_inicial,
                'fecha_final'   => $fecha_final
            ];
        } elseif (isset($_SESSION['filtros_clases_teoricas'])) {

            // 🔹 Caso 2: redirect sin POST
            $fecha_inicial = $_SESSION['filtros_clases_teoricas']['fecha_inicial'];
            $fecha_final   = $_SESSION['filtros_clases_teoricas']['fecha_final'];
        } else {

            // 🔹 Caso 3: carga sin POST y sin sesión
            $fecha_inicial = date('Y-m-d');
            $fecha_final   = date('Y-m-d');
        }


        // Normalizar valores para la vista
        $fecha_inicial = $fecha_inicial ?: '';
        $fecha_final   = $fecha_final   ?: '';

        // ----------------------------------------------------------
        // 🔹 2. Construcción dinámica del WHERE
        // ----------------------------------------------------------
        $dateColumn = 'fecha';

        $whereParts = ["ct.empresa_id = :empresa_id"];
        $params = [
            ':empresa_id' => $empresa_id
        ];

        if ($fecha_inicial && $fecha_final) {
            $whereParts[] = "ct.$dateColumn BETWEEN :fi AND :ff";
            $params[':fi'] = $fecha_inicial . " 00:00:00";
            $params[':ff'] = $fecha_final   . " 23:59:59";
        } elseif ($fecha_inicial) {
            $whereParts[] = "ct.$dateColumn >= :fi";
            $params[':fi'] = $fecha_inicial . " 00:00:00";
        } elseif ($fecha_final) {
            $whereParts[] = "ct.$dateColumn <= :ff";
            $params[':ff'] = $fecha_final . " 23:59:59";
        }

        // ----------------------------------------------------------
        // 🔹 3. Query principal
        // ----------------------------------------------------------
        $query = "
            SELECT 
                ct.*,
                p.nombre AS programa_nombre,
                a.nombre AS aula_nombre,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre_completo,
                pet.nombre AS estado_nombre,
                ctt.nombre AS tema_nombre,
                ctt.descripcion AS tema_descripcion
            FROM clases_teoricas ct
            LEFT JOIN programas p 
                ON ct.programa_id = p.id
            LEFT JOIN aulas a 
                ON ct.aula_id = a.id
            LEFT JOIN instructores i 
                ON ct.instructor_id = i.id
            LEFT JOIN param_estados_clases pet 
                ON ct.estado_id = pet.id
            LEFT JOIN clases_teoricas_temas ctt 
                ON ct.tema_id = ctt.id
            WHERE " . implode(" AND ", $whereParts) . "
            ORDER BY ct.$dateColumn DESC
        ";

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            if ($key === ':empresa_id') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();
        $clasesTeoricas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ----------------------------------------------------------
        // 🔹 4. Render vista
        // ----------------------------------------------------------
        ob_start();
        include '../modules/clases/views/clases_teoricas/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }




    public function indexInstructores()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        // Obtener el ID del instructor vinculado al user actual
        $stmt = $this->conn->prepare("SELECT instructor_id FROM users WHERE id = :user_id LIMIT 1");
        $stmt->bindParam(':user_id', $currentUserId, PDO::PARAM_INT);
        $stmt->execute();
        $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$instructor) {
            $_SESSION['error_message'] = 'No se encontró un instructor asociado a este usuario.';
            header('Location: /dashboard');
            exit;
        }

        $instructor_id = $instructor['instructor_id'];

        $query = "
                SELECT 
                    ct.*,
                    p.nombre AS programa_nombre,
                    a.nombre AS aula_nombre,
                    CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre_completo,
                    pet.nombre AS estado_nombre,
                    ctt.nombre AS tema_nombre,
                    ctt.descripcion AS tema_descripcion
                FROM 
                    clases_teoricas ct
                LEFT JOIN 
                    programas p ON ct.programa_id = p.id
                LEFT JOIN 
                    aulas a ON ct.aula_id = a.id
                LEFT JOIN 
                    instructores i ON ct.instructor_id = i.id
                LEFT JOIN 
                    param_estados_clases pet ON ct.estado_id = pet.id
                LEFT JOIN 
                    clases_teoricas_temas ctt ON ct.tema_id = ctt.id
                WHERE 
                    ct.empresa_id = :empresa_id
                    AND ct.instructor_id = :instructor_id
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
        $stmt->execute();

        $clasesTeoricas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/clases/views/clases_teoricas/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create($fecha = null, $hora_inicio = null)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        $programas = $this->getProgramas($empresaId);
        $aulas = $this->getAulas();
        $instructores = $this->getInstructores();
        $estados = $this->getEstados(); // Obtener los estados para el formulario

        // Devolver datos en formato JSON si es una solicitud AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            echo json_encode([
                'programas' => $programas,
                'aulas' => $aulas,
                'instructores' => $instructores,
                'estados' => $estados,
                'fecha' => $fecha,
                'hora_inicio' => $hora_inicio
            ]);
            exit;
        }

        ob_start();
        include '../modules/clases/views/clases_teoricas/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    private function getEstados()
    {
        $query = "SELECT * FROM param_estados_clases";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getInstructores()
    {
        $empresa_id = $_SESSION['empresa_id'];

        $query = "SELECT id, nombres, apellidos 
                  FROM instructores
                  WHERE empresa_id = :empresa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $_SESSION['empresa_id'], PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function store()
    {
        $routes = include '../config/Routes.php';
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        // ----------------------------------------------------------
        // 🔹 Validar permisos
        // ----------------------------------------------------------
        if (!$permissionController->hasPermission($currentUserId, 'create_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 Datos del formulario
        // ----------------------------------------------------------
        $programa_id   = $_POST['programa_id'];
        $tema_id       = $_POST['tema_id'];
        $aula_id       = $_POST['aula_id'];
        $instructor_id = $_POST['instructor_id'];
        $fecha         = $_POST['fecha'];
        $hora_inicio   = $_POST['hora_inicio'];
        $hora_fin      = $_POST['hora_fin'];
        $estado_id     = 1;
        $observaciones = $_POST['observaciones'];

        // ----------------------------------------------------------
        // 🔹 1. Buscar el tema_global_id del tema seleccionado
        // ----------------------------------------------------------
        $qTema = $this->conn->prepare("
                SELECT tema_global_id 
                FROM clases_teoricas_temas 
                WHERE id = :tema_id
            ");
        $qTema->bindValue(':tema_id', $tema_id, PDO::PARAM_INT);
        $qTema->execute();
        $tema_global_id = $qTema->fetchColumn();

        // ----------------------------------------------------------
        // 🔹 1.5 Validar que no exista otra clase en la misma aula y horario
        // ----------------------------------------------------------
        // ----------------------------------------------------------
        // 🔹 Validar que no exista otra clase en la misma aula y horario
        // ----------------------------------------------------------
        $qCheck = $this->conn->prepare("
                SELECT COUNT(*) 
                FROM clases_teoricas
                WHERE aula_id = :aula_id
                AND fecha = :fecha
                AND (
                    (hora_inicio < :hora_fin)  -- la clase existente empieza antes de que termine la nueva
                    AND (hora_fin > :hora_inicio)  -- y termina después de que empieza la nueva
                )
            ");
        $qCheck->bindValue(':aula_id', $aula_id, PDO::PARAM_INT);
        $qCheck->bindValue(':fecha', $fecha);
        $qCheck->bindValue(':hora_inicio', $hora_inicio);
        $qCheck->bindValue(':hora_fin', $hora_fin);
        $qCheck->execute();

        if ($qCheck->fetchColumn() > 0) {
            $_SESSION['error_message'] = 'Ya existe una clase programada en esta aula y horario.';
            header("Location: /clasesteoricascreate/");
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 2. Insertar la clase teórica principal
        // ----------------------------------------------------------
        $query = "
                INSERT INTO clases_teoricas (
                    programa_id, 
                    tema_id,
                    aula_id,
                    instructor_id, 
                    fecha, 
                    hora_inicio, 
                    hora_fin, 
                    estado_id, 
                    observaciones,
                    empresa_id
                ) VALUES (
                    :programa_id, 
                    :tema_id,
                    :aula_id, 
                    :instructor_id, 
                    :fecha, 
                    :hora_inicio, 
                    :hora_fin, 
                    :estado_id, 
                    :observaciones,
                    :empresa_id
                )
            ";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id);
        $stmt->bindParam(':tema_id', $tema_id);
        $stmt->bindParam(':aula_id', $aula_id);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':estado_id', $estado_id);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':empresa_id', $empresa_id);

        if (!$stmt->execute()) {
            $_SESSION['error_message'] = 'Error al crear la clase teórica.';
            header("Location: /clasesteoricascreate/");
            exit;
        }

        $clase_teorica_id = $this->conn->lastInsertId();

        // ----------------------------------------------------------
        // 🔹 3. Si el tema tiene un tema_global_id, buscar programas equivalentes
        // ----------------------------------------------------------
        if (!empty($tema_global_id)) {
            $qProgramas = $this->conn->prepare("
                    SELECT DISTINCT clase_teorica_programa_id AS programa_id
                    FROM clases_teoricas_temas
                    WHERE tema_global_id = :global_id
                ");
            $qProgramas->bindValue(':global_id', $tema_global_id, PDO::PARAM_INT);
            $qProgramas->execute();
            $programas = $qProgramas->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $programas = [$programa_id];
        }

        // ----------------------------------------------------------
        // 🔹 4. Asociar la clase a todos los programas equivalentes
        // ----------------------------------------------------------
        $insert = $this->conn->prepare("
                INSERT INTO clases_teoricas_programas (clase_teorica_id, programa_id)
                VALUES (:clase_teorica_id, :programa_id)
            ");
        foreach ($programas as $pid) {
            $insert->bindValue(':clase_teorica_id', $clase_teorica_id, PDO::PARAM_INT);
            $insert->bindValue(':programa_id', $pid, PDO::PARAM_INT);
            $insert->execute();
        }

        // ----------------------------------------------------------
        // 🔹 5. Mensaje de confirmación
        // ----------------------------------------------------------
        $_SESSION['success_message'] = 'Clase teórica creada correctamente (sin conflictos de horario).';
        header("Location: /clasesteoricascreate/");
        exit;
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Clase actual
        $query = "SELECT * FROM clases_teoricas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $clase = $stmt->fetch(PDO::FETCH_ASSOC);

        // Catálogos
        $programas    = $this->getProgramas($empresaId);
        $aulas        = $this->getAulas();
        $instructores = $this->getInstructores();

        // ====== NUEVO: Temas del programa seleccionado ======
        $temas = [];
        if ($clase && !empty($clase['programa_id'])) {
            $temas = $this->getTemasPorPrograma((int)$clase['programa_id']);
        }

        ob_start();
        include '../modules/clases/views/clases_teoricas/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update($id)
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // ====== CAMBIOS AQUI: lectura de POST, tema y saneo ======
        $programa_id   = isset($_POST['programa_id'])   ? (int)$_POST['programa_id']   : null;
        $tema_id       = (isset($_POST['tema_id']) && $_POST['tema_id'] !== '') ? (int)$_POST['tema_id'] : null; // puede ser NULL
        $aula_id       = isset($_POST['aula_id'])       ? (int)$_POST['aula_id']       : null;
        $instructor_id = isset($_POST['instructor_id']) ? (int)$_POST['instructor_id'] : null;
        $fecha         = isset($_POST['fecha'])         ? $_POST['fecha']              : null; // YYYY-MM-DD
        $hora_inicio   = isset($_POST['hora_inicio'])   ? $_POST['hora_inicio']        : null; // HH:MM:SS
        $hora_fin      = isset($_POST['hora_fin'])      ? $_POST['hora_fin']           : null; // HH:MM:SS
        $estado        = isset($_POST['estado'])        ? (int)$_POST['estado']        : null;
        $observaciones = (isset($_POST['observaciones']) && $_POST['observaciones'] !== '') ? $_POST['observaciones'] : null;

        // ====== Validaciones básicas ======
        // 1) Horario consistente
        if ($hora_inicio && $hora_fin && strtotime($hora_fin) <= strtotime($hora_inicio)) {
            $_SESSION['error_message'] = 'La hora de fin debe ser mayor que la hora de inicio.';
            header("Location: " . $routes['clases_teoricas_edit'] . $id);
            exit;
        }

        try {
            // Opcional: transacción si luego agregas más operaciones
            $this->conn->beginTransaction();

            $sql = "UPDATE clases_teoricas 
                SET 
                    programa_id  = :programa_id,
                    tema_id      = :tema_id,
                    aula_id      = :aula_id,
                    instructor_id= :instructor_id,
                    fecha        = :fecha,
                    hora_inicio  = :hora_inicio,
                    hora_fin     = :hora_fin,
                    estado_id    = :estado,
                    observaciones= :observaciones
                WHERE id = :id AND empresa_id = :empresa_id";

            $stmt = $this->conn->prepare($sql);

            // Binds
            $stmt->bindValue(':programa_id',   $programa_id,   PDO::PARAM_INT);
            // tema_id puede ser NULL
            if ($tema_id === null) {
                $stmt->bindValue(':tema_id', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':tema_id', $tema_id, PDO::PARAM_INT);
            }
            $stmt->bindValue(':aula_id',       $aula_id,       PDO::PARAM_INT);
            $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
            $stmt->bindValue(':fecha',         $fecha,         PDO::PARAM_STR);
            $stmt->bindValue(':hora_inicio',   $hora_inicio,   PDO::PARAM_STR);
            $stmt->bindValue(':hora_fin',      $hora_fin,      PDO::PARAM_STR);
            $stmt->bindValue(':estado',        $estado,        PDO::PARAM_INT);
            // observaciones puede ser NULL
            if ($observaciones === null) {
                $stmt->bindValue(':observaciones', null, PDO::PARAM_NULL);
            } else {
                $stmt->bindValue(':observaciones', $observaciones, PDO::PARAM_STR);
            }
            $stmt->bindValue(':id',           $id,         PDO::PARAM_INT);
            $stmt->bindValue(':empresa_id',   $empresaId,  PDO::PARAM_INT);

            $stmt->execute();

            $this->conn->commit();

            // rowCount puede ser 0 si no hubo cambios, igual notificamos OK
            $_SESSION['success_message'] = 'Clase teórica modificada con éxito.';
            header("Location: /clases_teoricas/");
            exit;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }
            // Log interno si tienes logger
            // error_log('Update clase_teorica error: '.$e->getMessage());

            $_SESSION['error_message'] = 'Ocurrió un error actualizando la clase teórica.';
            header("Location: /clases_teoricas/");
            exit;
        }
    }



    public function delete($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        try {
            // ----------------------------------------------------------
            // 🔹 1) Verificar que la clase NO tenga estudiantes asignados
            // ----------------------------------------------------------
            $sqlCheck = "
                    SELECT COUNT(*) AS total
                    FROM clases_teoricas_estudiantes
                    WHERE clase_teorica_id = :id
                ";

            $st = $this->conn->prepare($sqlCheck);
            $st->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $st->execute();

            $total = (int)$st->fetchColumn();

            if ($total > 0) {
                $_SESSION['error_message'] = 'No se puede eliminar: hay estudiantes inscritos en esta clase.';
                header("Location: /clases_teoricas/");
                exit;
            }





            // ----------------------------------------------------------
            // 🔹 2) Eliminar la clase (sin validar empresa)
            // ----------------------------------------------------------
            $sqlDel = "DELETE FROM clases_teoricas WHERE id = :id";

            $stmt = $this->conn->prepare($sqlDel);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $_SESSION['success_message'] = 'Clase teórica eliminada correctamente.';
            } else {
                $_SESSION['error_message'] = 'No se pudo eliminar la clase (no encontrada).';
            }


            echo "aqui supuestamente se elimino ";
            exit;


            header("Location: /clases_teoricas/");
            exit;
        } catch (PDOException $e) {
            // FK u otros errores
            // error_log('Delete clase_teorica error: '.$e->getMessage());
            $_SESSION['error_message'] = 'Ocurrió un error al eliminar la clase.';
            header("Location: /clases_teoricas/");
            exit;
        }
    }

    // ----------------------------------------------------------
    public function deleteAjax()
    {
        try {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                echo json_encode(['status' => 'error', 'msg' => 'ID inválido']);
                exit;
            }

            // ----------------------------------------------------------
            // 🔹 1) Verificar que la clase NO tenga estudiantes asignados
            // ----------------------------------------------------------
            $sqlCheck = "
                    SELECT COUNT(*) FROM clases_teoricas_estudiantes
                    WHERE clase_teorica_id = :id
                ";

            $st = $this->conn->prepare($sqlCheck);
            $st->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $st->execute();

            if ((int)$st->fetchColumn() > 0) {
                echo json_encode([
                    'status' => 'error',
                    'msg' => 'No se puede eliminar: hay estudiantes inscritos en esta clase.'
                ]);
                exit;
            }

            // ----------------------------------------------------------
            // 🔹 2) Eliminar asociaciones en clases_teoricas_programas
            // ----------------------------------------------------------
            $sqlDelProg = "DELETE FROM clases_teoricas_programas WHERE clase_teorica_id = :id";
            $stmtProg = $this->conn->prepare($sqlDelProg);
            $stmtProg->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmtProg->execute();



            // ----------------------------------------------------------
            // 🔹 2) Eliminar la clase
            // ----------------------------------------------------------
            $sqlDel = "DELETE FROM clases_teoricas WHERE id = :id";
            $stmt = $this->conn->prepare($sqlDel);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                echo json_encode([
                    'status' => 'ok',
                    'msg' => 'Clase eliminada correctamente.'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'msg' => 'Clase no encontrada.'
                ]);
            }
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'msg' => 'EXCEPCIÓN: ' . $e->getMessage()
            ]);
            exit;
        }
    }






    public function detail($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT ct.*, p.nombre AS programa_nombre, a.nombre AS aula_nombre, i.nombres AS instructor_nombres
                  FROM clases_teoricas ct
                  LEFT JOIN programas p ON ct.programa_id = p.id
                  LEFT JOIN aulas a ON ct.aula_id = a.id
                  LEFT JOIN instructores i ON ct.instructor_id = i.id
                  WHERE ct.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $clase = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($clase) {
            ob_start();
            include '../modules/clases/views/clases_teoricas/detail.php';
            $content = ob_get_clean();
            echo $content;
        } else {
            echo "Clase no encontrada";
        }
    }

    private function isAvailable($fecha, $hora_inicio, $hora_fin, $aula_id, $instructor_id)
    {
        $query = "SELECT COUNT(*) FROM clases_teoricas
                  WHERE fecha = :fecha
                  AND ((hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
                  AND (aula_id = :aula_id OR instructor_id = :instructor_id))";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':aula_id', $aula_id);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->execute();

        return $stmt->fetchColumn() == 0;
    }

    public function checkAvailability()
    {
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $aula_id = $_POST['aula_id'];
        $instructor_id = $_POST['instructor_id'];

        $isAvailable = $this->isAvailable($fecha, $hora_inicio, $hora_fin, $aula_id, $instructor_id);

        echo json_encode(['isAvailable' => $isAvailable]);
    }

    private function getProgramas($empresaId)
    {
        $query = "SELECT * FROM programas WHERE empresa_id = :empresa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAulas()
    {
        $query = "SELECT * FROM aulas";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvents()
    {
        $query = "
            SELECT 
                ct.id, 
                ct.fecha AS start, 
                ct.hora_inicio, 
                ct.hora_fin, 
                CONCAT(p.nombre, '<br>Instructor: ', i.nombres, ' ', i.apellidos, '<br>Aula: ', a.nombre) AS title, 
                ct.fecha AS end 
            FROM 
                clases_teoricas ct
            JOIN 
                programas p ON ct.programa_id = p.id
            JOIN 
                instructores i ON ct.instructor_id = i.id
            JOIN 
                aulas a ON ct.aula_id = a.id
        ";

        $stmt = $this->conn->query($query);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Format the start and end times
        foreach ($events as &$event) {
            $event['start'] .= 'T' . $event['hora_inicio'];
            $event['end'] .= 'T' . $event['hora_fin'];
        }

        echo json_encode($events);
    }

    public function calendar()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        ob_start();
        include '../modules/clases/views/clases_teoricas/calendar.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function calendariodos($date = null)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Validar y sanitizar la fecha
        if ($date && !strtotime($date)) {
            header('Location: /clases_teoricas/calendariodos/');
            exit;
        }

        $date = $date ?: date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $endDate = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

        $query = "
            SELECT 
                ct.id AS clase_id,
                ct.fecha,
                ct.hora_inicio,
                ct.hora_fin,
                ct.estado_id,
                ct.observaciones,
                p.nombre AS programa_nombre,
                a.nombre AS aula_nombre,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre_completo,
                ctt.nombre AS tema_nombre
            FROM 
                clases_teoricas ct
            LEFT JOIN 
                clases_teoricas_temas ctt ON ct.tema_id = ctt.id
            LEFT JOIN 
                clases_teoricas_programas ctp ON ct.programa_id = ctp.id
            LEFT JOIN 
                programas p ON ctp.programa_id = p.id
            LEFT JOIN 
                aulas a ON ct.aula_id = a.id
            LEFT JOIN 
                instructores i ON ct.instructor_id = i.id
            WHERE 
                ct.fecha >= :start_date AND 
                ct.fecha <= :end_date AND
                ct.empresa_id = :empresa_id;
        ";


        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/clases/views/clases_teoricas/calendariodos.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function getTemasByPrograma($programa_id)
    {
        $query = "SELECT id, nombre FROM clases_teoricas_temas WHERE clase_teorica_programa_id = :programa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id);
        $stmt->execute();
        $temas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($temas);
    }

    public function getClasesByTema($tema_id)
    {
        $query = "SELECT id, nombre FROM clases_teoricas_clases WHERE clase_teorica_temas_id = :tema_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':tema_id', $tema_id);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($clases);
    }

    public function listadoEstudiantes($idClaseTeorica)
    {
        $currentUserId = $_SESSION['user_id'];

        $queryClase = "
                SELECT 
                    ct.id,
                    ct.fecha,
                    ct.hora_inicio,
                    ct.hora_fin,
                    a.nombre AS aula_nombre,
                    CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre_completo,
                    i.foto AS instructor_foto,
                    p.nombre AS programa_nombre,      
                    ctt.nombre AS tema_nombre,
                    ctt.descripcion AS tema_descripcion
                FROM 
                    clases_teoricas ct
                LEFT JOIN aulas a ON ct.aula_id = a.id
                LEFT JOIN instructores i ON ct.instructor_id = i.id
                LEFT JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
                LEFT JOIN programas p ON ctt.clase_teorica_programa_id = p.id   
                WHERE ct.id = :id
                LIMIT 1
            ";

        $stmtClase = $this->conn->prepare($queryClase);
        $stmtClase->bindParam(':id', $idClaseTeorica, PDO::PARAM_INT);
        $stmtClase->execute();
        $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

        if (!$clase) {
            $_SESSION['error_message'] = 'No se encontró la clase.';
            header('Location: /clases_teoricas');
            exit;
        }

        $query = "
                SELECT 
                    e.id AS estudiante_id,
                    e.foto,
                    CONCAT(e.nombres, ' ', e.apellidos) AS nombre_completo,
                    e.numero_documento,
                    e.celular,
                    p.nombre AS programa_nombre,                
                    cte.id AS clase_teorica_estudiante_id,
                    cte.asistencia
                FROM 
                    clases_teoricas_estudiantes cte
                INNER JOIN 
                    matriculas m ON cte.matricula_id = m.id
                INNER JOIN 
                    estudiantes e ON m.estudiante_id = e.id
                INNER JOIN 
                    matricula_programas mp ON mp.matricula_id = m.id
                INNER JOIN 
                    programas p ON mp.programa_id = p.id
                WHERE 
                    cte.clase_teorica_id = :clase_id
                ORDER BY e.apellidos, e.nombres
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clase_id', $idClaseTeorica, PDO::PARAM_INT);
        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cargar la vista con clase + estudiantes
        ob_start();
        include '../modules/clases/views/clases_teoricas/listado_estudiantes.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    // ----------------------------------------------------------
    // 🔹 AJAX: Listado de estudiantes para modal
    // ----------------------------------------------------------
    public function ajaxListadoEstudiantes($idClaseTeorica)
    {
        // Mismo código de listadoEstudiantes, pero sin layout
        $queryClase = "
                SELECT ct.id, ct.fecha, ct.hora_inicio, ct.hora_fin,
                    a.nombre AS aula_nombre,
                    CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre_completo,
                    i.foto AS instructor_foto,
                    p.nombre AS programa_nombre,
                    ctt.nombre AS tema_nombre,
                    ctt.descripcion AS tema_descripcion
                FROM clases_teoricas ct
                LEFT JOIN aulas a ON ct.aula_id = a.id
                LEFT JOIN instructores i ON ct.instructor_id = i.id
                LEFT JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
                LEFT JOIN programas p ON ctt.clase_teorica_programa_id = p.id
                WHERE ct.id = :id
                LIMIT 1
            ";

        $stmtClase = $this->conn->prepare($queryClase);
        $stmtClase->bindParam(':id', $idClaseTeorica, PDO::PARAM_INT);
        $stmtClase->execute();
        $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

        if (!$clase) {
            http_response_code(404);
            echo "No se encontró la clase.";
            exit;
        }

        // Estudiantes
        $query = "
                SELECT 
                    e.id AS estudiante_id,
                    e.foto,
                    CONCAT(e.nombres, ' ', e.apellidos) AS nombre_completo,
                    e.numero_documento,
                    e.celular,
                    p.nombre AS programa_nombre,
                    cte.id AS clase_teorica_estudiante_id,
                    cte.asistencia
                FROM clases_teoricas_estudiantes cte
                INNER JOIN matriculas m ON cte.matricula_id = m.id
                INNER JOIN estudiantes e ON m.estudiante_id = e.id
                INNER JOIN matricula_programas mp ON mp.matricula_id = m.id
                INNER JOIN programas p ON mp.programa_id = p.id
                WHERE cte.clase_teorica_id = :clase_id
                ORDER BY e.apellidos, e.nombres
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clase_id', $idClaseTeorica, PDO::PARAM_INT);
        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Renderizar SOLO el contenido del modal
        include '../modules/clases/views/clases_teoricas/listado_estudiantes_modal.php';
    }




    public function guardarAsistencia()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . ($routes['clases_teoricas_index'] ?? '/clases_teoricas/'));
            exit;
        }

        $claseId = (int)($_POST['clase_id'] ?? 0);
        $idsMarcados = array_keys($_POST['asistencia'] ?? []);

        if ($claseId <= 0) {
            $_SESSION['error_message'] = 'Clase inválida.';
            header('Location: /clases_teoricas/');
            exit;
        }

        try {
            $this->conn->beginTransaction();

            // ==========================================================
            // 1. Marcar asistencia SOLO en la clase actual
            // ==========================================================

            // 1.1 Marcar ASISTIÓ (1)
            if (!empty($idsMarcados)) {

                $placeholders = implode(',', array_fill(0, count($idsMarcados), '?'));

                $sqlAsistio = "
                UPDATE clases_teoricas_estudiantes
                SET asistencia = 1,
                    fecha_asistencia = CURRENT_DATE
                WHERE clase_teorica_id = ?
                  AND id IN ($placeholders)
            ";

                $params = array_merge([$claseId], $idsMarcados);
                $stmtAsistio = $this->conn->prepare($sqlAsistio);
                $stmtAsistio->execute($params);
            }

            // 1.2 Marcar NO ASISTIÓ (2) SOLO en esta clase
            if (!empty($idsMarcados)) {

                $placeholders = implode(',', array_fill(0, count($idsMarcados), '?'));

                $sqlNoAsistio = "
                UPDATE clases_teoricas_estudiantes
                SET asistencia = 2,
                    fecha_asistencia = CURRENT_DATE
                WHERE clase_teorica_id = ?
                  AND id NOT IN ($placeholders)
            ";

                $params = array_merge([$claseId], $idsMarcados);
                $stmtNoAsistio = $this->conn->prepare($sqlNoAsistio);
                $stmtNoAsistio->execute($params);
            } else {

                // Nadie marcado → todos NO asistieron (solo esta clase)
                $sqlNoAsistio = "
                UPDATE clases_teoricas_estudiantes
                SET asistencia = 2,
                    fecha_asistencia = CURRENT_DATE
                WHERE clase_teorica_id = ?
            ";

                $stmtNoAsistio = $this->conn->prepare($sqlNoAsistio);
                $stmtNoAsistio->execute([$claseId]);
            }

            // ==========================================================
            // 2. Propagar ASISTENCIA (1) de forma transversal
            //    🔑 SOLO cuando se marca asistencia
            // ==========================================================

            foreach ($idsMarcados as $claseEstudianteId) {

                // ------------------------------------------------------
                // Obtener contexto desde la matrícula marcada
                // ------------------------------------------------------
                $qContexto = $this->conn->prepare("
                SELECT
                    m.estudiante_id,
                    ct.aula_id,
                    ct.instructor_id,
                    ct.fecha,
                    ct.hora_inicio,
                    ct.hora_fin
                FROM clases_teoricas_estudiantes cte
                INNER JOIN matriculas m ON m.id = cte.matricula_id
                INNER JOIN clases_teoricas ct ON ct.id = cte.clase_teorica_id
                WHERE cte.id = ?
            ");
                $qContexto->execute([$claseEstudianteId]);
                $contexto = $qContexto->fetch(PDO::FETCH_ASSOC);

                if (!$contexto) {
                    continue;
                }

                // ------------------------------------------------------
                // Buscar clases equivalentes (evento físico)
                // ------------------------------------------------------
                $qClasesEq = $this->conn->prepare("
                SELECT id
                FROM clases_teoricas
                WHERE aula_id = ?
                  AND instructor_id = ?
                  AND fecha = ?
                  AND hora_inicio = ?
                  AND hora_fin = ?
            ");

                $qClasesEq->execute([
                    $contexto['aula_id'],
                    $contexto['instructor_id'],
                    $contexto['fecha'],
                    $contexto['hora_inicio'],
                    $contexto['hora_fin'],
                ]);

                $clasesEquivalentes = $qClasesEq->fetchAll(PDO::FETCH_COLUMN);

                if (empty($clasesEquivalentes)) {
                    continue;
                }

                // ------------------------------------------------------
                // Buscar otras matrículas del mismo estudiante
                // ------------------------------------------------------
                $placeholders = implode(',', array_fill(0, count($clasesEquivalentes), '?'));

                $params = array_merge(
                    [$contexto['estudiante_id']],
                    $clasesEquivalentes
                );

                $qOtrasMatriculas = $this->conn->prepare("
                SELECT cte.id
                FROM clases_teoricas_estudiantes cte
                INNER JOIN matriculas m ON m.id = cte.matricula_id
                WHERE m.estudiante_id = ?
                  AND cte.clase_teorica_id IN ($placeholders)
            ");

                $qOtrasMatriculas->execute($params);
                $idsRelacionados = $qOtrasMatriculas->fetchAll(PDO::FETCH_COLUMN);

                if (empty($idsRelacionados)) {
                    continue;
                }

                // ------------------------------------------------------
                // Propagar ASISTIÓ (1)
                // ------------------------------------------------------
                $placeholders = implode(',', array_fill(0, count($idsRelacionados), '?'));

                $qPropagar = $this->conn->prepare("
                UPDATE clases_teoricas_estudiantes
                SET asistencia = 1,
                    fecha_asistencia = CURRENT_DATE
                WHERE id IN ($placeholders)
            ");

                $qPropagar->execute($idsRelacionados);
            }

            $this->conn->commit();
            $_SESSION['success_message'] = 'Asistencia actualizada correctamente.';
        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            $_SESSION['error_message'] = 'Error al guardar asistencia.';
        }

        header('Location: /clases_teoricas/');
        exit;
    }








    public function formularioInforme()
    {
        ob_start();
        include '../modules/clases/views/clases_teoricas/formulario_informe.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function exportarInformeClasesTeoricas()
    {
        $fechaInicio = $_POST['fecha_inicio'];
        $fechaFin = $_POST['fecha_fin'];

        $query = "
            SELECT 
                e.nombres,
                e.apellidos,
                td.nombre AS tipo_documento,
                e.numero_documento,
                p.nombre AS programa_nombre,
                ctt.nombre AS tema_nombre,
                ct.fecha,
                ct.hora_inicio,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
                cte.asistencia
            FROM clases_teoricas_estudiantes cte
            INNER JOIN matriculas m ON cte.matricula_id = m.id
            INNER JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
            INNER JOIN clases_teoricas ct ON cte.clase_teorica_id = ct.id
            INNER JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
            INNER JOIN programas p ON ctt.clase_teorica_programa_id = p.id
            INNER JOIN instructores i ON ct.instructor_id = i.id
            WHERE ct.fecha BETWEEN :inicio AND :fin
            ORDER BY ct.fecha, ct.hora_inicio
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':inicio', $fechaInicio);
        $stmt->bindParam(':fin', $fechaFin);
        $stmt->execute();
        $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Crear el Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // 1. Encabezado del informe
        $sheet->setCellValue('A1', "Informe de clases teóricas del {$fechaInicio} al {$fechaFin}");
        $sheet->mergeCells('A1:J1'); // Unir columnas para centrar el título
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal('center');

        // 2. Títulos de columnas
        $sheet->fromArray([
            'Nombre',
            'Apellido',
            'Tipo Documento',
            'Número Documento',
            'Programa',
            'Tema',
            'Fecha Clase',
            'Hora Inicio',
            'Instructor',
            'Asistencia'
        ], null, 'A2');

        // 3. Datos desde la fila 3
        $fila = 3;
        foreach ($datos as $row) {
            $sheet->setCellValue("A$fila", $row['nombres']);
            $sheet->setCellValue("B$fila", $row['apellidos']);
            $sheet->setCellValue("C$fila", $row['tipo_documento']);
            $sheet->setCellValue("D$fila", $row['numero_documento']);
            $sheet->setCellValue("E$fila", $row['programa_nombre']);
            $sheet->setCellValue("F$fila", $row['tema_nombre']);
            $sheet->setCellValue("G$fila", $row['fecha']);
            $sheet->setCellValue("H$fila", substr($row['hora_inicio'], 0, 5));
            $sheet->setCellValue("I$fila", $row['instructor_nombre']);
            $sheet->setCellValue("J$fila", $row['asistencia'] == 1 ? 'Sí' : 'No');
            $fila++;
        }

        // Descargar el archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="informe_clases_teoricas.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportarInformePorClase($claseId)
    {
        // 1. Consultar encabezado de la clase
        $stmt = $this->conn->prepare("
            SELECT 
                ct.fecha,
                ct.hora_inicio,
                p.nombre AS programa,
                ctt.nombre AS tema,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor
            FROM clases_teoricas ct
            LEFT JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
            LEFT JOIN programas p ON ctt.clase_teorica_programa_id = p.id
            LEFT JOIN instructores i ON ct.instructor_id = i.id
            WHERE ct.id = :id
        ");
        $stmt->bindParam(':id', $claseId, PDO::PARAM_INT);
        $stmt->execute();
        $clase = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$clase) {
            $_SESSION['error_message'] = 'Clase no encontrada.';
            header('Location: /clases_teoricas');
            exit;
        }

        // 2. Consultar estudiantes
        $stmt = $this->conn->prepare("
            SELECT 
                e.nombres,
                e.apellidos,
                td.nombre AS tipo_documento,
                e.numero_documento,
                m.id AS matricula,
                ct.fecha,
                ct.hora_inicio,
                cte.asistencia
            FROM clases_teoricas_estudiantes cte
            INNER JOIN matriculas m ON cte.matricula_id = m.id
            INNER JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
            INNER JOIN clases_teoricas ct ON cte.clase_teorica_id = ct.id
            WHERE cte.clase_teorica_id = :id
        ");
        $stmt->bindParam(':id', $claseId, PDO::PARAM_INT);
        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // 3. Crear Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezado de clase
        $sheet->setCellValue('A1', 'Programa: ' . $clase['programa']);
        $sheet->setCellValue('A2', 'Tema: ' . $clase['tema']);
        $sheet->setCellValue('A3', 'Instructor: ' . $clase['instructor']);
        $sheet->setCellValue('A4', 'Fecha: ' . $clase['fecha']);
        $sheet->setCellValue('A5', 'Hora Inicio: ' . substr($clase['hora_inicio'], 0, 5));

        // Títulos desde fila 7
        $sheet->fromArray([
            'Nombre',
            'Apellido',
            'Tipo Documento',
            'Número Documento',
            'Matrícula',
            'Fecha',
            'Hora Inicio',
            'Asistencia'
        ], null, 'A7');

        // Datos desde fila 8
        $fila = 8;
        foreach ($estudiantes as $row) {
            $sheet->setCellValue("A$fila", $row['nombres']);
            $sheet->setCellValue("B$fila", $row['apellidos']);
            $sheet->setCellValue("C$fila", $row['tipo_documento']);
            $sheet->setCellValue("D$fila", $row['numero_documento']);
            $sheet->setCellValue("E$fila", $row['matricula']);
            $sheet->setCellValue("F$fila", $row['fecha']);
            $sheet->setCellValue("G$fila", substr($row['hora_inicio'], 0, 5));
            $sheet->setCellValue("H$fila", $row['asistencia'] == 1 ? 'Sí' : 'No');
            $fila++;
        }

        // Descargar archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="informe_clase_' . $claseId . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // Trae los temas por programa
    private function getTemasPorPrograma($programaId)
    {
        $sql = "SELECT id, nombre FROM clases_teoricas_temas 
            WHERE clase_teorica_programa_id = :pid
            ORDER BY nombre ASC";
        $st = $this->conn->prepare($sql);
        $st->bindValue(':pid', $programaId, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function unsubscribeEstudianteAdmin()
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'] ?? null;
        $empresaId     = $_SESSION['empresa_id'] ?? null;

        if (!$currentUserId || !$empresaId) {
            $_SESSION['error_message'] = 'Sesión inválida.';
            header('Location: /clases_teoricas/');
            exit;
        }

        // Usa el permiso que manejes para administración de clases
        // if (!$permissionController->hasPermission($currentUserId, 'edit_clases_teoricas')) {
        //     header('Location: /permission-denied/');
        //     exit;
        // }

        $cteId   = isset($_POST['cte_id'])   ? (int)$_POST['cte_id']   : 0;
        $claseId = isset($_POST['clase_id']) ? (int)$_POST['clase_id'] : 0;

        if ($cteId <= 0 || $claseId <= 0) {
            $_SESSION['error_message'] = 'Solicitud inválida.';
            header('Location: /clases_teoricas/');
            exit;
        }

        try {
            // Verificar que la inscripción exista y pertenezca a la empresa y clase indicadas
            $sqlCheck = "
            SELECT cte.id, cte.asistencia
            FROM clases_teoricas_estudiantes cte
            INNER JOIN clases_teoricas ct ON ct.id = cte.clase_teorica_id
            WHERE cte.id = :cte_id
              AND ct.id = :clase_id
              AND ct.empresa_id = :empresa_id
            LIMIT 1";
            $st = $this->conn->prepare($sqlCheck);
            $st->bindValue(':cte_id', $cteId, PDO::PARAM_INT);
            $st->bindValue(':clase_id', $claseId, PDO::PARAM_INT);
            $st->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
            $st->execute();
            $row = $st->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $_SESSION['error_message'] = 'No se encontró la inscripción o no pertenece a su empresa.';
                header('Location: ' . $this->routeListadoEstudiantes($claseId));
                exit;
            }

            // (Opcional) No permitir si ya tiene asistencia registrada
            if (!empty($row['asistencia'])) {
                $_SESSION['error_message'] = 'No se puede desagendar: el alumno ya tiene asistencia registrada.';
                header('Location: ' . $this->routeListadoEstudiantes($claseId));
                exit;
            }

            $this->conn->beginTransaction();

            $del = $this->conn->prepare("DELETE FROM clases_teoricas_estudiantes WHERE id = :cte_id");
            $del->bindValue(':cte_id', $cteId, PDO::PARAM_INT);
            $del->execute();

            $this->conn->commit();

            $_SESSION['success'] = 'Alumno desagendado correctamente.';
            header('Location: ' . $routes['clases_teoricas_listado_estudiantes'] . $claseId);

            exit;
        } catch (Exception $e) {
            if ($this->conn->inTransaction()) $this->conn->rollBack();
            // error_log('unsubscribeEstudianteAdmin error: ' . $e->getMessage());
            $_SESSION['error_message'] = 'Ocurrió un error al desagendar al alumno.';
            header('Location: ' . $routes['clases_teoricas_listado_estudiantes'] . $claseId);
            exit;
        }
    }

    public function cargaMasivaForm()
    {
        ob_start();
        include '../modules/clases/views/clases_teoricas/carga_masiva_index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // public function cargaMasivaProcess()
    // {
    //     $routes = include '../config/Routes.php';

    //     if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {

    //         $file = $_FILES['archivo'];

    //         // Validar extensión
    //         $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    //         if ($ext !== 'xlsx') {
    //             $_SESSION['error_message'] = "El archivo debe estar en formato .xlsx";
    //             header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
    //             exit;
    //         }

    //         // Ruta destino (guardar en files/uploads/)
    //         $uploadDir = '../files/archivos_cargue_masivo/';


    //         if (!is_dir($uploadDir)) {
    //             mkdir($uploadDir, 0777, true);
    //         }

    //         $filePath = $uploadDir . uniqid('clases_') . '.xlsx';
    //         if (!move_uploaded_file($file['tmp_name'], $filePath)) {
    //             $_SESSION['error_message'] = "Error al subir el archivo.";
    //             header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
    //             exit;
    //         }

    //         try {
    //             // =====================
    //             // Leer archivo Excel con PhpSpreadsheet
    //             // =====================
    //             $spreadsheet = IOFactory::load($filePath);
    //             $worksheet = $spreadsheet->getActiveSheet();

    //             // ✅ Validar encabezados
    //             $encabezadosEsperados = [
    //                 "programa_id",
    //                 "tema_id",
    //                 "clase_id",
    //                 "aula_id",
    //                 "instructor_id",
    //                 "fecha (YYYY-MM-DD)",
    //                 "hora_inicio (HH:MM:SS)",
    //                 "hora_fin (HH:MM:SS)",
    //                 "estado_id",
    //                 "observaciones",
    //                 "empresa_id"
    //             ];

    //             $primerFila = $worksheet->rangeToArray('A1:K1')[0];

    //             if ($primerFila !== $encabezadosEsperados) {
    //                 $_SESSION['error_message'] = "❌ El archivo no corresponde a la plantilla oficial";
    //                 header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
    //                 exit;
    //             }

    //             $rows = $worksheet->toArray();

    //             // Ignorar encabezado (primera fila)
    //             $errores = [];
    //             $insertados = 0;

    //             // Preparar el INSERT una sola vez
    //             $sql = "INSERT INTO clases_teoricas 
    //                         (programa_id, tema_id, clase_id, aula_id, instructor_id, fecha, hora_inicio, hora_fin, estado_id, observaciones, empresa_id) 
    //                     VALUES 
    //                         (:programa_id, :tema_id, :clase_id, :aula_id, :instructor_id, :fecha, :hora_inicio, :hora_fin, :estado_id, :observaciones, :empresa_id)";
    //             $stmt = $this->conn->prepare($sql);

    //             for ($i = 1; $i < count($rows); $i++) {
    //                 $fila = $rows[$i];

    //                 $programa_id   = $fila[0];
    //                 $tema_id       = $fila[1];
    //                 $clase_id      = $fila[2];
    //                 $aula_id       = $fila[3];
    //                 $instructor_id = $fila[4];
    //                 $fecha         = $fila[5];
    //                 $hora_inicio   = $fila[6];
    //                 $hora_fin      = $fila[7];
    //                 $estado_id     = $fila[8];
    //                 $observaciones = $fila[9];
    //                 $empresa_id    = $fila[10];

    //                 // Validaciones básicas
    //                 if (empty($programa_id) || empty($instructor_id) || empty($fecha) || empty($hora_inicio) || empty($hora_fin)) {
    //                     $errores[] = "Fila " . ($i + 1) . ": faltan datos obligatorios.";
    //                     continue;
    //                 }

    //                 if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
    //                     $errores[] = "Fila " . ($i + 1) . ": hora inicio debe ser menor que hora fin.";
    //                     continue;
    //                 }

    //                 // Normalizar fecha (Excel puede traer timestamp numérico)
    //                 if (is_numeric($fecha)) {
    //                     $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha)->format('Y-m-d');
    //                 } else {
    //                     $fecha = date('Y-m-d', strtotime($fecha));
    //                 }

    //                 // Normalizar hora inicio
    //                 if (is_numeric($hora_inicio)) {
    //                     $hora_inicio = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($hora_inicio)->format('H:i:s');
    //                 } else {
    //                     $hora_inicio = date('H:i:s', strtotime($hora_inicio));
    //                 }

    //                 // Normalizar hora fin
    //                 if (is_numeric($hora_fin)) {
    //                     $hora_fin = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($hora_fin)->format('H:i:s');
    //                 } else {
    //                     $hora_fin = date('H:i:s', strtotime($hora_fin));
    //                 }

    //                 try {
    //                     $stmt->bindValue(':programa_id', $programa_id, PDO::PARAM_INT);
    //                     $stmt->bindValue(':tema_id', $tema_id ?: null, PDO::PARAM_INT);
    //                     $stmt->bindValue(':clase_id', $clase_id ?: null, PDO::PARAM_INT);
    //                     $stmt->bindValue(':aula_id', $aula_id, PDO::PARAM_INT);
    //                     $stmt->bindValue(':instructor_id', $instructor_id, PDO::PARAM_INT);
    //                     $stmt->bindValue(':fecha', $fecha);
    //                     $stmt->bindValue(':hora_inicio', $hora_inicio);
    //                     $stmt->bindValue(':hora_fin', $hora_fin);
    //                     $stmt->bindValue(':estado_id', $estado_id, PDO::PARAM_INT);
    //                     $stmt->bindValue(':observaciones', $observaciones);
    //                     $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);

    //                     $stmt->execute();
    //                     $insertados++;
    //                 } catch (Exception $e) {
    //                     $errores[] = "Fila " . ($i + 1) . ": error al insertar → " . $e->getMessage();
    //                 }
    //             }

    //             $_SESSION['success_message'] = "✅ El archivo fue validado correctamente y el cargue se realizó con éxito.";
    //             header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
    //             exit;
    //         } catch (Exception $e) {
    //             $_SESSION['error_message'] = "Error leyendo el archivo: " . $e->getMessage();
    //         }

    //         header("Location: /cargar_clases_teoricas.php");
    //         exit;
    //     }
    // }



    public function cargaMasivaProcess()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['archivo'])) {

            $file = $_FILES['archivo'];

            // ----------------------------------------------------------
            // 🔹 1. Validar extensión del archivo
            // ----------------------------------------------------------
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            if ($ext !== 'xlsx') {
                $_SESSION['error_message'] = "El archivo debe estar en formato .xlsx";
                header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
                exit;
            }

            // ----------------------------------------------------------
            // 🔹 2. Guardar archivo en carpeta temporal
            // ----------------------------------------------------------
            $uploadDir = '../files/archivos_cargue_masivo/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $filePath = $uploadDir . uniqid('clases_') . '.xlsx';
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                $_SESSION['error_message'] = "Error al subir el archivo.";
                header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
                exit;
            }

            try {
                // ----------------------------------------------------------
                // 🔹 3. Leer archivo Excel con PhpSpreadsheet
                // ----------------------------------------------------------
                $spreadsheet = IOFactory::load($filePath);
                $worksheet = $spreadsheet->getActiveSheet();

                $encabezadosEsperados = [
                    "programa_id",
                    "tema_id",
                    "aula_id",
                    "instructor_id",
                    "fecha (YYYY-MM-DD)",
                    "hora_inicio (HH:MM:SS)",
                    "hora_fin (HH:MM:SS)",
                    "estado_id",
                    "observaciones",
                    "empresa_id"
                ];

                $primerFila = $worksheet->rangeToArray('A1:J1')[0];
                if ($primerFila !== $encabezadosEsperados) {
                    $_SESSION['error_message'] = "❌ El archivo no corresponde a la plantilla oficial";
                    header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
                    exit;
                }

                $rows = $worksheet->toArray();
                $errores = [];
                $insertados = 0;

                // ----------------------------------------------------------
                // 🔹 4. Preparar queries
                // ----------------------------------------------------------
                $qInsertClase = $this->conn->prepare("INSERT INTO clases_teoricas 
                        (programa_id, tema_id, aula_id, instructor_id, fecha, hora_inicio, hora_fin, estado_id, observaciones, empresa_id)
                    VALUES
                        (:programa_id, :tema_id, :aula_id, :instructor_id, :fecha, :hora_inicio, :hora_fin, :estado_id, :observaciones, :empresa_id)
                ");

                $qTemaGlobal = $this->conn->prepare("SELECT tema_global_id FROM clases_teoricas_temas WHERE id = :tema_id");

                $qProgramasEquivalentes = $this->conn->prepare("SELECT DISTINCT clase_teorica_programa_id AS programa_id
                    FROM clases_teoricas_temas
                    WHERE tema_global_id = :global_id
                ");

                $qInsertRelacion = $this->conn->prepare("INSERT INTO clases_teoricas_programas (clase_teorica_id, programa_id)
                    VALUES (:clase_teorica_id, :programa_id)
                ");

                // ----------------------------------------------------------
                // 🔹 5. Procesar cada fila
                // ----------------------------------------------------------
                for ($i = 1; $i < count($rows); $i++) {
                    $fila = $rows[$i];
                    if (count($fila) < 10) continue;

                    list(
                        $programa_id,
                        $tema_id,
                        $aula_id,
                        $instructor_id,
                        $fecha,
                        $hora_inicio,
                        $hora_fin,
                        $estado_id,
                        $observaciones,
                        $empresa_id
                    ) = $fila;

                    // 🔸 Validaciones básicas
                    if (empty($programa_id) || empty($instructor_id) || empty($fecha) || empty($hora_inicio) || empty($hora_fin)) {
                        $errores[] = "Fila " . ($i + 1) . ": faltan datos obligatorios.";
                        continue;
                    }

                    if (strtotime($hora_inicio) >= strtotime($hora_fin)) {
                        $errores[] = "Fila " . ($i + 1) . ": hora inicio debe ser menor que hora fin.";
                        continue;
                    }

                    // 🔸 Normalizar fecha y hora
                    if (is_numeric($fecha)) {
                        $fecha = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($fecha)->format('Y-m-d');
                    } else {
                        $fecha = date('Y-m-d', strtotime($fecha));
                    }

                    if (is_numeric($hora_inicio)) {
                        $hora_inicio = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($hora_inicio)->format('H:i:s');
                    } else {
                        $hora_inicio = date('H:i:s', strtotime($hora_inicio));
                    }

                    if (is_numeric($hora_fin)) {
                        $hora_fin = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($hora_fin)->format('H:i:s');
                    } else {
                        $hora_fin = date('H:i:s', strtotime($hora_fin));
                    }

                    // ----------------------------------------------------------
                    // 🔹 6. Insertar la clase principal
                    // ----------------------------------------------------------
                    try {
                        $qInsertClase->execute([
                            ':programa_id' => $programa_id,
                            ':tema_id' => $tema_id,
                            ':aula_id' => $aula_id,
                            ':instructor_id' => $instructor_id,
                            ':fecha' => $fecha,
                            ':hora_inicio' => $hora_inicio,
                            ':hora_fin' => $hora_fin,
                            ':estado_id' => $estado_id,
                            ':observaciones' => $observaciones,
                            ':empresa_id' => $empresa_id
                        ]);

                        $clase_teorica_id = $this->conn->lastInsertId();

                        // ----------------------------------------------------------
                        // 🔹 7. Asociar programas equivalentes según tema global
                        // ----------------------------------------------------------
                        $qTemaGlobal->execute([':tema_id' => $tema_id]);
                        $tema_global_id = $qTemaGlobal->fetchColumn();

                        if ($tema_global_id) {
                            $qProgramasEquivalentes->execute([':global_id' => $tema_global_id]);
                            $programas = $qProgramasEquivalentes->fetchAll(PDO::FETCH_COLUMN);
                        } else {
                            $programas = [$programa_id];
                        }

                        foreach ($programas as $pid) {
                            $qInsertRelacion->execute([
                                ':clase_teorica_id' => $clase_teorica_id,
                                ':programa_id' => $pid
                            ]);
                        }

                        $insertados++;
                    } catch (Exception $e) {
                        $errores[] = "Fila " . ($i + 1) . ": error → " . $e->getMessage();
                    }
                }

                // ----------------------------------------------------------
                // 🔹 8. Resultado final
                // ----------------------------------------------------------
                if ($insertados > 0) {
                    $_SESSION['success_message'] = "✅ Se insertaron {$insertados} clases teóricas correctamente.";
                } else {
                    $_SESSION['error_message'] = "⚠️ No se insertó ninguna clase. Revise el archivo.";
                }

                if (!empty($errores)) {
                    $_SESSION['error_details'] = implode('<br>', $errores);
                }

                header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
                exit;
            } catch (Exception $e) {
                $_SESSION['error_message'] = "Error procesando el archivo: " . $e->getMessage();
                header('Location: ' . $routes['clases_teoricas_carga_masiva_form']);
                exit;
            }
        }
    }

    public function getAsociables()
    {
        $empresa_id = $_SESSION['empresa_id'];
        $fecha = $_POST['fecha'] ?? null;

        if (!$fecha) {
            echo json_encode([]);
            return;
        }

        $query = "
                SELECT 
                    ct.id,
                    ct.clase_id,
                    p.nombre AS programa,
                    ctt.nombre AS tema,
                    ct.hora_inicio,
                    ct.hora_fin
                FROM clases_teoricas ct
                INNER JOIN programas p ON ct.programa_id = p.id
                INNER JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
                WHERE ct.clase_id IS NOT NULL
                AND ct.fecha = :fecha
                AND ct.empresa_id = :empresa_id
                ORDER BY ct.hora_inicio
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':empresa_id', $empresa_id);
        $stmt->execute();

        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($data);
    }

    public function mostrarFormAlmacenamientoMultiple()
    {
        ob_start();
        include '../modules/clases/views/clases_teoricas/multiple_form.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function storeMultiple()
    {
        try {
            $empresaId = $_SESSION['empresa_id'] ?? null;

            if (!$empresaId) {
                echo json_encode(['status' => 'error', 'message' => 'No se encontró empresa_id en la sesión.']);
                return;
            }

            $data = json_decode(file_get_contents("php://input"), true);

            if (empty($data['fecha']) || empty($data['clases'])) {
                echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
                return;
            }

            $fecha = $data['fecha'];
            $clases = $data['clases'];

            if (!is_array($clases) || count($clases) === 0) {
                echo json_encode(['status' => 'error', 'message' => 'No hay clases para guardar']);
                return;
            }

            $insertCount = 0;


            foreach ($clases as $c) {
                // 🔹 Validación mínima: asegurar que no vengan campos vacíos
                if (
                    empty($c['hora_inicio']) ||
                    empty($c['hora_fin']) ||
                    empty($c['programa_id']) ||
                    empty($c['tema_id']) ||
                    empty($c['instructor_id']) ||
                    empty($c['aula_id'])
                ) {
                    continue; // salta clase incompleta
                }

                // 🔹 Insertar directamente sin validaciones de cruce
                $stmt = $this->conn->prepare("
                        INSERT INTO clases_teoricas 
                            (programa_id, tema_id, aula_id, instructor_id, fecha, hora_inicio, hora_fin, estado_id, empresa_id)
                        VALUES 
                            (:programa_id, :tema_id, :aula_id, :instructor_id, :fecha, :hora_inicio, :hora_fin, 1, :empresa_id)
                    ");

                $stmt->bindParam(':programa_id', $c['programa_id']);
                $stmt->bindParam(':tema_id', $c['tema_id']);
                $stmt->bindParam(':aula_id', $c['aula_id']);
                $stmt->bindParam(':instructor_id', $c['instructor_id']);
                $stmt->bindParam(':fecha', $fecha);
                $stmt->bindParam(':hora_inicio', $c['hora_inicio']);
                $stmt->bindParam(':hora_fin', $c['hora_fin']);
                $stmt->bindParam(':empresa_id', $empresaId);

                if ($stmt->execute()) {
                    $insertCount++;
                }
            }

            echo json_encode([
                'status' => 'success',
                'message' => "Se guardaron {$insertCount} clases correctamente."
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
