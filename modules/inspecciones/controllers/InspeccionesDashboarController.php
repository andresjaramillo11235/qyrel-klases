<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../shared/utils/ImageHelper.php';

class InspeccionesDashboarController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        if (!isset($_SESSION)) { session_start(); }
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }

    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = (int) $_SESSION['empresa_id'];

        // --- Fechas (Bogotá) ---
        $tz = new DateTimeZone('-05:00');
        $hoy = (new DateTime('now', $tz))->format('Y-m-d');
        $inicioMes = (new DateTime('first day of this month', $tz))->format('Y-m-d');
        $finMes    = (new DateTime('last day of this month', $tz))->format('Y-m-d');

        // --- Conteos HOY ---
        $vehHoy = $this->fetchCount("
            SELECT COUNT(*) c
            FROM inspeccion_vehiculos
            WHERE empresa_id = :empresa_id AND DATE(fecha_hora) = :hoy
        ", [':empresa_id' => $empresa_id, ':hoy' => $hoy]);

        $motoHoy = $this->fetchCount("
            SELECT COUNT(*) c
            FROM inspeccion_motos
            WHERE empresa_id = :empresa_id AND DATE(fecha_hora) = :hoy
        ", [':empresa_id' => $empresa_id, ':hoy' => $hoy]);

        // --- Conteos MES ---
        $vehMes = $this->fetchCount("
            SELECT COUNT(*) c
            FROM inspeccion_vehiculos
            WHERE empresa_id = :empresa_id AND DATE(fecha_hora) BETWEEN :ini AND :fin
        ", [':empresa_id' => $empresa_id, ':ini' => $inicioMes, ':fin' => $finMes]);

        $motoMes = $this->fetchCount("
            SELECT COUNT(*) c
            FROM inspeccion_motos
            WHERE empresa_id = :empresa_id AND DATE(fecha_hora) BETWEEN :ini AND :fin
        ", [':empresa_id' => $empresa_id, ':ini' => $inicioMes, ':fin' => $finMes]);

        // --- Vehículos con clases HOY (uno por vehículo)
        $vehiculosConClasesHoy = $this->fetchColumnAll("
            SELECT DISTINCT cp.vehiculo_id
            FROM clases_practicas cp
            WHERE cp.empresa_id = :empresa_id
              AND cp.vehiculo_id IS NOT NULL
              AND cp.fecha = :hoy
        ", [':empresa_id' => $empresa_id, ':hoy' => $hoy]);

        // --- Vehículos ya inspeccionados HOY (en cualquiera de las 2 tablas)
        $vehiculosInspeccionadosHoy = $this->fetchColumnAll("
            SELECT id_vehiculo FROM inspeccion_vehiculos
             WHERE empresa_id = :empresa_id AND DATE(fecha_hora) = :hoy
            UNION
            SELECT id_vehiculo FROM inspeccion_motos
             WHERE empresa_id = :empresa_id AND DATE(fecha_hora) = :hoy
        ", [':empresa_id' => $empresa_id, ':hoy' => $hoy]);

        $faltantesIds = array_values(array_diff($vehiculosConClasesHoy ?: [], $vehiculosInspeccionadosHoy ?: []));
        $faltantes = [];

        if (!empty($faltantesIds)) {
            $in = implode(',', array_fill(0, count($faltantesIds), '?'));
            $sql = "
                SELECT v.id, v.placa, v.foto, v.tipo_vehiculo_id, v.modelo
                FROM vehiculos v
                WHERE v.empresa_id = ? AND v.id IN ($in)
                ORDER BY v.placa ASC
            ";
            $stmt = $this->conn->prepare($sql);
            $params = array_merge([$empresa_id], $faltantesIds);
            $stmt->execute($params);
            $faltantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        // --- Armar data para la vista ---
        $data = [
            'rango' => ['hoy' => $hoy, 'mes' => ['inicio' => $inicioMes, 'fin' => $finMes]],
            'hoy'   => [
                'vehiculos' => (int) $vehHoy,
                'motos'     => (int) $motoHoy,
                'total'     => (int) $vehHoy + (int) $motoHoy,
                'faltantes' => ['cantidad' => count($faltantes), 'vehiculos' => $faltantes],
            ],
            'mes'   => [
                'vehiculos' => (int) $vehMes,
                'motos'     => (int) $motoMes,
                'total'     => (int) $vehMes + (int) $motoMes,
            ],
        ];

        // --- Render ---
        $dashboard = $data; // alias corto para la vista
        ob_start();
        include '../modules/inspecciones/views/index_dashboard.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // ===== Helpers =====
    private function fetchCount(string $sql, array $params): int
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return isset($row['c']) ? (int)$row['c'] : 0;
    }

    private function fetchColumnAll(string $sql, array $params): array
    {
        $stmt = $this->conn->prepare($sql);
        foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: [];
    }
}
