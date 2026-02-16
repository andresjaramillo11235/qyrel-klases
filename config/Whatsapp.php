<?php
return [
    'provider' => 'meta', // 'meta' | 'twilio'

    'meta' => [
        'phone_number_id' => 'XXXXXXXXXXXXXXX',
        'access_token'    => 'EAA...',
        'timeout'         => 12,

        // Mapeo: clave interna -> plantilla de Meta (nombre/idioma y orden de parámetros del BODY)
        'meta_templates'  => [
            'onboarding_cliente' => [
                'name'        => 'onboarding_alumno', // nombre exacto en tu Manager
                'language'    => 'es',
                'body_params' => ['nombre', 'documento', 'login_url'],
            ],
            'matricula_pagada' => [
                'name'        => 'matricula_pagada',  // ejemplo
                'language'    => 'es',
                'body_params' => ['nombre', 'programa', 'valor', 'link_recibo'],
            ],
        ],
    ],

    // Twilio si lo usas…
    'twilio' => [ /* ... */],
];
