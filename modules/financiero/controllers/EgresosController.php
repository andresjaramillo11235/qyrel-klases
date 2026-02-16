<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class EgresosController
{
    private $conn;

    public function __construct()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function index($page = null, $perPage = null)
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id    = (int) $_SESSION['empresa_id'];
        $rol           = $_SESSION['rol_nombre'] ?? '';

        // ===== Filtros =====
        $fecha_inicio   = !empty($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : null;
        $fecha_fin      = !empty($_REQUEST['fecha_fin']) ? $_REQUEST['fecha_fin'] : null;
        $tipo_egreso_id = !empty($_REQUEST['tipo_egreso_id']) ? (int) $_REQUEST['tipo_egreso_id'] : null;
        $q              = !empty($_REQUEST['q']) ? trim($_REQUEST['q']) : null; // documento, tercero, observaciones

        $where  = [];
        $params = [];

        $where[] = 'e.empresa_id = :empresa_id';
        $params[':empresa_id'] = $empresa_id;

        if ($rol !== 'ADMIN') {
            $where[] = 'e.user_id = :user_id';
            $params[':user_id'] = $currentUserId;
        }

        if ($fecha_inicio && $fecha_fin) {
            $where[] = 'e.fecha BETWEEN :fecha_inicio AND :fecha_fin';
            $params[':fecha_inicio'] = $fecha_inicio;
            $params[':fecha_fin']    = $fecha_fin;
        } elseif ($fecha_inicio) {
            $where[] = 'e.fecha >= :fecha_inicio';
            $params[':fecha_inicio'] = $fecha_inicio;
        } elseif ($fecha_fin) {
            $where[] = 'e.fecha <= :fecha_fin';
            $params[':fecha_fin'] = $fecha_fin;
        }

        if ($tipo_egreso_id) {
            $where[] = 'e.tipo_egreso_id = :tipo_egreso_id';
            $params[':tipo_egreso_id'] = $tipo_egreso_id;
        }

        if ($q) {
            $where[] = '(e.documento LIKE :q OR e.nombre_tercero LIKE :q OR e.observaciones LIKE :q)';
            $params[':q'] = '%' . $q . '%';
        }

        $whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';

        // ===== Totales del resultado filtrado =====
        $totales = $this->fetchOne("
                SELECT COUNT(*) AS cantidad, COALESCE(SUM(e.valor),0) AS total_valor
                FROM egresos e
                $whereSql
            ", $params);

        // ===== Listado =====

        // ===== Resumen por tipo de egreso (similar a ingresos) =====
        $resumenEgresos = $this->fetchAll("
                SELECT 
                    te.nombre AS tipo_egreso,
                    SUM(e.valor) AS total
                FROM egresos e
                INNER JOIN param_tipo_egreso te ON te.id = e.tipo_egreso_id
                $whereSql
                GROUP BY te.nombre
                ORDER BY te.nombre ASC
            ", $params);


        // ===== Resumen por CUENTA DE EGRESO =====
        $resumenCuentas = $this->fetchAll("
                SELECT 
                    ce.nombre AS cuenta,
                    SUM(e.valor) AS total
                FROM egresos e
                LEFT JOIN egresos_cuentas_egreso ce ON ce.id = e.cuenta_egreso_id
                $whereSql
                GROUP BY ce.nombre
                ORDER BY ce.nombre ASC
            ", $params);


        $sql = "
            SELECT
                e.id, e.fecha, e.valor, e.documento, e.nombre_tercero, e.observaciones,
                e.soporte_ruta, e.soporte_nombre, e.soporte_mime,
                e.tipo_documento_id,
                e.tipo_egreso_id, e.cuenta_egreso_id, e.sub_cuenta_egreso_id,
                te.nombre AS tipo_egreso,
                ce.nombre AS cuenta_nombre,
                sce.nombre AS subcuenta_nombre,
                u.first_name, u.last_name
            FROM egresos e
            INNER JOIN param_tipo_egreso te ON te.id = e.tipo_egreso_id
            LEFT JOIN egresos_cuentas_egreso ce ON ce.id = e.cuenta_egreso_id
            LEFT JOIN egresos_sub_cuenta_egreso sce ON sce.id = e.sub_cuenta_egreso_id
            INNER JOIN users u ON u.id = e.user_id
            $whereSql
            ORDER BY e.fecha DESC, e.id DESC
            
            ";

        $egresos = $this->fetchAll($sql, $params);

        // ===== Combos para el modal Editar =====
        $tiposEgreso = $this->fetchAll("
            SELECT id, nombre FROM param_tipo_egreso
            WHERE estado = 1 ORDER BY nombre
            ");

        $cuentasAll = $this->fetchAll("
            SELECT id, nombre, tipo_egreso_id
            FROM egresos_cuentas_egreso
            WHERE empresa_id = :empresa_id AND estado = 1
            ORDER BY nombre
            ", [':empresa_id' => $empresa_id]);

        $subcuentasAll = $this->fetchAll("
            SELECT id, nombre, cuenta_egreso_id
            FROM egresos_sub_cuenta_egreso
            WHERE estado = 1
            AND cuenta_egreso_id IN (SELECT id FROM egresos_cuentas_egreso WHERE empresa_id = :empresa_id)
            ORDER BY nombre
            ", [':empresa_id' => $empresa_id]);

        $tiposDocumento = $this->fetchAll("
            SELECT id, nombre
            FROM param_tipo_documento
            ORDER BY nombre
            ");

        $resumenEgresos = $resumenEgresos ?? [];
        $resumenCuentas = $resumenCuentas ?? [];

        // ===== Render =====
        ob_start();
        include '../modules/financiero/views/egresos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        // Permisos (descomenta si aplica)
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        // if (!$permissionController->hasPermission($currentUserId, 'create_egresos')) {
        //     header('Location: /permission-denied/');
        //     exit;
        // }

        // Contexto
        $empresa_id = (int) ($_SESSION['empresa_id'] ?? 0);

        // ----------------------------------------------------------
        // 🔹 Cargar catálogos base
        // ----------------------------------------------------------

        $tiposDocumento = $this->fetchAll("
        SELECT id, nombre
        FROM param_tipo_documento
        ORDER BY nombre
    ");

        $tiposEgreso = $this->fetchAll("
        SELECT id, nombre
        FROM param_tipo_egreso
        WHERE estado = 1
        ORDER BY nombre
    ");

        // ----------------------------------------------------------
        // 🔹 Cargar Cajas activas (sin filtrar por empresa, según tu instrucción)
        // ----------------------------------------------------------
        $cajas = $this->fetchAll("
        SELECT id, nombre
        FROM cajas
        WHERE estado = 1
        ORDER BY nombre ASC
    ");

        // ----------------------------------------------------------
        // 🔹 Cuentas y Subcuentas
        // ----------------------------------------------------------

        $cuentasAll = $this->fetchAll("
        SELECT id, nombre, tipo_egreso_id
        FROM egresos_cuentas_egreso
        WHERE empresa_id = :empresa_id AND estado = 1
        ORDER BY nombre
    ", [':empresa_id' => $empresa_id]);

        $subcuentasAll = $this->fetchAll("
        SELECT sc.id, sc.nombre, sc.cuenta_egreso_id
        FROM egresos_sub_cuenta_egreso sc
        INNER JOIN egresos_cuentas_egreso c 
            ON c.id = sc.cuenta_egreso_id
        WHERE c.empresa_id = :empresa_id 
        AND sc.estado = 1
        ORDER BY sc.nombre
    ", [':empresa_id' => $empresa_id]);

        // ----------------------------------------------------------
        // 🔹 Dataset JSON para JS (opcional)
        // ----------------------------------------------------------

        $datasetsJson = json_encode([
            'cuentas' => array_map(fn($c) => [
                'id' => (int)$c['id'],
                'nombre' => $c['nombre'],
                'tipo_egreso_id' => (int)$c['tipo_egreso_id'],
            ], $cuentasAll),

            'subcuentas' => array_map(fn($s) => [
                'id' => (int)$s['id'],
                'nombre' => $s['nombre'],
                'cuenta_egreso_id' => (int)$s['cuenta_egreso_id'],
            ], $subcuentasAll),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        // ----------------------------------------------------------
        // 🔹 Render de la vista
        // ----------------------------------------------------------

        ob_start();
        include '../modules/financiero/views/egresos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    public function store()
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = (int) $_SESSION['empresa_id'];


        // if (!$permissionController->hasPermission($currentUserId, 'create_egresos')) {
        //     header('Location: /permission-denied/');
        //     exit;
        // }

        // --- Validaciones simples ---
        $required = ['tipo_documento_id', 'documento', 'nombre_tercero', 'tipo_egreso_id', 'valor', 'fecha'];
        foreach ($required as $r) {
            if (empty($_POST[$r])) {
                $_SESSION['flash_error'] = "Falta el campo: $r";
                header('Location: /egresos/create/');
                return;
            }
        }

        $tipo_documento_id = (int) $_POST['tipo_documento_id'];
        $documento         = trim($_POST['documento']);
        $nombre_tercero    = trim($_POST['nombre_tercero']);
        $tipo_egreso_id    = (int) $_POST['tipo_egreso_id'];
        $valor             = (float) $_POST['valor'];
        $fecha             = $_POST['fecha'];
        $observaciones     = !empty($_POST['observaciones']) ? trim($_POST['observaciones']) : null;

        $cuenta_egreso_id     = !empty($_POST['cuenta_egreso_id']) ? (int) $_POST['cuenta_egreso_id'] : null;
        $sub_cuenta_egreso_id = !empty($_POST['sub_cuenta_egreso_id']) ? (int) $_POST['sub_cuenta_egreso_id'] : null;

        // --- Manejo de archivo (opcional) ---
        $soporte_ruta = $soporte_nombre = $soporte_mime = null;
        $soporte_tamano = null;

        if (!empty($_FILES['soporte']) && $_FILES['soporte']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upload = $_FILES['soporte'];

            if ($upload['error'] !== UPLOAD_ERR_OK) {
                $_SESSION['flash_error'] = 'Error subiendo el archivo (código ' . $upload['error'] . ').';
                header('Location: /egresos/create/');
                return;
            }

            // Validación de tipo y tamaño
            $maxBytes = 10 * 1024 * 1024; // 10MB
            if ($upload['size'] > $maxBytes) {
                $_SESSION['flash_error'] = 'El archivo supera el tamaño máximo de 10 MB.';
                header('Location: /egresos/create/');
                return;
            }

            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime  = $finfo->file($upload['tmp_name']);
            $allowed = [
                'application/pdf' => 'pdf',
                'image/jpeg'      => 'jpg',
                'image/png'       => 'png',
                'image/webp'      => 'webp',
            ];
            if (!isset($allowed[$mime])) {
                $_SESSION['flash_error'] = 'Tipo de archivo no permitido. Solo PDF/JPG/PNG/WEBP.';
                header('Location: /egresos/create/');
                return;
            }

            $ext = $allowed[$mime];

            // === Ruta de soportes (sin crear directorios) ===
            // URL pública donde luego se servirá el archivo:
            $relDir = '../files/soportes_egresos';

            // Carpeta física: un nivel arriba del DOCUMENT_ROOT
            // (si tu public root es .../ceacloud/public, esto apunta a .../ceacloud/files/soportes_egresos)
            //$projectRoot = realpath(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/..');
            //$absDir = $projectRoot . $relDir;
            $absDir = $relDir;

            // Asegurar que existe y que el proceso puede escribir
            if (!is_dir($absDir) || !is_writable($absDir)) {
                $_SESSION['flash_error'] = 'La carpeta de soportes no existe o no tiene permisos de escritura: ' . $absDir;
                header('Location: /egresos/create/');
                return;
            }

            // Nombre de archivo seguro (ya traes $safeOriginal, $filename)
            $safeOriginal = preg_replace('/[^A-Za-z0-9._-]/', '_', $upload['name']);
            $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '_' . $safeOriginal;
            $dest = $absDir . '/' . $filename;

            if (!move_uploaded_file($upload['tmp_name'], $dest)) {
                $_SESSION['flash_error'] = 'No se pudo guardar el archivo en el servidor.';
                header('Location: /egresos/create/');
                return;
            }

            // Metadatos para DB (ruta web relativa)
            $soporte_ruta   = $relDir . '/' . $filename;
            $soporte_nombre = $upload['name'];
            $soporte_mime   = $mime;
            $soporte_tamano = (int) $upload['size'];
        }

        // --- Insert ---
        $sql = "INSERT INTO egresos (
                    empresa_id, tipo_documento_id, documento, nombre_tercero,
                    tipo_egreso_id, cuenta_egreso_id, sub_cuenta_egreso_id,
                    valor, fecha, observaciones,
                    soporte_ruta, soporte_nombre, soporte_mime, soporte_tamano,
                    user_id
                ) VALUES (
                    :empresa_id, :tipo_documento_id, :documento, :nombre_tercero,
                    :tipo_egreso_id, :cuenta_egreso_id, :sub_cuenta_egreso_id,
                    :valor, :fecha, :observaciones,
                    :soporte_ruta, :soporte_nombre, :soporte_mime, :soporte_tamano,
                    :user_id
                )";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindValue(':tipo_documento_id', $tipo_documento_id, PDO::PARAM_INT);
        $stmt->bindValue(':documento', $documento);
        $stmt->bindValue(':nombre_tercero', $nombre_tercero);
        $stmt->bindValue(':tipo_egreso_id', $tipo_egreso_id, PDO::PARAM_INT);
        $stmt->bindValue(':cuenta_egreso_id', $cuenta_egreso_id, $cuenta_egreso_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':sub_cuenta_egreso_id', $sub_cuenta_egreso_id, $sub_cuenta_egreso_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':observaciones', $observaciones);
        $stmt->bindValue(':soporte_ruta', $soporte_ruta);
        $stmt->bindValue(':soporte_nombre', $soporte_nombre);
        $stmt->bindValue(':soporte_mime', $soporte_mime);
        $stmt->bindValue(':soporte_tamano', $soporte_tamano, $soporte_tamano !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
        $stmt->execute();

        $egresoId = $this->conn->lastInsertId();

        // ---------------------------------------------------------------
        // 🔹 Registrar MOVIMIENTO DE CAJA por este EGRESO
        // ---------------------------------------------------------------
        date_default_timezone_set('America/Bogota');

        $caja_id = isset($_POST['caja_id']) ? (int) $_POST['caja_id'] : null;

        if ($caja_id) {

            $fechaColombia = date('Y-m-d H:i:s');

            $sqlMov = "INSERT INTO caja_movimientos
                (empresa_id, caja_id, tipo, origen, egreso_id, valor, descripcion, user_id, fecha)
               VALUES
                (:empresa_id, :caja_id, 'EGRESO', 'egreso', :egreso_id, :valor, :descripcion, :user_id, :fecha)";

            $stmtMov = $this->conn->prepare($sqlMov);
            $stmtMov->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmtMov->bindValue(':caja_id', $caja_id, PDO::PARAM_INT);
            $stmtMov->bindValue(':egreso_id', $egresoId, PDO::PARAM_INT);
            $stmtMov->bindValue(':valor', $valor);
            $stmtMov->bindValue(':descripcion', "Egreso registrado: $nombre_tercero");
            $stmtMov->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
            $stmtMov->bindValue(':fecha', $fechaColombia);

            $stmtMov->execute();
        }

        // ---------------------------------------------------------------
        // (Opcional) Auditoría
        // (new AuditoriaController())->registrar($currentUserId, 'CREATE', 'EGRESOS', 'Creó egreso ID '.$this->conn->lastInsertId(), $empresa_id);

        $_SESSION['success_message'] = 'Egreso creado.';
        header('Location: ' . $routes['egresos_index']);
        exit;
    }

    public function update()
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id    = (int) $_SESSION['empresa_id'];

        // if (!$permissionController->hasPermission($currentUserId, 'edit_egresos')) {
        //     $_SESSION['flash_error'] = 'No tiene permisos para editar egresos.';
        //     header('Location: /egresos/');
        //     return;
        // }

        // ===== Datos del formulario
        $id = (int)($_POST['id'] ?? 0);
        $fecha = $_POST['fecha'] ?? null;

        $tipo_documento_id = (int)($_POST['tipo_documento_id'] ?? 0);
        $documento         = trim($_POST['documento'] ?? '');
        $nombre_tercero    = trim($_POST['nombre_tercero'] ?? '');

        $tipo_egreso_id = (int)($_POST['tipo_egreso_id'] ?? 0);
        $cuenta_egreso_id = !empty($_POST['cuenta_egreso_id']) ? (int)$_POST['cuenta_egreso_id'] : null;
        $sub_cuenta_egreso_id = !empty($_POST['sub_cuenta_egreso_id']) ? (int)$_POST['sub_cuenta_egreso_id'] : null;
        $valor = (float)($_POST['valor'] ?? 0);
        $observaciones = !empty($_POST['observaciones']) ? trim($_POST['observaciones']) : null;
        $remove_soporte = !empty($_POST['remove_soporte']) ? 1 : 0;

        if ($id <= 0 || !$fecha || $tipo_egreso_id <= 0 || $valor < 0 || $tipo_documento_id <= 0 || $documento === '' || $nombre_tercero === '') {
            $_SESSION['flash_error'] = 'Datos incompletos para actualizar el egreso.';
            header('Location: /egresos/');
            return;
        }

        // ===== Egreso de la empresa + soporte actual
        $egreso = $this->fetchOne("
            SELECT id, soporte_ruta, soporte_nombre, soporte_mime, soporte_tamano
            FROM egresos
            WHERE id = :id AND empresa_id = :empresa_id
                ", [':id' => $id, ':empresa_id' => $empresa_id]);

        if (!$egreso) {
            $_SESSION['flash_error'] = 'Egreso no encontrado.';
            header('Location: /egresos/');
            return;
        }

        // ===== Validaciones de cuenta/subcuenta (igual que ya tenías)
        if ($cuenta_egreso_id) {
            $cuenta = $this->fetchOne("
                SELECT id, tipo_egreso_id FROM egresos_cuentas_egreso
                WHERE id = :id AND empresa_id = :empresa_id AND estado = 1
                ", [':id' => $cuenta_egreso_id, ':empresa_id' => $empresa_id]);

            if (!$cuenta) {
                $_SESSION['flash_error'] = 'La cuenta de egreso seleccionada no es válida.';
                header('Location: /egresos/');
                return;
            }
            if ((int)$cuenta['tipo_egreso_id'] !== $tipo_egreso_id) {
                $_SESSION['flash_error'] = 'La cuenta no corresponde al tipo de egreso.';
                header('Location: /egresos/');
                return;
            }
        }
        if ($sub_cuenta_egreso_id) {
            $sub = $this->fetchOne("
            SELECT sc.id, sc.cuenta_egreso_id
            FROM egresos_sub_cuenta_egreso sc
            INNER JOIN egresos_cuentas_egreso c ON c.id = sc.cuenta_egreso_id
            WHERE sc.id = :id AND c.empresa_id = :empresa_id AND sc.estado = 1
        ", [':id' => $sub_cuenta_egreso_id, ':empresa_id' => $empresa_id]);
            if (!$sub) {
                $_SESSION['flash_error'] = 'La subcuenta seleccionada no es válida.';
                header('Location: /egresos/');
                return;
            }
            if ($cuenta_egreso_id && (int)$sub['cuenta_egreso_id'] !== (int)$cuenta_egreso_id) {
                $_SESSION['flash_error'] = 'La subcuenta no corresponde a la cuenta seleccionada.';
                header('Location: /egresos/');
                return;
            }
            if (!$cuenta_egreso_id) $cuenta_egreso_id = (int)$sub['cuenta_egreso_id'];
        }

        // ===== Manejo de SOPORTE
        $soporte_ruta   = $egreso['soporte_ruta'] ?? null;
        $soporte_nombre = $egreso['soporte_nombre'] ?? null;
        $soporte_mime   = $egreso['soporte_mime'] ?? null;
        $soporte_tamano = $egreso['soporte_tamano'] ?? null;

        $deleteOld = false;

        // ¿Hay archivo nuevo?
        if (!empty($_FILES['soporte']['name']) && $_FILES['soporte']['error'] === UPLOAD_ERR_OK) {

            $upload = $_FILES['soporte'];
            $maxSize = 10 * 1024 * 1024; // 10MB

            if ($upload['size'] > $maxSize) {
                $_SESSION['flash_error'] = 'El soporte excede el límite de 10MB.';
                header('Location: /egresos/');
                return;
            }

            $tmp  = $upload['tmp_name'];

            $mime = mime_content_type($tmp) ?: '';
            $allowed = ['application/pdf', 'image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($mime, $allowed, true)) {
                $_SESSION['flash_error'] = 'Tipo de archivo no permitido. Solo PDF o imagen.';
                header('Location: /egresos/');
                return;
            }

            // ruta fija /files/soportes_egresos (ya existe en despliegue)
            $absDir = '../files/soportes_egresos';
            //$projectRoot = realpath(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/..');
            //$absDir = $relDir;

            if (!is_dir($absDir) || !is_writable($absDir)) {
                $_SESSION['flash_error'] = 'La carpeta de soportes no existe o no tiene permisos: ' . $absDir;
                header('Location: /egresos/');
                return;
            }

            $safeOriginal = preg_replace('/[^A-Za-z0-9._-]/', '_', $upload['name']);
            $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '_' . $safeOriginal;
            $dest = $absDir . '/' . $filename;

            if (!move_uploaded_file($upload['tmp_name'], $dest)) {
                $_SESSION['flash_error'] = 'No se pudo guardar el archivo en el servidor.';
                header('Location: /egresos/');
                return;
            }

            // set nuevos metadatos
            $soporte_ruta   = $absDir . '/' . $filename;
            $soporte_nombre = $upload['name'];
            $soporte_mime   = $mime;
            $soporte_tamano = (int)$upload['size'];

            $deleteOld = true;
        } elseif ($remove_soporte) {
            // marcar para eliminar el actual
            $soporte_ruta = $soporte_nombre = $soporte_mime = null;
            $soporte_tamano = null;
            $deleteOld = true;
        }

        // ===== Update =====
        $stmt = $this->conn->prepare("
            UPDATE egresos
            SET fecha = :fecha,
                tipo_documento_id = :tipo_doc,
                documento = :documento,
                nombre_tercero = :tercero,
                tipo_egreso_id = :tipo,
                cuenta_egreso_id = :cuenta,
                sub_cuenta_egreso_id = :subcuenta,
                valor = :valor,
                observaciones = :obs,
                soporte_ruta = :sruta,
                soporte_nombre = :snombre,
                soporte_mime = :smime,
                soporte_tamano = :stamano,
                updated_at = NOW()
            WHERE id = :id AND empresa_id = :empresa_id
        ");
        $stmt->bindValue(':fecha', $fecha);
        $stmt->bindValue(':tipo_doc', $tipo_documento_id, PDO::PARAM_INT);
        $stmt->bindValue(':documento', $documento);
        $stmt->bindValue(':tercero', $nombre_tercero);
        $stmt->bindValue(':tipo', $tipo_egreso_id, PDO::PARAM_INT);
        $stmt->bindValue(':cuenta', $cuenta_egreso_id, $cuenta_egreso_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':subcuenta', $sub_cuenta_egreso_id, $sub_cuenta_egreso_id ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':valor', $valor);
        $stmt->bindValue(':obs', $observaciones);
        $stmt->bindValue(':sruta', $soporte_ruta);
        $stmt->bindValue(':snombre', $soporte_nombre);
        $stmt->bindValue(':smime', $soporte_mime);
        $stmt->bindValue(':stamano', $soporte_tamano, $soporte_tamano !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        // ===== Borrar archivo anterior (si corresponde)
        if ($deleteOld && !empty($egreso['soporte_ruta'])) {
            $projectRoot = realpath(rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/..');
            $oldAbs = $egreso['soporte_ruta'];
            if (is_file($oldAbs)) @unlink($oldAbs);
        }

        $_SESSION['success_message'] = 'Egreso actualizado.';
        header('Location: ' . $routes['egresos_index']);
        exit;
    }

    public function delete()
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id    = (int) $_SESSION['empresa_id'];

        // if (!$permissionController->hasPermission($currentUserId, 'delete_egresos')) {
        //     $_SESSION['flash_error'] = 'No tiene permisos para eliminar egresos.';
        //     header('Location: /egresos/');
        //     return;
        // }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            $_SESSION['flash_error'] = 'ID inválido.';
            header('Location: /egresos/');
            return;
        }

        // Validar que pertenece a la empresa
        $egreso = $this->fetchOne("
                SELECT id, soporte_ruta FROM egresos
                WHERE id = :id AND empresa_id = :empresa_id
            ", [':id' => $id, ':empresa_id' => $empresa_id]);

        if (!$egreso) {
            $_SESSION['flash_error'] = 'Egreso no encontrado.';
            header('Location: /egresos/');
            return;
        }

        // (Opcional) eliminar archivo físico del soporte
        if (!empty($egreso['soporte_ruta'])) {
            $projectRoot = "../files/soportes_egresos";
            $absPath = $projectRoot . $egreso['soporte_ruta'];
            if (is_file($absPath)) @unlink($absPath);
        }

        $stmt = $this->conn->prepare("DELETE FROM egresos WHERE id = :id AND empresa_id = :empresa_id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = 'Egreso eliminado.';
        header('Location: ' . $routes['egresos_index']);
        exit;
    }

    // ===== Helpers =====
    private function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function fetchOne(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: ['cantidad' => 0, 'total_valor' => 0];
    }
}
