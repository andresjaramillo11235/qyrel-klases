<?php

// ----------------------------------------------------------
// 🔹 Clase centralizada de Labels (por empresa)
// ----------------------------------------------------------
class LabelHelper
{
    private static $labels = [];

    public static function load($conn, $empresa_id)
    {
        $stmt = $conn->prepare("
            SELECT 
                lm.clave,
                COALESCE(l.valor, lm.valor_default) AS valor
            FROM labels_master lm
            LEFT JOIN labels l 
                ON l.clave = lm.clave 
                AND l.empresa_id = ?
        ");

        $stmt->execute([$empresa_id]);

        self::$labels = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function get($clave)
    {
        return self::$labels[$clave] ?? $clave;
    }
}
