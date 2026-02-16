<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../shared/utils/TablasParametricasUtils.php';

class UsersController
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
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_users')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        // Filter by empresa_id if user is not super admin
        $query = "SELECT users.*, roles.name AS role_name, empresas.nombre AS empresa_nombre 
            FROM users 
            LEFT JOIN roles ON users.role_id = roles.id 
            LEFT JOIN empresas ON users.empresa_id = empresas.id
            WHERE users.empresa_id = :empresa_id
            AND (users.is_super_admin IS NULL)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);

        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/users/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function getUserAdmin($empresaId)
    {
        $currentUserId = $_SESSION['user_id'];

        // Consulta para obtener el usuario administrador
        $query = "SELECT users.*, roles.name AS role_name, empresas.nombre AS empresa_nombre 
              FROM users 
              LEFT JOIN roles ON users.role_id = roles.id 
              LEFT JOIN empresas ON users.empresa_id = empresas.id
              WHERE users.empresa_id = :empresa_id AND users.is_admin = 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->execute();

        // Fetch del usuario administrador
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/users/views/index.php'; // Pasa el usuario admin a la vista
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create($empresaId)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'create_users')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $tablasParametricas = new TablasParametricasUtils($this->conn);
        $paramTiposDocumentos = $tablasParametricas->getParamTiposDocumentos();


        //$roles = $this->getRoles();
        //$empresas = $this->getEmpresas();

        ob_start();
        include '../modules/users/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        try {
            $permissionController = new PermissionController();
            $currentUserId = $_SESSION['user_id'];

            if (!$permissionController->hasPermission($currentUserId, 'create_users')) {
                echo "No tienes permiso para realizar esta acción.";
                return;
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                $empresaId = $_POST['empresa_id'];
                $username = $_POST['username'];
                $email = strtolower($_POST['email']);
                $password = $_POST['password'];
                $password_confirm = $_POST['password_confirm'];
                $first_name = $_POST['first_name'];
                $last_name = $_POST['last_name'];
                $phone = $_POST['phone'];
                $address = $_POST['address'];
                $status = 1; // Activo
                $is_admin = 1; // Definir como administrador
                $role_id = 3; // Rol de Administrador

                // Verificar si las contraseñas coinciden
                if ($password !== $password_confirm) {
                    $_SESSION['error_create'] = 'Las contraseñas no coinciden.';
                    $_SESSION['form_data'] = $_POST; // Preserva los datos del formulario
                    header('Location: /users-create/' . $empresaId);
                    exit;
                }

                // Validar si el username ya existe
                $query = "SELECT COUNT(*) FROM users WHERE username = :username";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $userExists = $stmt->fetchColumn();

                if ($userExists) {

                    $_SESSION['error_create'] = 'El nombre de usuario ya está en uso. Por favor, elige otro.';
                    $_SESSION['form_data'] = $_POST; // Preserva los datos del formulario
                    header('Location: /users-create/' . $empresaId);
                    exit;
                } else {

                    // Insertar en la tabla administrativos
                    $adminQuery = "INSERT INTO administrativos (
                        nombres, 
                        apellidos, 
                        tipo_documento, 
                        numero_documento, 
                        correo, 
                        celular, 
                        direccion, 
                        estado,
                        empresa_id
                    ) 
                    VALUES (
                        :nombres, 
                        :apellidos, 
                        :tipo_documento, 
                        :numero_documento, 
                        :correo, 
                        :celular, 
                        :direccion, 
                        :estado,
                        :empresa_id
                    )";

                    $adminStmt = $this->conn->prepare($adminQuery);
                    $adminStmt->bindParam(':nombres', $first_name);
                    $adminStmt->bindParam(':apellidos', $last_name);
                    // Los valores de tipo_documento y numero_documento deben ser proporcionados por el formulario
                    $adminStmt->bindParam(':tipo_documento', $_POST['tipo_documento']);
                    $adminStmt->bindParam(':numero_documento', $_POST['numero_documento']);
                    $adminStmt->bindParam(':correo', $email);
                    $adminStmt->bindParam(':celular', $phone);
                    $adminStmt->bindParam(':direccion', $address);
                    $adminStmt->bindParam(':estado', $status);
                    $adminStmt->bindParam(':empresa_id', $empresaId);

                    if ($adminStmt->execute()) {
                        // Obtener el ID del administrativo recién creado
                        $adminId = $this->conn->lastInsertId();

                        // Crear el usuario utilizando el ID del administrativo
                        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                        $query = "INSERT INTO users (
                            username, 
                            email, 
                            password, 
                            first_name, 
                            last_name, 
                            phone, 
                            address, 
                            status, 
                            role_id, 
                            empresa_id, 
                            is_admin,
                            administrativo_id
                        ) 
                        VALUES (
                            :username, 
                            :email, 
                            :password, 
                            :first_name, 
                            :last_name, 
                            :phone, 
                            :address, 
                            :status, 
                            :role_id, 
                            :empresa_id, 
                            :is_admin,
                            :administrativo_id
                        )";

                        $stmt = $this->conn->prepare($query);
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':password', $hashed_password);
                        $stmt->bindParam(':first_name', $first_name);
                        $stmt->bindParam(':last_name', $last_name);
                        $stmt->bindParam(':phone', $phone);
                        $stmt->bindParam(':address', $address);
                        $stmt->bindParam(':status', $status);
                        $stmt->bindParam(':role_id', $role_id);
                        $stmt->bindParam(':empresa_id', $empresaId);
                        $stmt->bindParam(':is_admin', $is_admin);
                        // Aquí se enlaza el id del administrativo recién creado
                        $stmt->bindParam(':administrativo_id', $adminId);

                        // Ejecutar la creación del usuario
                        if ($stmt->execute()) {
                            $_SESSION['success_create'] = "Nuevo usuario creado.";
                            header('Location: /empresas-admin/' . $empresaId);
                            exit;
                        } else {
                            echo "Error al crear el usuario.";
                        }
                    } else {
                        echo "Error al crear el administrativo.";
                    }
                }
            }
        } catch (PDOException $e) {

            error_log($e->getMessage());
            $_SESSION['error_create'] = 'Error en la base de datos. Intente nuevamente.';
            $_SESSION['form_data'] = $_POST;
            header('Location: /users-create/' . $empresaId);
            exit;
        } catch (Exception $e) {

            $_SESSION['error_create'] = $e->getMessage();
            $_SESSION['form_data'] = $_POST;
            header('Location: /users-create/' . $empresaId);
            exit;
        }
    }

    private function getRoles()
    {
        $currentUserId = $_SESSION['user_id'];
        $userUtils = new UserUtils();

        if ($userUtils->isSuperAdmin($currentUserId)) {
            $query = "SELECT * FROM roles";
        } else {
            $query = "SELECT * FROM roles WHERE name = 'ASISOP'";
        }

        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'edit_users')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $roles = $this->getRoles();
        $empresas = $this->getEmpresas();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = $_POST['username'];
            $email = strtolower($_POST['email']);
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $status = $_POST['status'];
            $role_id = $_POST['role_id'];

            // Super Admin puede asignar cualquier empresa, admin solo su empresa
            if ($this->userUtils->isSuperAdmin($currentUserId)) {
                $empresa_id = $_POST['empresa_id'];
            } else {
                $empresa_id = $empresaId;
            }

            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                $query = "UPDATE users SET username = :username, email = :email, password = :password, first_name = :first_name, last_name = :last_name, phone = :phone, address = :address, status = :status, role_id = :role_id, empresa_id = :empresa_id WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':password', $password);
            } else {
                $query = "UPDATE users SET username = :username, email = :email, first_name = :first_name, last_name = :last_name, phone = :phone, address = :address, status = :status, role_id = :role_id, empresa_id = :empresa_id WHERE id = :id";
                $stmt = $this->conn->prepare($query);
            }

            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->bindParam(':empresa_id', $empresa_id);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                $_SESSION['success_edit'] = 'El usuario fue modificado correctamente.';
                header('Location: /users/');
                exit;
            }
        }

        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/users/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function delete($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'delete_users')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        // log de auditoría
        $query = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $old_values = $stmt->fetch(PDO::FETCH_ASSOC);
        // log de auditoría

        $query = "DELETE FROM users WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            $this->logAudit($id, 'DELETE', json_encode($old_values), null, $_SESSION['user_id']);

            header('Location: /users/');
            exit;
        }
    }

    private function getEmpresas()
    {
        $query = "SELECT * FROM empresas";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function logAudit($user_id, $action, $old_values = null, $new_values = null, $changed_by = null)
    {
        $query = "INSERT INTO audit_users (user_id, action, old_values, new_values, changed_at, changed_by) 
                  VALUES (:user_id, :action, :old_values, :new_values, NOW(), :changed_by)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':action', $action);
        $stmt->bindParam(':old_values', $old_values);
        $stmt->bindParam(':new_values', $new_values);
        $stmt->bindParam(':changed_by', $changed_by);
        $stmt->execute();
    }
}
