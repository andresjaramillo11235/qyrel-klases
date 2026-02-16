<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class PermissionsAdminController
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

        if (!$permissionController->hasPermission($currentUserId, 'view_permissions')) {
            header('Location: /permission-denied/');
            exit;
        }

        $titulo = 'Administrador de Permisos';

        ob_start();
        include '../modules/permissions/views/index_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_permissions')) {
            header('Location: /permission-denied/');
            exit;
        }

        $titulo = 'Crear Permiso';

        ob_start();
        include '../modules/permissions/views/create_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function createPermission()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_permissions')) {
            header('Location: /permission-denied/');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $permission_name = $_POST['permission_name'];
            $permission_description = $_POST['permission_description'];

            $config = new DatabaseConfig();
            $conn = $config->getConnection();

            $query = "INSERT INTO permissions (name, description) VALUES (:permission_name, :permission_description)";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':permission_name', $permission_name);
            $stmt->bindParam(':permission_description', $permission_description);

            if ($stmt->execute()) {
                header('Location: /permissionslist/');
                exit;
            } else {
                echo "Error al crear el permiso.";
            }
        }
    }

    public function listPermission()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_permissions')) {
            header('Location: /permission-denied/');
            exit;
        }

        $config = new DatabaseConfig();
        $conn = $config->getConnection();

        $query = "SELECT * FROM permissions";
        $stmt = $conn->prepare($query);
        $stmt->execute();

        $permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $titulo = 'Listado de Permisos';

        ob_start();
        include '../modules/permissions/views/list_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_permissions')) {
            header('Location: /permission-denied/');
            exit;
        }

        $config = new DatabaseConfig();
        $conn = $config->getConnection();

        $query = "SELECT * FROM permissions WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $permission = $stmt->fetch(PDO::FETCH_ASSOC);
        $titulo = 'Editar Permiso';

        ob_start();
        include '../modules/permissions/views/update_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function updatePermission()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_permissions')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $permission_id = $_POST['id'];
            $permission_name = $_POST['permission_name'];
            $permission_description = $_POST['permission_description'];

            $config = new DatabaseConfig();
            $conn = $config->getConnection();

            $query = "UPDATE permissions SET name = :permission_name, description = :permission_description WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':permission_name', $permission_name);
            $stmt->bindParam(':permission_description', $permission_description);
            $stmt->bindParam(':id', $permission_id);

            if ($stmt->execute()) {
                header('Location: /permissionslist/');
                exit;
            } else {
                echo "Error al actualizar el permiso.";
            }
        }
    }

    public function delete($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'delete_permissions')) {
            header('Location: /permission-denied/');
            exit;
        }

        $config = new DatabaseConfig();
        $conn = $config->getConnection();

        $query = "DELETE FROM permissions WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            header('Location: /permissionslist/');
            exit;
        } else {
            echo "Error al eliminar el permiso.";
        }
    }
}
?>
