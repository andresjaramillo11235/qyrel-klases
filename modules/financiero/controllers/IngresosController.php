<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class IngresosController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }

    public function index($rango = null)
    {
        require_once '../shared/utils/TokenHelper.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_ingresos')) {
            header('Location: /permission-denied/');
            exit;
        }

        // ✅ Extraer filtros del token
        $filtros = decodificarToken($rango);
        $fecha_inicial = $filtros['fecha_inicial'] ?? null;
        $fecha_final   = $filtros['fecha_final'] ?? null;

        $query = "SELECT 
                i.*, 
                mi.nombre AS motivo_ingreso, 
                ti.nombre AS tipo_ingreso,
                e.nombres AS estudiante_nombres,
                e.apellidos AS estudiante_apellidos,
                e.numero_documento AS estudiante_cedula,
                p.nombre AS programa_nombre,
                cl.nombre AS categoria_licencia,
                c.nombre AS convenio_nombre   -- 🔹 convenio agregado
            FROM financiero_ingresos i
            LEFT JOIN param_motivos_financiero_ingresos mi ON i.motivo_ingreso_id = mi.id
            LEFT JOIN param_tipos_financiero_ingresos ti ON i.tipo_ingreso_id = ti.id
            LEFT JOIN matriculas m ON i.matricula_id = m.id
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN matricula_programas mp ON mp.matricula_id = i.matricula_id
            LEFT JOIN programas p ON p.id = mp.programa_id
            LEFT JOIN categorias_licencia cl ON cl.id = p.categoria
            LEFT JOIN convenios c ON c.id = m.convenio_id   -- 🔹 join con convenios
            WHERE i.empresa_id = :empresa_id";

        if ($fecha_inicial && $fecha_final) {
            $query .= " AND i.fecha BETWEEN :fecha_inicial AND :fecha_final";
        }

        $query .= " ORDER BY i.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresa_id);

        if ($fecha_inicial && $fecha_final) {
            $stmt->bindParam(':fecha_inicial', $fecha_inicial);
            $stmt->bindParam(':fecha_final', $fecha_final);
        }

        $stmt->execute();
        $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ✅ Pasar filtros también a la vista si se quieren mantener en el formulario
        ob_start();
        include '../modules/financiero/views/ingresos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Método para mostrar la vista de creación de un ingreso
    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        // Verificar permisos para crear ingresos
        if (!$permissionController->hasPermission($currentUserId, 'create_ingresos')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Obtener los motivos de ingreso desde la base de datos
        $query = "SELECT id, nombre FROM param_motivos_financiero_ingresos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $motivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Obtener los tipos de ingreso desde la base de datos
        $queryTipos = "SELECT id, nombre FROM param_tipos_financiero_ingresos";
        $stmtTipos = $this->conn->prepare($queryTipos);
        $stmtTipos->execute();
        $tipos = $stmtTipos->fetchAll(PDO::FETCH_ASSOC);

        // Obtener cajas activas de la empresa
        $empresaId = $_SESSION['empresa_id'];

        $queryCajas = "SELECT id, nombre
                FROM cajas
                WHERE estado = 1
                ORDER BY nombre ASC
            ";

        $stmtCajas = $this->conn->prepare($queryCajas);
        $stmtCajas->execute();
        $cajas = $stmtCajas->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/financiero/views/ingresos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Método para guardar un nuevo ingreso en la base de datos
    public function store()
    {
        date_default_timezone_set('America/Bogota');

        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        // Verificar permisos para guardar ingresos
        if (!$permissionController->hasPermission($currentUserId, 'create_ingresos')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Validaciones y sanitización de los datos obtenidos desde $_POST
        $matricula_id = isset($_POST['matricula_id']) ? $_POST['matricula_id'] : null;
        $valor = isset($_POST['valor']) ? intval($_POST['valor']) : null;
        $motivo_ingreso_id = isset($_POST['motivo_ingreso_id']) ? intval($_POST['motivo_ingreso_id']) : null;
        $tipo_ingreso_id = isset($_POST['tipo_ingreso_id']) ? intval($_POST['tipo_ingreso_id']) : null;
        $observaciones = isset($_POST['observaciones']) ? htmlspecialchars($_POST['observaciones']) : '';

        //$fecha = isset($_POST['fecha']) ? $_POST['fecha'] : null;
        $fecha = date('Y-m-d');

        if (!$matricula_id || !$valor || !$motivo_ingreso_id || !$tipo_ingreso_id || !$fecha) {
            $_SESSION['error_message'] = 'Todos los campos son obligatorios.';
            header('Location: /ruta-al-formulario-de-ingreso/');
            exit;
        }

        // Generar número de recibo único
        $empresa_code = str_pad($_SESSION['empresa_id'], 3, '0', STR_PAD_LEFT);

        // Buscar el último recibo de esta empresa y matrícula para generar el siguiente número en secuencia
        $query = "SELECT numero_recibo FROM financiero_ingresos 
            WHERE numero_recibo LIKE :prefix 
            AND empresa_id = :empresa_id
            ORDER BY numero_recibo DESC 
            LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':prefix', "$empresa_code%");
        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();
        $lastRecibo = $stmt->fetchColumn();

        if ($lastRecibo) {
            // Incrementar el último consecutivo en 1
            $consecutivo = intval(substr($lastRecibo, -6)) + 1; // Ajuste para considerar el nuevo cero
        } else {
            // Iniciar con 000001 si es el primer recibo
            $consecutivo = 1;
        }

        $numero_recibo = $empresa_code . str_pad($consecutivo, 6, '0', STR_PAD_LEFT);

        // Insertar el ingreso en la base de datos, incluyendo el campo empresa_id
        $query = "
            INSERT INTO financiero_ingresos 
            (matricula_id, valor, motivo_ingreso_id, tipo_ingreso_id, observaciones, fecha, numero_recibo, empresa_id)
            VALUES 
            (:matricula_id, :valor, :motivo_ingreso_id, :tipo_ingreso_id, :observaciones, :fecha, :numero_recibo, :empresa_id)
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matricula_id);
        $stmt->bindParam(':valor', $valor, PDO::PARAM_INT);
        $stmt->bindParam(':motivo_ingreso_id', $motivo_ingreso_id, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_ingreso_id', $tipo_ingreso_id, PDO::PARAM_INT);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':fecha', $fecha);
        $stmt->bindParam(':numero_recibo', $numero_recibo);
        $stmt->bindParam(':empresa_id', $empresa_id);

        if ($stmt->execute()) {

            // Obtener ID del ingreso recién creado
            $ingreso_id = $this->conn->lastInsertId();

            // ===============================================
            // 🔹 REGISTRAR MOVIMIENTO EN CAJA
            // ===============================================

            // Obtener la caja seleccionada por el usuario
            $caja_id = isset($_POST['caja_id']) ? intval($_POST['caja_id']) : null;

            if ($caja_id) {

                // Fecha y hora exacta de Colombia
                date_default_timezone_set('America/Bogota');
                $fechaColombia = date('Y-m-d H:i:s');

                // Construir descripción del movimiento
                $descripcionMovimiento = "Ingreso recibo $numero_recibo - Matrícula $matricula_id";

                // Insertar movimiento
                $queryMovimiento = "
                        INSERT INTO caja_movimientos 
                        (empresa_id, caja_id, tipo, origen, ingreso_id, valor, descripcion, user_id, fecha, created_at)
                        VALUES
                        (:empresa_id, :caja_id, 'INGRESO', 'ingreso', :ingreso_id, :valor, :descripcion, :user_id, :fecha, :created_at)
                    ";

                $stmtMov = $this->conn->prepare($queryMovimiento);
                $stmtMov->bindParam(':empresa_id', $empresa_id);
                $stmtMov->bindParam(':caja_id', $caja_id);
                $stmtMov->bindParam(':ingreso_id', $ingreso_id); // <-- este valor lo definimos abajo
                $stmtMov->bindParam(':valor', $valor);
                $stmtMov->bindParam(':descripcion', $descripcionMovimiento);
                $stmtMov->bindParam(':user_id', $currentUserId);
                $stmtMov->bindParam(':fecha', $fechaColombia);
                $stmtMov->bindParam(':created_at', $fechaColombia);

                $stmtMov->execute();
            }

            $_SESSION['success_message'] = 'Ingreso creado correctamente.';
            header('Location: ' . $routes['ingresos_index']);
        } else {
            $_SESSION['error_message'] = 'Error al guardar el ingreso.';
            header('Location: ' . $routes['ingresos_index']);
        }
        exit;
    }

    // Método para mostrar el formulario de edición de un ingreso
    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        // Verificar permisos para editar ingresos
        if (!$permissionController->hasPermission($currentUserId, 'edit_ingresos')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Consulta para obtener los datos del ingreso junto con la información del estudiante
        $query = "
        SELECT i.*, 
               mi.nombre AS motivo_ingreso, 
               ti.nombre AS tipo_ingreso,
               e.nombres AS estudiante_nombre,
               e.apellidos AS estudiante_apellidos,
               e.numero_documento AS estudiante_cedula,
               e.foto AS estudiante_foto
        FROM financiero_ingresos i
        LEFT JOIN param_motivos_financiero_ingresos mi ON i.motivo_ingreso_id = mi.id
        LEFT JOIN param_tipos_financiero_ingresos ti ON i.tipo_ingreso_id = ti.id
        LEFT JOIN matriculas m ON i.matricula_id = m.id
        LEFT JOIN estudiantes e ON m.estudiante_id = e.id
        WHERE i.id = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $ingreso = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$ingreso) {
            header('Location: /ingresos/');
            exit;
        }

        // Obtener los motivos y tipos de ingreso para llenar los selectores en el formulario
        $motivos = $this->getMotivosIngresos();
        $tipos = $this->getTiposIngresos();

        // Renderizar la vista de edición de ingresos
        ob_start();
        include '../modules/financiero/views/ingresos/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Obtener los motivos de ingreso desde la tabla param_motivos_financiero_ingresos
    private function getMotivosIngresos()
    {
        $query = "SELECT id, nombre FROM param_motivos_financiero_ingresos ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener los tipos de ingreso desde la tabla param_tipos_financiero_ingresos
    private function getTiposIngresos()
    {
        $query = "SELECT id, nombre FROM param_tipos_financiero_ingresos ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para actualizar un ingreso
    public function update()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $valor = $_POST['valor'];
            $fecha = $_POST['fecha'];
            $motivo_ingreso_id = $_POST['motivo_ingreso_id'];
            $tipo_ingreso_id = $_POST['tipo_ingreso_id'];
            $observaciones = $_POST['observaciones'];

            $query = "
            UPDATE financiero_ingresos
            SET valor = :valor, fecha = :fecha, motivo_ingreso_id = :motivo_ingreso_id, tipo_ingreso_id = :tipo_ingreso_id, observaciones = :observaciones
            WHERE id = :id
            ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':fecha', $fecha);
            $stmt->bindParam(':motivo_ingreso_id', $motivo_ingreso_id);
            $stmt->bindParam(':tipo_ingreso_id', $tipo_ingreso_id);
            $stmt->bindParam(':observaciones', $observaciones);

            if ($stmt->execute()) {
                $_SESSION['message'] = 'Ingreso actualizado correctamente';
                header("Location: {$routes['ingresos_index']}");
                exit;
            } else {
                $_SESSION['error'] = 'Error al actualizar el ingreso';
                header("Location: {$routes['ingresos_index']}");
            }
        }
    }

    public function delete($id)
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'delete_ingresos')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "DELETE FROM financiero_ingresos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['delete_message'] = 'Ingreso eliminado correctamente';
            header("Location: {$routes['ingresos_index']}");
            exit;
        } else {
            $_SESSION['error'] = 'Error al eliminar el ingreso';
            header("Location: {$routes['ingresos_index']}");
        }
    }

    // 🔹 Método para traer resumen de abonos por matrícula
    public function traer_abonos_matricula()
    {
        header('Content-Type: application/json');

        // Verificar que el ID de la matrícula esté presente
        if (!isset($_POST['matricula_id'])) {
            echo json_encode(['success' => false, 'message' => 'Matrícula no especificada']);
            return;
        }

        $matriculaId = $_POST['matricula_id'];

        // Obtener valor de la matrícula
        $queryMatricula = "SELECT valor_matricula FROM matriculas WHERE id = :matricula_id";
        $stmtMatricula = $this->conn->prepare($queryMatricula);
        $stmtMatricula->bindParam(':matricula_id', $matriculaId);
        $stmtMatricula->execute();
        $matricula = $stmtMatricula->fetch(PDO::FETCH_ASSOC);

        if (!$matricula) {
            echo json_encode(['success' => false, 'message' => 'Matrícula no encontrada']);
            return;
        }

        // Obtener suma de abonos
        $queryIngresos = "SELECT SUM(valor) AS total_abonos FROM financiero_ingresos WHERE matricula_id = :matricula_id";
        $stmtIngresos = $this->conn->prepare($queryIngresos);
        $stmtIngresos->bindParam(':matricula_id', $matriculaId);
        $stmtIngresos->execute();
        $ingresos = $stmtIngresos->fetch(PDO::FETCH_ASSOC);

        $totalMatricula = (int)$matricula['valor_matricula'];
        $totalAbonos = (int)($ingresos['total_abonos'] ?? 0);
        $saldo = $totalMatricula - $totalAbonos;

        echo json_encode([
            'success' => true,
            'valor_matricula' => $totalMatricula,
            'total_abonos' => $totalAbonos,
            'saldo' => $saldo
        ]);
    }

    public function informe($rango = null)
    {
        require_once '../shared/utils/TokenHelper.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_ingresos_informe')) {
            header('Location: /permission-denied/');
            exit;
        }

        $filtros = decodificarToken($rango);
        $fechaInicial = trim($filtros['fecha_inicial'] ?? '');
        $fechaFinal   = trim($filtros['fecha_final'] ?? '');

        $ingresos = [];
        $totales = [
            'total' => 0,
            'efectivo' => 0,
            'transferencia' => 0,
            'nequi' => 0,
            'qr' => 0,
        ];

        if ($fechaInicial && $fechaFinal) {
            $query = "
            SELECT 
                fi.fecha,
                fi.numero_recibo,
                fi.valor,
                fi.observaciones,
                fi.id,
                fi.created_by,
                fi.tipo_ingreso_id,

                e.nombres,
                e.apellidos,
                e.numero_documento,

                m.id AS matricula_id,
                m.valor_matricula,

                p.nombre AS categoria_matricula,

                ti.nombre AS tipo_ingreso_nombre,
                u.email AS usuario_creacion,

                -- Calcular saldo matrícula (valor total - abonos)
                m.valor_matricula - (
                    SELECT IFNULL(SUM(valor), 0)
                    FROM financiero_ingresos 
                    WHERE matricula_id = m.id
                ) AS saldo_matricula

            FROM 
                financiero_ingresos fi
            LEFT JOIN 
                matriculas m ON fi.matricula_id = m.id
            LEFT JOIN 
                estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN 
                matricula_programas mp ON m.id = mp.matricula_id
            LEFT JOIN 
                programas p ON mp.programa_id = p.id
            LEFT JOIN 
                param_tipos_financiero_ingresos ti ON fi.tipo_ingreso_id = ti.id
            LEFT JOIN 
                users u ON fi.created_by = u.id
            WHERE 
                fi.fecha BETWEEN :fecha_inicial AND :fecha_final
                AND fi.empresa_id = :empresa_id
            ORDER BY fi.fecha ASC
        ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':fecha_inicial', $fechaInicial);
            $stmt->bindValue(':fecha_final', $fechaFinal);
            $stmt->bindValue(':empresa_id', $empresa_id);
            $stmt->execute();
            $ingresos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener todos los tipos de ingreso disponibles
            $tiposQuery = "SELECT LOWER(TRIM(nombre)) AS nombre FROM param_tipos_financiero_ingresos";
            $tiposStmt = $this->conn->query($tiposQuery);
            $tiposIngreso = $tiposStmt->fetchAll(PDO::FETCH_COLUMN);

            $totales = ['total' => 0];
            foreach ($tiposIngreso as $tipo) {
                $totales[$tipo] = 0;
            }

            foreach ($ingresos as $ingreso) {
                $valor = (int) $ingreso['valor'];
                $totales['total'] += $valor;

                $tipo = strtolower(trim($ingreso['tipo_ingreso_nombre'] ?? ''));
                if (isset($totales[$tipo])) {
                    $totales[$tipo] += $valor;
                }
            }
        }

        // Pasar datos a la vista
        ob_start();
        include '../modules/financiero/views/ingresos/informe.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function informeServerSide()
    {
        header('Content-Type: application/json');

        $empresa_id = $_SESSION['empresa_id'];

        $draw = intval($_POST['draw'] ?? 0);
        $start = intval($_POST['start'] ?? 0);
        $length = intval($_POST['length'] ?? 10);
        $search = trim($_POST['search']['value'] ?? '');

        $fechaInicial = $_POST['fecha_inicial'] ?? null;
        $fechaFinal   = $_POST['fecha_final'] ?? null;

        // Seguridad mínima
        if (!$fechaInicial || !$fechaFinal || !$empresa_id) {
            echo json_encode([
                "draw" => $draw,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
            ]);
            return;
        }

        // 🧠 Columnas que DataTables puede ordenar (en el mismo orden que las columnas JS)
        $columns = [
            'fi.fecha',
            'fi.numero_recibo',
            'fi.valor',
            'e.numero_documento',
            'e.nombres',
            'e.apellidos',
            'm.id',
            'p.nombre',
            'c.nombre', // Convenio
            'ti.nombre',
            'u.email',
            'fi.observaciones',
            'saldo_matricula', // Alias calculado
            'fi.id'
        ];

        // 🔍 Captura la columna y dirección que envía DataTables
        $orderIndex = $_POST['order'][0]['column'] ?? 0;
        $orderDir = strtolower($_POST['order'][0]['dir'] ?? 'asc');

        // ✅ Valida que sea un índice válido y dirección válida
        $orderColumn = $columns[$orderIndex] ?? 'fi.fecha';
        $orderDir = in_array($orderDir, ['asc', 'desc']) ? $orderDir : 'asc';

        // Query base
        $baseQuery = "
                FROM financiero_ingresos fi
                LEFT JOIN matriculas m ON fi.matricula_id = m.id
                LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                LEFT JOIN matricula_programas mp ON m.id = mp.matricula_id
                LEFT JOIN programas p ON mp.programa_id = p.id
                LEFT JOIN convenios c ON m.convenio_id = c.id
                LEFT JOIN param_tipos_financiero_ingresos ti ON fi.tipo_ingreso_id = ti.id
                LEFT JOIN users u ON fi.created_by = u.id
                WHERE fi.fecha BETWEEN :fecha_inicial AND :fecha_final
                AND fi.empresa_id = :empresa_id
            ";

        $params = [
            ':fecha_inicial' => $fechaInicial,
            ':fecha_final' => $fechaFinal,
            ':empresa_id' => $empresa_id,
        ];

        // Filtro por búsqueda global
        $searchSQL = '';
        if (!empty($search)) {
            $searchSQL = " AND (
            fi.numero_recibo LIKE :search OR
            e.nombres LIKE :search OR
            e.apellidos LIKE :search OR
            e.numero_documento LIKE :search OR
            ti.nombre LIKE :search OR
            p.nombre LIKE :search
        )";
            $params[':search'] = "%$search%";
        }

        // Total registros sin filtro
        $stmt = $this->conn->prepare("SELECT COUNT(*) $baseQuery");
        $stmt->execute([
            ':fecha_inicial' => $fechaInicial,
            ':fecha_final' => $fechaFinal,
            ':empresa_id' => $empresa_id
        ]);
        $recordsTotal = $stmt->fetchColumn();

        // Total con filtro
        $stmt = $this->conn->prepare("SELECT COUNT(*) $baseQuery $searchSQL");
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        $recordsFiltered = $stmt->fetchColumn();

        // Traer registros paginados
        $dataSQL = "
                SELECT 
                    fi.fecha,
                    fi.numero_recibo,
                    fi.valor,
                    fi.observaciones,
                    fi.id,
                    e.nombres,
                    e.apellidos,
                    e.numero_documento,
                    m.id AS matricula_id,
                    p.nombre AS categoria_matricula,
                    c.nombre AS convenio_nombre,
                    ti.nombre AS tipo_ingreso_nombre,
                    u.email AS usuario_creacion,
                    m.valor_matricula - (
                        SELECT IFNULL(SUM(valor), 0)
                        FROM financiero_ingresos 
                        WHERE matricula_id = m.id
                    ) AS saldo_matricula
                $baseQuery
                $searchSQL
                ORDER BY $orderColumn $orderDir
                LIMIT :start, :length
            ";

        $stmt = $this->conn->prepare($dataSQL);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':length', $length, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ////////////////////////
        // Calcular totales
        $tiposQuery = "SELECT LOWER(TRIM(nombre)) AS nombre FROM param_tipos_financiero_ingresos";
        $tiposIngreso = $this->conn->query($tiposQuery)->fetchAll(PDO::FETCH_COLUMN);

        $totales = ['total' => 0];
        foreach ($tiposIngreso as $tipo) {
            $totales[$tipo] = 0;
        }

        foreach ($data as $ingreso) {
            $valor = (int) $ingreso['valor'];
            $totales['total'] += $valor;

            $tipo = strtolower(trim($ingreso['tipo_ingreso_nombre'] ?? ''));
            if (isset($totales[$tipo])) {
                $totales[$tipo] += $valor;
            }
        }

        // 1. Traer los datos totales del filtro actual SIN paginación
        $queryTotalFiltrado = "
                SELECT LOWER(TRIM(ti.nombre)) AS tipo, SUM(fi.valor) AS total
                FROM financiero_ingresos fi
                LEFT JOIN param_tipos_financiero_ingresos ti ON fi.tipo_ingreso_id = ti.id
                LEFT JOIN matriculas m ON fi.matricula_id = m.id
                WHERE fi.fecha BETWEEN :fecha_inicial AND :fecha_final
                AND fi.empresa_id = :empresa_id
                GROUP BY ti.nombre
            ";

        $stmtFiltrado = $this->conn->prepare($queryTotalFiltrado);
        $stmtFiltrado->bindValue(':fecha_inicial', $fechaInicial);
        $stmtFiltrado->bindValue(':fecha_final', $fechaFinal);
        $stmtFiltrado->bindValue(':empresa_id', $empresa_id);
        $stmtFiltrado->execute();

        $totalesFiltrados = ['total' => 0];
        foreach ($stmtFiltrado->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $tipo = strtolower(trim($fila['tipo']));
            $totalesFiltrados[$tipo] = (int) $fila['total'];
            $totalesFiltrados['total'] += (int) $fila['total'];
        }

        header('Content-Type: application/json');
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $data,
            "totales" => $totales,
            'totales_globales' => $totalesFiltrados,
            "fecha_inicial" => $fechaInicial,
            "fecha_final" => $fechaFinal,
            "empresa_nombre" => $_SESSION['empresa_nombre'] ?? 'Academia',
            "cantidad_registros" => count($data)
        ]);
        exit;


        ///////////////////////////



    }

    public function exportarExcelFiltro()
    {
        require_once '../vendor/autoload.php';
        $empresaId = $_SESSION['empresa_id'];

        $fechaInicial = $_POST['fecha_inicial'] ?? null;
        $fechaFinal = $_POST['fecha_final'] ?? null;

        if (!$fechaInicial || !$fechaFinal) {
            die('Fechas no válidas');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // TOTALES DEL FILTRO
        $totalesQuery = "
            SELECT LOWER(TRIM(ti.nombre)) AS tipo, SUM(fi.valor) AS total
            FROM financiero_ingresos fi
            LEFT JOIN param_tipos_financiero_ingresos ti ON fi.tipo_ingreso_id = ti.id
            WHERE fi.fecha BETWEEN :fecha_inicial AND :fecha_final
            AND fi.empresa_id = :empresa_id
            GROUP BY ti.nombre
            ";

        $stmtTotales = $this->conn->prepare($totalesQuery);
        $stmtTotales->bindValue(':fecha_inicial', $fechaInicial);
        $stmtTotales->bindValue(':fecha_final', $fechaFinal);
        $stmtTotales->bindValue(':empresa_id', $empresaId);
        $stmtTotales->execute();

        $totalesProgramaQuery = "
            SELECT p.nombre AS programa, SUM(fi.valor) AS total
            FROM financiero_ingresos fi
            LEFT JOIN matriculas m ON fi.matricula_id = m.id
            LEFT JOIN matricula_programas mp ON m.id = mp.matricula_id
            LEFT JOIN programas p ON mp.programa_id = p.id
            WHERE fi.fecha BETWEEN :fecha_inicial AND :fecha_final
            AND fi.empresa_id = :empresa_id
            GROUP BY p.nombre
            ORDER BY p.nombre ASC
        ";

        $stmtPrograma = $this->conn->prepare($totalesProgramaQuery);
        $stmtPrograma->bindValue(':fecha_inicial', $fechaInicial);
        $stmtPrograma->bindValue(':fecha_final', $fechaFinal);
        $stmtPrograma->bindValue(':empresa_id', $empresaId);
        $stmtPrograma->execute();

        $totalesPorPrograma = $stmtPrograma->fetchAll(PDO::FETCH_ASSOC);

        $totales = ['TOTAL' => 0];
        foreach ($stmtTotales->fetchAll(PDO::FETCH_ASSOC) as $fila) {
            $tipo = strtoupper(trim($fila['tipo']));
            $totales[$tipo] = (int) $fila['total'];
            $totales['TOTAL'] += (int) $fila['total'];
        }

        // ENCABEZADO
        $fila = 1;
        $sheet->setCellValue("A$fila", 'REPORTE INGRESOS FILTRADOS');
        $sheet->mergeCells("A$fila:F$fila");
        $fila++;

        $sheet->setCellValue("A$fila", 'FECHA INICIAL:');
        $sheet->setCellValue("B$fila", $fechaInicial);
        $fila++;
        $sheet->setCellValue("A$fila", 'FECHA FINAL:');
        $sheet->setCellValue("B$fila", $fechaFinal);
        $fila++;

        $sheet->setCellValue("A$fila", 'ACADEMIA:');
        $sheet->setCellValue("B$fila", $_SESSION['empresa_nombre'] ?? 'Academia');
        $fila += 2;

        $sheet->setCellValue("A$fila", 'TOTALES DEL FILTRO');
        $fila++;

        foreach ($totales as $tipo => $valor) {
            $sheet->setCellValue("A$fila", $tipo);
            $sheet->setCellValue("B$fila", $valor);
            $fila++;
        }

        $fila += 2;
        $sheet->setCellValue("A$fila", 'TOTALES POR PROGRAMA');
        $fila++;

        foreach ($totalesPorPrograma as $programa) {
            $sheet->setCellValue("A$fila", $programa['programa']);
            $sheet->setCellValue("B$fila", $programa['total']);
            $fila++;
        }

        // DETALLE DE REGISTROS
        $sheet->setCellValue("A$fila", 'FECHA');
        $sheet->setCellValue("B$fila", 'NUMERODOCUMENTO');
        $sheet->setCellValue("C$fila", 'NOMBRES');
        $sheet->setCellValue("D$fila", 'APELLIDOS');
        $sheet->setCellValue("E$fila", 'NUMERORECIBO');
        $sheet->setCellValue("F$fila", 'VALOR');
        $sheet->setCellValue("G$fila", 'NUMEROMATRICULA');
        $sheet->setCellValue("H$fila", 'CATEGORIAMATRICULA');
        $sheet->setCellValue("I$fila", 'TIPO DE PAGO');
        $sheet->setCellValue("J$fila", 'OBSERVACIONES');
        $sheet->setCellValue("K$fila", 'SALDOMATRICULA');
        $sheet->setCellValue("L$fila", 'USUARIOCREACION');
        $sheet->setCellValue("M$fila", 'ID');
        $sheet->setCellValue("N$fila", 'CONVENIO');
        $fila++;

        $queryDetalle = "
                SELECT 
                    fi.fecha,
                    fi.numero_recibo,
                    fi.valor,
                    fi.observaciones,
                    fi.id,
                    fi.created_by,
                    
                    e.numero_documento,
                    e.nombres,
                    e.apellidos,
                    
                    m.id AS matricula_id,
                    m.valor_matricula,
                    
                    p.nombre AS categoria_matricula,
                    
                    ti.nombre AS tipo_ingreso_nombre,
                    u.email AS usuario_creacion,

                    c.nombre AS convenio_nombre,

                    -- Saldo matrícula (total - abonos)
                    m.valor_matricula - (
                        SELECT IFNULL(SUM(valor), 0)
                        FROM financiero_ingresos 
                        WHERE matricula_id = m.id
                    ) AS saldo_matricula

                FROM financiero_ingresos fi
                LEFT JOIN matriculas m ON fi.matricula_id = m.id
                LEFT JOIN estudiantes e ON m.estudiante_id = e.id
                LEFT JOIN matricula_programas mp ON m.id = mp.matricula_id
                LEFT JOIN programas p ON mp.programa_id = p.id
                LEFT JOIN param_tipos_financiero_ingresos ti ON fi.tipo_ingreso_id = ti.id
                LEFT JOIN users u ON fi.created_by = u.id
                LEFT JOIN convenios c ON m.convenio_id = c.id
                WHERE fi.fecha BETWEEN :fecha_inicial AND :fecha_final
                AND fi.empresa_id = :empresa_id
                ORDER BY fi.fecha ASC
            ";

        $stmtDetalle = $this->conn->prepare($queryDetalle);
        $stmtDetalle->bindValue(':fecha_inicial', $fechaInicial);
        $stmtDetalle->bindValue(':fecha_final', $fechaFinal);
        $stmtDetalle->bindValue(':empresa_id', $empresaId);
        $stmtDetalle->execute();

        while ($row = $stmtDetalle->fetch(PDO::FETCH_ASSOC)) {
            $sheet->setCellValue("A$fila", $row['fecha']);
            $sheet->setCellValue("B$fila", $row['numero_documento']);
            $sheet->setCellValue("C$fila", $row['nombres']);
            $sheet->setCellValue("D$fila", $row['apellidos']);
            $sheet->setCellValue("E$fila", $row['numero_recibo']);
            $sheet->setCellValue("F$fila", $row['valor']);
            $sheet->setCellValue("G$fila", $row['matricula_id']);
            $sheet->setCellValue("H$fila", $row['categoria_matricula']);
            $sheet->setCellValue("I$fila", $row['tipo_ingreso_nombre']);
            $sheet->setCellValue("J$fila", $row['observaciones']);
            $sheet->setCellValue("K$fila", $row['saldo_matricula']);
            $sheet->setCellValue("L$fila", $row['usuario_creacion']);
            $sheet->setCellValue("M$fila", $row['id']);
            $sheet->setCellValue("N$fila", $row['convenio_nombre']);
            $fila++;
        }

        // EXPORTAR
        $filename = "reporte_ingresos_" . date('Ymd_His') . ".xlsx";
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function informeCartera()
    {
        $empresa_id = $_SESSION['empresa_id'];

        $query = "SELECT 
                m.id AS matricula_id,
                e.nombres AS estudiante_nombres,
                e.apellidos AS estudiante_apellidos,
                e.numero_documento AS estudiante_cedula,
                p.nombre AS programa_nombre,
                cl.nombre AS categoria_licencia,
                c.nombre AS convenio_nombre,   -- 🔹 convenio agregado
                m.valor_matricula,
                m.fecha_inscripcion,
                IFNULL(SUM(i.valor), 0) AS total_pagado,
                (m.valor_matricula - IFNULL(SUM(i.valor), 0)) AS saldo
            FROM matriculas m
            INNER JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN matricula_programas mp ON mp.matricula_id = m.id
            LEFT JOIN programas p ON p.id = mp.programa_id
            LEFT JOIN categorias_licencia cl ON cl.id = p.categoria
            LEFT JOIN financiero_ingresos i ON i.matricula_id = m.id
            LEFT JOIN convenios c ON c.id = m.convenio_id   -- 🔹 join con convenios
            WHERE m.empresa_id = :empresa_id
            GROUP BY 
                m.id,
                e.nombres,
                e.apellidos,
                e.numero_documento,
                p.nombre,
                cl.nombre,
                c.nombre,   -- 🔹 importante incluir en el GROUP BY
                m.valor_matricula,
                m.fecha_inscripcion
            HAVING saldo > 0
            ORDER BY m.fecha_inscripcion DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();
        $cartera = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalGeneral = 0;
        foreach ($cartera as $r) {
            $totalGeneral += $r['saldo'];
        }

        // Vista
        ob_start();
        include '../modules/financiero/views/ingresos/informe_cartera.php';

        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
