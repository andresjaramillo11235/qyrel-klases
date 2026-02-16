<?php
// shared/utils/WhatsappLink.php

class WhatsappLink
{
    /** Genera el enlace wa.me usando una plantilla y variables. */
    public static function link(string $telefono, string $templateKey, array $vars = [], string $pais = 'CO'): string
    {
        $numero = self::normalizePhone($telefono, $pais);
        if ($numero === '') return '';

        $mensaje = self::renderTemplate($templateKey, $vars);
        return 'https://wa.me/' . $numero . '?text=' . rawurlencode($mensaje);
    }

    /** Genera directamente el botón <a> listo para usar (Bootstrap). */
    public static function button(string $telefono, string $templateKey, array $vars = [], string $label = 'Enviar WhatsApp', string $classes = 'btn btn-success', string $pais = 'CO'): string
    {
        $href = self::link($telefono, $templateKey, $vars, $pais);
        if ($href === '') return '';
        $label = htmlspecialchars($label, ENT_QUOTES, 'UTF-8');
        $href  = htmlspecialchars($href,  ENT_QUOTES, 'UTF-8');

        // Icono opcional (Phosphor)
        return '<a class="' . $classes . '" target="_blank" rel="noopener" href="' . $href . '">' .
            '<i class="ph ph-whatsapp-logo me-1"></i> ' . $label .
            '</a>';
    }

    /** Carga y procesa la plantilla ({{clave}}) con variables. */
    public static function renderTemplate(string $templateKey, array $vars): string
    {
        // Si te pasan un texto directo en vez de key:
        if (str_starts_with($templateKey, 'text:')) {
            $tpl = substr($templateKey, 5);
        } else {
            $templates = include '../config/WhatsappTemplates.php';
            $tpl = $templates[$templateKey] ?? '';
        }

        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($m) use ($vars) {
            $k = $m[1];
            return isset($vars[$k]) ? (string)$vars[$k] : '';
        }, (string)$tpl);
    }

    /** Normaliza a E.164 simple (por defecto CO). */
    public static function normalizePhone(string $raw, string $pais = 'CO'): string
    {
        $digits = preg_replace('/\D+/', '', (string)$raw);
        if ($digits === '') return '';

        if ($pais === 'CO') {
            if (preg_match('/^57\d{10}$/', $digits))  return $digits;       // ya incluye 57
            if (preg_match('/^\d{10}$/', $digits))    return '57' . $digits;  // antepone 57
            return ''; // inválido
        }
        // Países extra: agrega tus reglas si lo necesitas
        return $digits;
    }

    public function sendTemplateKey(string $toRaw, string $tplKey, array $vars): array
    {
        $to = $this->normalizePhone($toRaw);
        if (!$to) return ['ok' => false, 'error' => 'Teléfono inválido'];

        // Si no estás en provider meta, hacemos fallback a wa.me (opcional)
        if (($this->cfg['provider'] ?? 'meta') !== 'meta') {
            // No hay envío server-side; puede que quieras registrar el link
            return ['ok' => false, 'error' => 'Provider no soportado para envío automático'];
        }

        $meta = $this->cfg['meta'];
        $map  = $meta['meta_templates'][$tplKey] ?? null;
        if (!$map) return ['ok' => false, 'error' => 'Plantilla no mapeada en config/Whatsapp.php'];

        // Construye el array de parámetros en el orden requerido por la plantilla Meta
        $ordered = [];
        foreach (($map['body_params'] ?? []) as $k) {
            $ordered[] = ['type' => 'text', 'text' => (string)($vars[$k] ?? '')];
        }

        $components = [['type' => 'body', 'parameters' => $ordered]];
        return $this->metaSendTemplate($to, $map['name'], $map['language'] ?? 'es', $components);
    }
}
