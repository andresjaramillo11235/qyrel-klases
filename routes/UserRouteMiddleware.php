<?php

// En UserRouteMiddleware.php
class UserRouteMiddleware
{
    public static function checkAuthentication($currentRoute)
    {
        // Define las rutas que cada tipo de usuario puede acceder
        $routesForCIO = [
            '/home/',
            '/login/',
            '/auth/',
            '/Assets/css/styless.css/'
        ];
        $routesForAdmin = [
            '/home/',
            '/login/',
            '/admission/',
            '/menu/',
            '/header/',
            '/auth/',
            '/process-reset-password/',
            '/process-new-password/',
            '/resetpassword/',
            '/updatePassword/',
            '/logout/',
            '/entidades_buscar/',
            '/obtenerDatosReferencia',
            '/entidades_guardar/',
            '/entidades_detalle/',
            '/entidades_crear/',
            '/getMunicipios/',
            '/getMunicipiosAndDepartamentos/',
            '/entidades_reporte/',
            '/documents/',
            '/documents-create/',
            '/history/{id}',
            '/usuario_consultaLog/',
            '/usuario_consultaLog/',
            '/usuario_detalleLog/',
            '/usuario_consultaLog/',
            '/usuario_consultar/',
            '/usuario_consultar/',
            '/usuario_consultar/',
            '/user/',
            '/usuario_aprobar/',
            '/crear_usuario/',
            '/procesar_crear_usuario/',
            '/usuario_reporte/',
            '/adopcion_consulta/',
            '/adopcion_soportes/',
            '/adopcion_actualizar/',
            '/adopcion_crear/',
            '/adopcion_crear_guardar/',
            '/adopcion_pdf/',
            '/generar_pdf/',
            '/consultaEstadoEntidad/',
            '/buscarEstadoEntidad/',
            '/trazabilidad_entidad/',
            '/modificar_estado/',
            '/lista_soportes/',
            '/cargar_soportes/', 
            '/eliminar_soportes/',
            '/myProfile/',
            '/updateMyProfile/',
            '/documents/',
            '/documents-create/',
            '/information/',
            '/creates/',
            '/usuario_consultar/update/',
            '/usuario_consultar/modificar/',
            '/usuario_consultar/updateactinact/',
            '/usuario_aprobar/update/',
            '/usuario_aprobar/updateactinact/',
            '/Assets/css/styless.css',
            '/Assets/css/custom-login.css',
            '/Assets/js/custom.js',
            '/Assets/img/logo-ipv6.png',
            '/Assets/img/solicitud-ipv6.png',
            '/Assets/img/reestablecer-ipv6.png',
            '/Assets/img/login-ipv6.png',
            '/Assets/img/logo-ipv6-mintic-1.png',
            '/Assets/img/logo-ipv6-mintic-2.png',
            '/Assets/img/icono-cambiar-password.png',
            '/Assets/img/govCo.png',
            '/Assets/img/img-fondo.png',
            '/Assets/img/icono-exportar.png',
            '/Assets/img/icono-editar.png',
            '/Assets/img/icono-inhabilitar.png',
            '/Assets/img/icono-habilitar.png',
            '/Assets/img/img_fondo_responsive.png',
            '/Assets/img/fondo_pdf.png',
            '/entidades_consultar/'
        ];
        $routesForGuests = [
            '/home/',
            '/login/',
            '/admission/', 
            '/menu/',
            '/header/',
            '/auth/',
            '/apply-admission/',
            '/logout/',
            '/reset-password/',
            '/update-password/([a-fA-F0-9]{64})',
            '/Assets/css/styless.css',
            '/Assets/css/custom-login.css',
            '/Assets/js/custom.js',
            '/Assets/img/logo-ipv6.png',
            '/Assets/img/solicitud-ipv6.png',
            '/Assets/img/reestablecer-ipv6.png',
            '/Assets/img/login-ipv6.png',
            '/Assets/img/logo-ipv6-mintic-1.png',
            '/Assets/img/logo-ipv6-mintic-2.png',
            '/Assets/img/icono-cambiar-password.png',
            '/Assets/img/govCo.png',
            '/Assets/img/img-fondo.png',
            '/Assets/img/icono-exportar.png',
            '/Assets/img/icono-editar.png',
            '/Assets/img/icono-inhabilitar.png',
            '/Assets/img/icono-habilitar.png',
            '/Assets/img/img_fondo_responsive.png',
            '/Assets/img/fondo_pdf.png',
        ];

        // Verifica si el usuario está logueado
        if (isset($_SESSION['user_type'])) {
            $currentUserType = $_SESSION['user_type'];

            // Verifica si el usuario tiene permiso para acceder a la ruta
            if ($currentUserType == 20 && !in_array($currentRoute, $routesForCIO)) {
                // Si el usuario es un CIO y la ruta no está en la lista de rutas permitidas para CIO, redirige a una página de error
                header("Location: /error/");
                exit();
            } elseif ($currentUserType == 10 && !in_array($currentRoute, $routesForAdmin)) {
                // Si el usuario es un administrador y la ruta no está en la lista de rutas permitidas para administradores, redirige a una página de error
                header("Location: /error/");
                exit();
            }
        } else {
            // Si el usuario no está logueado, verifica si la ruta actual está en la lista de rutas permitidas para usuarios no logueados
            if (!in_array($currentRoute, $routesForGuests)) {
                header("Location: /login/");
                exit();
            }
        }
    }
}
