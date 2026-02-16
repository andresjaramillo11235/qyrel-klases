<?php
function wa_link(string $telefono, string $mensaje, string $pais = 'CO'): string
{
    // Normaliza a E.164 simple
    $digits = preg_replace('/\D+/', '', $telefono);
    if ($pais === 'CO') {
        if (preg_match('/^57\d{10}$/', $digits)) {
            // ok
        } elseif (preg_match('/^\d{10}$/', $digits)) {
            $digits = '57' . $digits;
        } else {
            return '#'; // inválido
        }
    }
    return 'https://wa.me/' . $digits . '?text=' . rawurlencode($mensaje);
}

// Ejemplo de uso:
$nombre   = 'Andrés Jaramillo';
$doc      = '19785478';
$loginUrl = 'https://ceacloud.app/login/';
$mensaje  = "Hola $nombre, te registramos en la academia.\n" .
    "Usuario: $doc\n" .
    "Ingresa aquí para completar tu registro: $loginUrl\n" .
    "¡Gracias!";

$href = wa_link('3203920651', $mensaje); // -> https://wa.me/573203920651?text=...
?>
<a class="btn btn-success" target="_blank" href="<?= htmlspecialchars($href) ?>">
    <i class="ph ph-whatsapp-logo me-1"></i> Enviar por WhatsApp
</a>