<?php

class APIClient
{
    private $apiUrl;
    private $appKey;
    private $appSecret;
    private $accessToken;
    private $tokenExpiration;
    private $pdo;

    public function __construct($apiUrl, $appKey, $appSecret, $dsn, $username, $password)
    {
        $this->apiUrl = $apiUrl;
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;

        try {
            $this->pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
        } catch (PDOException $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }

        $this->loadToken();
    }

    /** ✅ Método para verificar si el token es válido **/
    private function isTokenValid()
    {
        return $this->accessToken && time() < $this->tokenExpiration;
    }

    /** ✅ Cargar el token desde la base de datos **/
    private function loadToken()
    {
        $query = "SELECT access_token, expires_at FROM api_tokens ORDER BY id DESC LIMIT 1";
        $stmt = $this->pdo->query($query);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $this->accessToken = $result['access_token'];
            $this->tokenExpiration = (int) $result['expires_at']; // 🔥 Convertir a timestamp UNIX correctamente

            // ✅ Si el token ya expiró, autenticar automáticamente
            if (!$this->isTokenValid()) {
                error_log("🔄 Token expirado, autenticando...");
                $this->authenticate();
            }
        } else {
            error_log("⚠️ No se encontró un token en la base de datos, autenticando...");
            $this->authenticate();
        }
    }

    /** ✅ Autenticación y renovación del token **/
    private function authenticate()
    {
        $params = [
            'method' => 'jimi.oauth.token.get',
            'timestamp' => date('Y-m-d H:i:s'),
            'app_key' => $this->appKey,
            'sign_method' => 'md5',
            'v' => '0.9',
            'format' => 'json',
            'user_id' => 'Tecnotransporte',
            'user_pwd_md5' => '19b02fbceef10883692fb14b26232764',
            'expires_in' => 7200
        ];
        $params['sign'] = $this->generateSign($params);

        $response = $this->sendRequest($params);

        if ($response && isset($response['code']) && $response['code'] === 0) {
            $this->saveTokenToDB($response['result']['accessToken'], $response['result']['expiresIn']);
        } else {
            die("Error de autenticación: " . ($response['message'] ?? 'Respuesta inesperada de la API'));
        }
    }

    /** ✅ Guardar o actualizar el token en la base de datos **/
    private function saveTokenToDB($accessToken, $expiresIn)
    {
        $expiresAt = time() + $expiresIn; // 🔥 Guardamos como timestamp UNIX
        $this->accessToken = $accessToken;
        $this->tokenExpiration = $expiresAt;

        $query = "INSERT INTO api_tokens (access_token, expires_at) 
                  VALUES (:access_token, :expires_at)
                  ON DUPLICATE KEY UPDATE access_token = VALUES(access_token), expires_at = VALUES(expires_at)";
                  
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':access_token' => $accessToken, ':expires_at' => $expiresAt]);
    }

    /** ✅ Obtener video de la cámara, validando antes el token **/
    public function getDevicePhotoVideo($deviceId)
    {
        // Si el token es inválido, renovarlo antes de hacer la consulta
        if (!$this->isTokenValid()) {
            error_log("🔄 Token caducado. Renovando...");
            $this->authenticate();
        }

        $params = [
            'method' => 'jimi.device.live.page.url',
            'timestamp' => date('Y-m-d H:i:s'),
            'app_key' => $this->appKey,
            'sign_method' => 'md5',
            'v' => '0.9',
            'format' => 'json',
            'access_token' => $this->accessToken,
            'target' => 'Tecnotransporte',
            'imei' => $deviceId,
            'type' => '1'
        ];
        $params['sign'] = $this->generateSign($params);

        return $this->sendRequest($params);
    }

    private function generateSign($params)
    {
        ksort($params);
        $concatenatedValues = implode('', $params);
        $stringToSign = $this->appSecret . $concatenatedValues . $this->appSecret;
        return strtoupper(md5($stringToSign));
    }

    private function sendRequest($params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}
?>
