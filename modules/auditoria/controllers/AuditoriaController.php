<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class AuditoriaController
{
	private $conn;

	public function __construct()
	{
		$config = new DatabaseConfig();
		$this->conn = $config->getConnection();
	}

	public function index()
	{
		$permissionController = new PermissionController();
		$currentUserId = $_SESSION['user_id'];
		$empresaId = $_SESSION['empresa_id'];

		// Verificar permiso para ver auditorías
		if (!$permissionController->hasPermission($currentUserId, 'view_auditorias')) {
			header('Location: /permission-denied/');
			exit;
		}

		// Consulta para obtener auditorías con datos del usuario
		$query = "
			SELECT 
				a.*, 
				u.first_name AS usuario_nombre, 
				u.last_name AS usuario_apellido
			FROM auditoria a
			LEFT JOIN users u ON a.usuario_id = u.id
			WHERE a.empresa_id = :empresa_id
			ORDER BY a.fecha DESC";

		// Preparar la consulta
		$stmt = $this->conn->prepare($query);
		$stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);

		// Ejecutar la consulta
		$stmt->execute();
		$auditorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

		// Renderizar la vista de listado de auditorías
		ob_start();
		include '../modules/auditoria/views/index.php';
		$content = ob_get_clean();
		include '../shared/views/layout.php';
	}




	public function registrar($usuario_id, $accion, $modulo, $descripcion, $empresa_id)
	{
		try {
			$query = "INSERT INTO auditoria (usuario_id, accion, modulo, descripcion, empresa_id) 
                  VALUES (:usuario_id, :accion, :modulo, :descripcion, :empresa_id)";

			$stmt = $this->conn->prepare($query);

			// Bind parameters
			$stmt->bindParam(':usuario_id', $usuario_id);
			$stmt->bindParam(':accion', $accion);
			$stmt->bindParam(':modulo', $modulo);
			$stmt->bindParam(':descripcion', $descripcion);
			$stmt->bindParam(':empresa_id', $empresa_id);

			// Execute the query without interrupting the flow
			$stmt->execute();
		} catch (Exception $e) {
			// Log the error but do not stop the application flow
			$this->logAuditoriaError($e->getMessage(), $usuario_id, $accion, $modulo, $empresa_id);
		}
	}

	// Method to log the error into a separate file
	private function logAuditoriaError($errorMessage, $usuario_id, $accion, $modulo, $empresa_id)
	{
		$logFile = __DIR__ . '/../logs/auditoria_errors.log';  // Path to log file
		$errorData = [
			'fecha' => date('Y-m-d H:i:s'),
			'usuario_id' => $usuario_id,
			'accion' => $accion,
			'modulo' => $modulo,
			'empresa_id' => $empresa_id,
			'error' => $errorMessage
		];

		$logMessage = json_encode($errorData) . PHP_EOL;

		file_put_contents($logFile, $logMessage, FILE_APPEND);
	}
}
