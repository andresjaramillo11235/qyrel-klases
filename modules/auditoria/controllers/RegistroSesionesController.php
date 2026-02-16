<?php

require_once '../config/DatabaseConfig.php';



class RegistroSesionesController
{
  private $conn;

  public function __construct()
  {
    $config = new DatabaseConfig();
    $this->conn = $config->getConnection();
  }

  public function registrarIngreso($userId, $empresaId)
  {
    // Configurar la zona horaria de Colombia
    date_default_timezone_set('America/Bogota');

    // Obtener la fecha y hora actual en formato MySQL (Y-m-d H:i:s)
    $loginTime = date('Y-m-d H:i:s');
    $ipAddress = $_SERVER['REMOTE_ADDR'];

    // Inserción de la fecha generada por PHP
    $query = "INSERT INTO registro_sesiones (user_id, empresa_id, login_time, status, ip_address)
              VALUES (:user_id, :empresa_id, :login_time, 'ACTIVO', :ip_address)";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':user_id', $userId);
    $stmt->bindParam(':empresa_id', $empresaId);
    $stmt->bindParam(':login_time', $loginTime);
    $stmt->bindParam(':ip_address', $ipAddress);

    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }
  }


  public function registrarSalida($userId)
  {
    // Configurar la zona horaria de Colombia
    date_default_timezone_set('America/Bogota');

    // Obtener la fecha y hora actual en formato MySQL (Y-m-d H:i:s)
    $logoutTime = date('Y-m-d H:i:s');

    // Actualización de la fecha generada por PHP
    $query = "UPDATE registro_sesiones 
              SET logout_time = :logout_time, status = 'CERRADO'
              WHERE user_id = :user_id AND status = 'ACTIVO'";

    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':logout_time', $logoutTime);
    $stmt->bindParam(':user_id', $userId);

    if ($stmt->execute()) {
      return true;
    } else {
      return false;
    }
  }
}
