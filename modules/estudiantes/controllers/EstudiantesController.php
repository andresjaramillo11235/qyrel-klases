<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/mail/MailController.php';

class EstudiantesController
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
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $permissionController = new PermissionController();

        if (!$permissionController->hasPermission($currentUserId, 'view_students')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT 
            e.*,
            td.nombre as tipo_documento_nombre,
            td.sigla as tipo_documento_sigla,
            gs.nombre as grupo_sanguineo_nombre,
            g.nombre as genero_nombre,
            ec.nombre as estado_civil_nombre,
            oc.nombre as ocupacion_nombre,
            jo.nombre as jornada_nombre,
            es.nombre as estrato_nombre,
            ss.nombre as seguridad_social_nombre,
            ne.nombre as nivel_educacion_nombre,
            di.nombre as discapacidad_nombre,
            em.nombre as empresa_nombre,
            u.username as nombre_de_usuario
          FROM estudiantes e
          LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
          LEFT JOIN param_grupo_sanguineo gs ON e.grupo_sanguineo = gs.id
          LEFT JOIN param_genero g ON e.genero = g.id
          LEFT JOIN param_estado_civil ec ON e.estado_civil = ec.id
          LEFT JOIN param_ocupacion oc ON e.ocupacion = oc.id
          LEFT JOIN param_jornada jo ON e.jornada = jo.id
          LEFT JOIN param_estrato es ON e.estrato = es.id
          LEFT JOIN param_seguridad_social ss ON e.seguridad_social = ss.id
          LEFT JOIN param_nivel_educacion ne ON e.nivel_educacion = ne.id
          LEFT JOIN param_discapacidad di ON e.discapacidad = di.id
          LEFT JOIN empresas em ON e.empresa_id = em.id
          LEFT JOIN users u ON e.id = u.estudiante_id
          WHERE e.empresa_id = :empresa_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);

        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/estudiantes/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $currentUserId = $_SESSION['user_id'];
        $permissionController = new PermissionController();

        if (!$permissionController->hasPermission($currentUserId, 'create_students')) {
            header('Location: /permission-denied/');
            exit;
        }

        $parametricTables = $this->getParametricTables();

        $userUtils = new UserUtils();
        $isSuperAdmin = $userUtils->isSuperAdmin($currentUserId);

        ob_start();
        include '../modules/estudiantes/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    private function generateUniqueCode()
    {
        return substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'), 0, 8);
    }

    public function store()
    {
        $currentUserId = $_SESSION['user_id'];
        $permissionController = new PermissionController();

        if (!$permissionController->hasPermission($currentUserId, 'create_students')) {
            header('Location: /permission-denied/');
            exit;
        }

        $numero_documento = $_POST['numero_documento'];

        // Manejo de la subida de la foto
        $foto = "img-defecto-estudiante.webp"; // Valor por defecto

        if (!empty($_FILES['foto']['name'])) {
            $file = $_FILES['foto'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $numero_documento . '_' . date('YmdHis') . '.' . $ext; // Nombre único
            $targetDir = "../files/fotos_estudiantes/";
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $foto = $filename; // Se guarda la foto subida
            } else {
                $_SESSION['form_data'] = $_POST;
                $_SESSION['error_message'] = 'Error al subir la foto.';
                header('Location: /estudiantescreate/');
                exit;
            }
        }

        // Generar un código único para el estudiante
        do {
            $codigo = $this->generateUniqueCode();
            $queryCheckCodigo = "SELECT COUNT(*) FROM estudiantes WHERE codigo = :codigo";
            $stmtCheckCodigo = $this->conn->prepare($queryCheckCodigo);
            $stmtCheckCodigo->bindParam(':codigo', $codigo);
            $stmtCheckCodigo->execute();
            $countCodigo = $stmtCheckCodigo->fetchColumn();
        } while ($countCodigo > 0);

        $query = "
            INSERT INTO estudiantes (
                codigo,
                nombres,
                apellidos,
                tipo_documento,
                numero_documento,
                expedicion_departamento,
                expedicion_ciudad,
                fecha_expedicion,
                grupo_sanguineo,
                genero,
                fecha_nacimiento,
                correo,
                celular,
                direccion_residencia,
                direccion_oficina,
                telefono_oficina,
                estado_civil,
                ocupacion,
                jornada,
                barrio,
                estrato,
                seguridad_social,
                nivel_educacion,
                ciudad_origen,
                discapacidad,
                nombre_contacto,
                telefono_contacto,
                observaciones,
                foto,
                estado,
                empresa_id
            ) 
            VALUES (
                :codigo,
                :nombres,
                :apellidos,
                :tipo_documento,
                :numero_documento,
                :expedicion_departamento,
                :expedicion_ciudad,
                :fecha_expedicion,
                :grupo_sanguineo,
                :genero,
                :fecha_nacimiento,
                :correo,
                :celular,
                :direccion_residencia,
                :direccion_oficina,
                :telefono_oficina,
                :estado_civil,
                :ocupacion,
                :jornada,
                :barrio,
                :estrato,
                :seguridad_social,
                :nivel_educacion,
                :ciudad_origen,
                :discapacidad,
                :nombre_contacto,
                :telefono_contacto,
                :observaciones,
                :foto,
                :estado,
                :empresa_id
            )
        ";

        // Crear variables y convertir a minúsculas donde sea necesario
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $tipo_documento = $_POST['tipo_documento'];
        $numero_documento = $_POST['numero_documento'];
        $expedicion_departamento = $_POST['expedicion_departamento'];
        $expedicion_ciudad = $_POST['expedicion_ciudad'];
        $fecha_expedicion = $_POST['fecha_expedicion'];
        $grupo_sanguineo = $_POST['grupo_sanguineo'];
        $genero = $_POST['genero'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $correo = strtolower($_POST['correo']);
        $celular = $_POST['celular'];
        $direccion_residencia = $_POST['direccion_residencia'];
        $direccion_oficina = $_POST['direccion_oficina'];
        $telefono_oficina = $_POST['telefono_oficina'];
        $estado_civil = $_POST['estado_civil'];
        $ocupacion = $_POST['ocupacion'];
        $jornada = $_POST['jornada'];
        $barrio = $_POST['barrio'];
        $estrato = $_POST['estrato'];
        $seguridad_social = $_POST['seguridad_social'];
        $nivel_educacion = $_POST['nivel_educacion'];
        $ciudad_origen = $_POST['ciudad_origen'];
        $discapacidad = $_POST['discapacidad'];
        $nombre_contacto = $_POST['nombre_contacto'];
        $telefono_contacto = $_POST['telefono_contacto'];
        $observaciones = $_POST['observaciones'];
        $estado = isset($_POST['estado']) ? 1 : 0;
        $empresa_id = $_SESSION['empresa_id'];

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':codigo', $codigo);
        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->bindParam(':expedicion_departamento', $expedicion_departamento);
        $stmt->bindParam(':expedicion_ciudad', $expedicion_ciudad);
        $stmt->bindParam(':fecha_expedicion', $fecha_expedicion);
        $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':direccion_residencia', $direccion_residencia);
        $stmt->bindParam(':direccion_oficina', $direccion_oficina);
        $stmt->bindParam(':telefono_oficina', $telefono_oficina);
        $stmt->bindParam(':estado_civil', $estado_civil);
        $stmt->bindParam(':ocupacion', $ocupacion);
        $stmt->bindParam(':jornada', $jornada);
        $stmt->bindParam(':barrio', $barrio);
        $stmt->bindParam(':estrato', $estrato);
        $stmt->bindParam(':seguridad_social', $seguridad_social);
        $stmt->bindParam(':nivel_educacion', $nivel_educacion);
        $stmt->bindParam(':ciudad_origen', $ciudad_origen);
        $stmt->bindParam(':discapacidad', $discapacidad);
        $stmt->bindParam(':nombre_contacto', $nombre_contacto);
        $stmt->bindParam(':telefono_contacto', $telefono_contacto);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':empresa_id', $empresa_id);

        if ($stmt->execute()) {

            ## Crea el estudiante en la tabla usuarios
            $estudiante_id = $this->conn->lastInsertId();
            $username = $numero_documento;
            $password = password_hash($numero_documento, PASSWORD_DEFAULT);
            $role_id = 5; // EST estudiante

            ## Generar nombre de usuario
            // $iniciales = $this->obtenerIniciales($nombres, $apellidos);
            $usernameBase = $numero_documento;
            $username = $usernameBase;

            // Verificar si el nombre de usuario ya existe y generar uno único
            $query = "SELECT COUNT(*) FROM users WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            $incremento = 1;
            while ($count > 0) {
                $username = $usernameBase . $incremento;
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                $incremento++;
            }

            $userQuery = "
            INSERT INTO users (
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
                estudiante_id
            ) 
            VALUES (
                :username, 
                :email, 
                :password, 
                :first_name, 
                :last_name, 
                :phone, 
                :address, 
                '1', 
                :role_id, 
                :empresa_id, 
                :estudiante_id
            )";

            $userStmt = $this->conn->prepare($userQuery);

            $userStmt->bindParam(':username', $username);
            $userStmt->bindParam(':email', $correo);
            $userStmt->bindParam(':password', $password);
            $userStmt->bindParam(':first_name', $nombres);
            $userStmt->bindParam(':last_name', $apellidos);
            $userStmt->bindParam(':phone', $celular);
            $userStmt->bindParam(':address', $direccion_residencia);
            $userStmt->bindParam(':role_id', $role_id);
            $userStmt->bindParam(':empresa_id', $empresa_id);
            $userStmt->bindParam(':estudiante_id', $estudiante_id);

            if ($userStmt->execute()) {
                $_SESSION['estudiante_creado'] = 'Estudiante creado exitosamente.';
                header('Location: /estudiantes/');
                exit;
            } else {
                echo "Error al crear el usuario para el estudiante.";
                exit;
            }
        } else {
            $_SESSION['error_message'] = 'Hubo un problema al crear el estudiante.';
        }

        header('Location: /estudiantes/');
        exit;
    }

    public function edit($id)
    {
        $currentUserId = $_SESSION['user_id'];
        $permissionController = new PermissionController();
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'edit_students')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT * FROM estudiantes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        $parametricTables = $this->getParametricTables();
        $isSuperAdmin = $userUtils->isSuperAdmin($currentUserId);

        ob_start();
        include '../modules/estudiantes/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update($id)
    {
        $currentUserId = $_SESSION['user_id'];
        $permissionController = new PermissionController();
        $userUtils = new UserUtils();
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_students')) {
            header('Location: /permission-denied/');
            exit;
        }

        $isSuperAdmin = $userUtils->isSuperAdmin($currentUserId);
        $empresa_id = $isSuperAdmin ? $_POST['empresa_id'] : $empresaId;

        // Obtener datos actuales del estudiante
        $query = "SELECT foto, estado FROM estudiantes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        $foto = $estudiante['foto']; // Foto actual

        // Manejo de la subida de la foto
        if (!empty($_FILES['foto']['name'])) {

            $file = $_FILES['foto'];
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $_POST['numero_documento'] . '_' . date('YmdHis') . '.' . $ext;

            $targetDir = "../files/fotos_estudiantes/";
            $targetFile = $targetDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $foto = $filename; // Asignar el nuevo nombre de la foto
            } else {
                $_SESSION['error_message'] = 'Error al subir la foto.';
                header('Location: /estudiantes/edit/' . $id);
                exit;
            }
        }

        if ($estudiante['estado'] == 5) {
            // cambiar estado a activo 1
            $estado_cambiar = 1;

            $stmt = $this->conn->prepare("
                    UPDATE clientes_nuevos
                    SET estado = 0,
                        completed_at = NOW(),
                        updated_at = NOW()
                    WHERE estudiante_id = :eid
                    AND estado = 1
                    AND empresa_id = :emp
                ");
            $stmt->execute([
                ':eid' => $id,
                ':emp' => $empresaId,
            ]);
        } else {
            $estado_cambiar = isset($_POST['estado']) ? 1 : 0;
        }


        $query = "
            UPDATE estudiantes 
            SET 
                nombres = :nombres,
                apellidos = :apellidos,
                tipo_documento = :tipo_documento,
                expedicion_departamento = :expedicion_departamento,
                expedicion_ciudad = :expedicion_ciudad,
                fecha_expedicion = :fecha_expedicion,
                grupo_sanguineo = :grupo_sanguineo,
                genero = :genero,
                fecha_nacimiento = :fecha_nacimiento,
                correo = :correo,
                celular = :celular,
                direccion_residencia = :direccion_residencia,
                direccion_oficina = :direccion_oficina,
                telefono_oficina = :telefono_oficina,
                estado_civil = :estado_civil,
                ocupacion = :ocupacion,
                jornada = :jornada,
                barrio = :barrio,
                estrato = :estrato,
                seguridad_social = :seguridad_social,
                nivel_educacion = :nivel_educacion,
                ciudad_origen = :ciudad_origen,
                discapacidad = :discapacidad,
                nombre_contacto = :nombre_contacto,
                telefono_contacto = :telefono_contacto,
                observaciones = :observaciones,
                foto = :foto,
                estado = :estado
            WHERE 
                id = :id
        ";


        // Definir variables
        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $tipo_documento = $_POST['tipo_documento'];
        $expedicion_departamento = $_POST['expedicion_departamento'];
        $expedicion_ciudad = $_POST['expedicion_ciudad'];
        $fecha_expedicion = $_POST['fecha_expedicion'];
        $grupo_sanguineo = $_POST['grupo_sanguineo'];
        $genero = $_POST['genero'];
        $fecha_nacimiento = $_POST['fecha_nacimiento'];
        $correo = $_POST['correo'];
        $celular = $_POST['celular'];
        $direccion_residencia = $_POST['direccion_residencia'];
        $direccion_oficina = $_POST['direccion_oficina'];
        $telefono_oficina = $_POST['telefono_oficina'];
        $estado_civil = $_POST['estado_civil'];
        $ocupacion = $_POST['ocupacion'];
        $jornada = $_POST['jornada'];
        $barrio = $_POST['barrio'];
        $estrato = $_POST['estrato'];
        $seguridad_social = $_POST['seguridad_social'];
        $nivel_educacion = $_POST['nivel_educacion'];
        $ciudad_origen = $_POST['ciudad_origen'];
        $discapacidad = $_POST['discapacidad'];
        $nombre_contacto = $_POST['nombre_contacto'];
        $telefono_contacto = $_POST['telefono_contacto'];
        $observaciones = $_POST['observaciones'];
        $estado = $estado_cambiar;

        // Preparar y ejecutar la declaración SQL
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':expedicion_departamento', $expedicion_departamento);
        $stmt->bindParam(':expedicion_ciudad', $expedicion_ciudad);
        $stmt->bindParam(':fecha_expedicion', $fecha_expedicion);
        $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':fecha_nacimiento', $fecha_nacimiento);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':direccion_residencia', $direccion_residencia);
        $stmt->bindParam(':direccion_oficina', $direccion_oficina);
        $stmt->bindParam(':telefono_oficina', $telefono_oficina);
        $stmt->bindParam(':estado_civil', $estado_civil);
        $stmt->bindParam(':ocupacion', $ocupacion);
        $stmt->bindParam(':jornada', $jornada);
        $stmt->bindParam(':barrio', $barrio);
        $stmt->bindParam(':estrato', $estrato);
        $stmt->bindParam(':seguridad_social', $seguridad_social);
        $stmt->bindParam(':nivel_educacion', $nivel_educacion);
        $stmt->bindParam(':ciudad_origen', $ciudad_origen);
        $stmt->bindParam(':discapacidad', $discapacidad);
        $stmt->bindParam(':nombre_contacto', $nombre_contacto);
        $stmt->bindParam(':telefono_contacto', $telefono_contacto);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        if ($estudiante['estado'] == 5) {
            $_SESSION['flash_success'] = '¡Perfil actualizado! Tu cuenta fue activada correctamente.';
            $_SESSION['estudiante_estado'] = 1;
            header('Location: /home/');
            exit;
        } else {
            $_SESSION['estudiante_modificado'] = "El estudiante se actualizó con éxito.";
            header('Location: /estudiantes/');
            exit;
        }
    }

    public function delete($id)
    {
        $currentUserId = $_SESSION['user_id'];
        $permissionController = new PermissionsController();

        if (!$permissionController->hasPermission($currentUserId, 'delete_estudiantes')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "DELETE FROM estudiantes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        header('Location: /estudiantes/');
    }

    private function getParametricTables()
    {
        $tables = [
            'param_tipo_documento' => 'tipo_documento',
            'param_grupo_sanguineo' => 'grupo_sanguineo',
            'param_genero' => 'genero',
            'param_estado_civil' => 'estado_civil',
            'param_ocupacion' => 'ocupacion',
            'param_jornada' => 'jornada',
            'param_estrato' => 'estrato',
            'param_seguridad_social' => 'seguridad_social',
            'param_nivel_educacion' => 'nivel_educacion',
            'param_discapacidad' => 'discapacidad',
            'empresas' => 'empresa'
        ];

        $parametricTables = [];
        foreach ($tables as $table => $alias) {
            $query = "SELECT id, nombre FROM $table";
            $stmt = $this->conn->query($query);
            $parametricTables[$alias] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $parametricTables;
    }

    public function detail($id)
    {
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $permissionController = new PermissionController();

        // Verificar permisos para ver estudiantes
        if (!$permissionController->hasPermission($currentUserId, 'view_students')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Consulta para obtener detalles del estudiante
        $queryEstudiante = "SELECT e.*, 
                             td.nombre as tipo_documento_nombre,
                             td.sigla as tipo_documento_sigla,
                             gs.nombre as grupo_sanguineo_nombre,
                             g.nombre as genero_nombre,
                             ec.nombre as estado_civil_nombre,
                             oc.nombre as ocupacion_nombre,
                             jo.nombre as jornada_nombre,
                             es.nombre as estrato_nombre,
                             ss.nombre as seguridad_social_nombre,
                             ne.nombre as nivel_educacion_nombre,
                             di.nombre as discapacidad_nombre,
                             em.nombre as empresa_nombre
                      FROM estudiantes e
                      LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
                      LEFT JOIN param_grupo_sanguineo gs ON e.grupo_sanguineo = gs.id
                      LEFT JOIN param_genero g ON e.genero = g.id
                      LEFT JOIN param_estado_civil ec ON e.estado_civil = ec.id
                      LEFT JOIN param_ocupacion oc ON e.ocupacion = oc.id
                      LEFT JOIN param_jornada jo ON e.jornada = jo.id
                      LEFT JOIN param_estrato es ON e.estrato = es.id
                      LEFT JOIN param_seguridad_social ss ON e.seguridad_social = ss.id
                      LEFT JOIN param_nivel_educacion ne ON e.nivel_educacion = ne.id
                      LEFT JOIN param_discapacidad di ON e.discapacidad = di.id
                      LEFT JOIN empresas em ON e.empresa_id = em.id
                      WHERE e.id = :id";
        $stmtEstudiante = $this->conn->prepare($queryEstudiante);
        $stmtEstudiante->bindParam(':id', $id);
        $stmtEstudiante->execute();
        $estudiante = $stmtEstudiante->fetch(PDO::FETCH_ASSOC);

        if (!$estudiante) {
            $_SESSION['error_message'] = 'Estudiante no encontrado.';
            header('Location: /estudiantes/');
            exit;
        }

        $queryMatricula = "
                SELECT 
                    m.*, 
                    ts.nombre AS tipo_solicitud_nombre,
                    c.nombre AS convenio_nombre,
                    GROUP_CONCAT(p.nombre SEPARATOR ', ') AS programas_nombre
                FROM 
                    matriculas m
                LEFT JOIN 
                    param_tipos_solicitud ts ON m.tipo_solicitud_id = ts.id
                LEFT JOIN 
                    convenios c ON m.convenio_id = c.id
                LEFT JOIN 
                    matricula_programas mp ON m.id = mp.matricula_id
                LEFT JOIN 
                    programas p ON mp.programa_id = p.id
                WHERE 
                    m.estudiante_id = :estudiante_id
                AND 
                    m.estado = 1
                GROUP BY 
                    m.id
            ";

        $stmtMatricula = $this->conn->prepare($queryMatricula);
        $stmtMatricula->bindParam(':estudiante_id', $id, PDO::PARAM_INT);
        $stmtMatricula->execute();
        $matriculas = $stmtMatricula->fetchAll(PDO::FETCH_ASSOC); // Obtener todas las matrículas

        foreach ($matriculas as &$matricula) {
            $matricula['clases_practicas'] = $this->getClasesPracticasPorMatricula($matricula['id']);
            $matricula['abonos'] = $this->getAbonosPorMatricula($matricula['id']);
            $matricula['total_abonos'] = array_sum(array_column($matricula['abonos'], 'valor'));
        }

        ob_start();
        include '../modules/estudiantes/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    private function getClasesPracticasPorMatricula($matriculaId)
    {
        $query = "
            SELECT 
                cp.*, 
                v.placa AS vehiculo_placa,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre
            FROM 
                clases_practicas cp
            LEFT JOIN 
                vehiculos v ON cp.vehiculo_id = v.id
            LEFT JOIN 
                instructores i ON cp.instructor_id = i.id
            WHERE 
                cp.matricula_id = :matricula_id
            ORDER BY 
                cp.fecha, cp.hora_inicio
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getAbonosPorMatricula($matriculaId)
    {
        $query = "
            SELECT 
                i.valor, 
                i.fecha, 
                mi.nombre AS motivo_ingreso, 
                ti.nombre AS tipo_ingreso
            FROM 
                financiero_ingresos i
            LEFT JOIN 
                param_motivos_financiero_ingresos mi ON i.motivo_ingreso_id = mi.id
            LEFT JOIN 
                param_tipos_financiero_ingresos ti ON i.tipo_ingreso_id = ti.id
            WHERE 
                i.matricula_id = :matricula_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function cuenta()
    {
        $currentUserId = $_SESSION['user_id'];
        $currentUserRole = $_SESSION['user_role'];

        // Verificar el permiso para ver la cuenta del estudiante
        $permissionController = new PermissionController();
        if (!$permissionController->hasPermission($currentUserId, 'view_cuenta')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener la información del estudiante
        $query = "SELECT e.*, 
            td.nombre AS tipo_documento_nombre, 
            gs.nombre AS grupo_sanguineo_nombre, 
            g.nombre AS genero_nombre, 
            ec.nombre AS estado_civil_nombre, 
            oc.nombre AS ocupacion_nombre, 
            jo.nombre AS jornada_nombre, 
            es.nombre AS estrato_nombre, 
            ss.nombre AS seguridad_social_nombre, 
            ne.nombre AS nivel_educacion_nombre, 
            di.nombre AS discapacidad_nombre
            FROM estudiantes e
            LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
            LEFT JOIN param_grupo_sanguineo gs ON e.grupo_sanguineo = gs.id
            LEFT JOIN param_genero g ON e.genero = g.id
            LEFT JOIN param_estado_civil ec ON e.estado_civil = ec.id
            LEFT JOIN param_ocupacion oc ON e.ocupacion = oc.id
            LEFT JOIN param_jornada jo ON e.jornada = jo.id
            LEFT JOIN param_estrato es ON e.estrato = es.id
            LEFT JOIN param_seguridad_social ss ON e.seguridad_social = ss.id
            LEFT JOIN param_nivel_educacion ne ON e.nivel_educacion = ne.id
            LEFT JOIN param_discapacidad di ON e.discapacidad = di.id
            WHERE e.id = :estudiante_id";


        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $_SESSION['estudiante_id']);
        $stmt->execute();
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);



        // ## MATRICULAS ===============================================

        $queryMatricula = "
                SELECT 
                    m.*, 
                    ts.nombre AS tipo_solicitud_nombre,
                    c.nombre AS convenio_nombre,
                    GROUP_CONCAT(p.nombre SEPARATOR ', ') AS programas_nombre
                FROM 
                    matriculas m
                LEFT JOIN 
                    param_tipos_solicitud ts ON m.tipo_solicitud_id = ts.id
                LEFT JOIN 
                    convenios c ON m.convenio_id = c.id
                LEFT JOIN 
                    matricula_programas mp ON m.id = mp.matricula_id
                LEFT JOIN 
                    programas p ON mp.programa_id = p.id
                WHERE 
                    m.estudiante_id = :estudiante_id
                AND 
                    m.estado = 1
                GROUP BY 
                    m.id
            ";

        $stmtMatricula = $this->conn->prepare($queryMatricula);
        $stmtMatricula->bindParam(':estudiante_id', $_SESSION['estudiante_id'], PDO::PARAM_INT);
        $stmtMatricula->execute();
        $matriculas = $stmtMatricula->fetchAll(PDO::FETCH_ASSOC); // Obtener todas las matrículas


        ## Clases prácticas =====================================================
        foreach ($matriculas as &$matricula) {
            $matricula['clases_practicas'] = $this->getClasesPracticasPorMatricula($matricula['id']);
            $matricula['abonos'] = $this->getAbonosPorMatricula($matricula['id']);
            $matricula['total_abonos'] = array_sum(array_column($matricula['abonos'], 'valor'));
        }



        ob_start();
        include '../modules/estudiantes/views/cuenta.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function matricula()
    {
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $permissionController = new PermissionController();

        if (!$permissionController->hasPermission($currentUserId, 'view_matriculas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener la matrícula del estudiante
        $queryMatricula = "SELECT * FROM matriculas WHERE estudiante_id = :estudiante_id AND empresa_id = :empresa_id LIMIT 1";
        $stmtMatricula = $this->conn->prepare($queryMatricula);
        $stmtMatricula->bindParam(':estudiante_id', $currentUserId);
        $stmtMatricula->bindParam(':empresa_id', $empresaId);
        $stmtMatricula->execute();
        $matricula = $stmtMatricula->fetch(PDO::FETCH_ASSOC);

        if ($matricula) {
            // Obtener los programas asociados a la matrícula
            $queryProgramas = "SELECT p.*
                           FROM programas p
                           INNER JOIN matricula_programa mp ON p.id = mp.programa_id
                           WHERE mp.matricula_id = :matricula_id";
            $stmtProgramas = $this->conn->prepare($queryProgramas);
            $stmtProgramas->bindParam(':matricula_id', $matricula['id']);
            $stmtProgramas->execute();
            $programas = $stmtProgramas->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $programas = [];
        }

        ob_start();
        include '../modules/estudiantes/views/matriculas.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function search()
    {
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $permissionController = new PermissionController();

        if (!$permissionController->hasPermission($currentUserId, 'view_students')) {
            header('Location: /permission-denied/');
            exit;
        }

        $numero_documento = $_POST['numero_documento'] ?? '';

        if (empty($numero_documento)) {
            // Redirect to index if no search term is provided
            header('Location: /estudiantes/');
            exit;
        }

        if ($this->userUtils->isSuperAdmin($currentUserId)) {
            $query = "SELECT e.*, 
                         td.nombre as tipo_documento_nombre,
                         gs.nombre as grupo_sanguineo_nombre,
                         g.nombre as genero_nombre,
                         ec.nombre as estado_civil_nombre,
                         oc.nombre as ocupacion_nombre,
                         jo.nombre as jornada_nombre,
                         es.nombre as estrato_nombre,
                         ss.nombre as seguridad_social_nombre,
                         ne.nombre as nivel_educacion_nombre,
                         di.nombre as discapacidad_nombre,
                         em.nombre as empresa_nombre
                  FROM estudiantes e
                  LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
                  LEFT JOIN param_grupo_sanguineo gs ON e.grupo_sanguineo = gs.id
                  LEFT JOIN param_genero g ON e.genero = g.id
                  LEFT JOIN param_estado_civil ec ON e.estado_civil = ec.id
                  LEFT JOIN param_ocupacion oc ON e.ocupacion = oc.id
                  LEFT JOIN param_jornada jo ON e.jornada = jo.id
                  LEFT JOIN param_estrato es ON e.estrato = es.id
                  LEFT JOIN param_seguridad_social ss ON e.seguridad_social = ss.id
                  LEFT JOIN param_nivel_educacion ne ON e.nivel_educacion = ne.id
                  LEFT JOIN param_discapacidad di ON e.discapacidad = di.id
                  LEFT JOIN empresas em ON e.empresa_id = em.id
                  WHERE e.numero_documento = :numero_documento";
            $stmt = $this->conn->prepare($query);
        } else {
            $query = "SELECT e.*, 
                         td.nombre as tipo_documento_nombre,
                         gs.nombre as grupo_sanguineo_nombre,
                         g.nombre as genero_nombre,
                         ec.nombre as estado_civil_nombre,
                         oc.nombre as ocupacion_nombre,
                         jo.nombre as jornada_nombre,
                         es.nombre as estrato_nombre,
                         ss.nombre as seguridad_social_nombre,
                         ne.nombre as nivel_educacion_nombre,
                         di.nombre as discapacidad_nombre,
                         em.nombre as empresa_nombre
                  FROM estudiantes e
                  LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
                  LEFT JOIN param_grupo_sanguineo gs ON e.grupo_sanguineo = gs.id
                  LEFT JOIN param_genero g ON e.genero = g.id
                  LEFT JOIN param_estado_civil ec ON e.estado_civil = ec.id
                  LEFT JOIN param_ocupacion oc ON e.ocupacion = oc.id
                  LEFT JOIN param_jornada jo ON e.jornada = jo.id
                  LEFT JOIN param_estrato es ON e.estrato = es.id
                  LEFT JOIN param_seguridad_social ss ON e.seguridad_social = ss.id
                  LEFT JOIN param_nivel_educacion ne ON e.nivel_educacion = ne.id
                  LEFT JOIN param_discapacidad di ON e.discapacidad = di.id
                  LEFT JOIN empresas em ON e.empresa_id = em.id
                  WHERE e.numero_documento = :numero_documento AND e.empresa_id = :empresa_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':empresa_id', $empresaId);
        }

        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($estudiantes)) {
            $_SESSION['error_message'] = 'No se encontró un estudiante con ese número de documento.';
            header('Location: /estudiantes/');
            exit;
        }

        ob_start();
        include '../modules/estudiantes/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function buscar()
    { 
        // ----------------------------------------------------------
        // 🔹 Contexto
        // ----------------------------------------------------------
        $empresaId = $_SESSION['empresa_id'];
        $termino = trim($_POST['termino'] ?? '');

        // Si el término es muy corto, devolver vacío
        if (strlen($termino) < 2) {
            echo json_encode([]);
            return;
        }

        // ----------------------------------------------------------
        // 🔹 Query principal de búsqueda
        // ----------------------------------------------------------
        $sql = "
            SELECT
                e.id,
                e.nombres,
                e.apellidos,
                e.numero_documento,
                e.foto
            FROM estudiantes e
            WHERE e.empresa_id = :empresa_id
            AND (
                    e.numero_documento = :documento
                OR e.nombres LIKE :like
                OR e.apellidos LIKE :like
                OR CONCAT(e.nombres, ' ', e.apellidos) LIKE :like
            )
            ORDER BY e.nombres, e.apellidos
            LIMIT 20
        ";

        $stmt = $this->conn->prepare($sql);

        $like = '%' . $termino . '%';

        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->bindParam(':documento',  $termino,   PDO::PARAM_STR);
        $stmt->bindParam(':like',       $like,      PDO::PARAM_STR);

        $stmt->execute();
        $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ----------------------------------------------------------
        // 🔹 Si no hay resultados → devolver array vacío (NO 404)
        // ----------------------------------------------------------
        if (!$estudiantes) {
            echo json_encode([]);
            return;
        }

        // ----------------------------------------------------------
        // 🔹 Cargar matrículas por estudiante
        // ----------------------------------------------------------
        foreach ($estudiantes as &$estudiante) {

            $sqlMatriculas = "
                SELECT
                    m.id,
                    p.id     AS programa_id,
                    p.nombre AS programa
                FROM matriculas m
                LEFT JOIN matricula_programas mp ON mp.matricula_id = m.id
                LEFT JOIN programas p            ON p.id = mp.programa_id
                WHERE m.estudiante_id = :estudiante_id
            ";

            $stmtMat = $this->conn->prepare($sqlMatriculas);
            $stmtMat->bindParam(':estudiante_id', $estudiante['id'], PDO::PARAM_INT);
            $stmtMat->execute();

            $estudiante['matriculas'] = $stmtMat->fetchAll(PDO::FETCH_ASSOC);
        }

        // ----------------------------------------------------------
        // 🔹 Respuesta final
        // ----------------------------------------------------------
        echo json_encode($estudiantes);
    }

    public function buscarEstudiantePorNombre()
    {
        if (!isset($_POST['termino']) || trim($_POST['termino']) === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Término vacío']);
            return;
        }

        $empresaId = $_SESSION['empresa_id'];
        $termino = strtoupper(trim($_POST['termino']));
        $palabras = preg_split('/\s+/', $termino);
        $numPalabras = count($palabras);

        $params = [];

        if (is_numeric($termino)) {
            $query = "SELECT e.id, e.nombres, e.apellidos, e.numero_documento, e.foto 
                  FROM estudiantes e 
                  WHERE e.numero_documento = :documento 
                  AND e.empresa_id = :empresa_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':documento', $termino);
            $stmt->bindParam(':empresa_id', $empresaId);
            $stmt->execute();
            $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Armar combinaciones
            $query = "SELECT e.id, e.nombres, e.apellidos, e.numero_documento, e.foto 
                  FROM estudiantes e 
                  WHERE (";

            $condiciones = [];

            if ($numPalabras === 1) {
                $condiciones[] = "e.nombres LIKE :word OR e.apellidos LIKE :word";
                $params[':word'] = '%' . $palabras[0] . '%';
            }

            if ($numPalabras === 2) {
                $condiciones[] = "(e.nombres LIKE :n1 AND e.apellidos LIKE :a1)";
                $params[':n1'] = '%' . $palabras[0] . '%';
                $params[':a1'] = '%' . $palabras[1] . '%';

                // Otra posible combinación: ambas palabras en nombres
                $condiciones[] = "e.nombres LIKE :n_completo";
                $params[':n_completo'] = '%' . implode(' ', $palabras) . '%';
            }

            if ($numPalabras >= 3) {
                $nombre = implode(' ', array_slice($palabras, 0, 2)); // mateo pepe
                $apellido = implode(' ', array_slice($palabras, 2)); // rodriguez rivera

                $condiciones[] = "(e.nombres LIKE :n2 AND e.apellidos LIKE :a2)";
                $params[':n2'] = '%' . $nombre . '%';
                $params[':a2'] = '%' . $apellido . '%';
            }

            $query .= implode(' OR ', $condiciones) . ") AND e.empresa_id = :empresa_id";

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindParam(':empresa_id', $empresaId);
            $stmt->execute();
            $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($estudiantes) {
            foreach ($estudiantes as &$estudiante) {
                $q = "SELECT m.id, p.id as programa_id, p.nombre AS programa 
                  FROM matriculas m
                  LEFT JOIN matricula_programas mp ON m.id = mp.matricula_id
                  LEFT JOIN programas p ON mp.programa_id = p.id
                  WHERE m.estudiante_id = :estudiante_id";

                $stmt = $this->conn->prepare($q);
                $stmt->bindParam(':estudiante_id', $estudiante['id']);
                $stmt->execute();
                $estudiante['matriculas'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            echo json_encode($estudiantes);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Estudiante no encontrado']);
        }
    }

    public function buscarEstudiantes()
    {
        try {
            // Obtener el término de búsqueda desde el cuerpo de la solicitud POST
            $data = json_decode(file_get_contents('php://input'), true);
            $termino = isset($data['termino']) ? trim($data['termino']) : '';

            $empresaId = $_SESSION['empresa_id']; // Obtener el ID de la empresa desde la sesión

            // Validar que el término no esté vacío
            if (empty($termino)) {
                echo json_encode(['error' => 'Debe proporcionar un término de búsqueda.']);
                return;
            }

            // Probar la búsqueda usando LIKE para coincidencias parciales
            $query = "SELECT e.id, e.codigo, e.nombres, e.apellidos, e.numero_documento
          FROM estudiantes e
          WHERE (e.numero_documento LIKE :termino 
                 OR e.nombres LIKE :nombre 
                 OR e.apellidos LIKE :apellido)
          AND e.empresa_id = :empresa_id";

            $stmt = $this->conn->prepare($query);
            $likeTerm = "%$termino%";
            $stmt->bindParam(':termino', $likeTerm);
            $stmt->bindParam(':nombre', $likeTerm);
            $stmt->bindParam(':apellido', $likeTerm); // Nuevo parámetro para buscar por apellido
            $stmt->bindParam(':empresa_id', $empresaId);
            $stmt->execute();
            $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($estudiantes) {
                echo json_encode($estudiantes);
            } else {
                echo json_encode(['error' => 'Estudiante no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500); // Enviar código de error 500 al cliente
            echo json_encode(['error' => 'Error en la búsqueda: ' . $e->getMessage()]);
        }
    }

    public function detalle($id)
    {
        $query = "SELECT 
                e.id, 
                e.numero_documento AS cedula, 
                e.nombres, 
                e.apellidos, 
                e.foto,
                m.id AS matricula_id, 
                p.id AS programa_id, 
                p.nombre AS programa
            FROM estudiantes e
            LEFT JOIN matriculas m ON e.id = m.estudiante_id
            LEFT JOIN matricula_programas mp ON m.id = mp.matricula_id
            LEFT JOIN programas p ON mp.programa_id = p.id
            WHERE e.id = :id;
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($resultados) {
            $data = [
                'cedula' => $resultados[0]['cedula'],
                'foto' => $resultados[0]['foto'],
                'nombres' => $resultados[0]['nombres'],
                'apellidos' => $resultados[0]['apellidos'],
                'matriculas' => []
            ];

            foreach ($resultados as $fila) {
                if (!empty($fila['matricula_id'])) {
                    $data['matriculas'][] = [
                        'matricula_id' => $fila['matricula_id'],
                        'programa_id' => $fila['programa_id'],
                        'programa' => $fila['programa']
                    ];
                }
            }

            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Estudiante no encontrado']);
        }
    }

    public function detallePrograma($programaId, $matriculaId)
    {
        // Obtener detalles del programa
        $query = "SELECT horas_practicas, tipo_vehiculo_id FROM programas WHERE id = :programaId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programaId', $programaId);
        $stmt->execute();
        $programa = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($programa) {

            // Calcular horas cursadas
            $query = "SELECT SUM(TIMESTAMPDIFF(HOUR, hora_inicio, hora_fin)) AS horas_cursadas 
                      FROM clases_practicas 
                      WHERE programa_id = :programaId AND matricula_id = :matriculaId";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':programaId', $programaId);
            $stmt->bindParam(':matriculaId', $matriculaId);
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            // Obtener listado de clases cursadas
            // $queryClases = "SELECT 
            //                     id, nombre, estado_id, fecha, hora_inicio, hora_fin, 
            //                     vehiculo_id, instructor_id, observaciones
            //                 FROM clases_practicas 
            //                 WHERE programa_id = :programaId AND matricula_id = :matriculaId 
            //                 ORDER BY fecha, hora_inicio";

            $queryClases = "SELECT 
            id, nombre, estado_id, fecha, hora_inicio, hora_fin, 
            vehiculo_id, instructor_id, observaciones,
            TIMESTAMPDIFF(HOUR, hora_inicio, hora_fin) AS numero_horas
        FROM clases_practicas 
        WHERE programa_id = :programaId AND matricula_id = :matriculaId 
        ORDER BY fecha, hora_inicio";



            $stmtClases = $this->conn->prepare($queryClases);
            $stmtClases->bindParam(':programaId', $programaId);
            $stmtClases->bindParam(':matriculaId', $matriculaId);
            $stmtClases->execute();
            $clasesCursadas = $stmtClases->fetchAll(PDO::FETCH_ASSOC);

            // Obtener resumen de horas por clase_programa
            $queryResumen = "SELECT cp.nombre_clase, SUM(TIMESTAMPDIFF(HOUR, c.hora_inicio, c.hora_fin)) AS horas_cursadas
                             FROM clases_practicas c
                             JOIN clases_programas cp ON c.clase_programa_id = cp.id
                             WHERE c.programa_id = :programaId AND c.matricula_id = :matriculaId
                             GROUP BY c.clase_programa_id, cp.nombre_clase
                             ORDER BY cp.nombre_clase";

            $stmtResumen = $this->conn->prepare($queryResumen);
            $stmtResumen->bindParam(':programaId', $programaId);
            $stmtResumen->bindParam(':matriculaId', $matriculaId);
            $stmtResumen->execute();
            $resumenPorTema = $stmtResumen->fetchAll(PDO::FETCH_ASSOC);

            $data = [
                'horas_practicas' => $programa['horas_practicas'],
                'horas_cursadas' => $resultado['horas_cursadas'] ?? 0,
                'tipo_vehiculo_id' => $programa['tipo_vehiculo_id'],
                'clases_cursadas' => $clasesCursadas,
                'resumen_por_tema' => $resumenPorTema
            ];

            echo json_encode($data);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Programa no encontrado']);
        }
    }

    public function seguimientoClases()
    {
        // Obtener el ID del usuario y el número de documento (cédula) del estudiante
        $currentUserId = $_SESSION['user_id'];
        $currentUsername = $_SESSION['username']; // Esto es la cédula del estudiante

        // Verificar el permiso para ver el seguimiento de clases
        $permissionController = new PermissionController();
        if (!$permissionController->hasPermission($currentUserId, 'view_seguimiento_clases') || $_SESSION['user_role'] !== 'EST') {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener el ID del estudiante usando el número de documento (cédula)
        $query = "SELECT id FROM estudiantes WHERE numero_documento = :numero_documento";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':numero_documento', $currentUsername);
        $stmt->execute();
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$estudiante) {
            echo "<p>No se encontró el estudiante con la cédula: $currentUsername.</p>";
            exit;
        }

        $estudianteId = $estudiante['id'];

        // Obtener los IDs de las matrículas asociadas al estudiante
        $query = "SELECT id FROM matriculas WHERE estudiante_id = :estudiante_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudianteId);
        $stmt->execute();
        $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($matriculas)) {
            echo "<p>No se encontraron matrículas para el estudiante.</p>";
            return;
        }

        // Inicializar el array de seguimiento
        $seguimiento = [];

        // Obtener los programas asociados a cada matrícula
        foreach ($matriculas as $matricula) {
            $matriculaId = $matricula['id'];

            // Obtener los programas asociados a esta matrícula
            $query = "SELECT mp.programa_id, p.id AS programa_id, p.nombre AS programa_nombre, p.descripcion AS programa_descripcion
                  FROM matricula_programas mp
                  JOIN programas p ON mp.programa_id = p.id
                  WHERE mp.matricula_id = :matricula_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':matricula_id', $matriculaId);
            $stmt->execute();
            $programasMatricula = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($programasMatricula)) {
                echo "<p>No se encontraron programas para la matrícula ID: $matriculaId.</p>";
                continue;
            }

            // Añadir los programas al seguimiento
            foreach ($programasMatricula as $programa) {
                if (!array_key_exists($programa['programa_id'], $seguimiento)) {
                    $seguimiento[$programa['programa_id']] = [
                        'programa' => [
                            'programa_id' => $programa['programa_id'],
                            'programa_nombre' => $programa['programa_nombre'],
                            'programa_descripcion' => $programa['programa_descripcion'],
                        ],
                        'clases' => [] // Añadir un array para las clases
                    ];

                    // Obtener las clases definidas en el programa
                    $queryClasesPrograma = "SELECT id, nombre_clase, duracion
                                        FROM clases_programas
                                        WHERE programa_id = :programa_id";
                    $stmtClasesPrograma = $this->conn->prepare($queryClasesPrograma);
                    $stmtClasesPrograma->bindParam(':programa_id', $programa['programa_id']);
                    $stmtClasesPrograma->execute();
                    $clasesPrograma = $stmtClasesPrograma->fetchAll(PDO::FETCH_ASSOC);

                    // Obtener las clases prácticas tomadas por el estudiante en este programa
                    $queryClasesPracticas = "SELECT cp.id AS clase_practica_id, cp.nombre, cp.fecha, cp.hora_inicio, cp.hora_fin, cp.estado_id
                                         FROM clases_practicas cp
                                         JOIN matriculas m ON cp.matricula_id = m.id
                                         WHERE m.estudiante_id = :estudiante_id AND cp.programa_id = :programa_id";
                    $stmtClasesPracticas = $this->conn->prepare($queryClasesPracticas);
                    $stmtClasesPracticas->bindParam(':estudiante_id', $estudianteId);
                    $stmtClasesPracticas->bindParam(':programa_id', $programa['programa_id']);
                    $stmtClasesPracticas->execute();
                    $clasesPracticas = $stmtClasesPracticas->fetchAll(PDO::FETCH_ASSOC);

                    // Verificar cuáles clases del programa están completas y cuáles faltan
                    foreach ($clasesPrograma as &$clase) {
                        $clase['completada'] = false;
                        foreach ($clasesPracticas as $clasePractica) {
                            if ($clasePractica['nombre'] === $clase['nombre_clase']) {
                                $clase['completada'] = true;
                                $clase['fecha'] = $clasePractica['fecha'];
                                $clase['hora_inicio'] = $clasePractica['hora_inicio'];
                                $clase['hora_fin'] = $clasePractica['hora_fin'];
                                break;
                            }
                        }
                    }

                    $seguimiento[$programa['programa_id']]['clases'] = $clasesPrograma;
                }
            }
        }

        if (empty($seguimiento)) {
            echo "<p>No se encontraron programas para el estudiante.</p>";
            return;
        }

        // Renderizar la vista
        ob_start();
        include '../modules/estudiantes/views/seguimiento_clases.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function verificarDocumento()
    {
        // Obtener datos del POST y sesión
        $numero_documento = $_POST['numero_documento'];
        $empresa_id = $_SESSION['empresa_id'];

        // Validar si los datos existen
        if (empty($numero_documento) || empty($empresa_id)) {
            echo json_encode(['status' => 'error', 'message' => 'Datos incompletos']);
            exit;
        }

        // Consulta SQL que filtra por empresa
        $query = "SELECT COUNT(*) as count 
                  FROM estudiantes 
                  WHERE numero_documento = :numero_documento 
                  AND empresa_id = :empresa_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':numero_documento', $numero_documento);
        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si el documento ya existe en la empresa
        if ($result['count'] > 0) {
            echo json_encode(['status' => 'exists', 'message' => 'El documento ya está registrado en esta empresa.']);
        } else {
            echo json_encode(['status' => 'available', 'message' => 'Documento disponible.']);
        }
    }

    public function verificarCorreo()
    {
        $correo = $_POST['correo'] ?? null;
        $empresaId = $_SESSION['empresa_id'] ?? null; // Obtener el ID de la empresa de la sesión

        if ($correo && $empresaId) {
            $query = "SELECT COUNT(*) as total 
                      FROM estudiantes 
                      WHERE correo = :correo AND empresa_id = :empresa_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                echo json_encode(['status' => 'exists']);
            } else {
                echo json_encode(['status' => 'available']);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Correo o empresa no proporcionados.'
            ]);
        }
    }

    public function obtenerIniciales($nombres, $apellidos)
    {
        $nombresArray = explode(' ', $nombres);
        $apellidosArray = explode(' ', $apellidos);
        $iniciales = '';

        foreach ($nombresArray as $nombre) {
            $iniciales .= substr($nombre, 0, 1);
        }

        foreach ($apellidosArray as $apellido) {
            $iniciales .= substr($apellido, 0, 1);
        }

        return $iniciales;
    }

    /**
     * Muestra el calendario de clases teóricas para el estudiante autenticado.
     */
    public function calendarioClasesTeoricas($date = null)
    {
        $programa_id = true;
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId     = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_teoricas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Estudiante del usuario
        $estudianteStmt = $this->conn->prepare("SELECT estudiante_id FROM users WHERE id = :uid");
        $estudianteStmt->bindValue(':uid', $currentUserId, PDO::PARAM_INT);
        $estudianteStmt->execute();
        $estudianteId = $estudianteStmt->fetchColumn();
        if (!$estudianteId) {
            header('Location: /no-student-found/');
            exit;
        }

        // 1) Todas las matrículas ACTIVAS del estudiante en esta empresa
        $mStmt = $this->conn->prepare("
                SELECT id
                FROM matriculas
                WHERE estudiante_id = :est
                AND empresa_id    = :emp
                AND estado        = 1
            ");
        $mStmt->bindValue(':est', $estudianteId, PDO::PARAM_INT);
        $mStmt->bindValue(':emp', $empresaId,   PDO::PARAM_INT);
        $mStmt->execute();
        $matriculaIds = $mStmt->fetchAll(PDO::FETCH_COLUMN);
        $matriculaIds = array_map('intval', $matriculaIds);

        // 2) Programas de esas matrículas
        if (!empty($matriculaIds)) {
            $inM = implode(',', $matriculaIds);

            $pStmt = $this->conn->prepare("
                SELECT DISTINCT mp.programa_id
                FROM matricula_programas mp
                WHERE mp.matricula_id IN ($inM)
            ");

            $pStmt->execute();
            $programaIds = array_map('intval', $pStmt->fetchAll(PDO::FETCH_COLUMN));
        } else {
            $programaIds = [];
        }

        // Si no hay programas, renderiza vista vacía
        if (empty($programaIds)) {
            $programa_id = false;
            ob_start();
            include '../modules/estudiantes/views/calendario_clases_teoricas.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
            exit;
        }

        // 3) Semana a mostrar
        $date      = $date ?: date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('monday this week', strtotime($date)));
        $endDate   = date('Y-m-d', strtotime('sunday this week', strtotime($date)));

        $programIdsString = implode(',', $programaIds);

        // Construye cláusula para JOIN de cte (todas las matrículas) o bloquea si vacío
        $cteJoinMatriculas = !empty($matriculaIds)
            ? "AND cte.matricula_id IN (" . implode(',', $matriculaIds) . ")"
            : "AND 1=0"; // evita unir inscripciones de otros estudiantes

        // 4) Query final (todas las matrículas + todos los programas del estudiante)
        $query = "
                SELECT 
                    ct.id AS clase_id,
                    ct.fecha,
                    ct.hora_inicio,
                    ct.hora_fin,
                    ct.estado_id,
                    ct.observaciones,
                    COALESCE(p.nombre, '') AS programa_nombre,
                    COALESCE(a.nombre, '') AS aula_nombre,
                    CONCAT(COALESCE(i.nombres,''), ' ', COALESCE(i.apellidos,'')) AS instructor_nombre_completo,
                    COALESCE(ctt.nombre, '') AS tema_nombre,
                    CASE WHEN cte.id IS NULL THEN 0 ELSE 1 END AS agendado,
                    cte.matricula_id AS matricula_asignada,
                    COALESCE(cte.asistencia, 0) AS asistencia,
                    i.id AS instructor_id
                FROM clases_teoricas ct
                LEFT JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
                LEFT JOIN programas p ON ct.programa_id = p.id
                LEFT JOIN aulas a ON ct.aula_id = a.id
                LEFT JOIN instructores i ON ct.instructor_id = i.id
                LEFT JOIN clases_teoricas_estudiantes cte
                    ON cte.clase_teorica_id = ct.id
                    $cteJoinMatriculas
                WHERE ct.fecha BETWEEN :start_date AND :end_date
                AND ct.empresa_id = :empresa_id
                AND ct.programa_id IN ($programIdsString)
                ORDER BY ct.fecha, ct.hora_inicio
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindValue(':end_date',   $endDate,   PDO::PARAM_STR);
        $stmt->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Render
        ob_start();
        include '../modules/estudiantes/views/calendario_clases_teoricas.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    /**
     * Recupera los IDs de los programas a los que un estudiante está matriculado.
     * 
     * Esta función realiza una consulta SQL que se une a las tablas 'matriculas' y 'matricula_programas'
     * para obtener los IDs de los programas asociados a las matrículas activas del estudiante especificado.
     * Solo incluye las matrículas que están en un estado 'activo', asegurándose de que se consideren
     * solo las matrículas actuales y relevantes.
     *
     * @param int $estudianteId El ID del estudiante para el cual recuperar los programas.
     * @return array Una lista de IDs de programas a los que el estudiante está matriculado.
     * 
     */
    public function obtenerProgramasEstudiante($estudianteId)
    {
        // Definición de la consulta SQL para obtener los programa_id de las matrículas activas del estudiante
        $query = "
            SELECT mp.programa_id
            FROM matriculas m
            JOIN matricula_programas mp ON m.id = mp.matricula_id
            WHERE m.estudiante_id = :estudianteId
            AND m.estado = '1'  
        ";

        // Preparación de la consulta SQL
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudianteId', $estudianteId, PDO::PARAM_INT);

        // Ejecución de la consulta
        $stmt->execute();

        // Recuperación de los IDs de los programas a los que el estudiante está matriculado
        $programaIds = $stmt->fetchAll(PDO::FETCH_COLUMN, 0); // Obtiene solo la columna de programa_id

        // Devuelve los IDs de los programas
        return $programaIds;
    }

    public function obtenerDetalleClase($claseId)
    {
        $stmt = $this->conn->prepare("
            SELECT 
                ct.id,
                ct.programa_id,
                ct.fecha,
                ct.hora_inicio,
                ct.hora_fin,
                a.nombre AS aula_nombre,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre_completo,
                i.foto AS instructor_foto,
                p.nombre AS programa_nombre,
                ctt.nombre AS tema_nombre
            FROM clases_teoricas ct
            LEFT JOIN aulas a ON ct.aula_id = a.id
            LEFT JOIN instructores i ON ct.instructor_id = i.id
            LEFT JOIN clases_teoricas_temas ctt ON ct.tema_id = ctt.id
            LEFT JOIN programas p ON ctt.clase_teorica_programa_id = p.id
            WHERE ct.id = :id
        ");

        $stmt->bindParam(':id', $claseId, PDO::PARAM_INT);
        $stmt->execute();
        $clase = $stmt->fetch(PDO::FETCH_ASSOC);

        // Retornar los datos como JSON
        header('Content-Type: application/json');
        echo json_encode($clase);
    }

    public function agendarClaseTeorica()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /estudiantes-agenda-teoricas");
            exit();
        }

        $claseTeoricaId  = (int)($_POST['clase_teorica_id'] ?? 0);
        $calendarioFecha = $_POST['calendario_fecha'] ?? null;

        try {
            $userId    = $_SESSION['user_id'] ?? null;
            $empresaId = $_SESSION['empresa_id'] ?? null;

            if (!$userId || !$empresaId) {
                throw new Exception("Sesión inválida.");
            }

            // ----------------------------------------------------------
            // 1. Obtener estudiante
            // ----------------------------------------------------------
            $qEst = $this->conn->prepare("
                SELECT estudiante_id 
                FROM users 
                WHERE id = :uid
            ");
            $qEst->execute([':uid' => $userId]);
            $estudianteId = $qEst->fetchColumn();

            if (!$estudianteId) {
                throw new Exception("No se encontró el estudiante asociado.");
            }

            // ----------------------------------------------------------
            // 2. Obtener clase base
            // ----------------------------------------------------------
            $qClase = $this->conn->prepare("
                SELECT 
                    ct.*,
                    a.capacidad,
                    ctt.nombre AS tema_nombre
                FROM clases_teoricas ct
                INNER JOIN aulas a ON a.id = ct.aula_id
                INNER JOIN clases_teoricas_temas ctt ON ctt.id = ct.tema_id
                WHERE ct.id = :cid
                AND ct.empresa_id = :emp
            ");
            $qClase->execute([
                ':cid' => $claseTeoricaId,
                ':emp' => $empresaId
            ]);

            $clase = $qClase->fetch(PDO::FETCH_ASSOC);
            if (!$clase) {
                throw new Exception("Clase no encontrada.");
            }

            // ----------------------------------------------------------
            // 3. VALIDACIÓN: NO repetir tema (inscrito o visto)
            // ----------------------------------------------------------
            $qTema = $this->conn->prepare("
                SELECT 1
                FROM clases_teoricas_estudiantes cte
                INNER JOIN clases_teoricas ct ON ct.id = cte.clase_teorica_id
                INNER JOIN matriculas m ON m.id = cte.matricula_id
                WHERE m.estudiante_id = :est
                AND ct.tema_id = :tema
                LIMIT 1
            ");
            $qTema->execute([
                ':est'  => $estudianteId,
                ':tema' => $clase['tema_id']
            ]);

            if ($qTema->fetchColumn()) {
                throw new Exception("No puedes agendar este tema porque ya lo tienes inscrito o ya lo cursaste.");
            }

            // ----------------------------------------------------------
            // 4. Detectar clases equivalentes (evento físico)
            // ----------------------------------------------------------
            $qEquiv = $this->conn->prepare("
                SELECT ct.id
                FROM clases_teoricas ct
                INNER JOIN clases_teoricas_temas t2 ON t2.id = ct.tema_id
                WHERE ct.empresa_id    = :emp
                AND ct.aula_id       = :aula
                AND ct.instructor_id = :instructor
                AND ct.fecha         = :fecha
                AND ct.hora_inicio   = :inicio
                AND ct.hora_fin      = :fin
                AND t2.nombre        = :tema
            ");

            $qEquiv->execute([
                ':emp'        => $empresaId,
                ':aula'       => $clase['aula_id'],
                ':instructor' => $clase['instructor_id'],
                ':fecha'      => $clase['fecha'],
                ':inicio'     => $clase['hora_inicio'],
                ':fin'        => $clase['hora_fin'],
                ':tema'       => $clase['tema_nombre'],
            ]);

            $clasesEquivalentes = $qEquiv->fetchAll(PDO::FETCH_COLUMN);
            if (empty($clasesEquivalentes)) {
                $clasesEquivalentes = [$claseTeoricaId];
            }

            // ----------------------------------------------------------
            // 5. Validar CUPO REAL (personas únicas)
            // ----------------------------------------------------------
            $placeholders = implode(',', array_fill(0, count($clasesEquivalentes), '?'));

            $qCupo = $this->conn->prepare("
                SELECT COUNT(DISTINCT m.estudiante_id)
                FROM clases_teoricas_estudiantes cte
                INNER JOIN matriculas m ON m.id = cte.matricula_id
                WHERE cte.clase_teorica_id IN ($placeholders)
            ");
            $qCupo->execute($clasesEquivalentes);

            if ((int)$qCupo->fetchColumn() >= (int)$clase['capacidad']) {
                throw new Exception("No hay cupos disponibles. El aula alcanzó su capacidad máxima.");
            }

            // ----------------------------------------------------------
            // 6. Cruce: matrículas activas vs clases equivalentes
            // ----------------------------------------------------------
            $qCruce = $this->conn->prepare("
                SELECT DISTINCT
                    m.id  AS matricula_id,
                    ct.id AS clase_teorica_id
                FROM matriculas m
                INNER JOIN matricula_programas mp ON mp.matricula_id = m.id
                INNER JOIN clases_teoricas ct ON ct.programa_id = mp.programa_id
                WHERE m.estudiante_id = ?
                AND m.empresa_id    = ?
                AND m.estado        = 1
                AND ct.id IN ($placeholders)
            ");

            $params = array_merge([$estudianteId, $empresaId], $clasesEquivalentes);
            $qCruce->execute($params);

            $clasesParaAgendar = $qCruce->fetchAll(PDO::FETCH_ASSOC);
            if (empty($clasesParaAgendar)) {
                throw new Exception("No tienes matrículas compatibles para esta clase.");
            }

            // ----------------------------------------------------------
            // 7. INSERTAR (agendamiento en bloque)
            // ----------------------------------------------------------
            $this->conn->beginTransaction();

            $qIns = $this->conn->prepare("
                INSERT INTO clases_teoricas_estudiantes
                    (matricula_id, clase_teorica_id, estado, fecha_registro, asistencia)
                VALUES
                    (:mid, :cid, 1, NOW(), 0)
            ");

            foreach ($clasesParaAgendar as $row) {
                $qIns->execute([
                    ':mid' => $row['matricula_id'],
                    ':cid' => $row['clase_teorica_id']
                ]);
            }

            $this->conn->commit();

            $_SESSION['success_message'] = "Clase agendada correctamente.";
        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            $_SESSION['error_message'] = $e->getMessage();
        }

        $redir = $calendarioFecha
            ? "/estudiantes-agenda-teoricas/" . urlencode($calendarioFecha)
            : "/estudiantes-agenda-teoricas";

        header("Location: " . $redir);
        exit();
    }

    public function desagendarClaseTeorica($claseTeoricaId)
    {

        echo '<pre>';
        print_r($_POST); // Debugging line to check the contents of $_POST  
        echo '</pre>';
        exit;



        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception("Sesión inválida.");
            }

            // ----------------------------------------------------------
            // 1. Obtener estudiante
            // ----------------------------------------------------------
            $qEst = $this->conn->prepare("
                SELECT estudiante_id 
                FROM users 
                WHERE id = :uid
            ");
            $qEst->execute([':uid' => $userId]);
            $estudianteId = $qEst->fetchColumn();

            if (!$estudianteId) {
                throw new Exception("No se encontró el estudiante.");
            }

            // ----------------------------------------------------------
            // 2. Obtener matrículas activas del estudiante
            // ----------------------------------------------------------
            $qMat = $this->conn->prepare("
                SELECT id 
                FROM matriculas
                WHERE estudiante_id = :est
                AND estado = 1
            ");
            $qMat->execute([':est' => $estudianteId]);
            $matriculas = $qMat->fetchAll(PDO::FETCH_COLUMN);

            if (empty($matriculas)) {
                throw new Exception("El estudiante no tiene matrículas activas.");
            }

            // ----------------------------------------------------------
            // 3. Obtener clase base (para detectar evento físico)
            // ----------------------------------------------------------
            $qClase = $this->conn->prepare("
                SELECT 
                    ct.*,
                    ctt.nombre AS tema_nombre
                FROM clases_teoricas ct
                INNER JOIN clases_teoricas_temas ctt ON ctt.id = ct.tema_id
                WHERE ct.id = :cid
            ");
            $qClase->execute([':cid' => (int)$claseTeoricaId]);
            $clase = $qClase->fetch(PDO::FETCH_ASSOC);

            if (!$clase) {
                throw new Exception("Clase no encontrada.");
            }

            // ----------------------------------------------------------
            // 4. Obtener clases equivalentes (evento físico)
            // ----------------------------------------------------------
            $qEquiv = $this->conn->prepare("
            SELECT ct.id
            FROM clases_teoricas ct
            INNER JOIN clases_teoricas_temas t2 ON t2.id = ct.tema_id
            WHERE ct.aula_id       = :aula
              AND ct.instructor_id = :instructor
              AND ct.fecha         = :fecha
              AND ct.hora_inicio   = :inicio
              AND ct.hora_fin      = :fin
              AND t2.nombre        = :tema
        ");

            $qEquiv->execute([
                ':aula'       => $clase['aula_id'],
                ':instructor' => $clase['instructor_id'],
                ':fecha'      => $clase['fecha'],
                ':inicio'     => $clase['hora_inicio'],
                ':fin'        => $clase['hora_fin'],
                ':tema'       => $clase['tema_nombre'],
            ]);

            $clasesEquivalentes = $qEquiv->fetchAll(PDO::FETCH_COLUMN);
            if (empty($clasesEquivalentes)) {
                $clasesEquivalentes = [$claseTeoricaId];
            }

            // ----------------------------------------------------------
            // 5. Obtener IDs reales de agendamiento (cruce FINAL)
            // ----------------------------------------------------------
            $phClases = implode(',', array_fill(0, count($clasesEquivalentes), '?'));
            $phMats   = implode(',', array_fill(0, count($matriculas), '?'));

            $qCTE = $this->conn->prepare("
                SELECT cte.id
                FROM clases_teoricas_estudiantes cte
                WHERE cte.clase_teorica_id IN ($phClases)
                AND cte.matricula_id IN ($phMats)
            ");

            $qCTE->execute(array_merge($clasesEquivalentes, $matriculas));
            $idsAgendamiento = $qCTE->fetchAll(PDO::FETCH_COLUMN);

            echo '<pre>';
            print_r($idsAgendamiento); // Debugging line to check the contents of $idsAgendamiento
            echo '</pre>';
            exit;


            if (empty($idsAgendamiento)) {
                throw new Exception("No se encontraron agendamientos para eliminar.");
            }

            // ----------------------------------------------------------
            // 6. Eliminar agendamientos (exactamente los creados)
            // ----------------------------------------------------------
            $phIds = implode(',', array_fill(0, count($idsAgendamiento), '?'));

            $this->conn->beginTransaction();

            $qDel = $this->conn->prepare("
            DELETE FROM clases_teoricas_estudiantes
            WHERE id IN ($phIds)
        ");
            $qDel->execute($idsAgendamiento);

            $this->conn->commit();

            $_SESSION['success_message'] = "Te desagendaste correctamente de la clase.";
        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            $_SESSION['error_message'] = $e->getMessage();
        }

        header("Location: /estudiantes-agenda-teoricas");
        exit();
    }

    public function obtenerInformacionEstudiante($id)
    {
        $query = "SELECT * FROM estudiantes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene la información del estudiante asociado a una matrícula.
     *
     * @param string $matriculaId ID de la matrícula.
     * @return array|null Información del estudiante (array asociativo) o null si no se encuentra.
     */
    public function getEstudianteByMatricula($matriculaId)
    {
        // Consulta para obtener la información del estudiante asociado a la matrícula
        $query = "
                SELECT 
                    e.nombres,
                    e.apellidos,
                    e.correo
                FROM matriculas m
                INNER JOIN estudiantes e ON m.estudiante_id = e.id
                WHERE m.id = :matricula_id
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId, PDO::PARAM_STR);
        $stmt->execute();

        // Retornar los datos del estudiante o null si no se encuentra
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function progresoTeorico()
    {
        $routes = include '../config/Routes.php';

        // ----------------------------------------------------------
        // 🔹 1) Validar sesión activa
        // ----------------------------------------------------------
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId) {
            header("Location:/login");
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 Variables que la vista SIEMPRE necesita (evita warnings)
        // ----------------------------------------------------------
        $programaSeleccionado = '';
        $progresoPorTema = [];
        $asistidos = $inscritos = $noAsistio = $noVistos = $totalTemas = 0;
        $temas = [];
        $inscripciones = [];

        // ----------------------------------------------------------
        // 🔹 2) Obtener el estudiante asociado a este usuario
        // ----------------------------------------------------------
        $qEst = $this->conn->prepare("
                SELECT estudiante_id 
                FROM users 
                WHERE id = :uid
                LIMIT 1
            ");
        $qEst->bindValue(':uid', $currentUserId, PDO::PARAM_INT);
        $qEst->execute();
        $estudianteId = $qEst->fetchColumn();

        if (!$estudianteId) {
            $_SESSION['error_message'] = "No se encontró el estudiante asociado.";
            header("Location:/");
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 3) Obtener matrículas activas
        // ----------------------------------------------------------
        $qMats = $this->conn->prepare("
                SELECT 
                    m.id AS matricula_id,
                    p.id AS programa_id,
                    p.nombre AS programa_nombre
                FROM matriculas m
                INNER JOIN matricula_programas mp ON mp.matricula_id = m.id
                INNER JOIN programas p ON p.id = mp.programa_id
                WHERE m.estudiante_id = :est
                AND m.estado = 1
                ORDER BY m.fecha_inscripcion DESC
            ");
        $qMats->bindValue(':est', $estudianteId, PDO::PARAM_INT);
        $qMats->execute();
        $matriculas = $qMats->fetchAll(PDO::FETCH_ASSOC);

        // ----------------------------------------------------------
        // 🔹 4) Determinar matrícula seleccionada
        // ----------------------------------------------------------
        $matriculaId = $_POST['matricula_id'] ?? null;

        if (!$matriculaId) {
            // Vista sin progreso
            ob_start();
            include '../modules/estudiantes/views/progreso_teorico.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
            return;
        }

        // ----------------------------------------------------------
        // 🔹 5) Obtener programa de la matrícula
        // ----------------------------------------------------------
        $programaId = null;

        foreach ($matriculas as $m) {
            if ($m['matricula_id'] == $matriculaId) {
                $programaId = $m['programa_id'];
                $programaSeleccionado = $m['programa_nombre'];
                break;
            }
        }

        if (!$programaId) {
            $_SESSION['error_message'] = "La matrícula seleccionada no tiene programa.";
            header("Location:/estudiantes/progreso");
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 6) Obtener temas del programa
        // ----------------------------------------------------------
        $qTemas = $this->conn->prepare("
                SELECT id AS tema_id, nombre AS tema_nombre
                FROM clases_teoricas_temas
                WHERE clase_teorica_programa_id = :pid
                ORDER BY id ASC
            ");
        $qTemas->bindValue(':pid', $programaId, PDO::PARAM_INT);
        $qTemas->execute();
        $temas = $qTemas->fetchAll(PDO::FETCH_ASSOC);

        // ----------------------------------------------------------
        // 🔹 7) Obtener inscripciones SOLO del programa
        // ----------------------------------------------------------
        $qIns = $this->conn->prepare("
                SELECT 
                    ce.clase_teorica_id AS clase_id,
                    ce.asistencia,
                    ct.tema_id,
                    ct.fecha,
                    ct.hora_inicio,
                    ct.hora_fin
                FROM clases_teoricas_estudiantes ce
                INNER JOIN clases_teoricas ct ON ct.id = ce.clase_teorica_id
                WHERE ce.matricula_id = :matricula_id
                AND ct.programa_id = :programa_id
            ");
        $qIns->bindValue(':matricula_id', $matriculaId, PDO::PARAM_INT);
        $qIns->bindValue(':programa_id', $programaId, PDO::PARAM_INT);
        $qIns->execute();
        $inscripciones = $qIns->fetchAll(PDO::FETCH_ASSOC);

        // Index por tema_id
        $insPorTema = [];
        foreach ($inscripciones as $i) {
            $insPorTema[$i['tema_id']] = $i;
        }

        // ----------------------------------------------------------
        // 🔹 8) Armar progreso por tema
        // ----------------------------------------------------------
        foreach ($temas as $t) {
            $temaId = $t['tema_id'];

            if (isset($insPorTema[$temaId])) {
                $ins = $insPorTema[$temaId];

                $estado =
                    ($ins['asistencia'] == 1 ? 'ASISTIÓ' : ($ins['asistencia'] == 2 ? 'NO ASISTIÓ' : 'INSCRITO'));

                $progresoPorTema[] = [
                    'tema_id'     => $temaId,
                    'tema'        => $t['tema_nombre'],
                    'estado'      => $estado,
                    'fecha'       => $ins['fecha'],
                    'hora_inicio' => $ins['hora_inicio'],
                    'hora_fin'    => $ins['hora_fin'],
                    'clase_id'    => $ins['clase_id'],
                ];
            } else {
                $progresoPorTema[] = [
                    'tema_id'     => $temaId,
                    'tema'        => $t['tema_nombre'],
                    'estado'      => 'NO VISTO',
                    'fecha'       => null,
                    'hora_inicio' => null,
                    'hora_fin'    => null,
                    'clase_id'    => null
                ];
            }
        }

        // ----------------------------------------------------------
        // 🔹 9) Barra de progreso
        // ----------------------------------------------------------
        $totalTemas = count($progresoPorTema);
        $asistidos = count(array_filter($progresoPorTema, fn($v) => $v['estado'] === 'ASISTIÓ'));
        $inscritos = count(array_filter($progresoPorTema, fn($v) => $v['estado'] === 'INSCRITO'));
        $noAsistio = count(array_filter($progresoPorTema, fn($v) => $v['estado'] === 'NO ASISTIÓ'));
        $noVistos = count(array_filter($progresoPorTema, fn($v) => $v['estado'] === 'NO VISTO'));

        $porcentajeInscrito = ($totalTemas > 0)
            ? round((($inscritos + $asistidos) / $totalTemas) * 100)
            : 0;

        $porcentajeAsistido = ($totalTemas > 0)
            ? round(($asistidos / $totalTemas) * 100)
            : 0;

        // ----------------------------------------------------------
        // 🔹 10) Cargar vista
        // ----------------------------------------------------------
        ob_start();
        include '../modules/estudiantes/views/progreso_teorico.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // ----------------------------------------------------------
    // 🔹 Función auxiliar: verifica si un tema pertenece al mismo global
    // ----------------------------------------------------------
    private function temaPerteneceAlGlobal($temaId, $temaGlobalId)
    {
        $stmt = $this->conn->prepare("
                SELECT 1 FROM clases_teoricas_temas 
                WHERE id = :id AND tema_global_id = :gid
                LIMIT 1
            ");
        $stmt->bindValue(':id', $temaId, PDO::PARAM_INT);
        $stmt->bindValue(':gid', $temaGlobalId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }

    private function getEstudianteId($userId)
    {
        $st = $this->conn->prepare("SELECT estudiante_id FROM users WHERE id = :uid");
        $st->bindValue(':uid', $userId, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchColumn();
    }

    private function getMatriculas($estudianteId, $empresaId)
    {
        $sql = "SELECT 
                    m.id,
                    m.fecha_inscripcion,
                    COALESCE(GROUP_CONCAT(DISTINCT p.nombre ORDER BY p.nombre SEPARATOR ', '), '') AS programas
                FROM matriculas m
                LEFT JOIN matricula_programas mp ON mp.matricula_id = m.id
                LEFT JOIN programas p ON p.id = mp.programa_id
                WHERE m.estudiante_id = :est
                AND m.empresa_id   = :emp
                AND m.estado       = 1
                GROUP BY m.id
                ORDER BY m.fecha_inscripcion DESC
            ";
        $st = $this->conn->prepare($sql);
        $st->bindValue(':est', $estudianteId, PDO::PARAM_INT);
        $st->bindValue(':emp', $empresaId, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getMatriculaSeleccionada(array $matriculasList)
    {
        $requestedMid  = ($_SERVER['REQUEST_METHOD'] === 'POST') ? ($_POST['mid'] ?? '') : '';
        $matriculasIds = array_column($matriculasList, 'id');
        if ($requestedMid && in_array($requestedMid, $matriculasIds, true)) {
            return $requestedMid;
        }
        return $matriculasList[0]['id'];
    }

    private function getProgramasIds($matriculaId)
    {
        $st = $this->conn->prepare("SELECT programa_id FROM matricula_programas WHERE matricula_id = :mid");
        $st->bindValue(':mid', $matriculaId, PDO::PARAM_INT);
        $st->execute();
        return array_map('intval', $st->fetchAll(PDO::FETCH_COLUMN));
    }

    private function getAggPorTema($empresaId, $matriculaId)
    {
        $sql = "SELECT 
                    ct.tema_id,
                    MAX(CASE WHEN cte.asistencia = 1 THEN 1 ELSE 0 END) AS attended,
                    SUM(CASE WHEN cte.asistencia = 2 THEN 1 ELSE 0 END) AS no_asist,
                    SUM(CASE WHEN cte.asistencia = 0 AND ct.fecha >= CURRENT_DATE THEN 1 ELSE 0 END) AS ins_futuro
                FROM clases_teoricas_estudiantes cte
                INNER JOIN clases_teoricas ct
                        ON ct.id = cte.clase_teorica_id
                    AND ct.empresa_id = :emp
                WHERE cte.matricula_id = :mid
                GROUP BY ct.tema_id
            ";
        $st = $this->conn->prepare($sql);
        $st->bindValue(':emp', $empresaId, PDO::PARAM_INT);
        $st->bindValue(':mid', $matriculaId, PDO::PARAM_INT);
        $st->execute();

        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $out[(int)$r['tema_id']] = [
                'attended'   => (int)($r['attended']   ?? 0),
                'no_asist'   => (int)($r['no_asist']   ?? 0),
                'ins_futuro' => (int)($r['ins_futuro'] ?? 0),
            ];
        }
        return $out;
    }

    private function getPendientesPorTema($empresaId, $matriculaId)
    {
        $sql = "SELECT 
                    cte.id AS cte_id,
                    cte.clase_teorica_id,
                    ct.tema_id,
                    ct.fecha,
                    ct.hora_inicio,
                    ct.hora_fin
                FROM clases_teoricas_estudiantes cte
                INNER JOIN clases_teoricas ct
                    ON ct.id = cte.clase_teorica_id
                WHERE cte.matricula_id = :mid
                AND cte.asistencia = 0
                AND ct.fecha >= CURRENT_DATE
                AND ct.empresa_id = :emp
            ";
        $st = $this->conn->prepare($sql);
        $st->bindValue(':mid', $matriculaId, PDO::PARAM_INT);
        $st->bindValue(':emp', $empresaId, PDO::PARAM_INT);
        $st->execute();

        $out = [];
        foreach ($st->fetchAll(PDO::FETCH_ASSOC) as $r) {
            $out[(int)$r['tema_id']] = $r;
        }
        return $out;
    }

    private function getTemas(array $programasIds)
    {
        $placeholders = implode(',', array_fill(0, count($programasIds), '?'));
        $sql = "SELECT
                    p.id   AS programa_id,
                    p.nombre   AS programa_nombre,
                    ctt.id AS tema_id,
                    ctt.nombre AS tema_nombre,
                    ctt.descripcion AS tema_descripcion
                FROM programas p
                INNER JOIN clases_teoricas_temas ctt
                        ON ctt.clase_teorica_programa_id = p.id
                WHERE p.id IN ($placeholders)
                ORDER BY p.nombre, ctt.id
            ";
        $st = $this->conn->prepare($sql);
        foreach ($programasIds as $k => $pid) {
            $st->bindValue($k + 1, $pid, PDO::PARAM_INT);
        }
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    private function armarProgreso(array $temas, array $aggPorTema, array $pendPerTema)
    {
        $porPrograma = [];
        foreach ($temas as $t) {
            $temaId = (int)$t['tema_id'];
            $agg    = $aggPorTema[$temaId] ?? ['attended' => 0, 'no_asist' => 0, 'ins_futuro' => 0];

            if ($agg['attended'] === 1) {
                $estado = 'VISTO';
            } elseif ($agg['no_asist'] > 0) {
                $estado = 'INSCRITO NO ASISTIÓ';
            } elseif ($agg['ins_futuro'] > 0) {
                $estado = 'INSCRITO';
            } else {
                $estado = 'NO VISTO';
            }

            $extra = [];
            if ($estado === 'INSCRITO' && isset($pendPerTema[$temaId])) {
                $extra = [
                    'cte_id'           => $pendPerTema[$temaId]['cte_id'],
                    'clase_teorica_id' => $pendPerTema[$temaId]['clase_teorica_id'],
                    'fecha'            => $pendPerTema[$temaId]['fecha'],
                    'hora_inicio'      => $pendPerTema[$temaId]['hora_inicio'],
                    'hora_fin'         => $pendPerTema[$temaId]['hora_fin'],
                ];
            }

            $pid = (int)$t['programa_id'];
            if (!isset($porPrograma[$pid])) {
                $porPrograma[$pid] = [
                    'programa_nombre' => $t['programa_nombre'],
                    'temas'           => [],
                ];
            }

            $porPrograma[$pid]['temas'][] = [
                'tema_id'     => $temaId,
                'tema_nombre' => $t['tema_nombre'],
                'estado'      => $estado,
            ] + $extra;
        }
        return $porPrograma;
    }


    public function estudianteDesinscribir()
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                throw new Exception("Sesión inválida.");
            }

            // ----------------------------------------------------------
            // 1. Obtener estudiante
            // ----------------------------------------------------------
            $qEst = $this->conn->prepare("
                SELECT estudiante_id
                FROM users
                WHERE id = :uid
            ");
            $qEst->execute([':uid' => $userId]);
            $estudianteId = $qEst->fetchColumn();

            if (!$estudianteId) {
                throw new Exception("No se encontró el estudiante.");
            }

            // ----------------------------------------------------------
            // 2. Obtener clase base
            // ----------------------------------------------------------
            $claseTeoricaId = (int)($_POST['clase_teorica_id'] ?? 0);

            if ($claseTeoricaId <= 0) {
                throw new Exception("ID de clase inválido.");
            }

            $qClase = $this->conn->prepare("
                SELECT 
                    ct.*,
                    ctt.nombre AS tema_nombre
                FROM clases_teoricas ct
                INNER JOIN clases_teoricas_temas ctt 
                    ON ctt.id = ct.tema_id
                WHERE ct.id = :cid
            ");

            $qClase->execute([':cid' => $claseTeoricaId]);
            $clase = $qClase->fetch(PDO::FETCH_ASSOC);

            if (!$clase) {
                throw new Exception("Clase no encontrada.");
            }

            // ----------------------------------------------------------
            // 3. Obtener clases equivalentes (evento físico)
            // ----------------------------------------------------------
            $qEquiv = $this->conn->prepare("
                SELECT ct.id
                FROM clases_teoricas ct
                INNER JOIN clases_teoricas_temas t2 ON t2.id = ct.tema_id
                WHERE ct.aula_id       = :aula
                AND ct.instructor_id = :instructor
                AND ct.fecha         = :fecha
                AND ct.hora_inicio   = :inicio
                AND ct.hora_fin      = :fin
                AND t2.nombre        = :tema
            ");

            $qEquiv->execute([
                ':aula'       => $clase['aula_id'],
                ':instructor' => $clase['instructor_id'],
                ':fecha'      => $clase['fecha'],
                ':inicio'     => $clase['hora_inicio'],
                ':fin'        => $clase['hora_fin'],
                ':tema'       => $clase['tema_nombre'],
            ]);

            $clasesEquivalentes = $qEquiv->fetchAll(PDO::FETCH_COLUMN);
            if (empty($clasesEquivalentes)) {
                $clasesEquivalentes = [$claseTeoricaId];
            }

            // ----------------------------------------------------------
            // 4. Obtener IDs reales de agendamiento (CRUCE CORRECTO)
            //    POR ESTUDIANTE + EVENTO FÍSICO
            // ----------------------------------------------------------
            $phClases = implode(',', array_fill(0, count($clasesEquivalentes), '?'));

            $qCTE = $this->conn->prepare("
                SELECT cte.id
                FROM clases_teoricas_estudiantes cte
                INNER JOIN matriculas m ON m.id = cte.matricula_id
                WHERE cte.clase_teorica_id IN ($phClases)
                AND m.estudiante_id = ?
            ");

            $params = array_merge($clasesEquivalentes, [$estudianteId]);
            $qCTE->execute($params);

            $idsAgendamiento = $qCTE->fetchAll(PDO::FETCH_COLUMN);

            if (empty($idsAgendamiento)) {
                throw new Exception("No se encontraron agendamientos para eliminar.");
            }

            // ----------------------------------------------------------
            // 5. Eliminar agendamientos (exactamente los creados)
            // ----------------------------------------------------------
            $phIds = implode(',', array_fill(0, count($idsAgendamiento), '?'));

            $this->conn->beginTransaction();

            $qDel = $this->conn->prepare("
                DELETE FROM clases_teoricas_estudiantes
                WHERE id IN ($phIds)
            ");
            $qDel->execute($idsAgendamiento);

            $this->conn->commit();

            $_SESSION['success_message'] = "Te desagendaste correctamente.";
        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            $_SESSION['error_message'] = $e->getMessage();
        }

        header("Location: /estudiantes-agenda-teoricas");
        exit();
    }
}
