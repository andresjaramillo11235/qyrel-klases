<?php

require_once '../modules/auditoria/controllers/RegistroSesionesController.php';

class Router
{
    private $routes = [];
    private $middleware = [];

    public function middleware($route, $callback)
    {
        $this->middleware[$route] = $callback;
    }

    public function get($route, $action)
    {
        $this->addRoute('GET', $route, $action);
    }

    public function post($route, $action)
    {
        $this->addRoute('POST', $route, $action);
    }

    public function getCss($route, $filePath)
    {
        $this->addRoute('GET', $route, function () use ($filePath) {
            if (file_exists($filePath)) {
                header('Content-Type: text/css');
                echo file_get_contents($filePath);
            } else {
                http_response_code(404);
                echo "Archivo no encontrado";
            }
        });
    }

    public function getImg($route, $directory)
    {
        $this->addRoute('GET', $route, function ($params) use ($directory) {
            $filename = $params['filename'];
            $filePath = $directory . '/' . $filename;

            if (file_exists($filePath)) {
                $fileInfo = pathinfo($filePath);
                $extension = strtolower($fileInfo['extension']);

                switch ($extension) {
                    case 'jpg':
                    case 'jpeg':
                        header('Content-Type: image/jpeg');
                        break;
                    case 'png':
                        header('Content-Type: image/png');
                        break;
                    case 'gif':
                        header('Content-Type: image/gif');
                        break;
                    default:
                        header('Content-Type: application/octet-stream');
                        break;
                }

                echo file_get_contents($filePath);
            } else {
                http_response_code(404);
                echo "Archivo no encontrado";
            }
        });
    }

    // ==================== Modificación para manejo de directorios ====================
    public function getDirectory($route, $directory)
    {
        $this->addRoute('GET', $route . '/{filename}', function ($params) use ($directory) {
            $filename = $params['filename'];
            $filePath = $directory . '/' . $filename;

            if (file_exists($filePath)) {
                $fileInfo = pathinfo($filePath);
                $extension = strtolower($fileInfo['extension']);

                // Manejar tipos MIME según la extensión del archivo
                switch ($extension) {
                    case 'css':
                        header('Content-Type: text/css');
                        break;
                    case 'js':
                        header('Content-Type: application/javascript');
                        break;
                    case 'jpg':
                    case 'jpeg':
                        header('Content-Type: image/jpeg');
                        break;
                    case 'png':
                        header('Content-Type: image/png');
                        break;
                    case 'gif':
                        header('Content-Type: image/gif');
                        break;
                    case 'svg':
                        header('Content-Type: image/svg+xml');
                        break;
                    default:
                        header('Content-Type: application/octet-stream');
                        break;
                }

                echo file_get_contents($filePath);
            } else {
                http_response_code(404);
                echo "Archivo no encontrado";
            }
        });
    }
    // ==================== Fin de la modificación ====================

    private function addRoute($method, $route, $action)
    {
        $routeData = [
            'method' => $method,
            'route' => $route,
            'action' => $action,
            'params' => []
        ];

        preg_match_all('/\{([a-zA-Z_]+)\}/', $route, $matches);


        if (!empty($matches[1])) {
            $routeData['params'] = $matches[1];
            $route = preg_replace('/\{[a-zA-Z_]+\}/', '([a-zA-Z0-9_\-\.]+)', $route);
        }

        // Asegurarse de que las rutas sin parámetros coincidan correctamente
        $route = rtrim($route, '/'); // Eliminar barra al final
        $routeData['pattern'] = '/^' . str_replace('/', '\/', $route) . '\/?$/';
        $this->routes[] = $routeData;

        // ==================== Depuración ====================
        //echo "<pre>Ruta añadida: " . print_r($routeData, true) . "</pre>";
    }

    public function handle($currentRoute, $requestMethod)
    {
        // ==================== Depuración ====================
        //echo "<pre>Middlewares registrados: " . print_r($this->middleware, true) . "</pre>";

        // Ejecutar middlewares registrados para la ruta actual
        foreach ($this->middleware as $routePattern => $middlewareCallback) {
            if (preg_match($routePattern, $currentRoute)) {
                call_user_func($middlewareCallback);
                break;
            }
        }

        $campo_obligatorio = "Campo obligatorio";

        // #########################################################################################
        // TIEMPO DE INACTIVIDAD ###################################################################


        // --- 1) Tiempo de inactividad (3h) ---
        $tiempoInactividad = 3 * 60 * 60; // 10800

        // (SUGERENCIA) Pon esto en el bootstrap ANTES de session_start():
        // ini_set('session.gc_maxlifetime', (string)$tiempoInactividad);
        // session_set_cookie_params([
        //   'lifetime' => $tiempoInactividad,
        //   'path'     => '/',
        //   'secure'   => !empty($_SERVER['HTTPS']),
        //   'httponly' => true,
        //   'samesite' => 'Lax',
        // ]);

        // --- 2) Rutas públicas donde NO aplicamos timeout (login, recuperar, etc.)
        $publicRoutePatterns = [
            '#^/login/?$#',
            '#^/password/forgot/?$#',
            '#^/password/reset/?$#',
            // agrega las tuyas si aplica
        ];

        $skipTimeout = false;
        foreach ($publicRoutePatterns as $pat) {
            if (preg_match($pat, $currentRoute)) {
                $skipTimeout = true;
                break;
            }
        }

        if (!$skipTimeout && isset($_SESSION['user_id'])) {
            if (isset($_SESSION['tiempoUltimaAccion'])) {
                $tiempoInactivo = time() - (int)$_SESSION['tiempoUltimaAccion'];

                if ($tiempoInactivo >= $tiempoInactividad) {
                    // 3) Registrar salida si hay user_id (sin bloquear si falla)
                    try {
                        $sessionController = new RegistroSesionesController();
                        $sessionController->registrarSalida($_SESSION['user_id']);
                    } catch (Throwable $e) {
                        error_log('[timeout] registrarSalida: ' . $e->getMessage());
                    }

                    // 4) Limpiar sesión y cookie
                    $_SESSION = [];
                    if (ini_get('session.use_cookies')) {
                        $p = session_get_cookie_params();
                        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'] ?? '', $p['secure'] ?? false, $p['httponly'] ?? true);
                    }
                    @session_destroy();

                    header('Location: /login/?timeout=1');
                    exit;
                }
            }

            // Actualizar última acción SOLO si es usuario autenticado y no ruta pública
            $_SESSION['tiempoUltimaAccion'] = time();
        } else {
            // Si no hay user_id, no tocamos el tiempo (evita “revivir” sesión en login)
        }


        /*
        $tiempoInactividad = 3 * 60 * 60; // 10800

        if (isset($_SESSION['tiempoUltimaAccion'])) {

            $tiempoInactivo = time() - $_SESSION['tiempoUltimaAccion'];

            if ($tiempoInactivo > $tiempoInactividad) {
                // Verificar si el user_id está correctamente establecido
                if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                    // Crear instancia del controlador de sesiones
                    $sessionController = new RegistroSesionesController();
                    $sessionController->registrarSalida($_SESSION['user_id']);
                    session_unset();
                    session_destroy();
                    header("Location: /login/");
                    exit;
                } else {
                    error_log("Error: user_id no está definido en la sesión.");
                }
            }
        }

        // Actualizar el tiempo de la última acción del usuario
        $_SESSION['tiempoUltimaAccion'] = time();
*/
        $routeHandled = false;

        // ==================== Depuración ====================
        //echo "<pre>Rutas registradas: " . print_r($this->routes, true) . "</pre>";
        //echo "Ruta actual: $currentRoute\n";
        //echo "Método de solicitud: $requestMethod\n";

        foreach ($this->routes as $route) {

            if ($route['method'] === $requestMethod) {

                if (preg_match($route['pattern'], $currentRoute, $matches)) {

                    array_shift($matches);

                    // ==================== Depuración ====================
                    //echo "<pre>Ruta coincidente encontrada: " . print_r($route, true) . "</pre>";
                    //echo "<pre>Parámetros coincidentes: " . print_r($matches, true) . "</pre>";

                    if (is_string($route['action'])) {

                        list($controllerInfo, $methodName) = explode('@', $route['action']);
                        //$controllerPath = __DIR__ . '/../' . str_replace('\\', '/', $controllerInfo) . '.php';
                        $controllerPath = '../' . str_replace('\\', '/', $controllerInfo) . '.php';

                        // ==================== Depuración ====================
                        //echo "<pre>Ruta del controlador: $controllerPath</pre>";

                        if (!file_exists($controllerPath)) {
                            die("Archivo del controlador no encontrado: $controllerPath");
                        }

                        require_once $controllerPath;

                        //echo "<pre>controllerInfo => $controllerInfo</pre>";

                        // Obtener el nombre de la clase del controlador correctamente
                        $controllerClass = strrchr($controllerInfo, '\\');

                        //echo "<pre>controllerClass => $controllerClass </pre>";

                        $controllerClass = ltrim($controllerClass, '\\');

                        //$controllerClass = basename($controllerInfo);

                        // ==================== Depuración ====================
                        //echo "<pre>Clase del controlador: $controllerClass</pre>";

                        // Verificar si la clase del controlador existe
                        if (!class_exists($controllerClass, false)) {
                            //echo "<pre>Definiciones de clases cargadas: " . print_r(get_declared_classes(), true) . "</pre>";
                            die("Clase del controlador no encontrada: $controllerClass");
                        }

                        $controller = new $controllerClass();
                    } elseif ($route['action'] instanceof Closure) {
                        $controller = $route['action'];
                        $methodName = null;
                    }

                    $params = [];
                    foreach ($route['params'] as $index => $paramName) {
                        $params[$paramName] = $matches[$index];
                    }

                    // ==================== Depuración ====================
                    //echo "Parámetros finales: " . print_r($params, true) . "\n";

                    if ($methodName) {
                        call_user_func_array([$controller, $methodName], $matches);
                    } else {
                        call_user_func($controller, $params);
                    }

                    $routeHandled = true;
                    return;
                }
            }
        }

        if (!$routeHandled) {
            echo "Ruta no encontrada";
        }
    }
}
