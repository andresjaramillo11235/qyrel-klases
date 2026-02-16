<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class MatriculasController
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

        if (!$permissionController->hasPermission($currentUserId, 'view_matriculas')) {
            header('Location: /permission-denied/');
            return;
        }

        // Filtro de fechas
        $fechaInicio = $_POST['fecha_inicio'] ?? null;
        $fechaFin = $_POST['fecha_fin'] ?? null;

        $whereFechas = '';
        if ($fechaInicio && $fechaFin) {
            $whereFechas = " AND m.fecha_inscripcion BETWEEN :fecha_inicio AND :fecha_fin";
        }

        $query = "
            SELECT 
                m.*, 
                e.nombres AS estudiante_nombres, 
                e.apellidos AS estudiante_apellidos, 
                e.numero_documento AS estudiante_numero_documento, 
                e.fecha_nacimiento,
                e.celular AS estudiante_celular,
                pg.nombre AS genero_nombre,
                pec.nombre AS estado_civil_nombre,
                ts.nombre AS tipo_solicitud_nombre, 
                p.nombre AS programa_nombre, 
                mp.programa_id,
                c.nombre AS convenio_nombre, 
                es.nombre AS estado_nombre
            FROM 
                matriculas m
            LEFT JOIN 
                estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN 
                param_genero pg ON e.genero = pg.id
            LEFT JOIN 
                param_estado_civil pec ON e.estado_civil = pec.id
            LEFT JOIN 
                param_tipos_solicitud ts ON m.tipo_solicitud_id = ts.id
            LEFT JOIN 
                matricula_programas mp ON m.id = mp.matricula_id
            LEFT JOIN 
                programas p ON mp.programa_id = p.id
            LEFT JOIN 
                convenios c ON m.convenio_id = c.id
            LEFT JOIN 
                param_matriculas_estados es ON m.estado = es.id
            WHERE 
                m.empresa_id = :empresa_id
                $whereFechas
            ORDER BY 
                m.fecha_inscripcion DESC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);

        if ($fechaInicio && $fechaFin) {
            $stmt->bindParam(':fecha_inicio', $fechaInicio);
            $stmt->bindParam(':fecha_fin', $fechaFin);
        }

        $stmt->execute();
        $matriculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/matriculas/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function getUsers()
    {
        $query = "SELECT estudiante_id FROM users WHERE role_id = 'EST'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'create_matriculas')) {
            header('Location: /permission-denied/');
            return;
        }

        $programas = $this->getProgramas();

        $estudiantes = $this->getEstudiantes();
        $tiposSolicitud = $this->getTiposSolicitud();
        $estadosMatricula = $this->getEstadosMatricula();
        $convenios = $this->getConveniosPorEmpresa($empresaId);

        ob_start();
        include '../modules/matriculas/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        require_once '../modules/mail/MailController.php';
        require_once '../modules/estudiantes/controllers/EstudiantesController.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_matriculas')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $programa_id = $_POST['programas'];

        $valor_matricula = $_POST['valor_matricula'];
        $estudiante_id = $_POST['estudiante_id'];
        $tipo_solicitud_id = $_POST['tipo_solicitud_id'];
        $convenio_id = $_POST['convenio_id']; // Nuevo campo
        $estado = $_POST['estado_id']; // Campo necesario
        $observaciones = strtoupper($_POST['observaciones']); // Convertir observaciones a mayúsculas

        // Obtener el número de documento del estudiante para generar el ID único
        $query = "SELECT numero_documento FROM estudiantes WHERE id = :estudiante_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        $stmt->execute();
        $numeroDocumento = $stmt->fetchColumn();

        $id = $this->generateStructuredId();
        $fecha_inscripcion = $_POST['fecha_inscripcion'];

        // Opcionales, validar si existen antes de asignar
        $fecha_enrolamiento = !empty($_POST['fecha_enrolamiento']) ? $_POST['fecha_enrolamiento'] : null;
        $fecha_aprovacion_teorico = !empty($_POST['fecha_aprovacion_teorico']) ? $_POST['fecha_aprovacion_teorico'] : null;
        $fecha_aprovacion_practico = !empty($_POST['fecha_aprovacion_practico']) ? $_POST['fecha_aprovacion_practico'] : null;
        $fecha_certificacion = !empty($_POST['fecha_certificacion']) ? $_POST['fecha_certificacion'] : null;

        // Asignar la fecha de vencimiento basada en la fecha de enrolamiento, si existe
        $fecha_vencimiento = $fecha_enrolamiento ? date('Y-m-d', strtotime($fecha_enrolamiento . ' + 90 days')) : null;

        // Agregar `valor_matricula` en la consulta de inserción
        $query = "
            INSERT INTO matriculas (
                id, 
                fecha_inscripcion, 
                fecha_enrolamiento, 
                fecha_vencimiento, 
                fecha_aprovacion_teorico, 
                fecha_aprovacion_practico, 
                fecha_certificacion, 
                estudiante_id, 
                tipo_solicitud_id, 
                convenio_id,  
                estado, 
                observaciones, 
                empresa_id, 
                valor_matricula -- Nuevo campo
            ) 
            VALUES (
                :id, 
                :fecha_inscripcion, 
                :fecha_enrolamiento, 
                :fecha_vencimiento, 
                :fecha_aprovacion_teorico, 
                :fecha_aprovacion_practico, 
                :fecha_certificacion, 
                :estudiante_id, 
                :tipo_solicitud_id, 
                :convenio_id,  
                :estado, 
                :observaciones, 
                :empresa_id, 
                :valor_matricula -- Nuevo campo
            )
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':fecha_inscripcion', $fecha_inscripcion);
        $stmt->bindParam(':fecha_enrolamiento', $fecha_enrolamiento);
        $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento);
        $stmt->bindParam(':fecha_aprovacion_teorico', $fecha_aprovacion_teorico);
        $stmt->bindParam(':fecha_aprovacion_practico', $fecha_aprovacion_practico);
        $stmt->bindParam(':fecha_certificacion', $fecha_certificacion);
        $stmt->bindParam(':estudiante_id', $estudiante_id);
        $stmt->bindParam(':tipo_solicitud_id', $tipo_solicitud_id);
        $stmt->bindParam(':convenio_id', $convenio_id); // Nuevo campo
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':empresa_id', $empresa_id);
        $stmt->bindParam(':valor_matricula', $valor_matricula, PDO::PARAM_INT);

        if ($stmt->execute()) {

            // Insertar el programa asociado a la matrícula (solo uno)
            if (!empty($programa_id)) { // Asegúrate de que $programa_id esté definido y no esté vacío
                $queryPrograma = "INSERT INTO matricula_programas (matricula_id, programa_id) VALUES (:matricula_id, :programa_id)";
                $stmtPrograma = $this->conn->prepare($queryPrograma);
                $stmtPrograma->bindParam(':matricula_id', $id);
                $stmtPrograma->bindParam(':programa_id', $programa_id);
                $stmtPrograma->execute();
            } else {
                // Manejar el caso en el que no se proporciona un programa (opcional)
                throw new Exception("No se ha proporcionado un programa para la matrícula.");
            }

            /*
            // Enviar el correo de confirmación 
            $mailController = new MailController();
            $estudiantesController = new EstudiantesController();
            $informacionEstudiante = $estudiantesController->obtenerInformacionEstudiante($estudiante_id);
            $nombreEstudiante = $informacionEstudiante['nombres'];
            $nombreEstudiante .= ' ' . $informacionEstudiante['apellidos'];
            $destinatario = $informacionEstudiante['correo'];

            // Cargar la plantilla
            $rutaPlantilla = '../modules/mail/views/plantilla_matricula.php';
            $variables = [
                'id_matricula' => $id,
                'fecha_inscripcion' => $fecha_inscripcion,
                'nombre_empresa' => $_SESSION['empresa_nombre']
            ];
            $contenidoHtml = $mailController->cargarPlantilla($rutaPlantilla, $variables);

            // Enviar correo
            $asunto = 'Confirmación de Matrícula en CeaCloud';

            $mailController->sendMail($destinatario, $nombreEstudiante, $asunto, $contenidoHtml);
            // Fin Enviar el correo de confirmación 
            */

            $_SESSION['matricula_creada'] = 'Matrícula creada con éxito.';
            header('Location: /matriculas/');
            exit;
        }
    }

    private function generateStructuredId(): string
    {
        date_default_timezone_set('America/Bogota');

        $this->conn->beginTransaction();
        try {
            // Asegura/crea la fila de la secuencia del día y la bloquea
            $lock = $this->conn->prepare("
                        SELECT last_seq FROM matricula_seq
                        WHERE seq_date = CURDATE()
                        FOR UPDATE
                    ");
            $lock->execute();
            $row = $lock->fetch(PDO::FETCH_ASSOC);

            if (!$row) {
                $ins = $this->conn->prepare("
                INSERT INTO matricula_seq (seq_date, last_seq)
                VALUES (CURDATE(), 0)
            ");
                $ins->execute();
            }

            $prefix = date('ymd'); // yymmdd
            $id = null;
            $tries = 0;

            while (true) {
                // Incrementa y obtiene el valor de secuencia de forma atómica
                $this->conn->exec("
                        UPDATE matricula_seq
                        SET last_seq = LAST_INSERT_ID(last_seq + 1)
                        WHERE seq_date = CURDATE()
                    ");
                $seq = (int) $this->conn->query("SELECT LAST_INSERT_ID()")->fetchColumn();

                if ($seq > 999) {
                    throw new Exception("Se alcanzó el máximo de matrículas (999) para el día " . date('Y-m-d'));
                }

                // Candidato de ID y comprobación de colisión
                $id = $prefix . str_pad((string)$seq, 3, '0', STR_PAD_LEFT);

                $chk = $this->conn->prepare("SELECT 1 FROM matriculas WHERE id = :id LIMIT 1");
                $chk->bindValue(':id', $id, PDO::PARAM_STR);
                $chk->execute();

                if (!$chk->fetchColumn()) {
                    // No existe -> nos lo quedamos
                    break;
                }

                // Existe -> seguimos intentando con el siguiente número
                if (++$tries > 1000) {
                    throw new Exception("No se pudo obtener un ID único para el día " . date('Y-m-d'));
                }
            }

            $this->conn->commit();
            return $id;
        } catch (\Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'edit_matriculas')) {
            header('Location: /permission-denied/');
            return;
        }

        $programas = $this->getProgramas();
        $estudiantes = $this->getEstudiantes();
        $empresas = $this->getEmpresas();
        $tiposSolicitud = $this->getTiposSolicitud();
        $estadosMatricula = $this->getEstadosMatricula();  // Obtener los estados de matrícula
        $convenios = $this->getConveniosPorEmpresa($empresaId);  // Obtener los convenios

        $query = "SELECT * FROM matriculas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $matricula = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener el programas asociado a la matrícula
        $query = "SELECT programa_id FROM matricula_programas WHERE matricula_id = :matricula_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $id);
        $stmt->execute();
        $matriculaProgramas = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $programaSeleccionado = $matriculaProgramas[0] ?? null;

        ob_start();
        include '../modules/matriculas/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'edit_matriculas')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $id = $_POST['id'];
            $fecha_inscripcion = $_POST['fecha_inscripcion'];
            $fecha_enrolamiento = !empty($_POST['fecha_enrolamiento']) ? $_POST['fecha_enrolamiento'] : NULL;
            $fecha_vencimiento = !empty($_POST['fecha_vencimiento']) ? $_POST['fecha_vencimiento'] : NULL;
            $fecha_aprovacion_teorico = !empty($_POST['fecha_aprovacion_teorico']) ? $_POST['fecha_aprovacion_teorico'] : NULL;
            $fecha_aprovacion_practico = !empty($_POST['fecha_aprovacion_practico']) ? $_POST['fecha_aprovacion_practico'] : NULL;
            $fecha_certificacion = !empty($_POST['fecha_certificacion']) ? $_POST['fecha_certificacion'] : NULL;
            $programa_id = $_POST['programa_id'];
            $tipo_solicitud_id = $_POST['tipo_solicitud_id'];
            $estado_id = $_POST['estado_id']; // Estado seleccionado
            $convenio_id = $_POST['convenio_id']; // Convenio seleccionado
            $observaciones = $_POST['observaciones'];
            $programa_ids = $_POST['programa_id'];
            $valor_matricula = isset($_POST['valor_matricula']) ? (int)$_POST['valor_matricula'] : null;

            $query = "
                UPDATE matriculas 
                SET 
                    fecha_inscripcion = :fecha_inscripcion,
                    fecha_enrolamiento = :fecha_enrolamiento,
                    fecha_vencimiento = :fecha_vencimiento,
                    valor_matricula = :valor_matricula,
                    fecha_aprovacion_teorico = :fecha_aprovacion_teorico,
                    fecha_aprovacion_practico = :fecha_aprovacion_practico,
                    fecha_certificacion = :fecha_certificacion,
                    tipo_solicitud_id = :tipo_solicitud_id,
                    estado = :estado_id,
                    convenio_id = :convenio_id,
                    observaciones = :observaciones
                WHERE 
                    id = :id
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':fecha_inscripcion', $fecha_inscripcion);
            $stmt->bindParam(':fecha_enrolamiento', $fecha_enrolamiento);
            $stmt->bindParam(':fecha_vencimiento', $fecha_vencimiento);
            $stmt->bindParam(':valor_matricula', $valor_matricula);
            $stmt->bindParam(':fecha_aprovacion_teorico', $fecha_aprovacion_teorico);
            $stmt->bindParam(':fecha_aprovacion_practico', $fecha_aprovacion_practico);
            $stmt->bindParam(':fecha_certificacion', $fecha_certificacion);
            $stmt->bindParam(':tipo_solicitud_id', $tipo_solicitud_id);
            $stmt->bindParam(':estado_id', $estado_id);  // Actualización del estado
            $stmt->bindParam(':convenio_id', $convenio_id);  // Actualización del convenio
            $stmt->bindParam(':observaciones', $observaciones);

            if ($stmt->execute()) {

                // Actualizar el programa asociado a la matrícula
                $queryPrograma = "
                    UPDATE matricula_programas 
                    SET programa_id = :programa_id
                    WHERE matricula_id = :matricula_id
                ";

                $stmtPrograma = $this->conn->prepare($queryPrograma);
                $stmtPrograma->bindParam(':programa_id', $programa_id, PDO::PARAM_INT);
                $stmtPrograma->bindParam(':matricula_id', $id, PDO::PARAM_STR);
                $stmtPrograma->execute();

                $_SESSION['matricula_modificada'] = 'Matrícula modificada con éxito.';
                header('Location: /matriculas/');
                exit;
            }
        } else {

            $query = "SELECT * FROM matriculas WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $matricula = $stmt->fetch(PDO::FETCH_ASSOC);

            ob_start();
            include '../modules/matriculas/views/edit.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        }
    }

    public function delete($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'delete_matriculas')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            // Iniciar transacción
            $this->conn->beginTransaction();

            // ✅ Verificar si la matrícula existe antes de eliminar
            $queryVerificarMatricula = "SELECT id FROM matriculas WHERE id = :id";
            $stmtVerificar = $this->conn->prepare($queryVerificarMatricula);
            $stmtVerificar->bindParam(':id', $id);
            $stmtVerificar->execute();

            if ($stmtVerificar->rowCount() === 0) {
                $_SESSION['matricula_error'] = "La matrícula con ID $id no existe.";
                header('Location: /matriculas/');
                exit();
            }

            // 1️⃣ Obtener IDs de Clases Prácticas asociadas a la matrícula
            $queryClasesPracticas = "SELECT id FROM clases_practicas WHERE matricula_id = :id";
            $stmtClasesPracticas = $this->conn->prepare($queryClasesPracticas);
            $stmtClasesPracticas->bindParam(':id', $id);
            $stmtClasesPracticas->execute();
            $clasesPracticas = $stmtClasesPracticas->fetchAll(PDO::FETCH_COLUMN);

            // 2️⃣ Eliminar calificaciones de Clases Prácticas
            if (!empty($clasesPracticas)) {
                $placeholders = implode(',', array_fill(0, count($clasesPracticas), '?'));
                $queryEliminarCalificaciones = "DELETE FROM control_clases_practicas WHERE clase_practica_id IN ($placeholders)";
                $stmtEliminarCalificaciones = $this->conn->prepare($queryEliminarCalificaciones);
                $stmtEliminarCalificaciones->execute($clasesPracticas);
            }

            // 3️⃣ Eliminar Clases Prácticas asociadas
            $queryClases = "DELETE FROM clases_practicas WHERE matricula_id = :id";
            $stmtClases = $this->conn->prepare($queryClases);
            $stmtClases->bindParam(':id', $id);
            $stmtClases->execute();

            // 4️⃣ Eliminar Abonos (Tabla: financiero_ingresos)
            $queryAbonos = "DELETE FROM financiero_ingresos WHERE matricula_id = :id";
            $stmtAbonos = $this->conn->prepare($queryAbonos);
            $stmtAbonos->bindParam(':id', $id);
            $stmtAbonos->execute();

            // 5️⃣ Eliminar Programas asociados a la matrícula (Tabla: matricula_programas)
            $queryProgramas = "DELETE FROM matricula_programas WHERE matricula_id = :id";
            $stmtProgramas = $this->conn->prepare($queryProgramas);
            $stmtProgramas->bindParam(':id', $id);
            $stmtProgramas->execute();

            // 6️⃣ Eliminar Clases Teóricas por Estudiante (Tabla: clases_teoricas_estudiantes)
            $queryTeoricas = "DELETE FROM clases_teoricas_estudiantes WHERE matricula_id = :id";
            $stmtTeoricas = $this->conn->prepare($queryTeoricas);
            $stmtTeoricas->bindParam(':id', $id);
            $stmtTeoricas->execute();

            // 7️⃣ Finalmente, eliminar la matrícula
            $queryMatricula = "DELETE FROM matriculas WHERE id = :id";
            $stmtMatricula = $this->conn->prepare($queryMatricula);
            $stmtMatricula->bindParam(':id', $id);
            $stmtMatricula->execute();

            // ✅ Confirmar transacción
            $this->conn->commit();

            // 🔎 Registrar auditoría
            $descripcion = "Se eliminó la matrícula con ID " . $id . " y todos los registros asociados.";
            $auditoriaController = new AuditoriaController();
            $auditoriaController->registrar($currentUserId, 'Eliminar', 'Matrículas', $descripcion, $empresaId);

            // 🔔 Notificación de éxito
            $_SESSION['matricula_eliminada'] = "La matrícula $id ha sido eliminada exitosamente.";

            // Redirigir al listado
            header('Location: /matriculas/');
            exit();
        } catch (PDOException $e) {
            // ⚠️ Rollback en caso de error
            $this->conn->rollBack();

            // Guardar el mensaje de error en sesión
            $_SESSION['matricula_error'] = "No fue posible eliminar la matrícula. Error: " . $e->getMessage();

            // Redirigir al listado de matrículas
            header('Location: /matriculas/');
            exit();
        }
    }


    public function detail($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'view_matriculas')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        $query = "SELECT m.*, 
                 e.nombres AS estudiante_nombres, 
                 e.apellidos AS estudiante_apellidos, 
                 ts.nombre AS tipo_solicitud_nombre, 
                 em.nombre AS empresa_nombre, 
                 c.nombre AS convenio_nombre, 
                 es.nombre AS estado_nombre
          FROM matriculas m 
          LEFT JOIN estudiantes e ON m.estudiante_id = e.id 
          LEFT JOIN param_tipos_solicitud ts ON m.tipo_solicitud_id = ts.id 
          LEFT JOIN empresas em ON m.empresa_id = em.id
          LEFT JOIN convenios c ON m.convenio_id = c.id
          LEFT JOIN param_matriculas_estados es ON m.estado = es.id
          WHERE m.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $matricula = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener los programas asociados a la matrícula
        $queryProgramas = "SELECT p.nombre AS programa_nombre 
                           FROM matricula_programas mp 
                           LEFT JOIN programas p ON mp.programa_id = p.id 
                           WHERE mp.matricula_id = :matricula_id";
        $stmtProgramas = $this->conn->prepare($queryProgramas);
        $stmtProgramas->bindParam(':matricula_id', $id);
        $stmtProgramas->execute();
        $programas = $stmtProgramas->fetchAll(PDO::FETCH_ASSOC);

        $matricula['programas'] = $programas;

        ob_start();
        include '../modules/matriculas/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    private function calcularValorMatricula($programasSeleccionados)
    {
        $query = "SELECT SUM(valor_total) AS valor_total FROM programas WHERE id IN (" . implode(',', $programasSeleccionados) . ")";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['valor_total'];
    }

    private function getProgramas()
    {
        $empresaId = $_SESSION['empresa_id'];

        $query = "SELECT * FROM programas WHERE empresa_id = :empresa_id AND estado = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validarProgramaEstudiante($estudianteId, $programaId)
    {
        $empresaId = $_SESSION['empresa_id'];

        $query = "
            SELECT COUNT(*) as total
            FROM matricula_programas mp
            INNER JOIN matriculas m ON mp.matricula_id = m.id
            WHERE m.estudiante_id = :estudiante_id
            AND mp.programa_id = :programa_id
            AND m.empresa_id = :empresa_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':estudiante_id', $estudianteId, PDO::PARAM_INT);
        $stmt->bindParam(':programa_id', $programaId, PDO::PARAM_INT);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['exists' => $result['total'] > 0]);
    }

    private function getEstudiantes()
    {
        $query = "SELECT * FROM estudiantes";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getVehiculos()
    {
        $query = "SELECT * FROM vehiculos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEmpresas()
    {
        $query = "SELECT * FROM empresas";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTiposSolicitud()
    {
        $query = "SELECT id, nombre FROM param_tipos_solicitud";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getEmpresaById($id)
    {
        $query = "SELECT id, nombre FROM empresas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function activate($estudianteId)
    {
        $currentUserId = $_SESSION['user_id'];
        $permissionController = new PermissionController();

        if (!$permissionController->hasPermission($currentUserId, 'activate_student')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener la información del estudiante
        $query = "SELECT * FROM estudiantes WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $estudianteId);
        $stmt->execute();
        $estudiante = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($estudiante) {
            // Crear el usuario
            $username = $estudiante['numero_documento'];
            $password = password_hash($estudiante['codigo'], PASSWORD_DEFAULT);
            $email = $estudiante['correo']; // Suponiendo que hay un campo correo en estudiantes
            $firstName = $estudiante['nombres'];
            $lastName = $estudiante['apellidos'];
            $empresaId = $estudiante['empresa_id'];
            $roleId = 'EST';

            $query = "INSERT INTO users (username, email, password, first_name, last_name, empresa_id, role_id)
                  VALUES (:username, :email, :password, :first_name, :last_name, :empresa_id, :role_id)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':first_name', $firstName);
            $stmt->bindParam(':last_name', $lastName);
            $stmt->bindParam(':empresa_id', $empresaId);
            $stmt->bindParam(':role_id', $roleId);
            $stmt->execute();

            $_SESSION['success_message'] = "El usuario ha sido activado correctamente.";
        } else {
            $_SESSION['error_message'] = "Estudiante no encontrado.";
        }

        header('Location: /matriculas/');
    }

    private function getEstadosMatricula()
    {
        $query = "SELECT * FROM param_matriculas_estados";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getConveniosPorEmpresa($empresaId)
    {
        $query = "SELECT * FROM convenios WHERE empresa_id = :empresa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEstudianteByMatricula($matriculaId)
    {
        // Consulta para obtener el estudiante a partir de la matrícula
        $query = "
            SELECT 
                e.id AS estudiante_id,
                e.numero_documento,
                CONCAT(e.nombres, ' ', e.apellidos) AS nombre_completo,
                e.email,
                e.telefono,
                e.direccion,
                e.fecha_nacimiento
            FROM matriculas m
            INNER JOIN estudiantes e ON m.estudiante_id = e.id
            WHERE m.id = :matricula_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId, PDO::PARAM_STR);
        $stmt->execute();

        // Retornar la información del estudiante o null si no se encuentra
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function matriculasDashboard()
    {
        $empresaId = $_SESSION['empresa_id'];
        $hoy = date('Y-m-d');
        $inicioMes = date('Y-m-01');

        // Consulta de resumen por día y mes
        $queryResumen = "
        SELECT 
            'dia' AS tipo,
            COUNT(*) AS total_matriculas,
            SUM(valor_matricula) AS total_valor
        FROM matriculas
        WHERE empresa_id = :empresa_id AND fecha_inscripcion = :hoy

        UNION

        SELECT 
            'mes' AS tipo,
            COUNT(*) AS total_matriculas,
            SUM(valor_matricula) AS total_valor
        FROM matriculas
        WHERE empresa_id = :empresa_id AND fecha_inscripcion BETWEEN :inicio_mes AND :hoy
    ";

        $stmtResumen = $this->conn->prepare($queryResumen);
        $stmtResumen->bindParam(':empresa_id', $empresaId);
        $stmtResumen->bindParam(':hoy', $hoy);
        $stmtResumen->bindParam(':inicio_mes', $inicioMes);
        $stmtResumen->execute();
        $resumen = $stmtResumen->fetchAll(PDO::FETCH_ASSOC);

        // Consulta agrupada por programa en el mes actual
        $queryPorPrograma = "
                SELECT 
            p.nombre AS programa_nombre,
            COUNT(DISTINCT m.id) AS total_matriculas,
            SUM(m.valor_matricula) AS total_valor
        FROM matriculas m
        LEFT JOIN matricula_programas mp ON mp.matricula_id = m.id
        LEFT JOIN programas p ON p.id = mp.programa_id
        WHERE m.empresa_id = :empresa_id 
        AND m.fecha_inscripcion BETWEEN :inicio_mes AND :hoy
        AND p.id IS NOT NULL
        GROUP BY p.nombre
        ORDER BY total_matriculas DESC
        ";

        $stmtPrograma = $this->conn->prepare($queryPorPrograma);
        $stmtPrograma->bindParam(':empresa_id', $empresaId);
        $stmtPrograma->bindParam(':inicio_mes', $inicioMes);
        $stmtPrograma->bindParam(':hoy', $hoy);
        $stmtPrograma->execute();
        $matriculasPorPrograma = $stmtPrograma->fetchAll(PDO::FETCH_ASSOC);

        // Vista
        ob_start();
        include '../modules/matriculas/views/dashboard.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
