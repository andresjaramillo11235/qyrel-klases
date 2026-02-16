<?php
require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class CuentasEgresoController
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


    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id    = (int) $_SESSION['empresa_id'];
        $rol           = $_SESSION['rol_nombre'] ?? '';

        // if (!$permissionController->hasPermission($currentUserId, 'manage_cuentas_egreso')) {
        //     header('Location: /permission-denied/');
        //     exit;
        // }

        // Tipos de egreso (para modales y mapeo)
        $tiposEgreso = $this->fetchAll("
        SELECT id, nombre
        FROM param_tipo_egreso
        WHERE estado = 1
        ORDER BY nombre
    ");
        $mapTipos = [];
        foreach ($tiposEgreso as $t) {
            $mapTipos[(int)$t['id']] = $t['nombre'];
        }

        // Cuentas de la empresa + nombre del tipo (JOIN)
        $cuentas = $this->fetchAll("
        SELECT c.id, c.empresa_id, c.tipo_egreso_id, c.nombre, c.estado,
               te.nombre AS tipo_nombre
        FROM egresos_cuentas_egreso c
        INNER JOIN param_tipo_egreso te ON te.id = c.tipo_egreso_id
        WHERE c.empresa_id = :empresa_id
        ORDER BY c.nombre ASC
    ", [':empresa_id' => $empresa_id]);

        // Subcuentas por cuenta
        $mapSub = [];
        if (!empty($cuentas)) {
            $ids = array_column($cuentas, 'id');
            $in  = implode(',', array_fill(0, count($ids), '?'));
            $stmt = $this->conn->prepare("
            SELECT id, cuenta_egreso_id, nombre, estado
            FROM egresos_sub_cuenta_egreso
            WHERE cuenta_egreso_id IN ($in)
            ORDER BY nombre ASC
        ");
            $stmt->execute($ids);
            $subcuentas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            foreach ($subcuentas as $sc) {
                $mapSub[$sc['cuenta_egreso_id']][] = $sc;
            }
        }

        ob_start();
        // disponibles: $tiposEgreso, $mapTipos, $cuentas, $mapSub
        include '../modules/financiero/views/egresos/cuentas_subcuentas.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    /** CUENTAS DE EGRESO */

    /**
     * Crear cuenta
     */
    public function storeCuenta()
    {
        $routes = include '../config/Routes.php';

        $empresa_id     = (int) $_SESSION['empresa_id'];
        $tipo_egreso_id = (int) ($_POST['tipo_egreso_id'] ?? 0);
        $nombre         = trim($_POST['nombre'] ?? '');

        if ($tipo_egreso_id <= 0 || $nombre === '') {
            $_SESSION['error_message'] = 'Tipo de egreso y nombre son obligatorios.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        $dup = $this->fetchOne("
            SELECT id
            FROM egresos_cuentas_egreso
            WHERE empresa_id = :empresa_id
                AND tipo_egreso_id = :tipo
                AND nombre = :nombre
            LIMIT 1
            ", [':empresa_id' => $empresa_id, ':tipo' => $tipo_egreso_id, ':nombre' => $nombre]);

        if ($dup) {
            $_SESSION['error_message'] = 'Ya existe una cuenta con ese nombre para ese tipo de egreso.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        $stmt = $this->conn->prepare("
                INSERT INTO egresos_cuentas_egreso (empresa_id, tipo_egreso_id, nombre, estado)
                VALUES (:empresa_id, :tipo_egreso_id, :nombre, 1)
            ");

        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->bindValue(':tipo_egreso_id', $tipo_egreso_id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();

        $_SESSION['success_message'] = 'Cuenta de egreso creada correctamente.';
        header('Location: ' . $routes['egresos_cuentas_egreso_index']);
    }

    /**
     * Actualizar cuenta
     */
    public function updateCuenta()
    {
        $routes = include '../config/Routes.php';

        $empresa_id = (int) $_SESSION['empresa_id'];
        $id     = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($id <= 0 || $nombre === '') {
            $_SESSION['error_message'] = 'Datos incompletos para actualizar la cuenta.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 1) Verificar que la cuenta exista y pertenezca a la empresa,
        //    y recuperar su tipo_egreso_id
        $cuenta = $this->fetchOne("
                SELECT id, tipo_egreso_id
                FROM egresos_cuentas_egreso
                WHERE id = :id AND empresa_id = :empresa_id
            ", [':id' => $id, ':empresa_id' => $empresa_id]);

        if (!$cuenta) {
            $_SESSION['error_message'] = 'Cuenta no encontrada o sin permisos.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        $tipoId = (int) $cuenta['tipo_egreso_id'];

        // 2) Validar duplicado por (empresa_id, tipo_egreso_id, nombre)
        //    Nota: con colación *_ci, la comparación es case-insensitive.
        $dup = $this->fetchOne("
                SELECT id
                FROM egresos_cuentas_egreso
                WHERE empresa_id = :empresa_id
                AND tipo_egreso_id = :tipo
                AND nombre = :nombre
                AND id <> :id
                LIMIT 1
            ", [
            ':empresa_id' => $empresa_id,
            ':tipo'       => $tipoId,
            ':nombre'     => $nombre,
            ':id'         => $id
        ]);

        if ($dup) {
            $_SESSION['error_message'] = 'Ya existe una cuenta con ese nombre para ese tipo de egreso.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 3) Actualizar solo el nombre
        $stmt = $this->conn->prepare("
            UPDATE egresos_cuentas_egreso
            SET nombre = :nombre
            WHERE id = :id AND empresa_id = :empresa_id
        ");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = 'Cuenta de egreso actualizada correctamente.';
        header('Location: ' . $routes['egresos_cuentas_egreso_index']);
        exit;
    }

    /**
     * Borrar cuenta
     */
    public function deleteCuenta()
    {
        $routes = include '../config/Routes.php';

        $empresa_id = (int) $_SESSION['empresa_id'];
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $this->flashAndBack('ID inválido.', 'error');
            return;
        }

        // 1) Verificar que la cuenta exista y pertenezca a la empresa
        $cuenta = $this->fetchOne("
                SELECT id
                FROM egresos_cuentas_egreso
                WHERE id = :id AND empresa_id = :empresa_id
                LIMIT 1
            ", [':id' => $id, ':empresa_id' => $empresa_id]);

        if (!$cuenta) {
            $_SESSION['error_message'] = 'Cuenta no encontrada o sin permisos.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 2) Contar dependencias: subcuentas y egresos (directos e indirectos por subcuenta)
        $deps = $this->fetchOne("
                SELECT
                (SELECT COUNT(*) FROM egresos_sub_cuenta_egreso sc WHERE sc.cuenta_egreso_id = :id) AS subcuentas,
                (SELECT COUNT(*) FROM egresos e WHERE e.empresa_id = :empresa_id AND e.cuenta_egreso_id = :id) AS egresos_directos,
                (SELECT COUNT(*) FROM egresos e
                    WHERE e.empresa_id = :empresa_id
                    AND e.sub_cuenta_egreso_id IN (
                            SELECT sc.id FROM egresos_sub_cuenta_egreso sc WHERE sc.cuenta_egreso_id = :id
                    )
                ) AS egresos_por_subcuentas
            ", [':id' => $id, ':empresa_id' => $empresa_id]);

        $subCount   = (int)($deps['subcuentas'] ?? 0);
        $dirCount   = (int)($deps['egresos_directos'] ?? 0);
        $indCount   = (int)($deps['egresos_por_subcuentas'] ?? 0);
        $egresosTot = $dirCount + $indCount;

        if ($subCount > 0) {
            $_SESSION['error_message'] = "No se puede eliminar: la cuenta tiene $subCount subcuenta(s).";
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }
        if ($egresosTot > 0) {
            $_SESSION['error_message'] = "No se puede eliminar: hay $egresosTot egreso(s) asociados a esta cuenta.";
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 3) Eliminar (sin dependencias)
        $stmt = $this->conn->prepare("
                DELETE FROM egresos_cuentas_egreso
                WHERE id = :id AND empresa_id = :empresa_id
            ");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa_id', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = 'Cuenta de egreso eliminada correctamente.';
        header('Location: ' . $routes['egresos_cuentas_egreso_index']);
        exit;
    }

    /** SUBCUENTAS DE EGRESO */

    /**
     * Crear subcuenta
     */
    public function storeSubcuenta()
    {
        $routes = include '../config/Routes.php';

        $empresa_id = (int) $_SESSION['empresa_id'];
        $cuenta_id  = (int) ($_POST['cuenta_egreso_id'] ?? 0);
        $nombre     = trim($_POST['nombre'] ?? '');

        if ($cuenta_id <= 0 || $nombre === '') {
            $_SESSION['error_message'] = "Datos incompletos para crear subcuenta.";
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 1) Validar que la cuenta exista, sea de la empresa y esté activa
        $cuenta = $this->fetchOne("
            SELECT id
            FROM egresos_cuentas_egreso
            WHERE id = :id AND empresa_id = :empresa_id AND estado = 1
            LIMIT 1
        ", [':id' => $cuenta_id, ':empresa_id' => $empresa_id]);

        if (!$cuenta) {
            $_SESSION['error_message'] = "La cuenta no existe o no está activa/permitida para su empresa.";
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 2) Validación de duplicado por (cuenta_egreso_id, nombre)
        //    Con colación *_ci la comparación es case-insensitive.
        $dup = $this->fetchOne("
            SELECT id
            FROM egresos_sub_cuenta_egreso
            WHERE cuenta_egreso_id = :cid AND nombre = :nombre
            LIMIT 1
        ", [':cid' => $cuenta_id, ':nombre' => $nombre]);

        if ($dup) {
            $_SESSION['error_message'] = "Ya existe una subcuenta con ese nombre en esta cuenta.";
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 3) Insertar
        $stmt = $this->conn->prepare("
            INSERT INTO egresos_sub_cuenta_egreso (cuenta_egreso_id, nombre, estado)
            VALUES (:cid, :nombre, 1)
        ");
        $stmt->bindValue(':cid', $cuenta_id, PDO::PARAM_INT);
        $stmt->bindValue(':nombre', $nombre);
        $stmt->execute();

        $_SESSION['success_message'] = 'Subcuenta creada correctamente.';
        header('Location: ' . $routes['egresos_cuentas_egreso_index']);
        exit;
    }

    /**
     * Actualizar subcuenta
     */
    public function updateSubcuenta()
    {
        $routes = include '../config/Routes.php';

        $empresa_id = (int) $_SESSION['empresa_id'];
        $id     = (int) ($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');

        if ($id <= 0 || $nombre === '') {
            $_SESSION['error_message'] = 'Datos incompletos para actualizar la subcuenta.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 1) Traer la subcuenta y validar que pertenece a una cuenta de esta empresa
        $sub = $this->fetchOne("
                SELECT sc.id, sc.cuenta_egreso_id
                FROM egresos_sub_cuenta_egreso sc
                INNER JOIN egresos_cuentas_egreso c ON c.id = sc.cuenta_egreso_id
                WHERE sc.id = :id AND c.empresa_id = :empresa_id
                LIMIT 1
            ", [':id' => $id, ':empresa_id' => $empresa_id]);

        if (!$sub) {
            $_SESSION['error_message'] = 'Subcuenta no encontrada o sin permisos.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        $cuentaId = (int)$sub['cuenta_egreso_id'];

        // 3) Actualizar solo el nombre
        $stmt = $this->conn->prepare("
                UPDATE egresos_sub_cuenta_egreso
                SET nombre = :nombre
                WHERE id = :id
            ");
        $stmt->bindValue(':nombre', $nombre);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = 'Subcuenta actualizada.';
        header('Location: ' . $routes['egresos_cuentas_egreso_index']);
    }

    public function deleteSubcuenta()
    {
        $routes = include '../config/Routes.php';
        //$this->requirePermission('manage_cuentas_egreso');

        $empresa_id = (int) $_SESSION['empresa_id'];
        $id = (int) ($_POST['id'] ?? 0);

        if ($id <= 0) {
            $_SESSION['error_message'] = 'ID inválido.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 1) Validar que la subcuenta exista y sea de una cuenta de esta empresa
        $sub = $this->fetchOne("
                SELECT sc.id, sc.cuenta_egreso_id
                FROM egresos_sub_cuenta_egreso sc
                INNER JOIN egresos_cuentas_egreso c ON c.id = sc.cuenta_egreso_id
                WHERE sc.id = :id AND c.empresa_id = :empresa_id
                LIMIT 1
            ", [':id' => $id, ':empresa_id' => $empresa_id]);

        if (!$sub) {
            $_SESSION['error_message'] = 'Subcuenta no encontrada o sin permisos.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 2) Validar que no esté usada en egresos
        $deps = $this->fetchOne("
                SELECT COUNT(*) AS c
                FROM egresos
                WHERE empresa_id = :empresa_id
                AND sub_cuenta_egreso_id = :id
            ", [':empresa_id' => $empresa_id, ':id' => $id]);

        if ((int)($deps['c'] ?? 0) > 0) {
            $_SESSION['error_message'] = "No se puede eliminar: hay {$deps['c']} egreso(s) asociados a esta subcuenta.";
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            return;
        }

        // 3) Eliminar
        $stmt = $this->conn->prepare("DELETE FROM egresos_sub_cuenta_egreso WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

            $_SESSION['success_message'] = 'Subcuenta eliminada.';
            header('Location: ' . $routes['egresos_cuentas_egreso_index']);
            exit;
    }

    // ===== Helpers comunes =====

    private function requirePermission(string $perm)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'] ?? null;
        if (!$currentUserId || !$permissionController->hasPermission($currentUserId, $perm)) {
            header('Location: /permission-denied/');
            exit;
        }
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function fetchOne(string $sql, array $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function flashAndRedirect(string $msg, string $type = 'success')
    {
        $_SESSION['flash_' . $type] = $msg;
        header('Location: /cuentas-egreso/');
        exit;
    }

    private function flashAndBack(string $msg, string $type = 'error')
    {
        $_SESSION['flash_' . $type] = $msg;
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/cuentas-egreso/'));
        exit;
    }
}
