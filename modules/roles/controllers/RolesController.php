<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class RolesController
{
    private $conn;

    public function __construct()
    {
        $database = new DatabaseConfig();
        $this->conn = $database->getConnection();
    }

    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'manage_roles')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $query = "SELECT * FROM roles";
        $stmt = $this->conn->query($query);
        $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $titulo = 'Listado de Roles';

        ob_start();
        include '../modules/roles/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'manage_roles')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $titulo = 'Crear Rol';

        ob_start();
        include '../modules/roles/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'manage_roles')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $name = $_POST['name'];
        $description = $_POST['description'];
        $menu = $_POST['menu'];

        $query = "INSERT INTO roles (name, description, menu) VALUES (:name, :description, :menu)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':menu', $menu);
        $stmt->execute();

        header('Location: /roles/');
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'manage_roles')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $query = "SELECT * FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        $titulo = 'Editar Rol';

        ob_start();
        include '../modules/roles/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'manage_roles')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $name = $_POST['name'];
        $description = $_POST['description'];
        $menu = $_POST['menu'];

        $query = "UPDATE roles SET name = :name, description = :description, menu = :menu WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':menu', $menu);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header('Location: /roles/');
    }

    public function detail($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'manage_roles')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $query = "SELECT * FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $role = $stmt->fetch(PDO::FETCH_ASSOC);

        $titulo = 'Detalle del Rol';

        ob_start();
        include '../modules/roles/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function delete($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'manage_roles')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $query = "DELETE FROM roles WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header('Location: /roles/');
    }
}
