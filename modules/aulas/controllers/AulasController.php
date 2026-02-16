<?php

require_once '../config/DatabaseConfig.php';

class AulasController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function index()
    {
        $currentUserId = $_SESSION['user_id'] ?? null;

        $stmt = $this->conn->query("
            SELECT id, nombre, descripcion, capacidad
            FROM aulas
            ORDER BY nombre ASC
        ");

        $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/aulas/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $currentUserId = $_SESSION['user_id'] ?? null;

        ob_start();
        include '../modules/aulas/views/create.php'; // o views/form.php si prefieres parcial
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $currentUserId = $_SESSION['user_id'] ?? null;

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /aulas/');
            exit;
        }

        $routes = include '../config/Routes.php';

        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $capRaw      = $_POST['capacidad'] ?? null;

        // Validaciones
        if ($nombre === '') {
            $_SESSION['error_message'] = 'El nombre del aula es obligatorio.';
            header('Location: ' . ($routes['aulas_create'] ?? '/aulas/create/'));
            exit;
        }
        // (opcional) acotar longitud a 100 como en el esquema
        if (mb_strlen($nombre) > 100) {
            $nombre = mb_substr($nombre, 0, 100);
        }

        // Capacidad: obligatoria y entero >= 1
        if ($capRaw === null || $capRaw === '' || !ctype_digit((string)$capRaw) || (int)$capRaw < 1) {
            $_SESSION['error_message'] = 'La capacidad es obligatoria y debe ser un entero mayor o igual a 1.';
            header('Location: ' . ($routes['aulas_create'] ?? '/aulas/create/'));
            exit;
        }
        $capacidad = (int)$capRaw;

        try {
            $stmt = $this->conn->prepare("
            INSERT INTO aulas (nombre, descripcion, capacidad)
            VALUES (:nombre, :descripcion, :capacidad)
        ");
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindValue(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success_message'] = 'Aula creada correctamente.';
        } catch (PDOException $e) {
            // Si quieres, loguea $e->getMessage()
            $_SESSION['error_message'] = 'No se pudo crear el aula. Intenta de nuevo.';
        }

        header('Location: ' . ($routes['aulas_index'] ?? '/aulas/'));
        exit;
    }

    public function edit($id)
    {

        $currentUserId = $_SESSION['user_id'] ?? null;

        $stmt = $this->conn->prepare("
            SELECT id, nombre, descripcion, capacidad
            FROM aulas
            WHERE id = :id
        ");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        $aula = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$aula) {
            $_SESSION['error_message'] = 'Aula no encontrada.';
            header('Location: ' . ($routes['aulas_index'] ?? '/aulas/'));
            exit;
        }

        ob_start();
        include '../modules/aulas/views/edit.php'; // o el mismo form parcial
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update($id)
    {
        $routes = include '../config/Routes.php';
        $currentUserId = $_SESSION['user_id'] ?? null;

        $id           = (int)$id;
        $nombre       = trim($_POST['nombre'] ?? '');
        $descripcion  = trim($_POST['descripcion'] ?? '');
        $capRaw       = $_POST['capacidad'] ?? null;

        // Validaciones
        if ($nombre === '') {
            $_SESSION['error_message'] = 'El nombre del aula es obligatorio.';
            header('Location: ' . ($routes['aulas_edit'] ?? '/aulas/edit/') . urlencode((string)$id));
            exit;
        }

        // Capacidad: obligatoria y entero >= 1
        if ($capRaw === null || $capRaw === '' || !ctype_digit((string)$capRaw) || (int)$capRaw < 1) {
            $_SESSION['error_message'] = 'La capacidad es obligatoria y debe ser un entero mayor o igual a 1.';
            header('Location: ' . ($routes['aulas_edit'] ?? '/aulas/edit/') . urlencode((string)$id));
            exit;
        }
        $capacidad = (int)$capRaw;

        try {
            $stmt = $this->conn->prepare("
                UPDATE aulas
                SET nombre = :nombre,
                    descripcion = :descripcion,
                    capacidad  = :capacidad
                WHERE id = :id
            ");
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindValue(':capacidad', $capacidad, PDO::PARAM_INT);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success_message'] = 'Aula actualizada correctamente.';
        } catch (PDOException $e) {
            // Log opcional: error_log($e->getMessage());
            $_SESSION['error_message'] = 'No se pudo actualizar el aula. Intenta de nuevo.';
        }

        header('Location: ' . ($routes['aulas_index'] ?? '/aulas/'));
        exit;
    }

    public function delete($id)
    {
        $currentUserId = $_SESSION['user_id'] ?? null;
        $routes = include '../config/Routes.php';
        $id = (int)$id;

        try {
            // 1) Verificar que el aula exista
            $stExist = $this->conn->prepare("SELECT id FROM aulas WHERE id = :id LIMIT 1");
            $stExist->bindValue(':id', $id, PDO::PARAM_INT);
            $stExist->execute();
            if (!$stExist->fetchColumn()) {
                $_SESSION['error_message'] = 'El aula no existe o ya fue eliminada.';
                header('Location: ' . ($routes['aulas_index'] ?? '/aulas/'));
                exit;
            }

            // 2) Bloqueo lógico: si está usada en clases teóricas, no permitir
            $stmtChk = $this->conn->prepare("SELECT COUNT(*) FROM clases_teoricas WHERE aula_id = :id");
            $stmtChk->bindValue(':id', $id, PDO::PARAM_INT);
            $stmtChk->execute();
            $usos = (int)$stmtChk->fetchColumn();

            if ($usos > 0) {
                $_SESSION['error_message'] = 'No se puede eliminar: el aula está asociada a una o más clases teóricas.';
                header('Location: ' . ($routes['aulas_index'] ?? '/aulas/'));
                exit;
            }

            // 3) Eliminar
            $stmt = $this->conn->prepare("DELETE FROM aulas WHERE id = :id");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();

            $_SESSION['success_message'] = 'Aula eliminada correctamente.';
        } catch (PDOException $e) {
            // Si existe FK con RESTRICT/NO ACTION, capturamos violaciones (23000)
            if ((int)$e->getCode() === 23000) {
                $_SESSION['error_message'] = 'No se puede eliminar: el aula está asociada a una o más clases teóricas.';
            } else {
                // error_log($e->getMessage());
                $_SESSION['error_message'] = 'Error al eliminar el aula.';
            }
        }

        header('Location: ' . ($routes['aulas_index'] ?? '/aulas/'));
        exit;
    }

    public function getAulas()
    {
        try {
            $query = "SELECT id, nombre 
                  FROM aulas 
                  ORDER BY nombre ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $aulas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($aulas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
