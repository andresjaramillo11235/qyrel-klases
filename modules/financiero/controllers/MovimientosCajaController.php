<?php
require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class MovimientosCajaController
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

    // ----------------------------------------------------------
    // 🔹 INDEX: Listado de movimientos con filtros y paginación
    // ----------------------------------------------------------
    public function index()
    {
        $routes     = include '../config/Routes.php';
        $empresa_id = (int) $_SESSION['empresa_id'];
        $user_id    = (int) $_SESSION['user_id'];

        $selCaja   = $_POST['caja_id']   ?? '';
        $selTipo   = $_POST['tipo']      ?? '';
        $selOrigen = $_POST['origen']    ?? '';

        $fechaIni  = $_POST['fecha_ini'] ?? '';
        $fechaFin  = $_POST['fecha_fin'] ?? '';

        // ----------------------------------------------------------
        // 🔹 PERMISOS
        // ----------------------------------------------------------
        // $permissionController = new PermissionController();
        // if (!$permissionController->hasPermission($user_id, 'view_movimientos_caja')) {
        //     header('Location: /permission-denied/');
        //     exit;
        // }

        // ----------------------------------------------------------
        // 🔹 Filtros (GET)
        // ----------------------------------------------------------
        $filtros = [
            'fecha_ini' => $_POST['fecha_ini'] ?? null,
            'fecha_fin' => $_POST['fecha_fin'] ?? null,
            'caja_id'   => $_POST['caja_id'] ?? null,
            'tipo'      => $_POST['tipo'] ?? null,
            'origen'    => $_POST['origen'] ?? null,
        ];

        // ----------------------------------------------------------
        // 🔹 Paginación
        // ----------------------------------------------------------
        $pagina      = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $porPagina   = 20;
        $offset      = ($pagina - 1) * $porPagina;

        // ----------------------------------------------------------
        // 🔹 Construir SQL dinámico
        // ----------------------------------------------------------
        $sqlBase = "
            FROM caja_movimientos cm
            LEFT JOIN cajas c ON c.id = cm.caja_id
            LEFT JOIN users u ON u.id = cm.user_id
            WHERE cm.empresa_id = :emp
        ";

        $params = [':emp' => $empresa_id];

        if (!empty($filtros['fecha_ini'])) {
            $sqlBase .= " AND cm.fecha >= :fecha_ini ";
            $params[':fecha_ini'] = $filtros['fecha_ini'] . " 00:00:00";
        }
        if (!empty($filtros['fecha_fin'])) {
            $sqlBase .= " AND cm.fecha <= :fecha_fin ";
            $params[':fecha_fin'] = $filtros['fecha_fin'] . " 23:59:59";
        }
        if (!empty($filtros['caja_id'])) {
            $sqlBase .= " AND cm.caja_id = :caja_id ";
            $params[':caja_id'] = $filtros['caja_id'];
        }
        if (!empty($filtros['tipo'])) {
            $sqlBase .= " AND cm.tipo = :tipo ";
            $params[':tipo'] = $filtros['tipo'];
        }
        if (!empty($filtros['origen'])) {
            $sqlBase .= " AND cm.origen = :origen ";
            $params[':origen'] = $filtros['origen'];
        }

        // ----------------------------------------------------------
        // 🔹 Total para calcular páginas
        // ----------------------------------------------------------
        $sqlTotal = "SELECT COUNT(*) " . $sqlBase;
        $stmt = $this->conn->prepare($sqlTotal);
        $stmt->execute($params);
        $totalRegistros = $stmt->fetchColumn();
        $totalPaginas   = ceil($totalRegistros / $porPagina);

        $sqlMovs = "
            SELECT 
                cm.id,
                cm.caja_id,
                cm.tipo,
                cm.origen,
                cm.valor,
                cm.fecha,
                cm.descripcion,
                c.nombre AS caja_nombre,
                CONCAT(u.first_name, ' ', u.last_name) AS user_nombre
            " . $sqlBase . "
            ORDER BY cm.fecha DESC
            LIMIT {$porPagina} OFFSET {$offset}
        ";

        $stmt = $this->conn->prepare($sqlMovs);
        $stmt->execute($params);
        $movimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ----------------------------------------------------------
        // 🔹 Catálogos para filtros
        // ----------------------------------------------------------
        $cajas = $this->fetchAll("SELECT id, nombre FROM cajas WHERE estado = 1 ORDER BY nombre ASC");


        // ----------------------------------------------------------
        // 🔹 Render
        // ----------------------------------------------------------
        ob_start();
        include '../modules/financiero/views/movimientos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // ----------------------------------------------------------
    // 🔹 Helper para SELECTs simples
    // ----------------------------------------------------------
    private function fetchAll($sql, $params = [])
    {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }






    public function detalle($id)
    {
        $empresa_id = $_SESSION['empresa_id'];

        $sql = "
                SELECT 
                    cm.*,
                    c.nombre AS caja_nombre,
                    CONCAT(u.first_name, ' ', u.last_name) AS user_nombre,

                    -- Datos del ingreso
                    fi.numero_recibo,
                    fi.valor AS ingreso_valor,
                    fi.observaciones AS ingreso_observaciones,
                    fi.fecha AS ingreso_fecha,
                    fi.motivo_ingreso_id,
                    fi.tipo_ingreso_id,
                    fi.matricula_id,

                    pm.nombre AS motivo_nombre,
                    pt.nombre AS tipo_nombre
                FROM caja_movimientos cm
                LEFT JOIN cajas c ON c.id = cm.caja_id
                LEFT JOIN users u ON u.id = cm.user_id
                LEFT JOIN financiero_ingresos fi ON fi.id = cm.ingreso_id
                LEFT JOIN param_motivos_financiero_ingresos pm ON pm.id = fi.motivo_ingreso_id
                LEFT JOIN param_tipos_financiero_ingresos pt ON pt.id = fi.tipo_ingreso_id
                WHERE cm.id = :id AND cm.empresa_id = :empresa
                LIMIT 1
            ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':empresa', $empresa_id, PDO::PARAM_INT);
        $stmt->execute();
        $mov = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$mov) {
            echo "Movimiento no encontrado.";
            return;
        }

        // Render
        ob_start();
        include '../modules/financiero/views/movimientos/detalle.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }











    
}
