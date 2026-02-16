<?php
function decodificarToken($token)
{
    if (!$token || !is_string($token)) return [];

    $decoded = base64_decode($token, true);
    if ($decoded === false) return [];

    $data = json_decode($decoded, true);
    return is_array($data) ? $data : [];
}
