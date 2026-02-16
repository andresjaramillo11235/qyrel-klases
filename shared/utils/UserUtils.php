<?php

require_once '../config/DatabaseConfig.php';

class UserUtils
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    public function isSuperAdmin($userId)
    {
        $query = "SELECT r.is_super_admin 
                  FROM users u 
                  JOIN roles r ON u.role_id = r.id 
                  WHERE u.id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['is_super_admin'] == 1;
    }
}
