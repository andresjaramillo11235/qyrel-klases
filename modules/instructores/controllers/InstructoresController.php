<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../shared/utils/ImageHelper.php';
require_once '../shared/utils/LabelHelper.php';

class InstructoresController
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
        LabelHelper::load($this->conn, $_SESSION['empresa_id']);

        if (!$permissionController->hasPermission($currentUserId, 'view_instructores')) {
            header('Location: /permission-denied/');
            exit;
        }

        if ($this->userUtils->isSuperAdmin($currentUserId)) {
            $query = "SELECT i.*, 
                             e.nombre AS empresa_nombre, 
                             GROUP_CONCAT(DISTINCT c.nombre SEPARATOR '<br> ') AS categorias_conduccion,
                             GROUP_CONCAT(DISTINCT ci.nombre SEPARATOR '<br> ') AS categorias_instructor
                      FROM instructores i
                      LEFT JOIN empresas e ON i.empresa_id = e.id
                      LEFT JOIN instructor_categoria_conduccion icc ON i.id = icc.instructor_id
                      LEFT JOIN param_categorias_conduccion c ON icc.categoria_conduccion_id = c.id
                      LEFT JOIN instructor_categoria_instructor ici ON i.id = ici.instructor_id
                      LEFT JOIN param_categorias_instructor ci ON ici.categoria_instructor_id = ci.id
                      GROUP BY i.id";
            $stmt = $this->conn->query($query);
        } else {
            $query = "SELECT i.*, 
                             e.nombre AS empresa_nombre, 
                             GROUP_CONCAT(DISTINCT c.nombre SEPARATOR '<br> ') AS categorias_conduccion,
                             GROUP_CONCAT(DISTINCT ci.nombre SEPARATOR '<br> ') AS categorias_instructor
                      FROM instructores i
                      LEFT JOIN empresas e ON i.empresa_id = e.id
                      LEFT JOIN instructor_categoria_conduccion icc ON i.id = icc.instructor_id
                      LEFT JOIN param_categorias_conduccion c ON icc.categoria_conduccion_id = c.id
                      LEFT JOIN instructor_categoria_instructor ici ON i.id = ici.instructor_id
                      LEFT JOIN param_categorias_instructor ci ON ici.categoria_instructor_id = ci.id
                      WHERE i.empresa_id = :empresa_id
                      GROUP BY i.id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':empresa_id', $empresaId);
        }

        $stmt->execute();
        $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/instructores/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    private function getCategoriasInstructorByInstructorId($instructorId)
    {
        $query = "SELECT pci.nombre 
                  FROM instructor_categoria_instructor ici
                  LEFT JOIN param_categorias_instructor pci ON ici.categoria_instructor_id = pci.id
                  WHERE ici.instructor_id = :instructor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCategoriasConduccionByInstructorId($instructorId)
    {
        $query = "SELECT pcc.nombre 
                  FROM instructor_categoria_conduccion icc
                  LEFT JOIN param_categorias_conduccion pcc ON icc.categoria_conduccion_id = pcc.id
                  WHERE icc.instructor_id = :instructor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function detail($id)
    {
        $query = "SELECT i.*, 
                     td.nombre AS tipo_documento_nombre, 
                     d.nombre AS expedicion_departamento_nombre, 
                     c.nombre AS expedicion_ciudad_nombre,
                     gs.nombre AS grupo_sanguineo_nombre, 
                     g.nombre AS genero_nombre,
                     ec.nombre AS estado_civil_nombre,
                     e.nombre AS empresa_nombre
              FROM instructores i
              LEFT JOIN param_tipo_documento td ON i.tipo_documento = td.id
              LEFT JOIN param_departamentos d ON i.expedicion_departamento = d.id
              LEFT JOIN param_ciudades c ON i.expedicion_ciudad = c.id
              LEFT JOIN param_grupo_sanguineo gs ON i.grupo_sanguineo = gs.id
              LEFT JOIN param_genero g ON i.genero = g.id
              LEFT JOIN param_estado_civil ec ON i.estado_civil = ec.id
              LEFT JOIN empresas e ON i.empresa_id = e.id
              WHERE i.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            ob_start();
            include '../modules/instructores/views/detail.php';
            $content = ob_get_clean();
            echo $content;
        } else {
            echo "Instructor no encontrado";
        }
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'edit_instructores')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT * FROM instructores WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetching instructor's driving categories
        $query = "SELECT categoria_conduccion_id FROM instructor_categoria_conduccion WHERE instructor_id = :instructor_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $id);
        $stmt->execute();
        $instructorCategoriasConduccion = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Fetching the dropdown data
        $paramTiposDocumentos = $this->getParamTiposDocumentos();
        $paramDepartamentos = $this->getParamDepartamentos();
        $paramCiudades = $this->getParamCiudades();
        $paramGrupoSanguineo = $this->getParamGrupoSanguineo();
        $paramGenero = $this->getParamGenero();
        $paramEstadoCivil = $this->getParamEstadoCivil();
        $paramCategoriasConduccion = $this->getParamCategoriasConduccion();
        $empresas = $this->getEmpresas();

        ob_start();
        include '../modules/instructores/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'edit_instructores')) {
            header('Location: /permission-denied/');
            exit;
        }

        $nombres = $_POST['nombres'];
        $apellidos = $_POST['apellidos'];
        $tipo_documento = $_POST['tipo_documento'];
        $expedicion_departamento = $_POST['expedicion_departamento'];
        $expedicion_ciudad = $_POST['expedicion_ciudad'];
        $fecha_expedicion = $_POST['fecha_expedicion'];
        $correo = $_POST['correo'];
        $celular = $_POST['celular'];
        $direccion = $_POST['direccion'];
        $grupo_sanguineo = $_POST['grupo_sanguineo'];
        $genero = $_POST['genero'];
        $estado_civil = $_POST['estado_civil'];
        $vencimiento_licencia_conduccion = $_POST['vencimiento_licencia_conduccion'];
        $vencimiento_licencia_instructor = $_POST['vencimiento_licencia_instructor'];
        $estado = isset($_POST['estado']) && $_POST['estado'] === 'on' ? 1 : 0;
        $observaciones = $_POST['observaciones'];
        $categorias_conduccion = $_POST['categorias_conduccion'];

        $foto = '';
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
            // Generar un nombre único alfanumérico de 16 caracteres
            $nombreArchivoUnico = bin2hex(random_bytes(8)); // Genera 16 caracteres hexadecimales (8 bytes * 2)
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION); // Obtener la extensión del archivo

            // Crear el nombre completo del archivo con la extensión
            $foto = $nombreArchivoUnico . '.' . $extension;

            // Mover el archivo al directorio especificado
            move_uploaded_file($_FILES['foto']['tmp_name'], '../files/fotos_instructores/' . $foto);
        }

        $query = "
            UPDATE instructores
            SET 
                nombres = :nombres,
                apellidos = :apellidos,
                tipo_documento = :tipo_documento,
                expedicion_departamento = :expedicion_departamento,
                expedicion_ciudad = :expedicion_ciudad,
                fecha_expedicion = :fecha_expedicion,
                correo = :correo,
                celular = :celular,
                direccion = :direccion,
                grupo_sanguineo = :grupo_sanguineo,
                genero = :genero,
                estado_civil = :estado_civil,
                vencimiento_licencia_conduccion = :vencimiento_licencia_conduccion,
                vencimiento_licencia_instructor = :vencimiento_licencia_instructor,
                estado = :estado,
                observaciones = :observaciones,
                foto = :foto
            WHERE 
                id = :id
        ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':nombres', $nombres);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':tipo_documento', $tipo_documento);
        $stmt->bindParam(':expedicion_departamento', $expedicion_departamento);
        $stmt->bindParam(':expedicion_ciudad', $expedicion_ciudad);
        $stmt->bindParam(':fecha_expedicion', $fecha_expedicion);
        $stmt->bindParam(':correo', $correo);
        $stmt->bindParam(':celular', $celular);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':grupo_sanguineo', $grupo_sanguineo);
        $stmt->bindParam(':genero', $genero);
        $stmt->bindParam(':estado_civil', $estado_civil);
        $stmt->bindParam(':vencimiento_licencia_conduccion', $vencimiento_licencia_conduccion);
        $stmt->bindParam(':vencimiento_licencia_instructor', $vencimiento_licencia_instructor);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':foto', $foto);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        // Actualiza las categorías de conducción del instructor
        $query = "DELETE FROM instructor_categoria_conduccion WHERE instructor_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        foreach ($categorias_conduccion as $categoria_id) {
            $query = "INSERT INTO instructor_categoria_conduccion (instructor_id, categoria_conduccion_id) VALUES (:instructor_id, :categoria_conduccion_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':instructor_id', $id);
            $stmt->bindParam(':categoria_conduccion_id', $categoria_id);
            $stmt->execute();
        }

        $_SESSION['instructor_modificado'] = "Los datos del instructor fueron modificados con éxito.";
        header("Location: /instructores/");
        exit;
    }

    public function create()
    {
        $categoriasInstructor = $this->getCategoriasInstructor();
        LabelHelper::load($this->conn, $_SESSION['empresa_id']);

        //$permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        // if (!$permissionController->hasPermission($currentUserId, 'create_instructores')) {
        //     header('Location: /permission-denied/');
        //     exit;
        // }

        // Fetching the dropdown data
        $paramTiposDocumentos = $this->getParamTiposDocumentos();
        //$paramDepartamentos = $this->getParamDepartamentos();
        //$paramCiudades = $this->getParamCiudades();
        $paramGrupoSanguineo = $this->getParamGrupoSanguineo();
        $paramGenero = $this->getParamGenero();
        //$paramEstadoCivil = $this->getParamEstadoCivil();
        //$paramCategoriasConduccion = $this->getParamCategoriasConduccion();
        //$empresas = $this->getEmpresas();

        ob_start();
        include '../modules/instructores/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }



    // ----------------------------------------------------------
    // 🔹 Crear Instructor (Versión Simplificada)
    // ----------------------------------------------------------
    public function store()
    {
        $empresa_id = $_SESSION['empresa_id'];

        // ----------------------------------------------------------
        // 🔹 Datos básicos del formulario
        // ----------------------------------------------------------
        $nombres = strtoupper($_POST['nombres']);
        $apellidos = strtoupper($_POST['apellidos']);
        $tipo_documento = $_POST['tipo_documento'];
        $numero_documento = $_POST['numero_documento'];
        $correo = strtolower($_POST['correo']);
        $celular = $_POST['celular'];
        $direccion = strtoupper($_POST['direccion']);
        $grupo_sanguineo = $_POST['grupo_sanguineo'];
        $genero = $_POST['genero'];
        $observaciones = strtoupper($_POST['observaciones'] ?? '');

        // ----------------------------------------------------------
        // 🔹 Campos que ya no usas → NULL
        // ----------------------------------------------------------
        $expedicion_departamento = null;
        $expedicion_ciudad = null;
        $fecha_expedicion = null;
        $estado_civil = null;
        $vencimiento_licencia_conduccion = null;
        $vencimiento_licencia_instructor = null;
        $estado = 1;

        // ----------------------------------------------------------
        // 🔹 Manejo de Foto
        // ----------------------------------------------------------
        $foto = 'img-defecto-instructor.png';

        if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {

            $nombreArchivoUnico = bin2hex(random_bytes(8));
            $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $foto = $nombreArchivoUnico . '.' . $extension;

            move_uploaded_file(
                $_FILES['foto']['tmp_name'],
                '../files/fotos_instructores/' . $foto
            );
        }

        // ----------------------------------------------------------
        // 🔹 Insert Instructor
        // ----------------------------------------------------------
        $query = "INSERT INTO instructores (
                nombres, apellidos, tipo_documento, numero_documento,
                expedicion_departamento, expedicion_ciudad, fecha_expedicion,
                correo, celular, direccion, grupo_sanguineo, genero,
                estado_civil, vencimiento_licencia_conduccion,
                vencimiento_licencia_instructor, estado,
                observaciones, foto, empresa_id
            ) VALUES (
                :nombres, :apellidos, :tipo_documento, :numero_documento,
                :expedicion_departamento, :expedicion_ciudad, :fecha_expedicion,
                :correo, :celular, :direccion, :grupo_sanguineo, :genero,
                :estado_civil, :vencimiento_licencia_conduccion,
                :vencimiento_licencia_instructor, :estado,
                :observaciones, :foto, :empresa_id
            )";

        $stmt = $this->conn->prepare($query);

        $stmt->execute([
            ':nombres' => $nombres,
            ':apellidos' => $apellidos,
            ':tipo_documento' => $tipo_documento,
            ':numero_documento' => $numero_documento,
            ':expedicion_departamento' => $expedicion_departamento,
            ':expedicion_ciudad' => $expedicion_ciudad,
            ':fecha_expedicion' => $fecha_expedicion,
            ':correo' => $correo,
            ':celular' => $celular,
            ':direccion' => $direccion,
            ':grupo_sanguineo' => $grupo_sanguineo,
            ':genero' => $genero,
            ':estado_civil' => $estado_civil,
            ':vencimiento_licencia_conduccion' => $vencimiento_licencia_conduccion,
            ':vencimiento_licencia_instructor' => $vencimiento_licencia_instructor,
            ':estado' => $estado,
            ':observaciones' => $observaciones,
            ':foto' => $foto,
            ':empresa_id' => $empresa_id
        ]);

        $instructor_id = $this->conn->lastInsertId();

        // ----------------------------------------------------------
        // 🔹 Crear Usuario Automático
        // ----------------------------------------------------------
        $username = $numero_documento;
        $password = password_hash($numero_documento, PASSWORD_DEFAULT);
        $role_id = 6;

        $userQuery = "INSERT INTO users (
                    username, email, password,
                    first_name, last_name, phone,
                    address, status, role_id,
                    empresa_id, instructor_id
                  ) VALUES (
                    :username, :email, :password,
                    :first_name, :last_name, :phone,
                    :address, 1, :role_id,
                    :empresa_id, :instructor_id
                  )";

        $userStmt = $this->conn->prepare($userQuery);

        $userStmt->execute([
            ':username' => $username,
            ':email' => $correo,
            ':password' => $password,
            ':first_name' => $nombres,
            ':last_name' => $apellidos,
            ':phone' => $celular,
            ':address' => $direccion,
            ':role_id' => $role_id,
            ':empresa_id' => $empresa_id,
            ':instructor_id' => $instructor_id
        ]);

        $_SESSION['instructor_creado'] = "Instructor creado con éxito.";
        header('Location: /instructores/');
        exit;
    }








    public function cuenta()
    {
        $currentUserId = $_SESSION['user_id'];
        $instructorId = $_SESSION['instructor_id'];

        if (!$instructorId) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT i.*, 
                         td.nombre AS tipo_documento_nombre, 
                         d.nombre AS expedicion_departamento_nombre, 
                         c.nombre AS expedicion_ciudad_nombre,
                         gs.nombre AS grupo_sanguineo_nombre, 
                         g.nombre AS genero_nombre,
                         ec.nombre AS estado_civil_nombre,
                         e.nombre AS empresa_nombre
                  FROM instructores i
                  LEFT JOIN param_tipo_documento td ON i.tipo_documento = td.id
                  LEFT JOIN param_departamentos d ON i.expedicion_departamento = d.id
                  LEFT JOIN param_ciudades c ON i.expedicion_ciudad = c.id
                  LEFT JOIN param_grupo_sanguineo gs ON i.grupo_sanguineo = gs.id
                  LEFT JOIN param_genero g ON i.genero = g.id
                  LEFT JOIN param_estado_civil ec ON i.estado_civil = ec.id
                  LEFT JOIN empresas e ON i.empresa_id = e.id
                  WHERE i.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $instructorId);
        $stmt->execute();
        $instructor = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($instructor) {
            ob_start();
            include '../modules/instructores/views/cuenta.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        } else {
            echo "Instructor no encontrado";
        }
    }

    private function getParamTiposDocumentos()
    {
        $query = "SELECT * FROM param_tipo_documento";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getParamDepartamentos()
    {
        $query = "SELECT * FROM departamentos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getParamCiudades()
    {
        $query = "SELECT * FROM municipios";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getParamGrupoSanguineo()
    {
        $query = "SELECT * FROM param_grupo_sanguineo";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getParamGenero()
    {
        $query = "SELECT * FROM param_genero";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getParamEstadoCivil()
    {
        $query = "SELECT * FROM param_estado_civil";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getParamCategoriasConduccion()
    {
        $query = "SELECT * FROM param_categorias_conduccion";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEmpresas()
    {
        $query = "SELECT * FROM empresas";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCategoriasInstructor()
    {
        $query = "SELECT * FROM param_categorias_instructor";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function saveInstructorCategorias($instructorId, $categorias)
    {
        $query = "INSERT INTO instructor_categoria_instructor (instructor_id, categoria_instructor_id) VALUES (:instructor_id, :categoria_instructor_id)";
        $stmt = $this->conn->prepare($query);
        foreach ($categorias as $categoriaId) {
            $stmt->execute([':instructor_id' => $instructorId, ':categoria_instructor_id' => $categoriaId]);
        }
    }

    public function cronogramaSemanal()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                $fechaInicio = $_POST['fecha_inicio'];
                $fechaInicio = new DateTime($fechaInicio);
                $fechaFin = clone $fechaInicio;
                $fechaFin->modify('+6 days');

                $instructorId = $_SESSION['instructor_id']; // Usando el ID del instructor almacenado en la sesión

                $query = "SELECT c.*, e.nombres AS estudiante_nombres, e.apellidos AS estudiante_apellidos
                          FROM clases_practicas c
                          JOIN matriculas m ON c.matricula_id = m.id
                          JOIN estudiantes e ON m.estudiante_id = e.id
                          WHERE c.instructor_id = :instructor_id
                          AND c.fecha BETWEEN :fecha_inicio AND :fecha_fin
                          ORDER BY c.fecha, c.hora_inicio";



                $stmt = $this->conn->prepare($query);

                $fechaInicioFormatted = $fechaInicio->format('Y-m-d'); // <=========== Cambios realizados aquí
                $fechaFinFormatted = $fechaFin->format('Y-m-d'); // <=========== Cambios realizados aquí

                $stmt->bindParam(':instructor_id', $instructorId);
                $stmt->bindParam(':fecha_inicio', $fechaInicioFormatted); // <=========== Cambios realizados aquí
                $stmt->bindParam(':fecha_fin', $fechaFinFormatted); // <=========== Cambios realizados aquí

                $stmt->execute();
                $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($clases);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        } else {
            ob_start();
            include '../modules/instructores/views/cronograma_semanal.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        }
    }

    public function obtenerDetalleClase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Validación de entrada
                if (!isset($_POST['clase_id']) || !ctype_digit($_POST['clase_id'])) {
                    throw new Exception('ID de clase inválido.');
                }
                $claseId = $_POST['clase_id'];

                $query = "SELECT c.*, e.nombres AS estudiante_nombres, e.apellidos AS estudiante_apellidos, v.placa AS vehiculo_placa
                          FROM clases_practicas c
                          JOIN matriculas m ON c.matricula_id = m.id
                          JOIN estudiantes e ON m.estudiante_id = e.id
                          LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
                          WHERE c.id = :clase_id";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
                $stmt->execute();
                $clase = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($clase) {
                    echo json_encode($clase);
                } else {
                    echo json_encode(['error' => 'Clase no encontrada']);
                }
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    public function actualizarClase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // Validación de entrada
                if (!isset($_POST['clase_id']) || !ctype_digit($_POST['clase_id'])) {
                    throw new Exception('ID de clase inválido.');
                }
                if (!isset($_POST['estado_id']) || !ctype_digit($_POST['estado_id'])) {
                    throw new Exception('ID de estado inválido.');
                }
                if (!isset($_POST['observaciones'])) {
                    throw new Exception('Observaciones no válidas.');
                }

                $claseId = $_POST['clase_id'];
                $estadoId = $_POST['estado_id'];
                $observaciones = $_POST['observaciones'];

                $query = "UPDATE clases_practicas 
                      SET estado_id = :estado_id, observaciones = :observaciones 
                      WHERE id = :clase_id";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':estado_id', $estadoId, PDO::PARAM_INT);
                $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
                $stmt->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode(['success' => 'Clase actualizada correctamente.']);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    public function obtenerEstadosClase()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            try {
                $query = "SELECT id, nombre FROM param_estados_clases";
                $stmt = $this->conn->prepare($query);
                $stmt->execute();
                $estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($estados);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    public function clasesProgramadas()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            try {
                $instructorId = $_SESSION['instructor_id']; // Usando el ID del instructor almacenado en la sesión
                $fechaActual = new DateTime(); // Fecha actual

                $query = "SELECT c.*, e.nombres AS estudiante_nombres, e.apellidos AS estudiante_apellidos
                      FROM clases_practicas c
                      JOIN matriculas m ON c.matricula_id = m.id
                      JOIN estudiantes e ON m.estudiante_id = e.id
                      WHERE c.instructor_id = :instructor_id
                      AND c.fecha >= :fecha_actual
                      ORDER BY c.fecha, c.hora_inicio";

                $stmt = $this->conn->prepare($query);
                $fechaActualFormatted = $fechaActual->format('Y-m-d');

                $stmt->bindParam(':instructor_id', $instructorId);
                $stmt->bindParam(':fecha_actual', $fechaActualFormatted);
                $stmt->execute();
                $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($clases);
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    public function cronogramaDiario()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            try {
                $instructorId = $_SESSION['instructor_id']; // Usando el ID del instructor almacenado en la sesión
                $fechaActual = new DateTime(); // Fecha actual

                // Consultar las clases programadas a partir de hoy
                $query = "SELECT c.*, e.nombres AS estudiante_nombres, e.apellidos AS estudiante_apellidos, e.foto AS estudiante_foto, e.celular AS estudiante_celular, v.placa AS vehiculo_placa, p.nombre AS curso_nombre
                          FROM clases_practicas c
                          JOIN matriculas m ON c.matricula_id = m.id
                          JOIN estudiantes e ON m.estudiante_id = e.id
                          LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
                          JOIN programas p ON c.programa_id = p.id
                          WHERE c.instructor_id = :instructor_id
                          AND c.fecha >= :fecha_actual
                          ORDER BY c.fecha, c.hora_inicio";

                $stmt = $this->conn->prepare($query);
                $fechaActualFormatted = $fechaActual->format('Y-m-d');
                $stmt->bindParam(':instructor_id', $instructorId);
                $stmt->bindParam(':fecha_actual', $fechaActualFormatted);
                $stmt->execute();
                $clasesProgramadas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                ob_start();
                include '../modules/instructores/views/cronograma_diario.php';
                $content = ob_get_clean();
                include '../shared/views/layout.php';
            } catch (Exception $e) {
                echo json_encode(['error' => $e->getMessage()]);
            }
        }
    }

    public function administracion()
    {
        try {
            $instructorId = $_SESSION['instructor_id']; // Usando el ID del instructor almacenado en la sesión
            $query = "SELECT c.*, e.nombres AS estudiante_nombres, e.apellidos AS estudiante_apellidos, 
                         v.placa AS vehiculo_placa, p.nombre AS programa_nombre, es.nombre AS estado_nombre
                  FROM clases_practicas c
                  JOIN matriculas m ON c.matricula_id = m.id
                  JOIN estudiantes e ON m.estudiante_id = e.id
                  LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
                  LEFT JOIN programas p ON c.programa_id = p.id
                  LEFT JOIN param_estados_clases es ON c.estado_id = es.id
                  WHERE c.instructor_id = :instructor_id
                  ORDER BY c.fecha, c.hora_inicio";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':instructor_id', $instructorId);
            $stmt->execute();
            $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Renderizar la vista con las clases obtenidas
            ob_start();
            include '../modules/instructores/views/administracion.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function verificarDocumento()
    {
        try {
            // Verificar si el número de documento fue enviado
            if (!isset($_POST['numero_documento'])) {
                throw new Exception("Número de documento no enviado");
            }

            $numero_documento = $_POST['numero_documento'];

            $query = "SELECT COUNT(*) as count FROM instructores WHERE numero_documento = :numero_documento";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':numero_documento', $numero_documento);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result === false) {
                throw new Exception("Error ejecutando la consulta");
            }

            // Mensaje de depuración
            error_log("Resultado de la consulta: " . json_encode($result));

            if ($result['count'] > 0) {
                echo json_encode(['status' => 'exists']);
            } else {
                echo json_encode(['status' => 'available']);
            }
        } catch (Exception $e) {
            // Manejo de errores
            error_log("Error en verificarDocumento: " . $e->getMessage());
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Obtiene la información completa de un instructor a partir de su ID.
     *
     * @param int $instructorId ID del instructor.
     * @return array|null Información del instructor (array asociativo) o null si no se encuentra.
     */
    public function getInstructorById($instructorId)
    {
        // Consulta para obtener toda la información del instructor
        $query = "
                SELECT *
                FROM instructores
                WHERE id = :instructor_id
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->execute();

        // Retornar los datos del instructor o null si no se encuentra
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getInstructores()
    {
        try {
            $query = "SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre 
                  FROM instructores 
                  WHERE estado = 1 
                  ORDER BY nombres ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($instructores, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (PDOException $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
