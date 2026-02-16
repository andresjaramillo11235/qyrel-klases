<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class ConsultaRapidaController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    // Lista de convenios
    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_consulta_rapida')) {
            header('Location: /permission-denied/');
            exit;
        }

        ob_start();
        include '../modules/consulta_rapida/view/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function buscarEstudiantesConsultaRapida()
    {
        if (!isset($_POST['termino']) || trim($_POST['termino']) === '') {
            http_response_code(400);
            echo json_encode(['error' => 'Término vacío']);
            return;
        }

        $empresaId = $_SESSION['empresa_id'];
        $termino = strtoupper(trim($_POST['termino']));
        $palabras = preg_split('/\s+/', $termino);
        $numPalabras = count($palabras);

        $params = [];

        if (is_numeric($termino)) {
            $query = "SELECT e.id, e.nombres, e.apellidos, e.numero_documento, e.foto 
                  FROM estudiantes e 
                  WHERE e.numero_documento = :documento 
                  AND e.empresa_id = :empresa_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':documento', $termino);
            $stmt->bindParam(':empresa_id', $empresaId);
            $stmt->execute();
            $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Armar combinaciones
            $query = "SELECT e.id, e.nombres, e.apellidos, e.numero_documento, e.foto 
                  FROM estudiantes e 
                  WHERE (";

            $condiciones = [];

            if ($numPalabras === 1) {
                $condiciones[] = "e.nombres LIKE :word OR e.apellidos LIKE :word";
                $params[':word'] = '%' . $palabras[0] . '%';
            }

            if ($numPalabras === 2) {
                $condiciones[] = "(e.nombres LIKE :n1 AND e.apellidos LIKE :a1)";
                $params[':n1'] = '%' . $palabras[0] . '%';
                $params[':a1'] = '%' . $palabras[1] . '%';

                // Otra posible combinación: ambas palabras en nombres
                $condiciones[] = "e.nombres LIKE :n_completo";
                $params[':n_completo'] = '%' . implode(' ', $palabras) . '%';
            }

            if ($numPalabras >= 3) {
                $nombre = implode(' ', array_slice($palabras, 0, 2)); // mateo pepe
                $apellido = implode(' ', array_slice($palabras, 2)); // rodriguez rivera

                $condiciones[] = "(e.nombres LIKE :n2 AND e.apellidos LIKE :a2)";
                $params[':n2'] = '%' . $nombre . '%';
                $params[':a2'] = '%' . $apellido . '%';
            }

            $query .= implode(' OR ', $condiciones) . ") AND e.empresa_id = :empresa_id";

            $stmt = $this->conn->prepare($query);
            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val);
            }
            $stmt->bindParam(':empresa_id', $empresaId);
            $stmt->execute();
            $estudiantes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($estudiantes) {
            echo json_encode($estudiantes);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Estudiante no encontrado']);
        }
    }

}
