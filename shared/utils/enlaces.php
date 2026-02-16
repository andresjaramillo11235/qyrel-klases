<?php

/**
 * Utils de enlaces
 * Tabla: enlaces (id INT AI, url VARCHAR(255), id_enlace VARCHAR(100), estado SMALLINT/TINYINT)
 *
 * Uso:
 *   require_once '../shared/utils/enlaces.php';
 *   $activos = enlaces_all();                         // solo activos
 *   $todos   = enlaces_all(false);                    // todos
 *   $map     = enlaces_map();                         // [id_enlace => fila]
 *   $url     = enlace_url('manual_usuario', '#');     // URL o '#'
 */

/**
 * require_once '../shared/utils/enlaces.php';
 * 1) Traer todos los enlaces activos
 *$enlaces = enlaces_all(); // [ [id, url, id_enlace, estado], ... ]
 * 2) Mapa por id_enlace (más cómodo)
 *$links = enlaces_map();   // [ 'manual_usuario' => [..], 'whatsapp' => [..], ... ]
 * 3) Obtener una URL específica
 *$manualUrl = enlace_url('manual_usuario', '#');
 *<a href="<?= htmlspecialchars($manualUrl) ?>" target="_blank">Manual de usuario</a>
 */



if (!function_exists('enlaces_all')) {
    function enlaces_all(bool $soloActivos = true): array
    {
        try {
            // Reutiliza un pequeño caché por request
            static $cacheActivos = null;
            static $cacheTodos   = null;

            if ($soloActivos && $cacheActivos !== null) return $cacheActivos;
            if (!$soloActivos && $cacheTodos !== null)   return $cacheTodos;

            // Conexión usando tu config del proyecto
            require_once '../config/DatabaseConfig.php';
            $db  = new DatabaseConfig();
            $pdo = $db->getConnection();

            $sql = "SELECT id, url, id_enlace, estado FROM enlaces";
            if ($soloActivos) $sql .= " WHERE estado = 1";
            $sql .= " ORDER BY id_enlace ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

            if ($soloActivos) $cacheActivos = $rows;
            else              $cacheTodos   = $rows;

            return $rows;
        } catch (Throwable $e) {
            error_log('[enlaces_all] ' . $e->getMessage());
            return [];
        }
    }
}

if (!function_exists('enlace_by_id_enlace')) {
    function enlace_by_id_enlace(string $idEnlace, bool $soloActivos = true): ?array
    {
        try {
            require_once '../config/DatabaseConfig.php';
            $db  = new DatabaseConfig();
            $pdo = $db->getConnection();

            $sql = "SELECT id, url, id_enlace, estado
                    FROM enlaces
                    WHERE id_enlace = :id_enlace";
            if ($soloActivos) $sql .= " AND estado = 1";
            $sql .= " LIMIT 1";

            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id_enlace', $idEnlace);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            return $row ?: null;
        } catch (Throwable $e) {
            error_log('[enlace_by_id_enlace] ' . $e->getMessage());
            return null;
        }
    }
}

if (!function_exists('enlaces_map')) {
    function enlaces_map(bool $soloActivos = true): array
    {
        $out = [];
        foreach (enlaces_all($soloActivos) as $row) {
            // id_enlace es el identificador lógico para consumir desde vistas
            $out[(string)$row['id_enlace']] = $row;
        }
        return $out;
    }
}

if (!function_exists('enlace_url')) {
    function enlace_url(string $idEnlace, string $default = '#', bool $soloActivos = true): string
    {
        $row = enlace_by_id_enlace($idEnlace, $soloActivos);
        return $row && !empty($row['url']) ? (string)$row['url'] : $default;
    }
}
