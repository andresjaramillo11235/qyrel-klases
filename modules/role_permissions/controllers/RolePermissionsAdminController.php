<?php

require_once '../config/DatabaseConfig.php';

class RolePermissionsAdminController
{
    public function index()
    {
        $titulo = 'Administrador de Asignación de Permisos a Roles';

        // Obtener todas las asignaciones de permisos a roles
        $config = new DatabaseConfig();
        $conn = $config->getConnection();
        $query = "SELECT rp.id, r.name as role_name, p.name as permission_name
              FROM role_permissions rp
              INNER JOIN roles r ON rp.role_id = r.id
              INNER JOIN permissions p ON rp.permission_id = p.id";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $rolePermissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener mensaje de error o éxito si existen
        $error_message = isset($_SESSION['error_message']) ? $_SESSION['error_message'] : '';
        $success_message = isset($_SESSION['success_message']) ? $_SESSION['success_message'] : '';

        ob_start();
        include '../modules/role_permissions/views/index_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $titulo = 'Asignar Permiso a Rol';

        // Obtener todos los roles disponibles
        $config = new DatabaseConfig();
        $conn = $config->getConnection();
        $queryRoles = "SELECT id, name FROM roles";
        $stmtRoles = $conn->prepare($queryRoles);
        $stmtRoles->execute();
        $roles = $stmtRoles->fetchAll(PDO::FETCH_ASSOC);

        // Obtener todos los permisos disponibles
        $queryPermissions = "SELECT id, name FROM permissions";
        $stmtPermissions = $conn->prepare($queryPermissions);
        $stmtPermissions->execute();
        $permissions = $stmtPermissions->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/role_permissions/views/create_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $role_id = $_POST['role_id'];
            $permission_id = $_POST['permission_id'];

            // Validar si la asignación ya existe
            $config = new DatabaseConfig();
            $conn = $config->getConnection();
            $query = "SELECT COUNT(*) FROM role_permissions WHERE role_id = :role_id AND permission_id = :permission_id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->bindParam(':permission_id', $permission_id);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count > 0) {
                $_SESSION['error_message'] = "La asignación de permiso a rol ya existe.";
                header('Location: /rolepermissions/');
                exit;
            } else {
                // Insertar la nueva asignación de permiso a rol en la base de datos
                $query = "INSERT INTO role_permissions (role_id, permission_id) VALUES (:role_id, :permission_id)";
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':role_id', $role_id);
                $stmt->bindParam(':permission_id', $permission_id);

                if ($stmt->execute()) {
                    header('Location: /rolepermissions/');
                    exit;
                } else {
                    echo "Error al asignar permiso a rol.";
                }
            }
        }
    }

    public function delete($id)
    {
        $config = new DatabaseConfig();
        $conn = $config->getConnection();
        $query = "DELETE FROM role_permissions WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "La asignación de permiso a rol ha sido eliminada.";
            header('Location: /rolepermissions/');
            exit;
        } else {
            $_SESSION['error_message'] = "Error al eliminar la asignación de permiso a rol.";
            header('Location: /rolepermissions/');
            exit;
        }
    }
}
