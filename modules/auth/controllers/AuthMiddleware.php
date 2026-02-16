<?php

class AuthMiddleware
{
    public static function handle()
    {
        // Patrones para archivos estáticos
        $staticResourcePatterns = [
            '/assets/fonts/.*',
            '/assets/fonts/fontawesome/.*',
            '/assets/fonts/tabler/.*',
            '/assets/fonts/feather/.*',
            '/assets/fonts/material/.*',
            '/assets/fonts/phosphor/.*',
            '/assets/fonts/phosphor/duotone/.*',
            '/assets/css/.*',
            '/assets/css/plugins.*',
            '/assets/js/.*',
            '/assets/js/plugins/.*',
            '/assets/js/fonts/.*',
            '/assets/js/pages/.*',
            '/assets/images/.*',
            '/assets/images/authentication/.*',
            '/assets/images/user/.*',
            '/assets/images/pages/.*',
            '/assets/images/application/.*',
            '/assets/images/admin/.*',
            '/assets/images/admin/.*',
            '/assets/images/component/.*',
            '/assets/images/customizer/.*',
            '/assets/images/layout/.*',
            '/update-password/',
        ];

        // Obtener la ruta solicitada
        $currentRoute = $_SERVER['REQUEST_URI'];

        // Verificar si la ruta actual coincide con alguna de las rutas de recursos estáticos
        foreach ($staticResourcePatterns as $pattern) {
            if (preg_match('|^' . $pattern . '|', $currentRoute)) {
                // Si es un recurso estático, permitir el acceso
                return;
            }
        }

        //Verificar si el usuario está autenticado
        if (!isset($_SESSION['user_id'])) {
            //$_SESSION['error_message'] = "Debes iniciar sesión para acceder a esta página.";
            header('Location: /login/');
            exit;
        }
    }
}
