<?php

require 'APIClient.php'; // Asegúrate de que la clase APIClient está correctamente incluida

// Configuración inicial
header('Content-Type: application/json');
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Conexión a la base de datos
$dsn = 'mysql:host=localhost;dbname=academias;charset=utf8mb4';
$username = 'anjarami';
$password = 'G7fY$kP2wLx1@hQ3';

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error de conexión a la base de datos: " . $e->getMessage()]);
    exit;
}

// **1. Verificar que se recibió `api_device_id`**
if (!isset($_GET['api_device_id']) || empty($_GET['api_device_id'])) {
    echo json_encode(["error" => "El parámetro 'api_device_id' es obligatorio."]);
    exit;
}

$apiDeviceId = $_GET['api_device_id'];

// **2. Obtener `accessToken` almacenado en la base de datos**
$query = "SELECT access_token, expires_at FROM api_tokens ORDER BY id DESC LIMIT 1";
$stmt = $pdo->query($query);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// **3. Si el token no existe o ha expirado, renovarlo automáticamente**
if (!$result || time() >= (int)$result['expires_at']) {
    $client = new APIClient(
        "https://us-open.tracksolidpro.com/route/rest",
        "8FB345B8693CCD0061511A2901818304339A22A4105B6558",
        "df5991968c8c4512b9aa32d5a74c4d8d",
        $dsn,
        $username,
        $password
    );

    $client->authenticate(); // 🔄 Renovar el token
    $query = "SELECT access_token FROM api_tokens ORDER BY id DESC LIMIT 1";
    $stmt = $pdo->query($query);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}

// **4. Usar el token actualizado**
$accessToken = $result['access_token'];

// **5. Hacer la petición a Tracksolid para obtener la URL del video**
$apiUrl = "https://us-open.tracksolidpro.com/route/rest";
$params = [
    "method" => "jimi.device.live.page.url",
    "timestamp" => date("Y-m-d H:i:s"),
    "app_key" => "8FB345B8693CCD0061511A2901818304339A22A4105B6558",
    "sign_method" => "md5",
    "v" => "0.9",
    "format" => "json",
    "access_token" => $accessToken,
    "target" => "Tecnotransporte",
    "imei" => $apiDeviceId,
    "type" => "1"
];

// **6. Generar la firma de la solicitud**
ksort($params);
$concatenatedValues = '';
foreach ($params as $key => $value) {
    if ($key === 'user_id') {
        $value = strtolower($value);
    }
    $concatenatedValues .= $value;
}
$stringToSign = "df5991968c8c4512b9aa32d5a74c4d8d" . $concatenatedValues . "df5991968c8c4512b9aa32d5a74c4d8d";
$params["sign"] = strtoupper(md5($stringToSign));

// **7. Enviar la solicitud a la API de Tracksolid**
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

// **8. Decodificar la respuesta**
$data = json_decode($response, true);

if (!isset($data['result']['UrlCamera'])) {
    echo json_encode(["error" => "No se pudo obtener el enlace del video"]);
    exit;
}

// **9. Devolver la URL de la cámara en JSON**
echo json_encode([
    "video_url" => $data['result']['UrlCamera']
]);
?>
