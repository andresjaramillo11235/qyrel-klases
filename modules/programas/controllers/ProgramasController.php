<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class ProgramasController
{
    private $conn;
    private $userUtils;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
        $this->userUtils = new UserUtils();
    }

    public function index()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_programas')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        // Filter by empresa_id if user is not super admin
        $query = "SELECT p.*, 
            e.nombre AS empresa_nombre, 
            c.nombre AS categoria_nombre,
            v.nombre AS vehiculo_nombre
            FROM programas p
            LEFT JOIN empresas e ON p.empresa_id = e.id
            LEFT JOIN categorias_licencia c ON p.categoria = c.id
            LEFT JOIN param_tipo_vehiculo v ON p.tipo_vehiculo_id = v.id
            WHERE p.empresa_id = :empresa_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);

        $stmt->execute();
        $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/programas/views/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $userUtils = new UserUtils();

        if (!$permissionController->hasPermission($currentUserId, 'create_programas')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $empresas = $this->getEmpresas();
        $categorias = $this->getCategorias();
        $tiposVehiculo = $this->getTiposVehiculo();

        ob_start();
        include '../modules/programas/views/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $routes = include '../config/Routes.php';

        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresaId = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'create_programas')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        // Validar y limpiar datos del formulario
        $nombre = $_POST['nombre'] ?? '';
        $descripcion = $_POST['descripcion'] ?? '';
        $valor_total = 0;
        $valor_hora = 0;
        $valor_texto = null;
        $horas_practicas = intval($_POST['horas_practicas'] ?? 0);
        $horas_teoricas = intval($_POST['horas_teoricas'] ?? 0);
        $categoria = intval($_POST['categoria'] ?? null);
        $siet = $_POST['siet'] ?? null;
        $estado = '1';
        $tipo_servicio = $_POST['tipo_servicio'] ?? '';
        $tipo_vehiculo_id = intval($_POST['tipo_vehiculo_id'] ?? null); // Nuevo campo agregado

        $query = "
            INSERT INTO programas (
                nombre,
                descripcion,
                valor_total,
                valor_hora,
                valor_texto,
                horas_practicas,
                horas_teoricas,
                categoria,
                siet,
                estado,
                tipo_servicio,
                empresa_id,
                tipo_vehiculo_id
            ) VALUES (
                :nombre,
                :descripcion,
                :valor_total,
                :valor_hora,
                :valor_texto,
                :horas_practicas,
                :horas_teoricas,
                :categoria,
                :siet,
                :estado,
                :tipo_servicio,
                :empresa_id,
                :tipo_vehiculo_id
            )
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':valor_total', $valor_total, PDO::PARAM_INT);
        $stmt->bindParam(':valor_hora', $valor_hora, PDO::PARAM_INT);
        $stmt->bindParam(':valor_texto', $valor_texto);
        $stmt->bindParam(':horas_practicas', $horas_practicas, PDO::PARAM_INT);
        $stmt->bindParam(':horas_teoricas', $horas_teoricas, PDO::PARAM_INT);
        $stmt->bindParam(':categoria', $categoria, PDO::PARAM_INT);
        $stmt->bindParam(':siet', $siet);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':tipo_servicio', $tipo_servicio);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->bindParam(':tipo_vehiculo_id', $tipo_vehiculo_id, PDO::PARAM_INT); // Nuevo parámetro

        if ($stmt->execute()) {

            // Registro auditoría
            $Id = $this->conn->lastInsertId();
            $descripcion = "Se creó el programa: " . $nombre . " con ID " . $Id;
            $auditoriaController = new AuditoriaController();
            $auditoriaController->registrar($currentUserId, 'Crear', 'Programas', $descripcion, $empresaId);

            $_SESSION['success_message'] = 'Programa creado correctamente.';
            header('Location: ' . $routes['programas_index']);
        } else {
            $_SESSION['error_message'] = 'Error al crear el programa.';
            header('Location: ' . $routes['programas_index']);
        }
        exit;
    }

    public function edit($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_programas')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        $query = "SELECT * FROM programas WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $programa = $stmt->fetch(PDO::FETCH_ASSOC);

        $empresas = $this->getEmpresas();
        $categorias = $this->getCategorias();
        $tiposVehiculo = $this->getTiposVehiculo();

        ob_start();
        include '../modules/programas/views/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }


    public function update($id)
    {
        $routes = include '../config/Routes.php';
        $empresaId = $_SESSION['empresa_id'];
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'edit_programas')) {
            echo "No tienes permiso para realizar esta acción.";
            return;
        }

        // Obtener los valores actuales del programa antes de la actualización
        $queryOld = "SELECT * FROM programas WHERE id = :id";
        $stmtOld = $this->conn->prepare($queryOld);
        $stmtOld->bindParam(':id', $id, PDO::PARAM_INT);
        $stmtOld->execute();
        $programaAntiguo = $stmtOld->fetch(PDO::FETCH_ASSOC);

        $estadoAntes = $programaAntiguo['estado'] == 1 ? 'ACTIVO' : 'INACTIVO';
        $estadoDespues = $_POST['estado'] == 1 ? 'ACTIVO' : 'INACTIVO';

        $query = "
                UPDATE programas 
                SET 
                    nombre = :nombre,
                    descripcion = :descripcion,
                    horas_practicas = :horas_practicas,
                    horas_teoricas = :horas_teoricas,
                    categoria = :categoria,
                    tipo_servicio = :tipo_servicio,
                    tipo_vehiculo_id = :tipo_vehiculo_id,
                    estado = :estado
                WHERE 
                    id = :id
            ";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':nombre', $_POST['nombre'], PDO::PARAM_STR);
        $stmt->bindParam(':descripcion', $_POST['descripcion'], PDO::PARAM_STR);
        $stmt->bindParam(':horas_practicas', $_POST['horas_practicas'], PDO::PARAM_INT);
        $stmt->bindParam(':horas_teoricas', $_POST['horas_teoricas'], PDO::PARAM_INT);
        $stmt->bindParam(':categoria', $_POST['categoria'], PDO::PARAM_INT);
        $stmt->bindParam(':tipo_servicio', $_POST['tipo_servicio'], PDO::PARAM_STR);
        $stmt->bindParam(':tipo_vehiculo_id', $_POST['tipo_vehiculo_id'], PDO::PARAM_INT);
        $stmt->bindParam(':estado', $_POST['estado'], PDO::PARAM_INT);

        if ($stmt->execute()) {

            // 🔎 Registrar auditoría
            $descripcion = "Se modificó el programa con ID $id. 
                Antes: 
                    Nombre: {$programaAntiguo['nombre']}, 
                    Descripción: {$programaAntiguo['descripcion']}, 
                    Horas Prácticas: {$programaAntiguo['horas_practicas']}, 
                    Horas Teóricas: {$programaAntiguo['horas_teoricas']}, 
                    Categoría: {$programaAntiguo['categoria']}, 
                    Tipo Servicio: {$programaAntiguo['tipo_servicio']}, 
                    Tipo Vehículo: {$programaAntiguo['tipo_vehiculo_id']}, 
                    Estado: {$estadoAntes} 
                Después: 
                    Nombre: {$_POST['nombre']}, 
                    Descripción: {$_POST['descripcion']}, 
                    Horas Prácticas: {$_POST['horas_practicas']}, 
                    Horas Teóricas: {$_POST['horas_teoricas']}, 
                    Categoría: {$_POST['categoria']}, 
                    Tipo Servicio: {$_POST['tipo_servicio']}, 
                    Tipo Vehículo: {$_POST['tipo_vehiculo_id']}, 
                    Estado: {$estadoDespues}";

            // Registrar en la auditoría
            $auditoriaController = new AuditoriaController();
            $auditoriaController->registrar($currentUserId, 'Modificar', 'Programas', $descripcion, $empresaId);

            $_SESSION['success_message'] = 'Programa modificado correctamente.';
            header('Location: ' . $routes['programas_index']);
        } else {
            $_SESSION['error_message'] = 'Error al modificar el programa.';
            header('Location: ' . $routes['programas_index']);
        }
    }

    public function delete($id)
    {
        $routes = include '../config/Routes.php';
        $empresaId = $_SESSION['empresa_id'];
        $currentUserId = $_SESSION['user_id']; // Usuario que realiza la acción

        try {
            // 1️⃣ Obtener los datos del programa antes de eliminarlo (para la auditoría)
            $queryPrograma = "SELECT * FROM programas WHERE id = :id";
            $stmtPrograma = $this->conn->prepare($queryPrograma);
            $stmtPrograma->bindParam(':id', $id);
            $stmtPrograma->execute();
            $programa = $stmtPrograma->fetch(PDO::FETCH_ASSOC);

            if (!$programa) {
                $_SESSION['error_message'] = "El programa no existe.";
                header("Location: " . $routes['programas_index']);
                exit;
            }

            // 2️⃣ Verificar si el programa está en una matrícula antes de eliminar
            $queryCheck = "SELECT COUNT(*) as total FROM matricula_programas WHERE programa_id = :programa_id";
            $stmtCheck = $this->conn->prepare($queryCheck);
            $stmtCheck->bindParam(':programa_id', $id);
            $stmtCheck->execute();
            $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if ($result['total'] > 0) {
                $_SESSION['error_message'] = "No se puede eliminar el programa porque está asociado a una matrícula.";
                header("Location: " . $routes['programas_index']);
                exit;
            }

            // 3️⃣ Eliminar las clases asociadas al programa antes de eliminarlo
            $queryDeleteClases = "DELETE FROM clases_programas WHERE programa_id = :programa_id";
            $stmtDeleteClases = $this->conn->prepare($queryDeleteClases);
            $stmtDeleteClases->bindParam(':programa_id', $id);
            $stmtDeleteClases->execute();

            // 4️⃣ Eliminar el programa de la base de datos
            $queryDeletePrograma = "DELETE FROM programas WHERE id = :id";
            $stmtDeletePrograma = $this->conn->prepare($queryDeletePrograma);
            $stmtDeletePrograma->bindParam(':id', $id);

            if ($stmtDeletePrograma->execute()) {
                // ✅ Registrar en la auditoría la eliminación
                $descripcion = "Se eliminó el programa con ID {$id}:
                    - Nombre: {$programa['nombre']}
                    - Descripción: {$programa['descripcion']}
                    - Horas Prácticas: {$programa['horas_practicas']}
                    - Horas Teóricas: {$programa['horas_teoricas']}
                    - Categoría: {$programa['categoria']}
                    - Tipo Servicio: {$programa['tipo_servicio']}
                    - Tipo Vehículo: {$programa['tipo_vehiculo_id']}.";

                $auditoriaController = new AuditoriaController();
                $auditoriaController->registrar($currentUserId, 'Eliminar', 'Programas', $descripcion, $empresaId);

                $_SESSION['success_message'] = "El programa y sus clases asociadas han sido eliminados correctamente.";
            } else {
                $_SESSION['error_message'] = "Hubo un error al eliminar el programa.";
            }
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Error al eliminar el programa: " . $e->getMessage();
        }

        // 5️⃣ Redirigir al listado de programas
        header("Location: " . $routes['programas_index']);
        exit;
    }


    public function detail($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_programas')) {
            echo "No tienes permiso para ver esta página.";
            return;
        }

        // Obtener información del programa
        $query = "
                SELECT 
                    p.*, 
                    e.nombre as empresa_nombre, 
                    c.nombre as categoria_nombre 
                FROM 
                    programas p
                LEFT JOIN 
                    empresas e ON p.empresa_id = e.id
                LEFT JOIN 
                    categorias_licencia c ON p.categoria = c.id
                WHERE 
                    p.id = :id
            ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $programa = $stmt->fetch(PDO::FETCH_ASSOC);

        // Obtener clases del programa
        $queryClases = "
                SELECT 
                    cp.*
                FROM 
                    clases_programas cp
                WHERE 
                    cp.programa_id = :programa_id
            ";

        $stmtClases = $this->conn->prepare($queryClases);
        $stmtClases->bindParam(':programa_id', $id);
        $stmtClases->execute();
        $clases = $stmtClases->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/programas/views/detail.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    private function getEmpresas()
    {
        $query = "SELECT id, nombre FROM empresas";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getCategorias()
    {
        $query = "SELECT id, nombre FROM categorias_licencia";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTiposVehiculo()
    {
        $query = "SELECT id, nombre FROM param_tipo_vehiculo WHERE estado = 1";
        $stmt = $this->conn->prepare($query); // $this->conn es tu conexión a la base de datos
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    ## recupera las clases que le faltan al estudiante
    public function getClasesPorPrograma($programa_id)
    {
        $query = "SELECT id, nombre_clase, numero_horas, orden 
                  FROM clases_programas 
                  WHERE programa_id = :programa_id 
                  ORDER BY orden ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programa_id);
        $stmt->execute();
        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($clases)) {
            error_log("No se encontraron clases para programa_id: " . $programa_id);
        }

        echo json_encode($clases);
    }

    // ----------------------------------------------------------
    // 🔹 Recupera SOLO las clases prácticas pendientes
    //    (no vistas por la matrícula)
    // ----------------------------------------------------------
    public function getClasesPendientesPorMatricula($matricula_id)
    {
        $query = "
            SELECT
                cp.id,
                cp.nombre_clase,
                cp.numero_horas,
                cp.orden
            FROM clases_programas cp

            INNER JOIN matricula_programas mp
                ON mp.programa_id = cp.programa_id

            WHERE mp.matricula_id = :matricula_id

            AND cp.id NOT IN (
                SELECT cpr.clase_programa_id
                FROM clases_practicas cpr
                WHERE cpr.matricula_id = :matricula_id
                AND (
                    cpr.fecha < CURRENT_DATE
                    OR (cpr.fecha = CURRENT_DATE AND cpr.hora_fin <= CURRENT_TIME)
                )
            )

            ORDER BY cp.orden ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matricula_id);
        $stmt->execute();

        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($clases)) {
            error_log("No se encontraron clases pendientes para matricula_id: " . $matricula_id);
        }

        echo json_encode($clases);
    }





    /**
     * Obtiene la información completa del estudiante a partir del ID de una matrícula.
     *
     * @param string $matriculaId ID de la matrícula.
     * @return array|null Información del estudiante (array asociativo) o null si no se encuentra.
     */
    public function getProgramaById($programaId)
    {
        // Consulta para obtener la información completa del programa
        $query = "SELECT *        
            FROM programas
            WHERE id = :programa_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':programa_id', $programaId, PDO::PARAM_INT);
        $stmt->execute();

        // Retornar la información del programa o null si no se encuentra
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ----------------------------------------------------------
    // 🔹 Método: obtener programas activos por empresa
    // ----------------------------------------------------------
    public function getProgramas()
    {
        try {
            $query = "SELECT id, nombre 
                  FROM programas 
                  WHERE estado = 1 
                  ORDER BY nombre ASC";

            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $programas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($programas);
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
