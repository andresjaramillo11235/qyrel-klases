<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class ClasesTeoricasTemasController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    /** ✅ Método para listar los temas teóricos de un programa **/
    public function index($programaId)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        // 🔒 Verificar permisos
        if (!$permissionController->hasPermission($currentUserId, 'view_temas_teoria')) {
            header('Location: /permission-denied/');
            return;
        }

        // 📌 1️⃣ Obtener el nombre del programa, incluso si no tiene temas
        $queryPrograma = "SELECT nombre FROM programas WHERE id = :programa_id AND empresa_id = :empresa_id";
        $stmtPrograma = $this->conn->prepare($queryPrograma);
        $stmtPrograma->bindParam(':programa_id', $programaId, PDO::PARAM_INT);
        $stmtPrograma->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmtPrograma->execute();
        $programa = $stmtPrograma->fetch(PDO::FETCH_ASSOC);
        $programaNombre = $programa['nombre'] ?? 'Programa Desconocido'; // 🔥 Ahora siempre obtenemos el nombre

        // 📌 2️⃣ Obtener los temas teóricos del programa
        $queryTemas = "SELECT t.* FROM clases_teoricas_temas t
                       WHERE t.clase_teorica_programa_id = :programa_id";
        $stmtTemas = $this->conn->prepare($queryTemas);
        $stmtTemas->bindParam(':programa_id', $programaId, PDO::PARAM_INT);
        $stmtTemas->execute();
        $temas = $stmtTemas->fetchAll(PDO::FETCH_ASSOC);

        // 📌 Renderizar la vista, enviando el nombre del programa
        ob_start();
        include '../modules/clases/views/clases_teoricas/temas/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create($idPrograma)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_temas_teoria')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Consultar nombre del programa
        $programaNombre = null;

        $query = "SELECT nombre FROM programas WHERE id = :programa_id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $idPrograma, PDO::PARAM_INT);
        $stmt->execute();

        $programa = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($programa) {
            $programaNombre = $programa['nombre'];
        }

        // Pasar a la vista
        ob_start();
        include '../modules/clases/views/clases_teoricas/temas/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $routes = include '../config/Routes.php';

        // Validar que llegan los campos necesarios
        if (!isset($_POST['clase_teorica_programa_id'], $_POST['nombre'])) {
            $_SESSION['error'] = 'Datos incompletos para guardar el tema.';
            header('Location: /temas/index');
            exit;
        }

        // Obtener y sanitizar datos
        $programaId = intval($_POST['clase_teorica_programa_id']);
        $nombre = strtoupper(trim($_POST['nombre']));
        $descripcion = isset($_POST['descripcion']) ? strtoupper(trim($_POST['descripcion'])) : null;

        // Insertar en la base de datos
        $query = "INSERT INTO clases_teoricas_temas (clase_teorica_programa_id, nombre, descripcion) 
              VALUES (:programa_id, :nombre, :descripcion)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programaId, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Tema guardado exitosamente.';
        } else {
            $_SESSION['error'] = 'Hubo un error al guardar el tema.';
        }

        header('Location: ' . $routes['clases_teoricas_temas_index'] . $programaId);
        exit;
    }

    public function edit($idTema)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        // if (!$permissionController->hasPermission($currentUserId, 'edit_temas_teoria')) {
        //     header('Location: /permission-denied/');
        //     exit;
        // }

        // Obtener información del tema
        $query = "SELECT * FROM clases_teoricas_temas WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $idTema, PDO::PARAM_INT);
        $stmt->execute();

        $tema = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tema) {
            header('Location: /error/');
            exit;
        }

        // Obtener nombre del programa
        $programaNombre = '';
        $queryPrograma = "SELECT nombre FROM programas WHERE id = :id LIMIT 1";
        $stmtPrograma = $this->conn->prepare($queryPrograma);
        $stmtPrograma->bindParam(':id', $tema['clase_teorica_programa_id'], PDO::PARAM_INT);
        $stmtPrograma->execute();

        $programa = $stmtPrograma->fetch(PDO::FETCH_ASSOC);
        if ($programa) {
            $programaNombre = $programa['nombre'];
        }

        $idPrograma = $tema['clase_teorica_programa_id'];

        // Cargar vista
        ob_start();
        include '../modules/clases/views/clases_teoricas/temas/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update()
    {
        $routes = include '../config/Routes.php';
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $idTema = $_POST['id'];
            $nombre = trim($_POST['nombre']);
            $descripcion = trim($_POST['descripcion']);

            $query = "UPDATE clases_teoricas_temas 
                  SET nombre = :nombre, descripcion = :descripcion 
                  WHERE id = :id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':descripcion', $descripcion);
            $stmt->bindParam(':id', $idTema, PDO::PARAM_INT);
            $stmt->execute();

            if ($stmt->execute()) {
                $_SESSION['success'] = 'Tema modificado exitosamente.';
            } else {
                $_SESSION['error'] = 'Hubo un error al modificar el tema.';
            }

            // Redireccionar de nuevo al listado del programa
            header('Location:' . $routes['clases_teoricas_temas_index'] . $_POST['clase_teorica_programa_id']);
        } else {
            header('Location:' . $routes['clases_teoricas_temas_index'] . $_POST['clase_teorica_programa_id']);
            exit;
        }
    }
}
