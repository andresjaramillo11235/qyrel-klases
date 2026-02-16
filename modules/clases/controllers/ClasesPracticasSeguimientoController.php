<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/UserUtils.php';

class ClasesPracticasSeguimientoController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }


    public function mapaSeguimiento($claseId)
    {
        $currentUserId = $_SESSION['user_id'];

        // Consulta de la clase práctica por ID
        $queryClase = "
            SELECT 
                c.id, c.nombre, c.fecha, c.hora_inicio, c.hora_fin, 
                c.instructor_id, c.vehiculo_id, c.estado, 
                i.nombres AS instructor_nombre, i.apellidos AS instructor_apellidos,
                v.placa AS vehiculo_placa
            FROM clases_practicas c
            LEFT JOIN instructores i ON c.instructor_id = i.id
            LEFT JOIN vehiculos v ON c.vehiculo_id = v.id
            WHERE c.id = :clase_id
        ";

        $stmtClase = $this->conn->prepare($queryClase);
        $stmtClase->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
        $stmtClase->execute();
        $clase = $stmtClase->fetch(PDO::FETCH_ASSOC);

        // Verificar si se obtuvo la clase
        if (!$clase) {
            header('Location: /not-found');
            exit;
        }

        // Preparar los valores de hora de inicio y fin en variables separadas
        $horaInicioCompleta = $clase['fecha'] . ' ' . $clase['hora_inicio'];
        $horaFinCompleta = $clase['fecha'] . ' ' . $clase['hora_fin'];

        // Consultar las posiciones del vehículo en el intervalo de la clase
        $queryPosiciones = "
            SELECT latitude AS lat, longitude AS lng, lastUpdate
            FROM posiciones_temporales
            WHERE vehiculo_id = :vehiculo_id
            AND lastUpdate BETWEEN :hora_inicio AND :hora_fin
            ORDER BY lastUpdate ASC
        ";

        $stmtPosiciones = $this->conn->prepare($queryPosiciones);
        $stmtPosiciones->bindParam(':vehiculo_id', $clase['vehiculo_id'], PDO::PARAM_INT);
        $stmtPosiciones->bindParam(':hora_inicio', $horaInicioCompleta, PDO::PARAM_STR);
        $stmtPosiciones->bindParam(':hora_fin', $horaFinCompleta, PDO::PARAM_STR);
        $stmtPosiciones->execute();
        $posiciones = $stmtPosiciones->fetchAll(PDO::FETCH_ASSOC);

        // Cargar la vista del mapa de seguimiento con la clase y las posiciones
        ob_start();
        include '../modules/clases/views/clases_practicas/mapa_seguimiento.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }





    // Método para obtener la última posición del vehículo
    public function getUltimaPosicion($vehiculoId)
    {
        try {
            $query = "
                SELECT 
                    latitude AS lat, 
                    longitude AS lng, 
                    lastUpdate AS fecha_hora,
                    speed,
                    course,
                    status
                FROM posiciones_temporales 
                WHERE vehiculo_id = :vehiculo_id 
                ORDER BY lastUpdate DESC 
                LIMIT 1";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':vehiculo_id', $vehiculoId, PDO::PARAM_INT);
            $stmt->execute();

            $posicion = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($posicion) {
                header('Content-Type: application/json');
                echo json_encode($posicion);
            } else {
                echo json_encode(['error' => 'No se encontró la posición.']);
            }
        } catch (Exception $e) {
            error_log("Error al obtener la última posición: " . $e->getMessage());
            echo json_encode(['error' => 'Error al obtener la posición']);
        }
    }
}
