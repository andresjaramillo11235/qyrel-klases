<?php

require_once '../config/DatabaseConfig.php';

class TemasController
{
    private $conn;

    public function __construct()
    {
        $dbConfig = new \DatabaseConfig();
        $this->conn = $dbConfig->getConnection();
    }

    // Listar temas de un programa específico
    public function index($programaId)
    {
        $query = "SELECT id, nombre_clase, numero_horas, orden 
                FROM clases_programas 
                WHERE programa_id = :programa_id 
                ORDER BY orden ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programaId, \PDO::PARAM_INT);
        $stmt->execute();
        $temas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/programas/views/temas/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function listadoTemasByPrograma($programaId)
    {
        try {
            $query = "SELECT id, nombre_clase FROM clases_programas WHERE programa_id = :programa_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':programa_id', $programaId, \PDO::PARAM_INT);
            $stmt->execute();
            $temas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Enviar los detalles de la clase en formato JSON
            header('Content-Type: application/json');

            // Codifica los detalles de la clase en JSON de una forma segura para evitar problemas de codificación
            $jsonResponse = json_encode($temas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);

            if ($jsonResponse === false) {
                throw new \Exception(json_last_error_msg());
            }

            echo $jsonResponse;
        } catch (\Exception $e) {
            // Manejo de errores
            http_response_code(500);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }





    // ----------------------------------------------------------
    // 🔹 Listar temas teóricos por programa
    // ----------------------------------------------------------
    public function listadoTemasTeoricosByPrograma($programaId)
    {
        try {
            $query = "SELECT id, nombre 
                  FROM clases_teoricas_temas 
                  WHERE clase_teorica_programa_id = :programa_id
                  ORDER BY id ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':programa_id', $programaId, PDO::PARAM_INT);
            $stmt->execute();
            $temas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Enviar respuesta JSON segura
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($temas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }







    public function store()
    {
        $routes = include '../config/Routes.php';

        $programaId   = $_POST['programa_id'];
        $nombreClase  = trim($_POST['nombre_clase']);
        $numeroHoras  = (int)$_POST['numero_horas'];
        $orden        = (int)$_POST['orden'];

        if (!$programaId || !$nombreClase || $numeroHoras <= 0 || $orden <= 0) {
            $_SESSION['error_message'] = "Datos inválidos. Por favor, verifique el formulario.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $query = "INSERT INTO clases_programas (programa_id, nombre_clase, numero_horas, orden)
              VALUES (:programa_id, :nombre_clase, :numero_horas, :orden)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programaId);
        $stmt->bindParam(':nombre_clase', $nombreClase);
        $stmt->bindParam(':numero_horas', $numeroHoras);
        $stmt->bindParam(':orden', $orden);

        try {
            $stmt->execute();
            $_SESSION['success_message'] = "Tema creado exitosamente.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error al crear el tema: " . $e->getMessage();
        }

        header('Location: ' . $routes['programas_temas_index'] . $programaId);
        exit;
    }


    // Editar un tema
    public function edit($id)
    {
        // Obtener el tema por su ID
        $query = "SELECT * FROM clases_programas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $tema = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$tema) {
            $_SESSION['error_message'] = "Tema no encontrado.";
            header('Location: /temas/index');
            exit;
        }

        // Cargar la vista de edición
        ob_start();
        include '../modules/programas/views/temas/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function update()
    {
        $routes = include '../config/Routes.php';

        $id = $_POST['id'];
        $nombreClase = trim($_POST['nombre_clase']);
        $numeroHoras = (int)$_POST['numero_horas'];
        $orden = (int)$_POST['orden'];

        if (!$id || !$nombreClase || $numeroHoras <= 0 || $orden <= 0) {
            $_SESSION['error_message'] = "Datos inválidos. Por favor, verifique el formulario.";
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        $query = "UPDATE clases_programas 
              SET nombre_clase = :nombre_clase, 
                  numero_horas = :numero_horas, 
                  orden = :orden 
              WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':nombre_clase', $nombreClase);
        $stmt->bindParam(':numero_horas', $numeroHoras);
        $stmt->bindParam(':orden', $orden);

        try {
            $stmt->execute();
            $_SESSION['success_message'] = "Tema actualizado exitosamente.";
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error al actualizar el tema: " . $e->getMessage();
        }

        header('Location: ' . $routes['programas_temas_index'] . $_POST['programa_id']);
        exit;
    }


    // Eliminar un tema
    public function destroy($id)
    {
        $programaId = $_POST['programa_id'];
        $query = "DELETE FROM clases_programas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        $_SESSION['success_message'] = 'Tema eliminado exitosamente.';
        header("Location: /programas/temas/$programaId");
        exit;
    }

    /**
     * Obtiene el nombre de una clase a partir de su ID.
     *
     * @param int $claseId ID de la clase.
     * @return string|null Nombre de la clase o null si no se encuentra.
     */
    public function getClaseById($claseId)
    {
        // Consulta para obtener toda la información de la clase
        $query = "
                SELECT *
                FROM clases_programas
                WHERE id = :clase_id
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':clase_id', $claseId, PDO::PARAM_INT);
        $stmt->execute();

        // Retornar la información completa de la clase o null si no se encuentra
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
