<?php

require_once '../config/DatabaseConfig.php';

class TablasParametricasUtils
{
    private $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function getParamTiposDocumentos()
    {
        $query = "SELECT * FROM param_tipo_documento";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParamDepartamentos()
    {
        $query = "SELECT * FROM param_departamentos";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParamCiudades()
    {
        $query = "SELECT * FROM param_ciudades";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParamGrupoSanguineo()
    {
        $query = "SELECT * FROM param_grupo_sanguineo";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParamGenero()
    {
        $query = "SELECT * FROM param_genero";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getParamEstadoCivil()
    {
        $query = "SELECT * FROM param_estado_civil";
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}

?>
