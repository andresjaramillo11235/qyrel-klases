<?php
require '../vendor/autoload.php';
include '../config/DatabaseConfig.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

ini_set('display_errors', 1);
error_reporting(E_ALL);

function safe_trim($value)
{
    return is_null($value) ? '' : trim($value);
}

$db = new DatabaseConfig();
$conn = $db->getConnection();

$archivo = __DIR__ . '/estudiantes.xlsx';
$documento = IOFactory::load($archivo);
$hoja = $documento->getActiveSheet();
$filas = $hoja->toArray();

$logErrores = [];

for ($i = 1; $i < count($filas); $i++) {
    $fila = $filas[$i];

    preg_match('/^([0-9]+)\)/', safe_trim($fila[0]), $match);
    $tipo_documento = isset($match[1]) ? (int)$match[1] : null;

    if (!$tipo_documento) {
        $logErrores[] = "Fila $i: tipo_documento inválido o ausente";
        continue;
    }

    $numero_documento = safe_trim($fila[1]);
    $nombres = strtoupper(safe_trim($fila[2]));
    $apellidos = strtoupper(safe_trim($fila[3]));
    $correo = strtolower(safe_trim($fila[4]));
    $celular = safe_trim($fila[5]);
    $direccion = strtoupper(safe_trim($fila[6]));

    $estado = '1'; // Activo
    $empresa_id = 19;
    $role_id = 5;
    $foto = 'img-defecto-estudiante.webp';
    $expedicion_departamento = 'CUNDINAMARCA';
    $expedicion_ciudad = 'BOGOTA';



    // Validar si ya existe ese correo
    $stmtCheckCorreo = $conn->prepare("SELECT COUNT(*) FROM estudiantes WHERE correo = ?");
    $stmtCheckCorreo->execute([$correo]);
    $correoDuplicado = $stmtCheckCorreo->fetchColumn() > 0;

    if ($correoDuplicado) {
        $correoOriginal = $correo;
        $correo = 'duplicado_' . uniqid() . '@temporal.com';
        $logErrores[] = "Fila $i: Correo duplicado - '$correoOriginal'. Se usó temporal: '$correo'";
    }




    // Validar duplicados solo por documento
    $stmtCheckEst = $conn->prepare("SELECT COUNT(*) FROM estudiantes WHERE numero_documento = ?");
    $stmtCheckEst->execute([$numero_documento]);
    if ($stmtCheckEst->fetchColumn() > 0) {
        $logErrores[] = "Fila $i: Estudiante duplicado - Documento: $numero_documento";
        continue;
    }







    // // Validar duplicados en estudiantes
    // $stmtCheckEst = $conn->prepare("SELECT COUNT(*) FROM estudiantes WHERE numero_documento = ? OR correo = ?");
    // $stmtCheckEst->execute([$numero_documento, $correo]);
    // if ($stmtCheckEst->fetchColumn() > 0) {
    //     $logErrores[] = "Fila $i: Estudiante duplicado - Documento: $numero_documento, Correo: $correo";
    //     continue;
    // }

    $stmtEst = $conn->prepare("INSERT INTO estudiantes 
        (codigo, nombres, apellidos, tipo_documento, numero_documento, correo, celular, direccion_residencia, estado, empresa_id, foto, expedicion_departamento, expedicion_ciudad) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $codigo = uniqid('EST');

    if (!$stmtEst->execute([$codigo, $nombres, $apellidos, $tipo_documento, $numero_documento, $correo, $celular, $direccion, $estado, $empresa_id, $foto, $expedicion_departamento, $expedicion_ciudad])) {
        $logErrores[] = "Fila $i: Error al insertar estudiante - $numero_documento";
        continue;
    }

    $estudiante_id = $conn->lastInsertId();

    $username = 'est' . $numero_documento;
    $passwordHash = password_hash($numero_documento, PASSWORD_DEFAULT);

    $stmtCheckUser = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmtCheckUser->execute([$username]);
    if ($stmtCheckUser->fetchColumn() > 0) {
        $logErrores[] = "Fila $i: Username duplicado en users - $username";
        continue;
    }

    $stmtUser = $conn->prepare("INSERT INTO users 
        (username, email, password, first_name, last_name, phone, address, status, role_id, empresa_id, estudiante_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?, ?, ?)");

    if (!$stmtUser->execute([$username, $correo, $passwordHash, $nombres, $apellidos, $celular, $direccion, $role_id, $empresa_id, $estudiante_id])) {
        $logErrores[] = "Fila $i: Error al insertar usuario para estudiante $numero_documento";
        continue;
    }

    echo "✅ Fila $i: Usuario creado - $username<br>";
}

if (!empty($logErrores)) {
    $logFile = __DIR__ . '/log_errores_importacion.txt';
    file_put_contents($logFile, implode("\n", $logErrores));
    echo "<br><br>❗ Se encontraron errores. Revisa el archivo log: log_errores_importacion.txt";
} else {
    echo "<br><br>✅ Cargue completado sin errores.";
}
