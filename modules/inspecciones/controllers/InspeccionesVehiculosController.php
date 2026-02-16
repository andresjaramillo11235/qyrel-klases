
<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';
require_once '../shared/utils/UserUtils.php';
require_once '../shared/utils/ImageHelper.php';


class InspeccionesVehiculosController
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

        if (!$permissionController->hasPermission($currentUserId, 'view_inspecciones_vehiculos')) {
            header('Location: /permission-denied/');
            exit;
        }

        $query = "SELECT iv.*,
                 v.placa,
                 CONCAT(u.first_name, ' ', u.last_name) AS usuario_nombre
          FROM inspeccion_vehiculos iv
          INNER JOIN vehiculos v ON iv.id_vehiculo = v.id
          INNER JOIN users u ON iv.id_usuario = u.id
          WHERE iv.empresa_id = :empresa_id";

        if ($rol !== 'ADMIN') {
            $query .= " AND iv.id_usuario = :user_id";
        }

        $query .= " ORDER BY iv.id DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresa_id);

        if ($rol !== 'ADMIN') {
            $stmt->bindParam(':user_id', $currentUserId);
        }

        $stmt->execute();
        $inspecciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/inspecciones/views/inspecciones_vehiculos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function store()
    {
        $routes = include '../config/Routes.php';

        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        $id_vehiculo = $_POST['id_vehiculo'];
        $kilometraje = $_POST['kilometraje'];
        $observaciones = strtoupper($_POST['observaciones']);
        $fecha_hora = date('Y-m-d H:i:s');

        // Manejo de fotos
        $foto1 = $foto2 = $foto3 = '';
        $ruta_fotos = '../files/fotos_inspecciones_vehiculos/';
        if (!file_exists($ruta_fotos)) {
            mkdir($ruta_fotos, 0777, true);
        }

        foreach (['foto1', 'foto2', 'foto3'] as $foto) {
            if (isset($_FILES[$foto]) && $_FILES[$foto]['error'] == 0) {
                $nombreArchivo = bin2hex(random_bytes(8)) . '.' . pathinfo($_FILES[$foto]['name'], PATHINFO_EXTENSION);
                move_uploaded_file($_FILES[$foto]['tmp_name'], $ruta_fotos . $nombreArchivo);
                $$foto = $nombreArchivo;
            }
        }

        $camposCheckbox = [
            'estado_carroceria',
            'faros_delanteros',
            'luces_traseras',
            'luces_freno',
            'luces_direccionales',
            'luces_reversa',
            'luces_parqueo',
            'luces_placa',
            'espejos_retrovisores',
            'parabrisas',
            'ventanas_laterales',
            'llantas',
            'tapa_tanque',
            'alineacion_ruedas',
            'cinturones_seguridad',
            'ajuste_asientos',
            'ajuste_espejos',
            'panel_instrumentos',
            'freno_estacionamiento',
            'ruidos_motor',
            'arranque_motor',
            'palanca_cambios',
            'inspeccion_motor',
            'transmision',
            'frenos',
            'direccion',
            'escape',
            'correas_motor',
            'aceite_motor',
            'refrigerante_radiador',
            'liquido_frenos',
            'direccion_asistida',
            'botiquin',
            'llanta_repuesto',
            'gato',
            'conos',
            'linterna',
            'extintor',
            'tacos',
            'llave_cruz',
            'kit_herramientas',
            'tarjeta_servicio',
            'licencia_transito',
            'seguro_obligatorio',
            'tecnico_mecanica',
            'licencia_conduccion',
            'cedula_ciudadania',
            'estado_bateria',
            'seguro',
            'revision_preventiva',
            'declaracion_responsabilidad',
            'declaracion_consumo',
            'declaracion_optimo'
        ];

        $estado = 1; // aprobado
        $valoresCampos = [];

        foreach ($camposCheckbox as $campo) {
            $valoresCampos[$campo] = isset($_POST[$campo]) ? 1 : 2;
            if (!isset($_POST[$campo])) {
                $estado = 2; // rechazado si alguno no está marcado
            }
        }

        $query = "INSERT INTO inspeccion_vehiculos 
            (fecha_hora, id_vehiculo, id_usuario, kilometraje, observaciones, foto1, foto2, foto3, estado, empresa_id,
            " . implode(", ", array_keys($valoresCampos)) . ") 
            VALUES 
            (:fecha_hora, :id_vehiculo, :id_usuario, :kilometraje, :observaciones, :foto1, :foto2, :foto3, :estado, :empresa_id,
            :" . implode(", :", array_keys($valoresCampos)) . ")";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fecha_hora', $fecha_hora);
        $stmt->bindParam(':id_vehiculo', $id_vehiculo);
        $stmt->bindParam(':id_usuario', $currentUserId);
        $stmt->bindParam(':kilometraje', $kilometraje);
        $stmt->bindParam(':observaciones', $observaciones);
        $stmt->bindParam(':foto1', $foto1);
        $stmt->bindParam(':foto2', $foto2);
        $stmt->bindParam(':foto3', $foto3);
        $stmt->bindParam(':estado', $estado);
        $stmt->bindParam(':empresa_id', $empresa_id);

        foreach ($valoresCampos as $campo => $valor) {
            $stmt->bindValue(':' . $campo, $valor);
        }

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'La inspección fue registrada con éxito.';
            header('Location: ' . $routes['inspecciones_vehiculos_index']);
            exit;
        } else {
            echo "Error al registrar la inspección.";
        }
    }

    public function create()
    {
        $empresa_id = $_SESSION['empresa_id'];

        $query = "SELECT id AS vehiculos_id, placa AS vehiculos_placa
        FROM vehiculos
        WHERE tipo_vehiculo_id = 1 AND empresa_id = :empresa_id
        ORDER BY placa ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresa_id);
        $stmt->execute();
        $vehiculos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/inspecciones/views/inspecciones_vehiculos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function view($id)
    {
        $permissionController = new PermissionController();
        $currentUserId = $_SESSION['user_id'];
        $empresa_id = $_SESSION['empresa_id'];

        if (!$permissionController->hasPermission($currentUserId, 'view_inspecciones_vehiculos')) {
            header('Location: /permission-denied/');
            exit;
        }

        $sql = "SELECT iv.*, v.placa
        FROM inspeccion_vehiculos iv
        INNER JOIN vehiculos v ON iv.id_vehiculo = v.id
        WHERE iv.id = :id AND iv.empresa_id = :empresa_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':empresa_id', $empresa_id);
        $stmt->execute();
        $inspeccion = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inspeccion) {
            header('Location: /inspeccionesvehiculos/');
            exit;
        }

        ob_start();
        include '../modules/inspecciones/views/inspecciones_vehiculos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
