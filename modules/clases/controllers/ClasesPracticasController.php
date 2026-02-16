<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class ClasesPracticasController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }

    ## listado clases practicas administrador
    public function index()
    {
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        $whereFechas = '';
        if ($fechaInicio && $fechaFin) {
            $whereFechas = "AND cp.fecha BETWEEN :fecha_inicio AND :fecha_fin";
        }

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_practicas')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $empresaId = $_SESSION['empresa_id'];

        $query = "
            SELECT 
                cp.id AS clase_id,
                cp.fecha,
                cp.nombre AS clase_nombre,
                cp.hora_inicio,
                cp.hora_fin,
                cp.matricula_id,
                cp.lugar,
                m.id AS matricula_id,
                e.numero_documento AS numero_documento,
                td.nombre AS tipo_documento,
                CONCAT(e.nombres, ' ', e.apellidos) AS estudiante_nombre,
                p.nombre AS programa_nombre,
                v.placa AS vehiculo_placa,
                v.foto AS vehiculo_foto,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
                i.foto AS instructor_foto,
                e.foto AS estudiante_foto,
                UPPER(pec.nombre) AS estado_nombre
            FROM 
                clases_practicas cp
            LEFT JOIN matriculas m ON cp.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
            LEFT JOIN programas p ON cp.programa_id = p.id
            LEFT JOIN vehiculos v ON cp.vehiculo_id = v.id
            LEFT JOIN instructores i ON cp.instructor_id = i.id
            LEFT JOIN param_estados_clases pec ON cp.estado_id = pec.id
            WHERE 
                cp.empresa_id = :empresa_id
                $whereFechas
            ORDER BY cp.fecha DESC, cp.hora_inicio ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);

        if ($fechaInicio && $fechaFin) {
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);
        }

        $stmt->execute();
        $clasesPracticas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/clases/views/clases_practicas/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    ## listarClasesEstudiante
    public function listarClasesEstudiante()
    {
        // Obtener el ID del estudiante desde la sesión
        $estudianteId = $_SESSION['estudiante_id'];
        $empresaId = $_SESSION['empresa_id'];

        $query = "
                SELECT 
                    cp.id AS clase_id,
                    cp.fecha,
                    cp.nombre AS clase_nombre,
                    cp.hora_inicio,
                    cp.hora_fin,
                    cp.lugar,
                    p.nombre AS programa_nombre,
                    v.placa AS vehiculo_placa,
                    v.foto AS vehiculo_foto,
                    CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
                    i.foto AS instructor_foto,
                    ec.nombre AS estado_clase,
                    ccp.estudiante_fecha_calificacion -- Campo para verificar si ya fue calificada
                FROM 
                    clases_practicas cp
                LEFT JOIN programas p ON cp.programa_id = p.id
                LEFT JOIN vehiculos v ON cp.vehiculo_id = v.id
                LEFT JOIN instructores i ON cp.instructor_id = i.id
                LEFT JOIN param_estados_clases ec ON cp.estado_id = ec.id
                LEFT JOIN control_clases_practicas ccp ON cp.id = ccp.clase_practica_id -- Relación con control_clases_practicas
                WHERE 
                    cp.empresa_id = :empresa_id
                    AND cp.matricula_id IN (
                        SELECT id FROM matriculas WHERE estudiante_id = :estudiante_id
                    )
                ORDER BY cp.fecha ASC, cp.hora_inicio ASC;
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->bindParam(':estudiante_id', $estudianteId);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cargar la vista para listar las clases del estudiante
        ob_start();
        include '../modules/clases/views/clases_practicas/clases_practicas_estudiante.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function cronograma()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_practicas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener los estados de clases desde la base de datos
        $queryEstados = "SELECT id, nombre FROM param_estados_clases";
        $stmtEstados = $this->conn->prepare($queryEstados);
        $stmtEstados->execute();
        $estadosClases = $stmtEstados->fetchAll(PDO::FETCH_ASSOC);

        // Definir la variable $instructores como un array vacío
        $instructores = [];

        // Cargar la vista inicial del cronograma
        ob_start();
        include '../modules/clases/views/clases_practicas/cronograma.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function cronogramaEstudiante()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_practicas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Definir la variable $instructores como un array vacío
        $instructores = [];

        // Cargar la vista inicial del cronograma
        ob_start();
        include '../modules/clases/views/clases_practicas/cronograma_estudiante.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function cronogramaInstructor()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_clases_practicas')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Definir la variable $instructores como un array vacío
        $instructores = [];

        // Cargar la vista inicial del cronograma
        ob_start();
        include '../modules/clases/views/clases_practicas/cronograma_instructor.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function getClasesByFecha($fecha)
    {
        // Verificar si la solicitud es AJAX o si estamos en un entorno de prueba (accediendo directamente desde el navegador)
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            // Comentar o eliminar esta línea si queremos permitir acceso directo sin restricciones
            // header('HTTP/1.1 403 Forbidden');
            // echo json_encode(['error' => 'Acceso no autorizado']);
            // exit;
        }

        // Verificar si la fecha fue proporcionada
        if (!$fecha) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Fecha no proporcionada']);
            exit;
        }

        $empresaId = $_SESSION['empresa_id'];

        $query = "SELECT 
                i.id AS instructor_id, 
                i.nombres, 
                i.apellidos, 
                c.id AS clase_id, 
                c.nombre AS clase_nombre, 
                c.hora_inicio, 
                c.hora_fin, 
                c.estado,
                c.estado_id,
                COALESCE(c.observaciones, '') AS observaciones,

                -- estudiante solo si hay matrícula
                CASE 
                    WHEN c.matricula_id IS NULL THEN '---'
                    ELSE COALESCE(e.nombres, '')
                END AS estudiante_nombre,
                CASE 
                    WHEN c.matricula_id IS NULL THEN ''
                    ELSE COALESCE(e.apellidos, '')
                END AS estudiante_apellidos,
                CASE 
                    WHEN c.matricula_id IS NULL THEN ''
                    ELSE COALESCE(e.numero_documento, '')
                END AS estudiante_documento,

                -- programa solo si no es reserva
                CASE 
                    WHEN c.programa_id = 999 THEN 'RESERVA'
                    ELSE COALESCE(p.nombre, '')
                END AS programa_nombre,

                COALESCE(v.placa, '') AS vehiculo_placa

            FROM instructores i
            LEFT JOIN clases_practicas c 
                ON i.id = c.instructor_id 
                AND c.fecha = :fecha
            LEFT JOIN matriculas m 
                ON c.matricula_id = m.id
            LEFT JOIN estudiantes e 
                ON m.estudiante_id = e.id
            LEFT JOIN programas p 
                ON c.programa_id = p.id
            LEFT JOIN vehiculos v 
                ON c.vehiculo_id = v.id
            WHERE i.empresa_id = :empresa_id
            AND (i.estado IS NULL OR i.estado != 0)
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enviar los datos como JSON
        header('Content-Type: application/json');
        echo json_encode($clases);
        exit;
    }

    public function getClasesEstudianteByFecha($fecha)
    {
        // Verificar si la solicitud es AJAX o si estamos en un entorno de prueba
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            // Comentar o eliminar esta línea si queremos permitir acceso directo sin restricciones
            // header('HTTP/1.1 403 Forbidden');
            // echo json_encode(['error' => 'Acceso no autorizado']);
            // exit;
        }

        // Verificar si la fecha fue proporcionada
        if (!$fecha) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Fecha no proporcionada']);
            exit;
        }

        // Verificar si el estudiante está autenticado
        if (!isset($_SESSION['estudiante_id'])) {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'No autorizado']);
            exit;
        }

        $empresaId = $_SESSION['empresa_id'];
        $estudianteId = $_SESSION['estudiante_id']; // Obtener el ID del estudiante desde la sesión

        // Consulta a la base de datos para obtener las clases prácticas del estudiante en la fecha seleccionada
        $query = "
            SELECT 
                i.id AS instructor_id, 
                i.nombres AS instructor_nombres, 
                i.apellidos AS instructor_apellidos, 
                c.id AS clase_id, 
                c.nombre AS clase_nombre, 
                c.hora_inicio, 
                c.hora_fin, 
                c.estado,
                c.matricula_id AS matricula_id,  
                e.id AS estudiante_id, 
                e.nombres AS estudiante_nombre, 
                e.apellidos AS estudiante_apellidos,
                e.numero_documento AS estudiante_documento,
                p.nombre AS programa_nombre, 
                v.placa AS vehiculo_placa
            FROM clases_practicas c
            LEFT JOIN instructores i ON i.id = c.instructor_id
            LEFT JOIN matriculas m ON c.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN programas p ON c.programa_id = p.id
            LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
            WHERE c.fecha = :fecha 
            AND e.id = :estudiante_id
            AND i.empresa_id = :empresa_id
        ";


        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':estudiante_id', $estudianteId, PDO::PARAM_INT);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enviar los datos como JSON
        header('Content-Type: application/json');
        echo json_encode($clases);
        exit;
    }

    # Muestra las clases prácticas del instructor por fecha
    public function getClasesInstructorByFecha($fecha)
    {
        // Verificar si la fecha fue proporcionada
        if (!$fecha) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'Fecha no proporcionada']);
            exit;
        }

        // Obtener empresa e instructor desde la sesión
        $empresaId = $_SESSION['empresa_id'];
        $instructorId = $_SESSION['instructor_id'];

        // Consulta a la base de datos para obtener las clases prácticas del instructor
        $query = "
                SELECT 
                    i.id AS instructor_id, 
                    i.nombres AS instructor_nombres, 
                    i.apellidos AS instructor_apellidos, 
                    c.id AS clase_id, 
                    c.nombre AS clase_nombre, 
                    c.hora_inicio, 
                    c.hora_fin, 
                    c.estado,
                    c.matricula_id AS matricula_id,  
                    e.id AS estudiante_id, 
                    e.nombres AS estudiante_nombre, 
                    e.apellidos AS estudiante_apellidos,
                    e.numero_documento AS estudiante_documento,
                    e.celular AS estudiante_telefono,
                    p.nombre AS programa_nombre, 
                    v.placa AS vehiculo_placa
                FROM clases_practicas c
                LEFT JOIN instructores i ON i.id = c.instructor_id
                LEFT JOIN matriculas m ON c.matricula_id = m.id
                LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                LEFT JOIN programas p ON c.programa_id = p.id
                LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
                WHERE c.fecha = :fecha
                AND c.instructor_id = :instructor_id
                AND c.empresa_id = :empresa_id
                ORDER BY c.hora_inicio ASC
            ";

        // Ejecutar la consulta
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enviar los datos como JSON
        header('Content-Type: application/json');
        echo json_encode($clases);
        exit;
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_clases_practicas')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'] ?? null;
        $estado_id = 1; // Estado inicial (por ejemplo, "programada")
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $matricula_id = $_POST['matricula_id'];
        $lugar = $_POST['lugar'] ?? null;
        $vehiculo_id = $_POST['vehiculo_id'] ?? null;
        $instructor_id = $_POST['instructor_id'];
        $observaciones = $_POST['observaciones'] ?? null;

        $query = "
            INSERT INTO clases_practicas (
                nombre, 
                descripcion, 
                estado_id, 
                fecha, 
                hora_inicio, 
                hora_fin, 
                matricula_id, 
                lugar, 
                vehiculo_id, 
                instructor_id, 
                observaciones, 
                empresa_id
            ) 
            VALUES (
                :nombre, 
                :descripcion, 
                :estado_id, 
                :fecha, 
                :hora_inicio, 
                :hora_fin, 
                :matricula_id, 
                :lugar, 
                :vehiculo_id, 
                :instructor_id, 
                :observaciones, 
                :empresa_id
            )
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':estado_id', $estado_id);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':hora_inicio', $hora_inicio);
        $stmt->bindParam(':hora_fin', $hora_fin);
        $stmt->bindParam(':matricula_id', $matricula_id);
        $stmt->bindParam(':lugar', $lugar);
        $stmt->bindParam(':vehiculo_id', $vehiculo_id);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':empresa_id', $empresaId);

        if ($stmt->execute()) {
            echo "Clase agregada exitosamente.";
        } else {
            echo "Error al agregar la clase.";
        }
    }

    public function storeClasePractica()
    {
        require_once '../modules/matriculas/controllers/MatriculasController.php';
        require_once '../modules/programas/controllers/ProgramasController.php';
        require_once '../modules/instructores/controllers/InstructoresController.php';
        require_once '../modules/vehiculos/controllers/VehiculosController.php';
        require_once '../modules/estudiantes/controllers/EstudiantesController.php';
        require_once '../shared/utils/FormatearFechaHumana.php';
        require_once '../modules/programas/controllers/TemasController.php';

        // Recoger datos del formulario
        $nombre = $_POST['nombre_clase'] ?? '';
        $estado_id = $_POST['estado_id'] ?? 1;
        $fecha = $_POST['fecha_clase'] ?? '';
        $hora_inicio = $_POST['hora_inicio'] ?? '';
        $hora_fin = $_POST['hora_fin'] ?? '';
        $matricula_id = $_POST['matricula_id'] ?? '';
        $programa_id = $_POST['programa_id'] ?? '';
        $clase_programa_id = $_POST['clase_programa_id'] ?? '';
        $lugar = $_POST['lugar_recogida'] ?? '';
        $vehiculo_id = $_POST['vehiculo_id'] ?? null;
        $instructor_id = $_POST['instructor_id'] ?? '';
        $observaciones = $_POST['observaciones'] ?? '';
        $empresa_id = $_SESSION['empresa_id'];

        // ======================= RESERVA ==========================
        if ((int)$estado_id === 10) {
            $datos = $this->prepararDatosReserva([
                'nombre'        => $_POST['nombre_clase'] ?? '',
                'fecha'         => $_POST['fecha_clase'] ?? '',
                'hora_inicio'   => $_POST['hora_inicio'] ?? '',
                'hora_fin'      => $_POST['hora_fin'] ?? '',
                'observaciones' => $_POST['observaciones'] ?? '',
                'instructor_id' => $_POST['instructor_id'] ?? '',
                'empresa_id'    => $_SESSION['empresa_id'],
            ]);

            try {
                $this->conn->beginTransaction();

                $query = "INSERT INTO clases_practicas 
                    (nombre, estado_id, fecha, hora_inicio, hora_fin, matricula_id, programa_id, clase_programa_id, lugar, vehiculo_id, instructor_id, observaciones, empresa_id) 
                    VALUES (:nombre, :estado_id, :fecha, :hora_inicio, :hora_fin, :matricula_id, :programa_id, :clase_programa_id, :lugar, :vehiculo_id, :instructor_id, :observaciones, :empresa_id)";

                $stmt = $this->conn->prepare($query);
                foreach ($datos as $k => $v) {
                    $stmt->bindValue(":$k", $v);
                }
                $stmt->execute();

                $this->conn->commit();

                echo json_encode(['success' => true, 'message' => 'Reserva creada exitosamente.']);
                return;
            } catch (Exception $e) {
                if ($this->conn->inTransaction()) $this->conn->rollBack();
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                return;
            }
        }

        // ======================= VALIDACIONES ==========================

        if (
            empty($nombre) || empty($fecha) || empty($hora_inicio) || empty($hora_fin) ||
            empty($matricula_id) || empty($programa_id) || empty($clase_programa_id) ||
            empty($instructor_id)
        ) {

            echo json_encode(['success' => false, 'error' => 'Datos faltantes. Verifica todos los campos.']);
            return;
        }

        try {

            // ----------------------------------------------------------
            // 🔥 VALIDACIÓN: estudiante no puede tener otra clase en el MISMO horario
            // ----------------------------------------------------------
            $queryEstudianteHorario = "
                SELECT COUNT(*) AS total
                FROM clases_practicas
                WHERE matricula_id = :matricula_id
                AND fecha = :fecha
                AND (
                    hora_inicio < :hora_fin
                    AND hora_fin > :hora_inicio
                )
            ";

            $stmtEst = $this->conn->prepare($queryEstudianteHorario);
            $stmtEst->bindParam(':matricula_id', $matricula_id);
            $stmtEst->bindParam(':fecha', $fecha);
            $stmtEst->bindParam(':hora_inicio', $hora_inicio);
            $stmtEst->bindParam(':hora_fin', $hora_fin);
            $stmtEst->execute();

            $ocupado = $stmtEst->fetch(PDO::FETCH_ASSOC);

            if ($ocupado['total'] > 0) {
                echo json_encode([
                    'success' => false,
                    'error' => 'El estudiante ya tiene una clase programada en este mismo horario.'
                ]);
                return;
            }

            // ----------------------------------------------------------
            // 🔹 Validación existente: vehículo o estudiante asignado
            // ----------------------------------------------------------
            $queryValidacion = "
                SELECT COUNT(*) AS total
                FROM clases_practicas
                WHERE fecha = :fecha
                AND (
                    (vehiculo_id = :vehiculo_id AND vehiculo_id IS NOT NULL) OR
                    (matricula_id = :matricula_id)
                )
                AND (
                    hora_inicio < :hora_fin AND hora_fin > :hora_inicio
                )
            ";

            $stmtValidacion = $this->conn->prepare($queryValidacion);
            $stmtValidacion->bindParam(':fecha', $fecha);
            $stmtValidacion->bindParam(':vehiculo_id', $vehiculo_id);
            $stmtValidacion->bindParam(':matricula_id', $matricula_id);
            $stmtValidacion->bindParam(':hora_inicio', $hora_inicio);
            $stmtValidacion->bindParam(':hora_fin', $hora_fin);
            $stmtValidacion->execute();

            $validacion = $stmtValidacion->fetch(PDO::FETCH_ASSOC);

            if ($validacion['total'] > 0) {
                echo json_encode([
                    'success' => false,
                    'error' => 'El vehículo o el estudiante ya están asignados a otra clase en el mismo horario.'
                ]);
                return;
            }

            // ----------------------------------------------------------
            // 🔹 Validación de horas permitidas (sin cambios)
            // ----------------------------------------------------------

            // ======================= INSERTAR CLASE ==========================
            $this->conn->beginTransaction();

            // 🔹 1. Reordenar clases futuras
            $this->reordenarClasesPracticasFuturas(
                $matricula_id,
                $programa_id,
                $clase_programa_id
            );

            $query = "INSERT INTO clases_practicas 
            (nombre, estado_id, fecha, hora_inicio, hora_fin, matricula_id, programa_id, clase_programa_id, lugar, vehiculo_id, instructor_id, observaciones, empresa_id) 
            VALUES (:nombre, :estado_id, :fecha, :hora_inicio, :hora_fin, :matricula_id, :programa_id, :clase_programa_id, :lugar, :vehiculo_id, :instructor_id, :observaciones, :empresa_id)";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':estado_id', $estado_id);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':matricula_id', $matricula_id);
            $stmt->bindParam(':programa_id', $programa_id);
            $stmt->bindParam(':clase_programa_id', $clase_programa_id);
            $stmt->bindParam(':lugar', $lugar);
            $stmt->bindParam(':vehiculo_id', $vehiculo_id);
            $stmt->bindParam(':instructor_id', $instructor_id);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->bindParam(':empresa_id', $empresa_id);

            $stmt->execute();

            $clasePracticaId = $this->conn->lastInsertId();

            $controlQuery = "INSERT INTO control_clases_practicas (clase_practica_id, estado_clase)
                         VALUES (:clase_practica_id, 'Sin Problemas')";
            $controlStmt = $this->conn->prepare($controlQuery);
            $controlStmt->bindParam(':clase_practica_id', $clasePracticaId);
            $controlStmt->execute();

            $this->conn->commit();

            echo json_encode(['success' => true, 'message' => 'Clase creada exitosamente.']);
            return;
        } catch (Exception $e) {

            if ($this->conn->inTransaction()) $this->conn->rollBack();

            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }



    private function reordenarClasesPracticasFuturas(
        int $matriculaId,
        int $programaId,
        int $claseProgramaNuevaId
    ) {
        // ----------------------------------------------------------
        // 1. Obtener el orden de la clase que se está insertando
        // ----------------------------------------------------------
        $stmt = $this->conn->prepare("
            SELECT orden
            FROM clases_programas
            WHERE id = :clase_programa_id
            AND programa_id = :programa_id
        ");
        $stmt->execute([
            ':clase_programa_id' => $claseProgramaNuevaId,
            ':programa_id'       => $programaId
        ]);

        $ordenNueva = $stmt->fetchColumn();

        if ($ordenNueva === false) {
            return; // seguridad
        }

        // ----------------------------------------------------------
        // 2. Obtener clases prácticas FUTURAS del alumno
        //    que correspondan a órdenes mayores
        // ----------------------------------------------------------
        $stmt = $this->conn->prepare("
            SELECT cp.id AS clase_practica_id,
                cp.clase_programa_id,
                prog.orden
            FROM clases_practicas cp
            INNER JOIN clases_programas prog
                ON prog.id = cp.clase_programa_id
            WHERE cp.matricula_id = :matricula_id
            AND cp.programa_id = :programa_id
            AND prog.orden >= :orden
            AND cp.fecha > CURRENT_DATE
            ORDER BY prog.orden DESC
        ");

        $stmt->execute([
            ':matricula_id' => $matriculaId,
            ':programa_id'  => $programaId,
            ':orden'        => $ordenNueva
        ]);

        $clasesAFuturas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($clasesAFuturas)) {
            return;
        }

        // ----------------------------------------------------------
        // 3. Correr las clases (de abajo hacia arriba)
        // ----------------------------------------------------------
        foreach ($clasesAFuturas as $clase) {

            $nuevoOrden = $clase['orden'] + 1;

            // Buscar el nuevo tema en el plan
            $stmtTema = $this->conn->prepare("
                SELECT id, nombre_clase
                FROM clases_programas
                WHERE programa_id = :programa_id
                AND orden = :orden
            ");

            $stmtTema->execute([
                ':programa_id' => $programaId,
                ':orden'       => $nuevoOrden
            ]);

            $nuevoTema = $stmtTema->fetch(PDO::FETCH_ASSOC);

            if (!$nuevoTema) {
                continue;
            }

            // Actualizar clase práctica
            $stmtUpdate = $this->conn->prepare("
                UPDATE clases_practicas
                SET clase_programa_id = :nuevo_clase_programa_id,
                    nombre = :nuevo_nombre
                WHERE id = :clase_practica_id
            ");

            $stmtUpdate->execute([
                ':nuevo_clase_programa_id' => $nuevoTema['id'],
                ':nuevo_nombre'            => $nuevoTema['nombre_clase'],
                ':clase_practica_id'       => $clase['clase_practica_id']
            ]);
        }
    }







    /**
     * Prepara los datos de una clase práctica cuando es RESERVA (estado_id=10).
     * - Si no hay hora_fin => suma 2 horas a hora_inicio.
     * - Observaciones: si viene vacío => "Reserva de espacio".
     * - Limpia campos que no aplican (estudiante, matrícula, programa, vehículo, instructor).
     */
    private function prepararDatosReserva(array $data): array
    {
        // Normalizar hora_inicio
        $horaInicioStr = $data['hora_inicio'] ?? '00:00:00';
        if (preg_match('/^\d{1,2}$/', $horaInicioStr)) {
            // Si viene solo "06" → convertir a "06:00:00"
            $horaInicioStr .= ':00:00';
        } elseif (preg_match('/^\d{1,2}:\d{2}$/', $horaInicioStr)) {
            // Si viene "06:00" → convertir a "06:00:00"
            $horaInicioStr .= ':00';
        }

        $horaInicio = new DateTime($horaInicioStr);

        // Normalizar hora_fin
        $horaFinStr = $data['hora_fin'] ?? '';
        if (!empty($horaFinStr)) {
            if (preg_match('/^\d{1,2}$/', $horaFinStr)) {
                $horaFinStr .= ':00:00';
            } elseif (preg_match('/^\d{1,2}:\d{2}$/', $horaFinStr)) {
                $horaFinStr .= ':00';
            }
            $horaFin = new DateTime($horaFinStr);
        } else {
            // Si no hay hora_fin, sumamos 2h a la de inicio
            $horaFin = (clone $horaInicio)->modify('+2 hours');
        }

        return [
            'nombre'            => 'Reserva',
            'estado_id'         => 10,
            'fecha'             => $data['fecha'],
            'hora_inicio'       => $horaInicio->format('H:i:s'),
            'hora_fin'          => $horaFin->format('H:i:s'),
            'matricula_id'      => null,
            'programa_id'       => 999, // fijo
            'clase_programa_id' => 999, // fijo
            'lugar'             => null,
            'vehiculo_id'       => null,
            'instructor_id'     => $data['instructor_id'],
            'observaciones'     => !empty($data['observaciones']) ? $data['observaciones'] : 'Reserva de espacio',
            'empresa_id'        => $data['empresa_id'],
        ];
    }


    ## LISTADO DE CLASES PRACTICAS POR INSTUCTOR
    public function listarClasesInstructor()
    {
        // Obtener el ID del instructor desde la sesión
        $instructorId = $_SESSION['instructor_id'];
        $empresaId = $_SESSION['empresa_id'];

        // Verificar si la solicitud es POST (filtrado por fechas)
        $filtroActivo = ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_inicio'], $_POST['fecha_fin']));

        $query = "
            SELECT 
                cp.id AS clase_id,
                cp.fecha,
                cp.hora_inicio,
                cp.hora_fin,
                cp.nombre AS clase_nombre,
                cp.lugar,
                p.nombre AS programa_nombre,
                v.placa AS vehiculo_placa,
                v.foto AS vehiculo_foto,
                CONCAT(e.nombres, ' ', e.apellidos) AS estudiante_nombre,
                e.foto AS estudiante_foto,
                e.numero_documento AS numero_documento,
                e.celular AS telefono,
                ec.id AS estado_id,
                ec.nombre AS estado_clase,
                ccp.instructor_fecha_calificacion,
                ccp.instructor_calificacion,
                ccp.instructor_observaciones, 
                TIMESTAMP(cp.fecha, cp.hora_fin) - INTERVAL 30 MINUTE <= CONVERT_TZ(NOW(), @@global.time_zone, '-05:00') AS clase_terminada
            FROM 
                clases_practicas cp
            LEFT JOIN programas p ON cp.programa_id = p.id
            LEFT JOIN vehiculos v ON cp.vehiculo_id = v.id
            LEFT JOIN matriculas m ON cp.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN param_estados_clases ec ON cp.estado_id = ec.id
            LEFT JOIN (
                SELECT c1.*
                FROM control_clases_practicas c1
                INNER JOIN (
                    SELECT clase_practica_id, MAX(id) AS max_id
                    FROM control_clases_practicas
                    WHERE instructor_calificacion IS NOT NULL
                    GROUP BY clase_practica_id
                ) c2 ON c1.id = c2.max_id
            ) ccp ON cp.id = ccp.clase_practica_id
            WHERE 
                cp.empresa_id = :empresa_id
                AND cp.instructor_id = :instructor_id
        ";

        // Si hay filtro por fechas, agregar condición a la consulta
        if ($filtroActivo) {
            $query .= " AND cp.fecha BETWEEN :fecha_inicio AND :fecha_fin";
        }

        // Agregar ordenamiento final
        $query .= " ORDER BY cp.fecha DESC, cp.hora_inicio ASC";

        // Preparar la consulta
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->bindParam(':instructor_id', $instructorId, PDO::PARAM_INT);

        // Si hay filtro por fechas, vincular parámetros adicionales
        if ($filtroActivo) {
            $stmt->bindParam(':fecha_inicio', $_POST['fecha_inicio'], PDO::PARAM_STR);
            $stmt->bindParam(':fecha_fin', $_POST['fecha_fin'], PDO::PARAM_STR);
        }

        // Ejecutar consulta
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cargar la vista para listar las clases del instructor
        ob_start();
        include '../modules/clases/views/clases_practicas/clases_practicas_instructor.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    ## FORMULARIO PARA CALIFICAR UNA CLASE POR PARTE DEL ESTUDIANTE
    public function calificarClaseEstudiante($clasePracticaId)
    {
        if (!$clasePracticaId) {
            die('ID de clase práctica no proporcionado.');
        }

        // Consultar detalles de la clase
        $queryClase = "
                SELECT 
                    cp.id AS clase_practica_id,
                    cp.fecha,
                    cp.hora_inicio,
                    cp.hora_fin,
                    cp.nombre AS clase_nombre,
                    p.nombre AS programa_nombre,
                    v.placa AS vehiculo_placa,
                    v.foto AS vehiculo_foto,
                    CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
                    i.foto AS instructor_foto,
                    ccp.id AS control_clase_practica_id,
                    ccp.estado_clase
                FROM 
                    clases_practicas cp
                LEFT JOIN programas p ON cp.programa_id = p.id
                LEFT JOIN vehiculos v ON cp.vehiculo_id = v.id
                LEFT JOIN instructores i ON cp.instructor_id = i.id
                LEFT JOIN control_clases_practicas ccp ON cp.id = ccp.clase_practica_id
                WHERE cp.id = :clase_practica_id
            ";

        $stmtClase = $this->conn->prepare($queryClase);
        $stmtClase->bindParam(':clase_practica_id', $clasePracticaId);
        $stmtClase->execute();
        $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

        if (!$clase) {
            die('No se encontró la clase práctica especificada.');
        }

        // Consultar posibles problemas
        $queryProblemas = "SELECT id, nombre FROM param_problemas_clases";
        $problemas = $this->conn->query($queryProblemas)->fetchAll(PDO::FETCH_ASSOC);

        // Cargar la vista de calificación
        ob_start();
        include '../modules/clases/views/clases_practicas/calificar_clase.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    /**
     * Obtiene la calificación de una clase práctica.
     *
     * @param int $claseId - ID de la clase práctica.
     * @return void - Responde en formato JSON.
     */
    public function obtenerCalificacionClase($claseId)
    {
        header('Content-Type: application/json');

        try {
            $query = "
                SELECT 
                    estudiante_calificacion,
                    estudiante_observaciones,
                    estudiante_fecha_calificacion
                FROM control_clases_practicas
                WHERE clase_practica_id = :claseId
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':claseId', $claseId, PDO::PARAM_INT);
            $stmt->execute();

            $calificacion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($calificacion) {
                echo json_encode([
                    'success' => true,
                    'data' => $calificacion
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró la calificación para esta clase.'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener la calificación: ' . $e->getMessage()
            ]);
        }
    }

    ## FORMULARIO PARA CALIFICAR UNA CLASE POR PARTE DEL INSTRUCTOR
    public function calificarClaseInstructor($clasePracticaId)
    {
        if (!$clasePracticaId) {
            die('ID de clase práctica no proporcionado.');
        }

        // Ajuste historial de clases del alumno =================================

        // Asegura empresa para filtrar (si aplica en tu multi-empresa)
        $empresaId = $_SESSION['empresa_id'] ?? null;

        // Asegúrate de traer matricula_id y programa_id de esta clase actual
        // (si tu SELECT inicial no los trae, añade cp.matricula_id y cp.programa_id allí)
        if (!isset($clase['matricula_id']) || !isset($clase['programa_id'])) {
            $qInfo = $this->conn->prepare("
                SELECT matricula_id, programa_id
                FROM clases_practicas
                WHERE id = :cid
                LIMIT 1
            ");
            $qInfo->bindValue(':cid', $clasePracticaId, PDO::PARAM_INT);
            $qInfo->execute();
            $extra = $qInfo->fetch(PDO::FETCH_ASSOC);
            $clase['matricula_id'] = $extra['matricula_id'] ?? null;
            $clase['programa_id']  = $extra['programa_id']  ?? null;
        }

        // Historial de clases de la MISMA matrícula (y empresa)
        $queryHist = "
            SELECT 
                cp.id                    AS clase_practica_id,
                cp.fecha,
                cp.hora_inicio,
                cp.hora_fin,
                cp.nombre                AS clase_nombre,
                p.nombre                 AS programa_nombre,
                v.placa                  AS vehiculo_placa,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
                ccp.instructor_calificacion,
                ccp.estudiante_calificacion,
                ccp.estado_clase,
                ccp.instructor_observaciones,
                ccp.estudiante_observaciones,
                ccp.hora_inicio_real,
                ccp.hora_fin_real,
                ccp.duracion_clase
            FROM clases_practicas cp
            LEFT JOIN programas p      ON cp.programa_id   = p.id
            LEFT JOIN vehiculos v      ON cp.vehiculo_id   = v.id
            LEFT JOIN instructores i   ON cp.instructor_id = i.id
            LEFT JOIN control_clases_practicas ccp ON ccp.clase_practica_id = cp.id
            WHERE cp.matricula_id = :mid
            " . ($empresaId ? "AND cp.empresa_id = :eid" : "") . "
            ORDER BY cp.fecha DESC, cp.hora_inicio DESC
        ";
        $stHist = $this->conn->prepare($queryHist);
        $stHist->bindValue(':mid', $clase['matricula_id'], PDO::PARAM_STR);
        if ($empresaId) $stHist->bindValue(':eid', $empresaId, PDO::PARAM_INT);
        $stHist->execute();
        $historialClases = $stHist->fetchAll(PDO::FETCH_ASSOC);

        // Pequeño resumen para mostrar en la card
        $totalClases = count($historialClases);
        $finalizadas = 0;
        $sumaInst = 0;
        $cantInst = 0;
        foreach ($historialClases as $h) {
            if (!empty($h['estado_clase'])) $finalizadas++;
            if ($h['instructor_calificacion'] !== null) {
                $sumaInst += (int)$h['instructor_calificacion'];
                $cantInst++;
            }
        }
        $promInstructor = $cantInst ? round($sumaInst / $cantInst, 1) : null;


        // Fin ajuste historial de clases del alumno =============================


        // Consultar detalles de la clase y el estudiante
        $queryClase = "
                SELECT 
                    cp.id AS clase_practica_id,
                    cp.fecha,
                    cp.hora_inicio,
                    cp.hora_fin,
                    cp.nombre AS clase_nombre,
                    p.nombre AS programa_nombre,
                    v.placa AS vehiculo_placa,
                    v.foto AS vehiculo_foto,
                    CONCAT(e.nombres, ' ', e.apellidos) AS estudiante_nombre,
                    e.foto AS estudiante_foto,
                    ccp.id AS control_clase_practica_id,
                    ccp.estado_clase
                FROM 
                    clases_practicas cp
                LEFT JOIN programas p ON cp.programa_id = p.id
                LEFT JOIN vehiculos v ON cp.vehiculo_id = v.id
                LEFT JOIN matriculas m ON cp.matricula_id = m.id
                LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                LEFT JOIN control_clases_practicas ccp ON cp.id = ccp.clase_practica_id
                WHERE cp.id = :clase_practica_id
            ";

        $stmtClase = $this->conn->prepare($queryClase);
        $stmtClase->bindParam(':clase_practica_id', $clasePracticaId, PDO::PARAM_INT);
        $stmtClase->execute();
        $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

        if (!$clase) {
            die('No se encontró la clase práctica especificada.');
        }

        // Consultar posibles problemas
        $queryProblemas = "SELECT id, nombre FROM param_problemas_clases";
        $problemas = $this->conn->query($queryProblemas)->fetchAll(PDO::FETCH_ASSOC);

        // Cargar la vista de calificación del instructor
        ob_start();
        include '../modules/clases/views/clases_practicas/calificar_clase_instructor.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    ## ALMACENA LA CALIFICACION QUE HACE EL INSTRUCTOR DEL ESTUDIANTE Y FINALIZA LA CLASE
    public function calificarClaseInstructorStore()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $clasePracticaId = $_POST['clase_practica_id'];
            $calificacion = isset($_POST['instructor_calificacion']) ? intval($_POST['instructor_calificacion']) : null;
            $observaciones = $_POST['instructor_observaciones'] ?? null;
            $finalizarClase = isset($_POST['finalizar_clase']) ? 1 : 0; // Si el switch está activado, se envía como 1

            try {
                // Iniciar una transacción
                $this->conn->beginTransaction();

                $queryUpdateControl = "
                        UPDATE control_clases_practicas 
                        SET 
                            instructor_calificacion = :calificacion,
                            instructor_observaciones = :observaciones,
                            instructor_fecha_calificacion = NOW()
                        WHERE clase_practica_id = :clase_practica_id
                    ";

                $stmt = $this->conn->prepare($queryUpdateControl);
                $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_INT);
                $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
                $stmt->bindParam(':clase_practica_id', $clasePracticaId, PDO::PARAM_INT);
                $stmt->execute();


                // Validar si se actualizó alguna fila
                if ($stmt->rowCount() === 0) {
                    throw new Exception('No se pudo actualizar la calificación. Verifica el ID de la clase.');
                }

                // Validar si se debe finalizar la clase
                if ($finalizarClase) {
                    $queryFinalizarClase = "
                        UPDATE clases_practicas 
                        SET estado_id = 3 
                        WHERE id = :clase_practica_id
                    ";

                    $stmtFinalizar = $this->conn->prepare($queryFinalizarClase);
                    $stmtFinalizar->bindParam(':clase_practica_id', $clasePracticaId, PDO::PARAM_INT);
                    $stmtFinalizar->execute();
                }

                // Confirmar la transacción
                $this->conn->commit();
                $_SESSION['success_message'] = 'La clase ha sido calificada.';
                header('Location: ' . $routes['clases_practicas_listado_instructor']);
                exit;
            } catch (Exception $e) {
                $this->conn->rollBack();
                die('Error al procesar la calificación: ' . $e->getMessage());
            }
        } else {
            die('Método de solicitud no válido.');
        }
    }

    ## ALMACENA LA CALIFICACION QUE HACE EL ESTUDIANTE DE LA CLASE
    public function calificarClaseEstudianteStore()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $clasePracticaId = $_POST['clase_practica_id'];
            $calificacion = isset($_POST['estudiante_calificacion']) ? intval($_POST['estudiante_calificacion']) : null;
            $observaciones = $_POST['estudiante_observaciones'] ?? null;
            $problemas = $_POST['problemas'] ?? [];

            try {
                // Verificar si el estudiante ya calificó la clase
                $queryVerificarCalificacion = "
                SELECT estudiante_fecha_calificacion 
                FROM control_clases_practicas 
                WHERE clase_practica_id = :clase_practica_id
            ";
                $stmtVerificar = $this->conn->prepare($queryVerificarCalificacion);
                $stmtVerificar->bindParam(':clase_practica_id', $clasePracticaId, PDO::PARAM_INT);
                $stmtVerificar->execute();

                $resultado = $stmtVerificar->fetch(PDO::FETCH_ASSOC);



                if ($resultado && !is_null($resultado['estudiante_fecha_calificacion'])) {

                    $_SESSION['error_message'] = 'La clase ya ha sido calificada por el estudiante.';
                    header('Location: ' . $routes['clases_practicas_listado_estudiante']);
                    exit;
                }

                // Iniciar una transacción
                $this->conn->beginTransaction();

                // Actualizar la tabla `control_clases_practicas`
                $queryUpdateControl = "
                UPDATE control_clases_practicas 
                SET 
                    estudiante_calificacion = :calificacion,
                    estudiante_observaciones = :observaciones,
                    estudiante_fecha_calificacion = NOW()
                WHERE clase_practica_id = :clase_practica_id
            ";

                $stmt = $this->conn->prepare($queryUpdateControl);
                $stmt->bindParam(':calificacion', $calificacion, PDO::PARAM_INT);
                $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
                $stmt->bindParam(':clase_practica_id', $clasePracticaId, PDO::PARAM_INT);
                $stmt->execute();

                // Insertar problemas seleccionados
                if (!empty($problemas)) {
                    $queryInsertProblemas = "
                    INSERT INTO clase_problemas (clase_practica_id, problema_id) 
                    VALUES (:clase_practica_id, :problema_id)
                ";

                    $stmtProblema = $this->conn->prepare($queryInsertProblemas);

                    foreach ($problemas as $problemaId) {
                        $stmtProblema->bindParam(':clase_practica_id', $clasePracticaId, PDO::PARAM_INT);
                        $stmtProblema->bindParam(':problema_id', $problemaId, PDO::PARAM_INT);
                        $stmtProblema->execute();
                    }
                }

                // Confirmar transacción
                $this->conn->commit();

                $_SESSION['success_message'] = 'La clase ha sido calificada.';
                header('Location: ' . $routes['clases_practicas_listado_estudiante']);
                exit;
            } catch (Exception $e) {
                // Revertir transacción en caso de error
                $this->conn->rollBack();
                die('Error al procesar la calificación: ' . $e->getMessage());
            }
        } else {
            die('Método de solicitud no válido.');
        }
    }

    ## Obtener instructores disponibles para una fecha y horario dado
    public function obtenerInstructoresDisponibles()
    {
        header('Content-Type: application/json');

        // Validar que se recibe por POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Método no permitido.']);
            return;
        }

        // Obtener datos enviados por POST
        $fecha = $_POST['fecha'] ?? null;
        $horaInicio = $_POST['hora_inicio'] ?? null;
        $horaFin = $_POST['hora_fin'] ?? null;
        $empresaId = $_SESSION['empresa_id'] ?? null;

        if (!$fecha || !$horaInicio || !$horaFin || !$empresaId) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan datos requeridos.']);
            return;
        }

        try {
            $query = "
                SELECT id, CONCAT(nombres, ' ', apellidos) AS nombre
                FROM instructores
                WHERE empresa_id = :empresa_id
                  AND estado = 1
                  AND id NOT IN (
                      SELECT instructor_id
                      FROM clases_practicas
                      WHERE fecha = :fecha
                        AND (hora_inicio < :hora_fin AND hora_fin > :hora_inicio)
                  )
                ORDER BY nombres ASC
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':empresa_id', $empresaId);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $horaInicio);
            $stmt->bindParam(':hora_fin', $horaFin);

            $stmt->execute();
            $instructores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($instructores);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
        }
    }

    // Controlador para obtener los detalles de una clase práctica
    public function getClasePracticaDetalle($claseId)
    {
        // Verificar que la solicitud es AJAX
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
            header('HTTP/1.1 403 Forbidden');
            echo json_encode(['error' => 'Acceso no autorizado']);
            exit;
        }

        // Verificar que el ID de la clase fue proporcionado
        if (!$claseId) {
            header('HTTP/1.1 400 Bad Request');
            echo json_encode(['error' => 'ID de clase no proporcionado']);
            exit;
        }

        // Consulta a la base de datos para obtener los detalles de la clase práctica
        $query = "
            SELECT 
                c.id AS clase_id, 
                c.nombre AS clase_nombre, 
                c.estado_id, 
                UPPER(pec.nombre) AS estado_nombre,  -- 🔥 Nombre del estado en mayúsculas
                c.fecha, 
                c.hora_inicio, 
                c.hora_fin, 
                c.matricula_id, 
                c.programa_id, 
                c.clase_programa_id,
                c.estado,
                cp.nombre_clase AS clase_programa_nombre,
                cp.numero_horas,
                c.lugar, 
                c.vehiculo_id, 
                c.instructor_id, 
                c.observaciones, 
                e.nombres AS estudiante_nombre, 
                e.apellidos AS estudiante_apellidos, 
                e.numero_documento AS cedula_estudiante, 
                CONCAT('/files/fotos_estudiantes/', e.foto) AS foto_estudiante_url, 
                i.nombres AS instructor_nombre, 
                i.apellidos AS instructor_apellidos, 
                v.placa AS vehiculo_placa, 
                p.tipo_vehiculo_id AS tipo_vehiculo_id,
                p.nombre AS programa_nombre
            FROM clases_practicas c
            LEFT JOIN clases_programas cp ON c.clase_programa_id = cp.id
            LEFT JOIN matriculas m ON c.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN instructores i ON c.instructor_id = i.id
            LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
            LEFT JOIN programas p ON c.programa_id = p.id
            LEFT JOIN param_estados_clases pec ON c.estado_id = pec.id  -- 🔥 Unir con la tabla de estados
            WHERE c.id = :clase_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clase_id', $claseId, PDO::PARAM_INT);

        if ($stmt->execute()) {

            $clase = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($clase) {
                // Enviar los detalles de la clase en formato JSON
                header('Content-Type: application/json');

                // Codifica los detalles de la clase en JSON de una forma segura para evitar problemas de codificación
                $jsonResponse = json_encode($clase, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

                if ($jsonResponse) {
                    echo $jsonResponse;
                } else {
                    // Error en la codificación de JSON
                    header('HTTP/1.1 500 Internal Server Error');
                    echo json_encode(['error' => 'Error al codificar los detalles de la clase en JSON']);
                }
            } else {
                // No se encontró la clase con el ID proporcionado
                header('HTTP/1.1 404 Not Found');
                echo json_encode(['error' => 'Clase no encontrada']);
            }
        } else {
            // Error en la consulta
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(['error' => 'Error al obtener los detalles de la clase']);
        }

        exit;
    }

    public function store()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_clases_practicas')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            $clase_id = $_POST['nombre_clase'];
            $fecha = $_POST['fecha'];
            $hora_inicio = explode(' - ', $_POST['hora'])[0];
            $duracion = (int)$_POST['duracion'];
            $matricula_id = $_POST['codigo_matricula'];
            $programa_id = $_POST['programa_id'];
            $vehiculo_id = $_POST['vehiculo_id'];
            $instructor_id = $_POST['instructor_id'];
            $lugar = $_POST['lugar'];
            $estado_id = 1; // Valor por defecto
            $empresa_id = $_SESSION['empresa_id'];
            $observaciones = $_POST['observaciones'];

            // [Obtener el nombre de la clase] start
            $query = "SELECT nombre_clase FROM clases_programas WHERE id = :clase_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':clase_id', $clase_id);
            $stmt->execute();
            $clase = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$clase) {
                throw new Exception("La clase con ID $clase_id no existe.");
            }
            $nombre_clase = $clase['nombre_clase'];
            // [Obtener el nombre de la clase] end


            // Calcular la hora de fin
            $hora_fin_timestamp = strtotime($hora_inicio) + ($duracion * 3600); // Calcular el timestamp de la hora final
            $hora_fin = date('H:i', $hora_fin_timestamp); // Formatear la hora final

            $query = "
                INSERT INTO clases_practicas (
                    nombre,
                    estado_id,
                    fecha,
                    hora_inicio,
                    hora_fin,
                    matricula_id,
                    programa_id,
                    lugar,
                    vehiculo_id,
                    instructor_id,
                    observaciones,
                    empresa_id
                ) VALUES (
                    :nombre,
                    :estado_id,
                    :fecha,
                    :hora_inicio,
                    :hora_fin,
                    :matricula_id,
                    :programa_id,
                    :lugar,
                    :vehiculo_id,
                    :instructor_id,
                    :observaciones,
                    :empresa_id
                )
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':nombre', $nombre_clase);
            $stmt->bindParam(':estado_id', $estado_id);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':hora_inicio', $hora_inicio);
            $stmt->bindParam(':hora_fin', $hora_fin);
            $stmt->bindParam(':matricula_id', $matricula_id);
            $stmt->bindParam(':programa_id', $programa_id);
            $stmt->bindParam(':lugar', $lugar);
            $stmt->bindParam(':vehiculo_id', $vehiculo_id);
            $stmt->bindParam(':instructor_id', $instructor_id);
            $stmt->bindParam(':observaciones', $observaciones);
            $stmt->bindParam(':empresa_id', $empresa_id);

            if ($stmt->execute()) {
                header('Location: /clasespracticascronograma/' . $fecha);
                exit;
            } else {
                $errorInfo = $stmt->errorInfo();
                echo "Error al guardar la clase práctica. SQLSTATE: " . $errorInfo[0] . " Código de error: " . $errorInfo[1] . " Mensaje: " . $errorInfo[2];
            }
        } catch (Exception $e) {
            error_log("Excepción al guardar la clase práctica: " . $e->getMessage());
            echo "Error al guardar la clase práctica: " . $e->getMessage();
        }
    }

    public function detalleClasePractica($claseId)
    {
        $routes = include '../config/Routes.php';

        // Verificar permisos
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        // if (!$permissionController->hasPermission($currentUserId, 'view_detalle_clase')) {
        //     echo "No tienes permiso para ver esta página.";
        //     return;
        // }

        $queryClase = "
                SELECT 
                cp.id AS clase_id,
                cp.fecha,
                cp.hora_inicio,
                cp.hora_fin,
                cp.matricula_id,
                cp.nombre AS clase_nombre,
                m.id AS matricula_id,
                e.numero_documento,
                e.foto AS estudiante_foto,
                CONCAT(e.nombres, ' ', e.apellidos) AS estudiante_nombre,
                p.nombre AS programa_nombre,
                v.placa AS vehiculo_placa,
                v.foto AS vehiculo_foto,
                CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
                i.foto AS instructor_foto,
                pec.nombre AS estado_clase_nombre, -- Aquí recuperamos el nombre del estado
                ccp.estudiante_calificacion,
                ccp.estudiante_observaciones,
                ccp.instructor_calificacion,
                ccp.instructor_observaciones,
                ccp.estado_clase,
                ccp.detalle_problemas,
                ccp.hora_inicio_real,
                ccp.hora_fin_real,
                ccp.duracion_clase,
                ccp.evidencias
            FROM clases_practicas cp
            LEFT JOIN matriculas m ON cp.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN programas p ON cp.programa_id = p.id
            LEFT JOIN vehiculos v ON cp.vehiculo_id = v.id
            LEFT JOIN instructores i ON cp.instructor_id = i.id
            LEFT JOIN param_estados_clases pec ON cp.estado_id = pec.id -- Relación con el estado
            LEFT JOIN control_clases_practicas ccp ON cp.id = ccp.clase_practica_id
            WHERE cp.id = :clase_id;";


        $stmt = $this->conn->prepare($queryClase);
        $stmt->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
        $stmt->execute();

        $claseDetalle = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$claseDetalle) {
            echo "No se encontraron detalles para esta clase.";
            return;
        }

        // Cargar vista de detalles
        ob_start();
        include '../modules/clases/views/clases_practicas/detalle_clase.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    /**
     * Elimina una clase práctica por su ID y los registros asociados en control_clases_practicas.
     *
     * @param int $claseId - ID de la clase a eliminar.
     * @return void - Devuelve una respuesta JSON.
     */
    public function eliminarClasePractica($claseId)
    {
        header('Content-Type: application/json');

        try {
            $this->conn->beginTransaction();

            // ----------------------------------------------------------
            // 1. Obtener clase a eliminar (contexto completo)
            // ----------------------------------------------------------
            $qClase = $this->conn->prepare("
                    SELECT 
                        cp.id,
                        cp.matricula_id,
                        cp.programa_id,
                        cp.clase_programa_id,
                        cp.fecha,
                        cp.hora_inicio,
                        cpp.orden
                    FROM clases_practicas cp
                    INNER JOIN clases_programas cpp 
                        ON cpp.id = cp.clase_programa_id
                    WHERE cp.id = :id
                ");
            $qClase->execute([':id' => $claseId]);
            $clase = $qClase->fetch(PDO::FETCH_ASSOC);

            if (!$clase) {
                throw new Exception('La clase práctica no existe.');
            }

            $ordenEliminado = (int)$clase['orden'];

            // ----------------------------------------------------------
            // 2. Eliminar la clase práctica
            // ----------------------------------------------------------
            $qDel = $this->conn->prepare("
                DELETE FROM clases_practicas 
                WHERE id = :id
            ");
            $qDel->execute([':id' => $claseId]);

            // ----------------------------------------------------------
            // 3. Obtener clases futuras del mismo estudiante / programa
            // ----------------------------------------------------------
            $qFuturas = $this->conn->prepare("
                SELECT 
                    cp.id,
                    cpp.orden
                FROM clases_practicas cp
                INNER JOIN clases_programas cpp 
                    ON cpp.id = cp.clase_programa_id
                WHERE cp.matricula_id = :matricula
                AND cp.programa_id  = :programa
                AND (
                        cp.fecha > :fecha
                    OR (cp.fecha = :fecha AND cp.hora_inicio > :hora)
                )
                ORDER BY cpp.orden ASC
            ");

            $qFuturas->execute([
                ':matricula' => $clase['matricula_id'],
                ':programa'  => $clase['programa_id'],
                ':fecha'     => $clase['fecha'],
                ':hora'      => $clase['hora_inicio'],
            ]);

            $clasesFuturas = $qFuturas->fetchAll(PDO::FETCH_ASSOC);

            if (!empty($clasesFuturas)) {

                // ----------------------------------------------------------
                // 4. Obtener temas oficiales del programa
                // ----------------------------------------------------------
                $qTemas = $this->conn->prepare("
                    SELECT id, nombre_clase, numero_horas, orden
                    FROM clases_programas
                    WHERE programa_id = :programa
                    ORDER BY orden ASC
                ");
                $qTemas->execute([':programa' => $clase['programa_id']]);
                $temas = $qTemas->fetchAll(PDO::FETCH_ASSOC);

                // Indexar por orden
                $temasPorOrden = [];
                foreach ($temas as $t) {
                    $temasPorOrden[(int)$t['orden']] = $t;
                }

                // ----------------------------------------------------------
                // 5. Reasignar temas y nombre
                // ----------------------------------------------------------
                $qUpdate = $this->conn->prepare("
                    UPDATE clases_practicas
                    SET 
                        clase_programa_id = :clase_programa_id,
                        nombre            = :nombre
                    WHERE id = :id
                ");

                foreach ($clasesFuturas as $claseFutura) {

                    $nuevoOrden = (int)$claseFutura['orden'] - 1;

                    if (!isset($temasPorOrden[$nuevoOrden])) {
                        continue;
                    }

                    $tema = $temasPorOrden[$nuevoOrden];
                    $nombre = $tema['nombre_clase'] . ' (' . $tema['numero_horas'] . ' horas)';

                    $qUpdate->execute([
                        ':clase_programa_id' => $tema['id'],
                        ':nombre'            => $nombre,
                        ':id'                => $claseFutura['id'],
                    ]);
                }
            }

            $this->conn->commit();

            echo json_encode([
                'success' => true,
                'message' => 'Clase eliminada y secuencia ajustada correctamente.'
            ]);
            return;
        } catch (Exception $e) {

            if ($this->conn->inTransaction()) {
                $this->conn->rollBack();
            }

            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            return;
        }
    }

    /**
     * Edita una clase práctica existente.
     *
     * @param int $claseId - ID de la clase a editar.
     * @return void - Responde en formato JSON.
     */
    public function editarClasePractica($claseId)
    {
        header('Content-Type: application/json');

        try {
            // Recoger los datos enviados
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data || !isset($data['clase_programa_id'], $data['vehiculo_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos incompletos.'
                ]);
                return;
            }

            // Construir dinámicamente el query si se desea cambiar el instructor
            $actualizarInstructor = isset($data['instructor_id']) && !empty($data['instructor_id']);

            $query = "UPDATE clases_practicas
                SET clase_programa_id = :clase_programa_id,
                    vehiculo_id = :vehiculo_id,
                    observaciones = :observaciones,
                    updated_at = NOW()";

            if ($actualizarInstructor) {
                $query .= ", instructor_id = :instructor_id";
            }

            $query .= " WHERE id = :claseId";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':clase_programa_id', $data['clase_programa_id'], PDO::PARAM_INT);
            $stmt->bindParam(':vehiculo_id', $data['vehiculo_id'], PDO::PARAM_INT);
            $stmt->bindParam(':observaciones', $data['observaciones'], PDO::PARAM_STR);
            $stmt->bindParam(':claseId', $claseId, PDO::PARAM_INT);

            if ($actualizarInstructor) {
                $stmt->bindParam(':instructor_id', $data['instructor_id'], PDO::PARAM_INT);
            }

            if ($stmt->execute()) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Clase práctica actualizada correctamente.'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar la clase práctica.'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar la clase práctica: ' . $e->getMessage()
            ]);
        }
    }

    ## Cambiar el estado de una clase practica
    public function cambiarEstadoClasePractica()
    {
        header('Content-Type: application/json');
        require_once '../modules/auditoria/controllers/AuditoriaController.php';

        try {
            // Verificar que los datos lleguen correctamente por POST
            if (!isset($_POST['clase_id']) || !isset($_POST['estado_id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan datos obligatorios para actualizar el estado.'
                ]);
                return;
            }

            // Obtener datos desde POST
            $claseId = $_POST['clase_id'];
            $estadoId = $_POST['estado_id'];
            $observaciones = $_POST['observaciones'] ?? null;

            // Obtener usuario y empresa de la sesión
            $currentUserId = $_SESSION['user_id'];
            $empresaId = $_SESSION['empresa_id'];

            // 1️⃣ Verificar si la clase existe
            $queryVerificar = "SELECT id, estado_id FROM clases_practicas WHERE id = :claseId";
            $stmtVerificar = $this->conn->prepare($queryVerificar);
            $stmtVerificar->bindParam(':claseId', $claseId, PDO::PARAM_INT);
            $stmtVerificar->execute();
            $clase = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if (!$clase) {
                echo json_encode([
                    'success' => false,
                    'message' => 'La clase práctica no existe.'
                ]);
                return;
            }

            // 2️⃣ Verificar si el estado es válido
            $queryEstado = "SELECT nombre FROM param_estados_clases WHERE id = :estadoId";
            $stmtEstado = $this->conn->prepare($queryEstado);
            $stmtEstado->bindParam(':estadoId', $estadoId, PDO::PARAM_INT);
            $stmtEstado->execute();
            $estado = $stmtEstado->fetch(PDO::FETCH_ASSOC);

            if (!$estado) {
                echo json_encode([
                    'success' => false,
                    'message' => 'El estado seleccionado no es válido.'
                ]);
                return;
            }

            // 3️⃣ Actualizar el estado de la clase y guardar observaciones
            $query = "UPDATE clases_practicas SET estado_id = :estadoId, observaciones = :observaciones WHERE id = :claseId";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':estadoId', $estadoId, PDO::PARAM_INT);
            $stmt->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
            $stmt->bindParam(':claseId', $claseId, PDO::PARAM_INT);

            if ($stmt->execute()) {

                // ------------------------------------------------------------
                // 🔹 Cancelación con multa (4) o sin multa (5)
                // ------------------------------------------------------------
                if ($estadoId == 4 || $estadoId == 5) {

                    // Obtener datos completos antes de registrar novedad
                    $queryClase = "SELECT * FROM clases_practicas WHERE id = :claseId";
                    $stmtClase = $this->conn->prepare($queryClase);
                    $stmtClase->bindParam(':claseId', $claseId, PDO::PARAM_INT);
                    $stmtClase->execute();
                    $claseData = $stmtClase->fetch(PDO::FETCH_ASSOC);

                    if (!$claseData) {
                        echo json_encode([
                            'success' => false,
                            'message' => 'No se encontraron datos de la clase para registrar la novedad.'
                        ]);
                        return;
                    }

                    // Registrar novedad
                    $queryNovedad = "INSERT INTO clases_novedades 
                        (clase_practica_id, param_estado_clase_id, novedad_estado, tiempo, user_id, observaciones, 
                        empresa_id, fecha, hora_inicio, hora_fin, instructor_id, vehiculo_id, matricula_id, programa_id, lugar, clase_nombre) 
                        VALUES 
                        (:claseId, :estadoId, 'activa', NOW(), :userId, :observaciones, 
                        :empresaId, :fecha, :hora_inicio, :hora_fin, :instructorId, :vehiculoId, :matriculaId, :programaId, :lugar, :claseNombre)";

                    $stmtNovedad = $this->conn->prepare($queryNovedad);
                    $stmtNovedad->bindParam(':claseId', $claseData['id'], PDO::PARAM_INT);
                    $stmtNovedad->bindParam(':estadoId', $estadoId, PDO::PARAM_INT);
                    $stmtNovedad->bindParam(':userId', $currentUserId, PDO::PARAM_INT);
                    $stmtNovedad->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
                    $stmtNovedad->bindParam(':empresaId', $claseData['empresa_id'], PDO::PARAM_INT);
                    $stmtNovedad->bindParam(':fecha', $claseData['fecha'], PDO::PARAM_STR);
                    $stmtNovedad->bindParam(':hora_inicio', $claseData['hora_inicio'], PDO::PARAM_STR);
                    $stmtNovedad->bindParam(':hora_fin', $claseData['hora_fin'], PDO::PARAM_STR);
                    $stmtNovedad->bindParam(':instructorId', $claseData['instructor_id'], PDO::PARAM_INT);
                    $stmtNovedad->bindParam(':vehiculoId', $claseData['vehiculo_id'], PDO::PARAM_INT);
                    $stmtNovedad->bindParam(':matriculaId', $claseData['matricula_id'], PDO::PARAM_STR);
                    $stmtNovedad->bindParam(':programaId', $claseData['programa_id'], PDO::PARAM_INT);
                    $stmtNovedad->bindParam(':lugar', $claseData['lugar'], PDO::PARAM_STR);
                    $stmtNovedad->bindParam(':claseNombre', $claseData['nombre'], PDO::PARAM_STR);
                    $stmtNovedad->execute();


                    // ------------------------------------------------------------------
                    // 🔹 SI EL ESTADO ES 4 → NO ELIMINAR LA CLASE
                    // ------------------------------------------------------------------
                    if ($estadoId == 4) {

                        // Registrar auditoría
                        $descripcion = "Se registró novedad (estado 4) para la clase práctica:\n";
                        $descripcion .= "Clase ID: {$claseId}\n";
                        $descripcion .= "Nombre: {$claseData['nombre']}\n";
                        $descripcion .= "Fecha: {$claseData['fecha']} {$claseData['hora_inicio']} - {$claseData['hora_fin']}\n";
                        $descripcion .= "La clase NO fue eliminada por ser estado 4.";

                        $auditoriaController = new AuditoriaController();
                        $auditoriaController->registrar($currentUserId, 'Actualizar', 'Clases Prácticas', $descripcion, $empresaId);

                        echo json_encode([
                            'success' => true,
                            'message' => 'Novedad registrada. La clase no fue eliminada porque está en estado 4.'
                        ]);
                        return;
                    }


                    // ------------------------------------------------------------------
                    // 🔹 SI EL ESTADO ES 5 → ELIMINAR LA CLASE COMO SIEMPRE
                    // ------------------------------------------------------------------

                    // Eliminar control
                    $queryEliminarControl = "DELETE FROM control_clases_practicas WHERE clase_practica_id = :claseId";
                    $stmtEliminarControl = $this->conn->prepare($queryEliminarControl);
                    $stmtEliminarControl->bindParam(':claseId', $claseId, PDO::PARAM_INT);
                    $stmtEliminarControl->execute();

                    // Eliminar clase
                    $queryEliminarClase = "DELETE FROM clases_practicas WHERE id = :claseId";
                    $stmtEliminarClase = $this->conn->prepare($queryEliminarClase);
                    $stmtEliminarClase->bindParam(':claseId', $claseId, PDO::PARAM_INT);
                    $stmtEliminarClase->execute();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Clase eliminada correctamente (estado 5).'
                    ]);
                    return;
                }







                // Si el estado es "Cancelada con multa" o "Cancelada sin multa", registrar la novedad y eliminar la clase
                // if ($estadoId == 4 || $estadoId == 5) {

                //     // Obtener los datos completos de la clase antes de eliminarla
                //     $queryClase = "SELECT * FROM clases_practicas WHERE id = :claseId";
                //     $stmtClase = $this->conn->prepare($queryClase);
                //     $stmtClase->bindParam(':claseId', $claseId, PDO::PARAM_INT);
                //     $stmtClase->execute();
                //     $claseData = $stmtClase->fetch(PDO::FETCH_ASSOC);

                //     if (!$claseData) {
                //         echo json_encode([
                //             'success' => false,
                //             'message' => 'No se encontraron datos de la clase para registrar la novedad.'
                //         ]);
                //         return;
                //     }

                //     // Insertar los datos de la clase en clases_novedades
                //     $queryNovedad = "INSERT INTO clases_novedades 
                //         (clase_practica_id, param_estado_clase_id, novedad_estado, tiempo, user_id, observaciones, 
                //         empresa_id, fecha, hora_inicio, hora_fin, instructor_id, vehiculo_id, matricula_id, programa_id, lugar, clase_nombre) 
                //         VALUES 
                //         (:claseId, :estadoId, 'activa', NOW(), :userId, :observaciones, 
                //         :empresaId, :fecha, :hora_inicio, :hora_fin, :instructorId, :vehiculoId, :matriculaId, :programaId, :lugar, :claseNombre)";

                //     $stmtNovedad = $this->conn->prepare($queryNovedad);
                //     $stmtNovedad->bindParam(':claseId', $claseData['id'], PDO::PARAM_INT);
                //     $stmtNovedad->bindParam(':estadoId', $estadoId, PDO::PARAM_INT);
                //     $stmtNovedad->bindParam(':userId', $currentUserId, PDO::PARAM_INT);
                //     $stmtNovedad->bindParam(':observaciones', $observaciones, PDO::PARAM_STR);
                //     $stmtNovedad->bindParam(':empresaId', $claseData['empresa_id'], PDO::PARAM_INT);
                //     $stmtNovedad->bindParam(':fecha', $claseData['fecha'], PDO::PARAM_STR);
                //     $stmtNovedad->bindParam(':hora_inicio', $claseData['hora_inicio'], PDO::PARAM_STR);
                //     $stmtNovedad->bindParam(':hora_fin', $claseData['hora_fin'], PDO::PARAM_STR);
                //     $stmtNovedad->bindParam(':instructorId', $claseData['instructor_id'], PDO::PARAM_INT);
                //     $stmtNovedad->bindParam(':vehiculoId', $claseData['vehiculo_id'], PDO::PARAM_INT);
                //     $stmtNovedad->bindParam(':matriculaId', $claseData['matricula_id'], PDO::PARAM_STR);
                //     $stmtNovedad->bindParam(':programaId', $claseData['programa_id'], PDO::PARAM_INT);
                //     $stmtNovedad->bindParam(':lugar', $claseData['lugar'], PDO::PARAM_STR);
                //     $stmtNovedad->bindParam(':claseNombre', $claseData['nombre'], PDO::PARAM_STR); // Agregar el nombre de la clase
                //     $stmtNovedad->execute();

                //     // Eliminar el registro de control_clases_practicas antes de eliminar la clase
                //     $queryEliminarControl = "DELETE FROM control_clases_practicas WHERE clase_practica_id = :claseId";
                //     $stmtEliminarControl = $this->conn->prepare($queryEliminarControl);
                //     $stmtEliminarControl->bindParam(':claseId', $claseId, PDO::PARAM_INT);
                //     $stmtEliminarControl->execute();

                //     // Luego, eliminar la clase de clases_practicas
                //     $queryEliminarClase = "DELETE FROM clases_practicas WHERE id = :claseId";
                //     $stmtEliminarClase = $this->conn->prepare($queryEliminarClase);
                //     $stmtEliminarClase->bindParam(':claseId', $claseId, PDO::PARAM_INT);
                //     $stmtEliminarClase->execute();

                //     // Construir descripción detallada para la auditoría
                //     $descripcion = "Se eliminó la clase práctica con los siguientes detalles:\n";
                //     $descripcion .= " ID de la Clase: {$claseId}\n";
                //     $descripcion .= " Nombre de la Clase: {$claseData['nombre']}\n";
                //     $descripcion .= " Fecha: {$claseData['fecha']}\n";
                //     $descripcion .= " Horario: {$claseData['hora_inicio']} - {$claseData['hora_fin']}\n";
                //     $descripcion .= " Lugar: " . ($claseData['lugar'] ?? 'No especificado') . "\n";
                //     $descripcion .= " Instructor: " . ($claseData['instructor_id'] ? "ID {$claseData['instructor_id']}" : "No asignado") . "\n";
                //     $descripcion .= " Vehículo: " . ($claseData['vehiculo_id'] ? "ID {$claseData['vehiculo_id']}" : "No asignado") . "\n";
                //     $descripcion .= " Programa ID: {$claseData['programa_id']}\n";
                //     $descripcion .= " Matricula ID: {$claseData['matricula_id']}\n";
                //     $descripcion .= " Estado anterior: '{$estado['nombre']}'\n";

                //     if ($observaciones) {
                //         $descripcion .= " Observaciones: {$observaciones}\n";
                //     }

                //     // Registrar en la auditoría
                //     $auditoriaController = new AuditoriaController();
                //     $auditoriaController->registrar($currentUserId, 'Eliminar', 'Clases Prácticas', $descripcion, $empresaId);

                //     echo json_encode([
                //         'success' => true,
                //         'message' => 'Novedad registrada y clase eliminada, recursos liberados.'
                //     ]);

                //     return;
                // }

                // echo json_encode([
                //     'success' => true,
                //     'message' => 'Estado de la clase actualizado correctamente.'
                // ]);

                // return;
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar el estado de la clase.'
                ]);
                return;
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el estado de la clase: ' . $e->getMessage()
            ]);
            return;
        }
    }
}
