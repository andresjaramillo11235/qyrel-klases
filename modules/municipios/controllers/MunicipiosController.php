<?php
require_once '../config/DatabaseConfig.php';


class MunicipiosController
{

    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    /**
     * Obtiene los municipios de un departamento.
     *
     * @return void
     */
    public function getListadoMunicipiosByDepartamento($departamentoId)
    {
        try {
            // Consultar municipios por departamento
            $query = "SELECT * FROM municipios WHERE departamento_id = :departamento_id ORDER BY municipio ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':departamento_id', $departamentoId, PDO::PARAM_INT);
            $stmt->execute();

            $municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Retornar municipios en formato JSON
            header('Content-Type: application/json');
            echo json_encode($municipios);
        } catch (Exception $e) {
            // Manejar errores
            http_response_code(500);
            echo json_encode(['error' => 'Ocurrió un error al obtener los municipios.']);
        }
    }
}
