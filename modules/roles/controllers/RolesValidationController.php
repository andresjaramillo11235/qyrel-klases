<?php

require_once '../config/DatabaseConfig.php';

class RolesValidationController
{
    private $conn;
    private $table_name = "users";

    public function __construct()
    {
        $database = new DatabaseConfig();
        $this->conn = $database->getConnection();
    }

    public function hasRole($userId, $roleName)
    {
        $query = "SELECT r.name
                  FROM roles r
                  JOIN user_roles ur ON r.id = ur.role_id
                  WHERE ur.user_id = :user_id AND r.name = :role_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':role_name', $roleName);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function hasPermission($userId, $permissionName)
    {
        $query = "SELECT p.name
                  FROM permissions p
                  JOIN role_permissions rp ON p.id = rp.permission_id
                  JOIN user_roles ur ON rp.role_id = ur.role_id
                  WHERE ur.user_id = :user_id AND p.name = :permission_name";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':permission_name', $permissionName);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}
