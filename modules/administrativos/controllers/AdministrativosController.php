<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/ImageHelper.php';
require_once '../shared/utils/TablasParametricasUtils.php';
require_once '../shared/utils/LabelHelper.php';

class AdministrativosController
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
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_administrativos')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT a.*, 
        e.nombre AS empresa_nombre,
        u.username,  
        r.name AS rol_name, 
        r.description AS rol_description
            FROM administrativos a
            LEFT JOIN empresas e ON a.empresa_id = e.id
            LEFT JOIN users u ON a.id = u.administrativo_id
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE a.empresa_id = :empresa_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);

        $stmt->execute();
        $administrativos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/administrativos/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_administrativos')) {
            header('Location: /permission-denied/');
            exit;
        }

        $roles = $this->getRolesFiltrados();
        $tablasParametricas = new TablasParametricasUtils($this->conn);

        $paramTiposDocumentos = $tablasParametricas->getParamTiposDocumentos();
        $paramDepartamentos = $tablasParametricas->getParamDepartamentos();
        $paramCiudades = $tablasParametricas->getParamCiudades();
        $paramGrupoSanguineo = $tablasParametricas->getParamGrupoSanguineo();
        $paramGenero = $tablasParametricas->getParamGenero();
        $paramEstadoCivil = $tablasParametricas->getParamEstadoCivil();

        ob_start();
        include '../modules/administrativos/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_administrativos')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Asignar los valores desde el formulario
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $tipo_documento = $_POST['tipo_documento'];
        $numero_documento = $_POST['numero_documento'];
        $username = $_POST['username'];
        $expedicion_departamento = $_POST['expedicion_departamento'];
        $expedicion_ciudad = $_POST['expedicion_ciudad'];
        $fecha_expedicion = $_POST['fecha_expedicion'];
        $correo = strtolower($_POST['correo']);
        $celular = $_POST['celular'];
        $direccion = $_POST['direccion'];
        $grupo_sanguineo = $_POST['grupo_sanguineo'];
        $genero = $_POST['genero'];
        $estado_civil = $_POST['estado_civil'];
        $estado = isset($_POST['estado']) ? 1 : 0;
        $observaciones = $_POST['observaciones'];
        $role_id = $_POST['rol'];

        // Validar si el nombre de usuario ya existe
        $query = "SELECT COUNT(*) FROM users WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $userCount = $stmt->fetchColumn();

        if ($userCount > 0) {
            $_SESSION['form_data'] = $_POST;
            $_SESSION['username_error'] = 'El nombre de usuario no se encuentra disponible.';
            header('Location: /administrativos-create');
            exit;
        }

        // Manejo de la foto
        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            // Obtener la extensión del archivo
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

            // Generar un nombre único para la foto
            $foto = date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;

            // Mover el archivo a la carpeta de destino
            move_uploaded_file($_FILES['foto']['tmp_name'], '../files/fotos_administrativos/' . $foto);
        }

        // Insertar en la tabla de administrativos
        $query = "INSERT INTO administrativos (nombres, apellidos, tipo_documento, numero_documento, expedicion_departamento, 
                expedicion_ciudad, fecha_expedicion, correo, celular, direccion, grupo_sanguineo, genero, estado_civil, 
                estado, observaciones, foto, empresa_id) 
              VALUES (:nombres, :apellidos, :tipo_documento, :numero_documento, :expedicion_departamento, 
                :expedicion_ciudad, :fecha_expedicion, :correo, :celular, :direccion, :grupo_sanguineo, :genero, :estado_civil, 
                :estado, :observaciones, :foto, :empresa_id)";

        $stmt = $this->conn->prepare($query);

        // Vincular parámetros
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->bindParam(':expedicion_departamento', $expedicion_departamento);
        $stmt->bindParam(':expedicion_ciudad', $expedicion_ciudad);
        $stmt->bindParam(':fecha_expedicion', $fecha_expedicion);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':estado_civil', $estado_civil);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':empresa_id', $empresa_id);

        if ($stmt->execute()) {

            $administrativo_id = $this->conn->lastInsertId();

            // Password es el numero de la cedula
            $password = password_hash($numero_documento, PASSWORD_DEFAULT);

            $userQuery = "INSERT INTO users (username, email, password, first_name, last_name, phone, 
                            address, status, role_id, empresa_id, administrativo_id) 
                        VALUES (:username, :email, :password, :first_name, :last_name, :phone, :address, 
                            '1', :role_id, :empresa_id, :administrativo_id)";

            $userStmt = $this->conn->prepare($userQuery);

            $userStmt->bindParam(':username', $username);
            $userStmt->bindParam(':email', $correo);
            $userStmt->bindParam(':password', $password);
            $userStmt->bindParam(':first_name', $nombres);
            $userStmt->bindParam(':last_name', $apellidos);
            $userStmt->bindParam(':phone', $celular);
            $userStmt->bindParam(':address', $direccion);
            $userStmt->bindParam(':role_id', $role_id);
            $userStmt->bindParam(':empresa_id', $empresa_id);
            $userStmt->bindParam(':administrativo_id', $administrativo_id);

            if ($userStmt->execute()) {
                $_SESSION['administrativo_creado'] = "El administrativo fue creado con éxito.";
                header('Location: /administrativos/');
                exit;
            } else {
                $_SESSION['error_message'] = "No fue posible crear el usuario para el administrativo.";
                $_SESSION['form_data'] = $_POST;
                header('Location: /administrativos-create/');
                exit;
            }
        } else {
            $_SESSION['error_message'] = "No fue posible crear el administrativo.";
            $_SESSION['form_data'] = $_POST;
            header('Location: /administrativos-create/');
            exit;
        }
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_administrativos')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "
            SELECT a.*, u.username, u.role_id AS rol
            FROM administrativos a
            LEFT JOIN users u ON a.id = u.administrativo_id
            WHERE a.id = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $administrativo = $stmt->fetch(PDO::FETCH_ASSOC);

        $tablasParametricas = new TablasParametricasUtils($this->conn);

        $paramTiposDocumentos = $tablasParametricas->getParamTiposDocumentos();
        $paramDepartamentos = $tablasParametricas->getParamDepartamentos();
        $paramCiudades = $tablasParametricas->getParamCiudades();
        $paramGrupoSanguineo = $tablasParametricas->getParamGrupoSanguineo();
        $paramGenero = $tablasParametricas->getParamGenero();
        $paramEstadoCivil = $tablasParametricas->getParamEstadoCivil();
        $roles = $this->getRolesFiltrados();

        ob_start();
        include '../modules/administrativos/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_administrativos')) {
            header('Location: /permission-denied/');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $nombres = $_POST['nombres'];
            $apellidos = $_POST['apellidos'];
            $tipo_documento = $_POST['tipo_documento'];
            $numero_documento = $_POST['numero_documento'];
            $expedicion_departamento = $_POST['expedicion_departamento'];
            $expedicion_ciudad = $_POST['expedicion_ciudad'];
            $fecha_expedicion = $_POST['fecha_expedicion'];
            $correo = strtolower($_POST['correo']);
            $celular = $_POST['celular'];
            $direccion = $_POST['direccion'];
            $grupo_sanguineo = $_POST['grupo_sanguineo'];
            $genero = $_POST['genero'];
            $estado_civil = $_POST['estado_civil'];
            $estado = isset($_POST['estado']) ? 1 : 0;
            $observaciones = $_POST['observaciones'];
            $rol = $_POST['rol'];

            // Obtener la foto actual
            $query = "SELECT foto FROM administrativos WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $administrativoActual = $stmt->fetch(PDO::FETCH_ASSOC);
            $fotoActual = $administrativoActual['foto'];

            // Manejo de la foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                // Si se selecciona una nueva foto, se procesa y se actualiza
                $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                $foto = date('YmdHis') . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
                move_uploaded_file($_FILES['foto']['tmp_name'], '../files/fotos_administrativos/' . $foto);
            } else {
                // Si no se selecciona una nueva foto, se mantiene la existente
                $foto = $fotoActual;
            }
            try {
                // Actualizar los datos del administrativo en la tabla administrativos
                $query = "UPDATE administrativos SET 
                            nombres = :nombres, 
                            apellidos = :apellidos, 
                            tipo_documento = :tipo_documento, 
                            numero_documento = :numero_documento, 
                            expedicion_departamento = :expedicion_departamento, 
                            expedicion_ciudad = :expedicion_ciudad, 
                            fecha_expedicion = :fecha_expedicion, 
                            correo = :correo, 
                            celular = :celular, 
                            direccion = :direccion, 
                            grupo_sanguineo = :grupo_sanguineo, 
                            genero = :genero, 
                            estado_civil = :estado_civil, 
                            estado = :estado, 
                            observaciones = :observaciones, 
                            foto = :foto
                          WHERE id = :id";

                $stmt = $this->conn->prepare($query);

                $stmt->bindParam(':nombres', $nombres);
                $stmt->bindParam(':apellidos', $apellidos);
                $stmt->bindParam(':tipo_documento', $tipo_documento);
                $stmt->bindParam(':numero_documento', $numero_documento);
                $stmt->bindParam(':expedicion_departamento', $expedicion_departamento);
                $stmt->bindParam(':expedicion_ciudad', $expedicion_ciudad);
                $stmt->bindParam(':fecha_expedicion', $fecha_expedicion);
                $stmt->bindParam(':correo', $correo);
                $stmt->bindParam(':celular', $celular);
                $stmt->bindParam(':direccion', $direccion);
                $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
                $stmt->bindParam(':genero', $genero);
                $stmt->bindParam(':estado_civil', $estado_civil);
                $stmt->bindParam(':estado', $estado);
                $stmt->bindParam(':observaciones', $observaciones);
                $stmt->bindParam(':foto', $foto);
                $stmt->bindParam(':id', $id);

                $stmt->execute();

                // Actualizar los datos del usuario en la tabla users
                $userQuery = "UPDATE users SET 
                    first_name = :first_name, 
                    last_name = :last_name, 
                    email = :email, 
                    phone = :phone, 
                    address = :address, 
                    role_id = :role_id, 
                    status = :status
                    WHERE administrativo_id = :administrativo_id";


                $userStmt = $this->conn->prepare($userQuery);

                $status = isset($_POST['estado']) ? 1 : 0;

                $userStmt->bindParam(':first_name', $nombres);
                $userStmt->bindParam(':last_name', $apellidos);
                $userStmt->bindParam(':email', $correo);
                $userStmt->bindParam(':phone', $celular);
                $userStmt->bindParam(':address', $direccion);
                $userStmt->bindParam(':role_id', $rol);
                $userStmt->bindParam(':status', $status);
                $userStmt->bindParam(':administrativo_id', $id);

                $userStmt->execute();

                $_SESSION['administrativo_modificado'] = "El administrativo fue modificado con éxito.";
                header('Location: /administrativos/');
                exit;
            } catch (PDOException $e) {
                // Manejar el error y mostrar un mensaje amigable
                $_SESSION['error'] = "Hubo un error al actualizar el administrativo. Inténtalo de nuevo.";
                header('Location: /administrativos-edit/' . $id);
                exit;
            }
        } else {
            header('Location: /administrativos/');
            exit;
        }
    }


    public function detail($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_administrativos')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Consulta para obtener los detalles del administrativo
        $query = "SELECT a.*, u.username, r.description as rol_description 
                  FROM administrativos a
                  LEFT JOIN users u ON a.id = u.administrativo_id
                  LEFT JOIN roles r ON u.role_id = r.id
                  WHERE a.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $administrativo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$administrativo) {
            // Manejo de error si no se encuentra el administrativo
            $_SESSION['error'] = "El administrativo no existe.";
            header('Location: /administrativos/');
            exit;
        }

        $tablasParametricas = new TablasParametricasUtils($this->conn);

        $paramTiposDocumentos = $tablasParametricas->getParamTiposDocumentos();
        $paramDepartamentos = $tablasParametricas->getParamDepartamentos();
        $paramCiudades = $tablasParametricas->getParamCiudades();
        $paramGrupoSanguineo = $tablasParametricas->getParamGrupoSanguineo();
        $paramGenero = $tablasParametricas->getParamGenero();
        $paramEstadoCivil = $tablasParametricas->getParamEstadoCivil();
        $roles = $this->getRolesFiltrados();

        ob_start();
        include '../modules/administrativos/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }



    private function getRolesFiltrados()
    {
        $query = "SELECT id, name, description FROM roles WHERE id IN (3, 4, 7, 8)";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
