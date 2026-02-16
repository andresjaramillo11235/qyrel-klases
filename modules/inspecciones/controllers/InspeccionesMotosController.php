<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../shared/utils/ImageHelper.php';

class InspeccionesMotosController
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
        $empresa_id = $_SESSION['empresa_id'];
        $rol = $_SESSION['rol_nombre'];

        if (!$permissionController->hasPermission($currentUserId, 'view_inspecciones_motos')) {
            header('Location: /permission-denied/');
            exit;
        }

        $filtros = [];
        $query = "SELECT im.*,
                     v.placa,
                     CONCAT(u.first_name, ' ', u.last_name) AS usuario_nombre
              FROM inspeccion_motos im
              INNER JOIN vehiculos v ON im.id_vehiculo = v.id
              INNER JOIN users u ON im.id_usuario = u.id
              WHERE im.empresa_id = :empresa_id
              AND v.tipo_vehiculo_id = 2";

        $filtros[':empresa_id'] = $empresa_id;

        if ($rol !== 'ADMIN') {
            $query .= " AND im.id_usuario = :user_id";
            $filtros[':user_id'] = $currentUserId;
        }

        // Filtro por fecha
        if (!empty($_POST['fecha_inicio']) && !empty($_POST['fecha_fin'])) {
            $query .= " AND DATE(im.fecha_hora) BETWEEN :fecha_inicio AND :fecha_fin";
            $filtros[':fecha_inicio'] = $_POST['fecha_inicio'];
            $filtros[':fecha_fin'] = $_POST['fecha_fin'];
        }

        // Ordenar por fecha más reciente
        $query .= " ORDER BY im.fecha_hora DESC";

        $stmt = $this->conn->prepare($query);
        foreach ($filtros as $param => $value) {
            $stmt->bindValue($param, $value);
        }

        $stmt->execute();
        $inspecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/inspecciones/views/inspecciones_motos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        $empresaId = $_SESSION['empresa_id'];

        // Obtener vehículos tipo motocicleta
        $query = "SELECT v.id AS vehiculos_id, v.placa AS vehiculos_placa
              FROM vehiculos v
              WHERE v.empresa_id = :empresa_id
              AND v.tipo_vehiculo_id = 2";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId);
        $stmt->execute();
        $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/inspecciones/views/inspecciones_motos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $routes = include '../config/Routes.php';

        $empresaId = $_SESSION['empresa_id'];
        $usuarioId = $_SESSION['user_id'];

        // Subir fotos
        $foto1 = $this->subirFoto('foto1');
        $foto2 = $this->subirFoto('foto2');
        $foto3 = $this->subirFoto('foto3');

        // Lista de campos tipo checkbox (booleanos)
        $camposCheckbox = [
            'estado_general',
            'faros_delanteros',
            'luces_traseras',
            'luces_direccionales',
            'luz_freno',
            'luz_placa',
            'manillar',
            'asiento',
            'controles',
            'limpiar_ajustar',
            'ruidos_inusuales',
            'motor_arranca',
            'interruptor_encendido',
            'inspeccion_visual',
            'sistema_frenos',
            'sistema_suspension',
            'sistema_escape',
            'cadena_transmision',
            'nivel_aceite',
            'nivel_refrigerante',
            'nivel_frenos',
            'fugas_fluidos',
            'cascos',
            'protecciones',
            'herramientas',
            'llave_encendido',
            'caja_herramientas',
            'tarjeta_servicio',
            'licencia_transito',
            'seguro_obligatorio',
            'tecnico_mecanica',
            'licencia_conduccion',
            'cedula_ciudadania'
        ];

        // Construir campos dinámicos
        $estado = 1; // aprobado
        $valores = [];
        $camposSQL = '';
        $placeholders = '';

        foreach ($camposCheckbox as $campo) {
            $camposSQL .= "$campo, ";
            $placeholders .= ":$campo, ";
            $valores[$campo] = isset($_POST[$campo]) ? 1 : 2;

            if (!isset($_POST[$campo])) {
                $estado = 2; // rechazado si alguno no está marcado
            }
        }

        // Datos adicionales
        $camposSQL .= "fecha_hora, id_vehiculo, id_usuario, empresa_id, kilometraje, observaciones, foto1, foto2, foto3, estado";
        $placeholders .= ":fecha_hora, :id_vehiculo, :id_usuario, :empresa_id, :kilometraje, :observaciones, :foto1, :foto2, :foto3, :estado";

        $valores['fecha_hora'] = date('Y-m-d H:i:s');
        $valores['id_vehiculo'] = $_POST['id_vehiculo'];
        $valores['id_usuario'] = $usuarioId;
        $valores['empresa_id'] = $empresaId;
        $valores['kilometraje'] = $_POST['kilometraje'];
        $valores['observaciones'] = $_POST['observaciones'];
        $valores['foto1'] = $foto1;
        $valores['foto2'] = $foto2;
        $valores['foto3'] = $foto3;
        $valores['estado'] = 1;

        // Insertar en la DB
        $query = "INSERT INTO inspeccion_motos ($camposSQL) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($query);

        foreach ($valores as $campo => $valor) {
            $stmt->bindValue(":$campo", $valor);
        }

        $stmt->execute();
        $_SESSION['success_message'] = 'Inspección de moto creada exitosamente.';
        header('Location: ' . $routes['inspecciones_motos_index']);

        exit;
    }

    private function subirFoto($nombreCampo)
    {
        if (isset($_FILES[$nombreCampo]) && $_FILES[$nombreCampo]['error'] === UPLOAD_ERR_OK) {
            $nombreOriginal = $_FILES[$nombreCampo]['name'];
            $extension = pathinfo($nombreOriginal, PATHINFO_EXTENSION);
            $nombreNuevo = uniqid('foto_', true) . '.' . $extension;

            $rutaDestino = '../files/fotos_inspecciones_motos/' . $nombreNuevo;

            if (move_uploaded_file($_FILES[$nombreCampo]['tmp_name'], $rutaDestino)) {
                return $nombreNuevo;
            }
        }

        return null;
    }

    public function view($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        // Validar permiso
        if (!$permissionController->hasPermission($currentUserId, 'view_inspecciones_motos')) {
            header('Location: /permission-denied/');
            exit;
        }

        // Consultar datos de la inspección
        $sql = "SELECT im.*, v.placa
            FROM inspeccion_motos im
            INNER JOIN vehiculos v ON im.id_vehiculo = v.id
            WHERE im.id = :id AND im.empresa_id = :empresa_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empresa_id', $empresa_id);
        $stmt->execute();
        $inspeccion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inspeccion) {
            header('Location: /inspeccionesmotos/');
            exit;
        }

        // Encapsular para que la vista siga usando $datosInspeccion[0]['campo']
        $datosInspeccion = [$inspeccion];

        $modoLectura = true;
        $idInspeccion = $id;

        ob_start();
        include '../modules/inspecciones/views/inspecciones_motos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
