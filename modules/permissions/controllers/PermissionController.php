<?php

require_once '../config/DatabaseConfig.php';

class PermissionController
{
    private $conn;

    public function __construct()
    {
        $database = new DatabaseConfig();
        $this->conn = $database->getConnection();
    }

    public function hasPermission($userId, $permissionName)
    {
        $query = "SELECT p.name
                  FROM permissions p
                  JOIN role_permissions rp ON p.id = rp.permission_id
                  JOIN users u ON rp.role_id = u.role_id
                  WHERE u.id = :user_id AND p.name = :permission_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':permission_name', $permissionName);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
