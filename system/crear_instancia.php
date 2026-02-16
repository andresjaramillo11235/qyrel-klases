<?php
// crear_instancia.php

// ----------- CONFIGURACIÓN GLOBAL (ajusta a tu entorno) ------------
$basePath         = "/var/www/ceacloud"; // carpeta donde viven los proyectos
$baseTemplatePath = "$basePath/base";    // carpeta del sistema base para copiar
$mysqlRootUser    = "root";
$mysqlRootPass    = "TU_PASSWORD";
$apacheSitesPath  = "/etc/apache2/sites-available"; // virtualhosts
$mainDomain       = "ceacloud.lat"; // dominio principal
// ------------------------------------------------------------------

// ----------- DATOS DEL NUEVO CLIENTE (vienen del formulario admin) ------------
$nombreCorto   = "internacional"; // lo defines tú
$subdominio    = "internacional.ceacloud.lat"; // lo define el cliente
$correo        = "cliente@ejemplo.com";
$fechaCreacion = date("Y-m-d H:i:s");
// -------------------------------------------------------------------------------

// 1. Crear directorio del cliente
$proyectoPath = "$basePath/$nombreCorto";
if (!file_exists($proyectoPath)) {
    mkdir($proyectoPath, 0755, true);
    exec("cp -r $baseTemplatePath/* $proyectoPath/");
    echo "✅ Directorio creado: $proyectoPath\n";
} else {
    die("❌ El directorio ya existe.\n");
}

// 2. Crear base de datos y usuario MySQL
$dbName     = "ceacloud_$nombreCorto";
$dbUser     = "user_$nombreCorto";
$dbPassword = bin2hex(random_bytes(6)); // contraseña segura

$conn = new mysqli("localhost", $mysqlRootUser, $mysqlRootPass);
if ($conn->connect_error) {
    die("❌ Error de conexión MySQL: " . $conn->connect_error);
}
$conn->query("CREATE DATABASE `$dbName`");
$conn->query("CREATE USER '$dbUser'@'localhost' IDENTIFIED BY '$dbPassword'");
$conn->query("GRANT ALL PRIVILEGES ON `$dbName`.* TO '$dbUser'@'localhost'");
$conn->query("FLUSH PRIVILEGES");
$conn->close();
echo "✅ Base de datos creada: $dbName (usuario: $dbUser)\n";

// 3. Crear archivo VirtualHost de Apache
$vhostConfig = "
<VirtualHost *:80>
    ServerName $subdominio
    DocumentRoot $proyectoPath

    <Directory $proyectoPath>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/$nombreCorto-error.log
    CustomLog \${APACHE_LOG_DIR}/$nombreCorto-access.log combined
</VirtualHost>
";
file_put_contents("$apacheSitesPath/$nombreCorto.conf", $vhostConfig);
exec("a2ensite $nombreCorto.conf && systemctl reload apache2");
echo "✅ VirtualHost configurado para $subdominio\n";

// 4. Registrar cliente en la base de datos global
$conexion = new mysqli("localhost", "root", $mysqlRootPass, "ceacloud_global");
$stmt = $conexion->prepare("
    INSERT INTO clientes (nombre_corto, subdominio, correo, fecha_creacion, ruta, base_datos)
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->bind_param("ssssss", $nombreCorto, $subdominio, $correo, $fechaCreacion, $proyectoPath, $dbName);
$stmt->execute();
$stmt->close();
$conexion->close();
echo "✅ Cliente registrado correctamente\n";
?>
