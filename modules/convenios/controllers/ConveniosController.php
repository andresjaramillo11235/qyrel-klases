<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class ConveniosController
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

        if (!$permissionController->hasPermission($currentUserId, 'view_convenios')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {

            $query = "
          SELECT 
              c.id, 
              c.nombre, 
              c.documento, 
              c.telefono, 
              ptc.nombre AS tipo_convenio 
          FROM 
              convenios c
          LEFT JOIN 
              param_tipos_convenio ptc 
          ON 
              c.tipo_convenio = ptc.id
          WHERE 
              c.empresa_id = :empresa_id
      ";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
            $stmt->execute();

            $convenios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Obtener las categorías
            $categorias = $this->getCategorias();

            ob_start();
            include '../modules/convenios/views/index.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        } catch (Exception $e) {
            echo "Error al obtener la lista de convenios: " . $e->getMessage();
        }
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_convenios')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            // Obtener tipos de convenios para el select
            $query = "SELECT * FROM param_tipos_convenio";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $tiposConvenio = $stmt->fetchAll(PDO::FETCH_ASSOC);

            ob_start();
            include '../modules/convenios/views/create.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        } catch (Exception $e) {
            echo "Error al cargar el formulario de creación: " . $e->getMessage();
        }
    }

    // Método para almacenar un nuevo convenio
    public function store()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_convenios')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            // Obtener el ID de la empresa desde la sesión
            $empresa_id = $_SESSION['empresa_id'];

            // Capturar los datos del formulario en variables
            $nombre = $_POST['nombre'];
            $documento = $_POST['documento'];
            $telefono = $_POST['telefono'];
            $tipo_convenio = $_POST['tipo_convenio'];

            // Consulta SQL con la inclusión del campo empresa_id
            $query = "INSERT INTO convenios (nombre, documento, telefono, tipo_convenio, empresa_id) 
                  VALUES (:nombre, :documento, :telefono, :tipo_convenio, :empresa_id)";

            $stmt = $this->conn->prepare($query);

            // Asignación de los valores de las variables a los placeholders de la consulta
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':documento', $documento);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':tipo_convenio', $tipo_convenio);
            $stmt->bindParam(':empresa_id', $empresa_id);

            // Ejecutar la consulta
            $stmt->execute();

            // Redirigir después de la creación
            $_SESSION['convenio_creado'] = 'Convenio creado exitosamente';
            header("Location: /convenios/");
            exit();
        } catch (Exception $e) {
            echo "Error al crear el convenio: " . $e->getMessage();
        }
    }

    // Método para mostrar el formulario de edición de un convenio
    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_convenios')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            // Obtener los datos del convenio
            $query = "SELECT * FROM convenios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $convenio = $stmt->fetch(PDO::FETCH_ASSOC);

            // Obtener tipos de convenios para el select
            $query = "SELECT * FROM param_tipos_convenio";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            $tiposConvenio = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Cargar la vista del formulario de edición
            ob_start();
            include '../modules/convenios/views/edit.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        } catch (Exception $e) {
            echo "Error al cargar el formulario de edición: " . $e->getMessage();
        }
    }

    // Método para actualizar un convenio
    public function update($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_convenios')) {
            header('Location: /permission-denied/');
            exit;
        }

        try {
            // Obtener los valores de los campos desde el formulario
            $nombre = $_POST['nombre'];
            $documento = $_POST['documento'];
            $telefono = $_POST['telefono'];
            $tipo_convenio = $_POST['tipo_convenio'];

            // Consulta para actualizar el convenio
            $query = "UPDATE convenios SET nombre = :nombre, documento = :documento, 
                  telefono = :telefono, tipo_convenio = :tipo_convenio 
                  WHERE id = :id";

            $stmt = $this->conn->prepare($query);

            // Vincular los parámetros con las variables
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':documento', $documento);
            $stmt->bindParam(':telefono', $telefono);
            $stmt->bindParam(':tipo_convenio', $tipo_convenio);
            $stmt->bindParam(':id', $id);

            // Ejecutar la consulta
            $stmt->execute();

            // Redirigir después de la actualización
            $_SESSION['convenio_modificado'] = 'Convenio actualizado exitosamente';
            header("Location: /convenios/");
            exit();
        } catch (Exception $e) {
            echo "Error al actualizar el convenio: " . $e->getMessage();
        }
    }

    // Método para eliminar un convenio
    public function delete($id)
    {
        try {
            $query = "DELETE FROM convenios WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            // Redirigir después de la eliminación
            $_SESSION['message'] = 'Convenio eliminado exitosamente';
            header("Location: /convenios/");
            exit();
        } catch (Exception $e) {
            echo "Error al eliminar el convenio: " . $e->getMessage();
        }
    }

    public function guardarValores()
    {
        $convenio_id = $_POST['convenio_id'];

        // Validar que $convenio_id no esté vacío
        if (empty($convenio_id)) {
            echo "Error: convenio_id está vacío.";
            return;
        }

        $programa_id = $_POST['programa']; // Cambiado a 'programa'
        $valor = $_POST['valor'];

        try {
            // Actualización del campo 'programa' en lugar de 'categoria'
            $query = "INSERT INTO convenios_valores (convenio_id, programa, valor) VALUES (:convenio_id, :programa_id, :valor)";
            $stmt = $this->conn->prepare($query);

            // Asociar parámetros
            $stmt->bindParam(':convenio_id', $convenio_id);
            $stmt->bindParam(':programa_id', $programa_id);
            $stmt->bindParam(':valor', $valor);

            $stmt->execute();

            // Mensaje de éxito
            $_SESSION['valor_creado'] = 'Valores de convenio guardados exitosamente.';
            header("Location: /convenios-valores/" . $convenio_id);
            exit();
        } catch (Exception $e) {
            // Manejo de errores
            echo "Error al guardar los valores del convenio: " . $e->getMessage();
        }
    }


    public function gestionarValores($convenio_id)
    {
        try {
            // Obtener el convenio
            $queryConvenio = "SELECT * FROM convenios WHERE id = :convenio_id";
            $stmtConvenio = $this->conn->prepare($queryConvenio);
            $stmtConvenio->bindParam(':convenio_id', $convenio_id);
            $stmtConvenio->execute();
            $convenio = $stmtConvenio->fetch(PDO::FETCH_ASSOC);

            if (!$convenio) {
                throw new Exception("Convenio no encontrado.");
            }

            $queryValores = "
                SELECT 
                    cv.*, 
                    p.nombre AS programa_nombre
                FROM 
                    convenios_valores cv
                LEFT JOIN 
                    programas p
                ON 
                    cv.programa = p.id
                WHERE 
                    cv.convenio_id = :convenio_id
            ";

            $stmtValores = $this->conn->prepare($queryValores);
            $stmtValores->bindParam(':convenio_id', $convenio_id);
            $stmtValores->execute();
            $valores = $stmtValores->fetchAll(PDO::FETCH_ASSOC);

            // Obtener las categorías disponibles
            $programas = $this->getProgramas();

            ob_start();
            include '../modules/convenios/views/valores.php';
            $content = ob_get_clean();
            include '../shared/views/layout.php';
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function updateValor()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'];
            $valor = $data['valor'];

            $query = "UPDATE convenios_valores SET valor = :valor WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':valor', $valor);
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
        }
    }

    private function getCategorias()
    {
        $query = "SELECT * FROM param_categorias_conduccion";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgramas()
    {
        $empresaId = $_SESSION['empresa_id']; // Asegúrate de tener el `empresa_id` en la sesión

        $query = "SELECT * FROM programas WHERE empresa_id = :empresa_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT); // Asociar el parámetro
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getValorConvenioPorPrograma($programaId, $convenioId)
    {
        if (!$programaId || !$convenioId) {
            echo json_encode(['success' => false, 'error' => 'Parámetros incompletos.']);
            return;
        }

        $query = "
                SELECT valor 
                FROM convenios_valores 
                WHERE programa = :programa_id AND convenio_id = :convenio_id
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programaId, PDO::PARAM_INT);
        $stmt->bindParam(':convenio_id', $convenioId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            echo json_encode(['success' => true, 'valor' => $result['valor']]);
        } else {
            echo json_encode(['success' => false, 'error' => 'No se encontró un valor para esta combinación.']);
        }
    }
}
