<?php

require_once '../config/DatabaseConfig.php';

class InformesController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function informeSietIndex()
    {
        // Traer los programas activos para el select
        $query = "SELECT id, nombre FROM programas ORDER BY nombre ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/informes/views/index_siet.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function informeSietResultado()
    {
        $programaId = $_POST['programa_id'];
        $desde = $_POST['fecha_desde'];
        $hasta = $_POST['fecha_hasta'];

$query = "SELECT 
            m.id AS numero_acta,
            e.numero_documento,
            e.nombres,
            e.apellidos,
            m.fecha_certificacion AS fecha_obtencion,
            p.nombre AS programa_nombre,
            m.fecha_inscripcion
          FROM matriculas m
          INNER JOIN matricula_programas mp ON mp.matricula_id = m.id
          LEFT JOIN estudiantes e ON e.id = m.estudiante_id
          LEFT JOIN programas p ON p.id = mp.programa_id
          WHERE p.id = :programa_id
            AND m.fecha_inscripcion BETWEEN :desde AND :hasta
          ORDER BY m.fecha_inscripcion ASC";


        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':programa_id', $programaId);
        $stmt->bindValue(':desde', $desde);
        $stmt->bindValue(':hasta', $hasta);
        $stmt->execute();
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/informes/views/resultado_siet.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
