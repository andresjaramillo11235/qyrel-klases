<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class CajasController
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

    // ==========================================================
    // 🔹 Helpers internos (mismo estilo CEACLOUD)
    // ==========================================================
    private function fetchAll($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function fetchOne($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function execute($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }


    // ==========================================================
    // 🔹 1. Listado
    // ==========================================================
    public function index()
    {
        $routes     = include '../config/Routes.php';
        $empresaId  = $_SESSION['empresa_id'];
        $userId     = $_SESSION['user_id'];

        // Permisos
        $permissionController = new PermissionController();
        if (!$permissionController->hasPermission($userId, 'view_cajas')) {
            header("Location: /permission-denied/");
            exit;
        }

        $cajas = $this->fetchAll("
                SELECT *
                FROM cajas
                WHERE empresa_id = :emp
                ORDER BY nombre ASC
            ", [':emp' => $empresaId]);

        ob_start();
        include '../modules/financiero/views/cajas/index.php';
        $content = ob_get_clean();

        include '../shared/views/layout.php';
    }


    // ==========================================================
    // 🔹 2. Form crear
    // ==========================================================
    public function create()
    {
        $routes     = include '../config/Routes.php';
        $userId     = $_SESSION['user_id'];

        $permissionController = new PermissionController();
        if (!$permissionController->hasPermission($userId, 'create_cajas')) {
            header("Location: /permission-denied/");
            exit;
        }

        ob_start();
        include '../modules/financiero/views/cajas/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    // ==========================================================
    // 🔹 3. Guardar nueva caja
    // ==========================================================
    public function store()
    {


        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . $routes['cajas_index']);
            exit;
        }

        $empresaId  = $_SESSION['empresa_id'];
        $userId     = $_SESSION['user_id'];

        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');


        $tipoInput = trim($_POST['tipo'] ?? 'EFECTIVO');

        $mapaTipos = [
            'EFECTIVO'            => 'EFECTIVO',
            'efectivo'            => 'EFECTIVO',

            'BANCO'               => 'BANCO',
            'banco'               => 'BANCO',

            'BILLETERA DIGITAL'   => 'BILLETERA_DIGITAL',
            'BILLETERA_DIGITAL'   => 'BILLETERA_DIGITAL',
            'billetera digital'   => 'BILLETERA_DIGITAL',

            'DATÁFONO / POS'      => 'DATFONO',
            'DATÁFONO'            => 'DATFONO',
            'DATFONO'             => 'DATFONO',
            'datfono'             => 'DATFONO',

            'QR'                  => 'QR',
            'qr'                  => 'QR',

            'OTRO'                => 'OTRO',
            'otros'               => 'OTRO',
        ];

        if (!isset($mapaTipos[$tipoInput])) {
            $_SESSION['error_message'] = "Tipo de caja inválido.";
            header("Location: " . $routes['cajas_create']);
            exit;
        }

        $tipo = $mapaTipos[$tipoInput];

        if (!$nombre) {
            $_SESSION['error_message'] = "El nombre de la caja es obligatorio.";
            header("Location: " . $routes['cajas_create']);
            exit;
        }

        $this->execute("
                INSERT INTO cajas (empresa_id, nombre, descripcion, tipo, created_by)
                VALUES (:emp, :nom, :des, :tipo, :uid)
            ", [
            ':emp'  => $empresaId,
            ':nom'  => $nombre,
            ':des'  => $descripcion,
            ':tipo' => $tipo,
            ':uid'  => $userId
        ]);

        $_SESSION['success_message'] = "Caja creada correctamente.";
        header("Location: " . $routes['cajas_index']);
        exit;
    }


    // ==========================================================
    // 🔹 4. Form editar
    // ==========================================================
    public function edit($id)
    {
        $routes     = include '../config/Routes.php';
        $empresaId  = $_SESSION['empresa_id'];
        $userId     = $_SESSION['user_id'];

        if (!$id) {
            header("Location: " . $routes['cajas_index']);
            exit;
        }

        $permissionController = new PermissionController();
        if (!$permissionController->hasPermission($userId, 'edit_cajas')) {
            header("Location: /permission-denied/");
            exit;
        }

        $caja = $this->fetchOne("
                SELECT *
                FROM cajas
                WHERE id = :id AND empresa_id = :emp
                LIMIT 1
            ", [
            ':id'  => $id,
            ':emp' => $empresaId
        ]);

        if (!$caja) {
            header("Location: " . $routes['cajas_index']);
            exit;
        }

        ob_start();
        include '../modules/financiero/views/cajas/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    // ==========================================================
    // 🔹 5. Actualizar
    // ==========================================================
    public function update()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . $routes['cajas_index']);
            exit;
        }

        $empresaId  = $_SESSION['empresa_id'];

        $id          = $_POST['id'] ?? null;
        $nombre      = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $tipo        = trim($_POST['tipo'] ?? 'efectivo');
        $estado      = (int)($_POST['estado'] ?? 1);

        if (!$id || !$nombre) {
            $_SESSION['error_message'] = "Datos incompletos para actualizar.";
            header("Location: " . $routes['cajas_edit'] . '?id=' . $id);
            exit;
        }

        $this->execute("
                UPDATE cajas
                SET nombre = :nom,
                    descripcion = :des,
                    tipo = :tipo,
                    estado = :est
                WHERE id = :id AND empresa_id = :emp
            ", [
            ':nom'  => $nombre,
            ':des'  => $descripcion,
            ':tipo' => $tipo,
            ':est'  => $estado,
            ':id'   => $id,
            ':emp'  => $empresaId
        ]);

        $_SESSION['success_message'] = "Caja actualizada correctamente.";
        header("Location: " . $routes['cajas_index']);
        exit;
    }


    // ==========================================================
    // 🔹 6. Eliminar
    // ==========================================================
    public function delete()
    {
        $routes = include '../config/Routes.php';

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $routes['cajas_index']);
            exit;
        }

        $id       = (int)($_POST['id'] ?? 0);
        $empresa  = (int)$_SESSION['empresa_id'];

        if ($id <= 0) {
            $_SESSION['error_message'] = "Solicitud inválida.";
            header("Location: " . $routes['cajas_index']);
            exit;
        }

        try {

            // 1️⃣ Validar si tiene movimientos
            $qMov = $this->conn->prepare("
            SELECT COUNT(*) 
            FROM movimientos_caja 
            WHERE caja_id = :cid 
            AND empresa_id = :emp
        ");
            $qMov->execute([
                ':cid' => $id,
                ':emp' => $empresa
            ]);
            $tieneMov = $qMov->fetchColumn();

            if ($tieneMov > 0) {
                $_SESSION['error_message'] =
                    "No puedes eliminar esta caja porque tiene movimientos registrados.";
                header("Location: " . $routes['cajas_index']);
                exit;
            }

            // 2️⃣ Eliminar caja
            $qDel = $this->conn->prepare("
            DELETE FROM cajas 
            WHERE id = :id AND empresa_id = :emp
            LIMIT 1
        ");
            $qDel->execute([
                ':id' => $id,
                ':emp' => $empresa
            ]);

            $_SESSION['success_message'] = "Caja eliminada correctamente.";
            header("Location: " . $routes['cajas_index']);
            exit;
        } catch (Exception $e) {

            $_SESSION['error_message'] =
                "Error al eliminar la caja: " . $e->getMessage();

            header("Location: " . $routes['cajas_index']);
            exit;
        }
    }


    // ----------------------------------------------------------
    // 🔹 MOSTRAR FORMULARIO CAJA DIARIA
    // ----------------------------------------------------------
    public function mostrarFormCajaDiaria()
    {
        // Validar sesión
        if (!isset($_SESSION['user_id'], $_SESSION['empresa_id'])) {
            header("Location: /login");
            exit;
        }

        // (Opcional) validar rol admin aquí si ya lo manejas
        // if (!$_SESSION['is_admin']) { ... }

        // Fecha por defecto: hoy
        $fecha = date('Y-m-d');

        $mostrarResultados = false;

        // Renderizar vista
        ob_start();
        include '../modules/financiero/views/cajas/caja_diaria_form.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    // ----------------------------------------------------------
    // 🔹 PROCESAR CAJA DIARIA
    // ----------------------------------------------------------
    public function procesarCajaDiaria()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: /caja-diaria");
            exit;
        }

        try {
            $empresaId = $_SESSION['empresa_id'] ?? null;
            if (!$empresaId) {
                throw new Exception("Sesión inválida.");
            }

            $fecha = $_POST['fecha'] ?? date('Y-m-d');

            // ----------------------------------------------------------
            // 1. CONSOLIDADO POR CAJA
            // ----------------------------------------------------------
            $qConsolidado = $this->conn->prepare("
            SELECT 
                c.id AS caja_id,
                c.nombre AS caja,
                SUM(CASE WHEN cm.tipo = 'INGRESO' THEN cm.valor ELSE 0 END) AS total_ingresos,
                SUM(CASE WHEN cm.tipo = 'EGRESO'  THEN cm.valor ELSE 0 END) AS total_egresos,
                SUM(
                    CASE 
                        WHEN cm.tipo = 'INGRESO' THEN cm.valor
                        WHEN cm.tipo = 'EGRESO'  THEN -cm.valor
                        ELSE 0
                    END
                ) AS saldo
            FROM caja_movimientos cm
            INNER JOIN cajas c ON c.id = cm.caja_id
            WHERE cm.empresa_id = :empresa
              AND DATE(cm.fecha) = :fecha
            GROUP BY c.id
            ORDER BY c.nombre
        ");

            $qConsolidado->execute([
                ':empresa' => $empresaId,
                ':fecha'   => $fecha
            ]);

            $consolidado = $qConsolidado->fetchAll(PDO::FETCH_ASSOC);

            // ----------------------------------------------------------
            // 2. TOTALES GENERALES
            // ----------------------------------------------------------
            $totalIngresos = 0;
            $totalEgresos  = 0;
            $saldoGeneral  = 0;

            foreach ($consolidado as $row) {
                $totalIngresos += $row['total_ingresos'];
                $totalEgresos  += $row['total_egresos'];
                $saldoGeneral  += $row['saldo'];
            }

            // ----------------------------------------------------------
            // 3. DETALLE DE MOVIMIENTOS
            // ----------------------------------------------------------
            $qDetalle = $this->conn->prepare("
            SELECT 
                cm.fecha,
                c.nombre AS caja,
                cm.tipo,
                cm.valor,
                cm.descripcion,
                u.username
            FROM caja_movimientos cm
            INNER JOIN cajas c ON c.id = cm.caja_id
            LEFT JOIN users u ON u.id = cm.user_id
            WHERE cm.empresa_id = :empresa
              AND DATE(cm.fecha) = :fecha
            ORDER BY cm.fecha ASC
        ");

            $qDetalle->execute([
                ':empresa' => $empresaId,
                ':fecha'   => $fecha
            ]);

            $detalle = $qDetalle->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $_SESSION['error_message'] = $e->getMessage();
            header("Location: /caja-diaria");
            exit;
        }

        $mostrarResultados = true;

        // ----------------------------------------------------------
        // Renderizar vista con resultados
        // ----------------------------------------------------------
        ob_start();
        include '../modules/financiero/views/cajas/caja_diaria_form.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
