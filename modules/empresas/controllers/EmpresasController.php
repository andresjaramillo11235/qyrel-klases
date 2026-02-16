<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class EmpresasController
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

        if (!$permissionController->hasPermission($currentUserId, 'view_empresas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener las empresas
        $query = "SELECT e.*, pe.nombre AS estado_nombre 
              FROM empresas e 
              LEFT JOIN param_estados_empresas pe ON e.estado = pe.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $empresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener los estados de empresas para el select
        $queryEstados = "SELECT * FROM param_estados_empresas";
        $stmtEstados = $this->conn->prepare($queryEstados);
        $stmtEstados->execute();
        $estadosEmpresas = $stmtEstados->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/empresas/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_empresas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener los estados de las empresas
        $query = "SELECT * FROM param_estados_empresas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $estadosEmpresas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/empresas/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    private function generateUniqueCode()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
    }

    public function store()
    {
        $permissionController = new PermissionController();
        $auditoriaController = new AuditoriaController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_empresas')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                // Procesar el logo
                $logo = null;

                if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {

                    $uploadDir = '../files/logos_empresas/';

                    // Obtener la extensión del archivo
                    $fileInfo = pathinfo($_FILES['logo']['name']);
                    $extension = $fileInfo['extension'];

                    // Generar un nombre único para el logo
                    $timestamp = date('Ymd-His'); // Formato YYYYMMDD-HHMMSS
                    $logoFileName = 'logo-empresa-' . $timestamp . '.' . $extension;

                    $uploadFilePath = $uploadDir . $logoFileName;

                    if (move_uploaded_file($_FILES['logo']['tmp_name'], $uploadFilePath)) {
                        $logo = $logoFileName; // Almacenar el nombre del archivo en la base de datos
                    } else {
                        throw new Exception('Error al subir el logo.');
                    }
                }

                // Generar el código único para la empresa
                $codigo = $this->generateUniqueCode();

                // Consulta con los nuevos campos nivel e inicio_facturacion
                $query = "INSERT INTO empresas (codigo, nombre, identificacion, direccion, ciudad, correo, telefono, logo, estado, notas, fecha_ingreso, nivel, inicio_facturacion) 
                    VALUES (:codigo, :nombre, :identificacion, :direccion, :ciudad, :correo, :telefono, :logo, :estado, :notas, :fecha_ingreso, :nivel, :inicio_facturacion)";

                $stmt = $this->conn->prepare($query);

                // Asignar parámetros
                $stmt->bindParam(':codigo', $codigo);
                $stmt->bindParam(':nombre', $_POST['nombre']);
                $stmt->bindParam(':identificacion', $_POST['identificacion']);
                $stmt->bindParam(':direccion', $_POST['direccion']);
                $stmt->bindParam(':ciudad', $_POST['ciudad']);
                $stmt->bindParam(':correo', $_POST['correo']);
                $stmt->bindParam(':telefono', $_POST['telefono']);
                $stmt->bindParam(':logo', $logo);
                $stmt->bindParam(':estado', $_POST['estado']);
                $stmt->bindParam(':notas', $_POST['notas']);
                $stmt->bindParam(':fecha_ingreso', $_POST['fecha_ingreso']); // Campo fecha_ingreso
                $stmt->bindParam(':nivel', $_POST['nivel']); // Nuevo campo nivel
                $stmt->bindParam(':inicio_facturacion', $_POST['inicio_facturacion']); // Nuevo campo inicio_facturacion

                if ($stmt->execute()) {

                    // Registrar auditoría de creación de empresa
                    $empresaInsertadaId = $this->conn->lastInsertId();
                    $descripcion = "Se creó la empresa: " . $_POST['nombre'] . " con ID " . $empresaInsertadaId;
                    $empresaId = $_SESSION['empresa_id'];
                    $auditoriaController = new AuditoriaController();
                    $auditoriaController->registrar($currentUserId, 'Crear', 'Empresas', $descripcion, $empresaId);

                    $_SESSION['empresa_success'] = "La empresa fue creada exitosamente.";
                    
                } else {
                    $_SESSION['empresa_error'] = "Error al crear la empresa.";
                }
            }
        } catch (Exception $e) {
            $_SESSION['empresa_error'] = "Ocurrió un error: " . $e->getMessage();
        }

        // Redirigir después de la creación
        header('Location: /empresas/');
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_empresas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Traer los datos de la empresa
        $query = "SELECT * FROM empresas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        // Traer los estados de la tabla paramétrica `param_estados_empresas`
        $queryEstados = "SELECT * FROM param_estados_empresas";
        $stmtEstados = $this->conn->prepare($queryEstados);
        $stmtEstados->execute();
        $estadosEmpresas = $stmtEstados->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/empresas/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update()
    {
        $permissionController = new PermissionController();
        $auditoriaController = new AuditoriaController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_empresas')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            $query = "UPDATE empresas SET nombre = :nombre, identificacion = :identificacion, direccion = :direccion, ciudad = :ciudad, correo = :correo, telefono = :telefono, dominio = :dominio, logo = :logo, estado = :estado, notas = :notas, fecha_ingreso = :fecha_ingreso WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            $stmt->bindParam(':nombre', $_POST['nombre']);
            $stmt->bindParam(':identificacion', $_POST['identificacion']);
            $stmt->bindParam(':direccion', $_POST['direccion']);
            $stmt->bindParam(':ciudad', $_POST['ciudad']);
            $stmt->bindParam(':correo', $_POST['correo']);
            $stmt->bindParam(':telefono', $_POST['telefono']);
            $stmt->bindParam(':dominio', $_POST['dominio']);
            $stmt->bindParam(':logo', $_POST['logo']);
            $stmt->bindParam(':estado', $_POST['estado']);
            $stmt->bindParam(':notas', $_POST['notas']);
            $stmt->bindParam(':fecha_ingreso', $_POST['fecha_ingreso']);
            $stmt->bindParam(':id', $_POST['id']);

            if ($stmt->execute()) {
                // Registrar auditoría de modificacion de empresa
                $empresaInsertadaId = $this->conn->lastInsertId();
                $descripcion = "Se modificó la información de la empresa: " . $_POST['nombre'] . " con ID " .  $_POST['id'];
                $empresaId = $_SESSION['empresa_id'];
                $auditoriaController = new AuditoriaController();
                $auditoriaController->registrar($currentUserId, 'Modificar', 'Empresas', $descripcion, $empresaId);

                $_SESSION['empresa_success'] = "Empresa actualizada correctamente.";
            } else {
                $_SESSION['empresa_error'] = "Error al actualizar la empresa.";
            }
        } catch (Exception $e) {
            $_SESSION['empresa_error'] = "Error: " . $e->getMessage();
        }

        header('Location: /empresas/');
        exit;
    }


    public function delete($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'delete_empresas')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $query = "DELETE FROM empresas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header('Location: /empresas/');
    }

    public function detail($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_empresas')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $query = "SELECT * FROM empresas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $empresa = $stmt->fetch(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/empresas/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
