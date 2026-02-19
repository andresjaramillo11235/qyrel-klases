
<?php
require_once '../config/DatabaseConfig.php';
require_once '../modules/auditoria/controllers/RegistroSesionesController.php';

class AuthController
{
    public function showLoginForm()
    {
        $titulo = 'Iniciar Sesión';
        $imgCaptcha = $this->generarCaptcha();
        ob_start();
        include '../modules/auth/views/login_view.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!isset($_POST['captcha']) || !isset($_SESSION['captcha'])) {
                $_SESSION['error_message'] = 'Captcha no enviado o expirado. Intenta de nuevo.';
                header('Location: /login/');
                exit;
            }
            $captchaInput = strtoupper(trim((string)$_POST['captcha']));
            $captchaSession = strtoupper((string)$_SESSION['captcha']);
            unset($_SESSION['captcha']);
            if (!hash_equals($captchaSession, $captchaInput)) {
                $_SESSION['error_message'] = 'Captcha incorrecto.';
                header('Location: /login/');
                exit;
            }
            $sessionController = new RegistroSesionesController();
            $username = $_POST['username'];
            $password = $_POST['password'];
            $config = new DatabaseConfig();
            $conn = $config->getConnection();
            $query = "SELECT users.*, empresas.nombre as empresa_nombre, empresas.logo as empresa_logo, roles.name as rol_nombre FROM users JOIN empresas ON users.empresa_id = empresas.id JOIN roles ON users.role_id = roles.id WHERE users.username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // Validación de estado del usuario
            if ($user && $user['status'] == 0) {
                $_SESSION['error_message'] = "El usuario está inactivo.";
                header('Location: /login/');
                exit;
            }
            if ($user && password_verify($password, $user['password'])) {
                // Verificar si el usuario ya tiene una sesión activa
                $queryCheckSession = "SELECT * FROM registro_sesiones WHERE user_id = :user_id AND status = 'ACTIVO'";
                $stmtCheckSession = $conn->prepare($queryCheckSession);
                $stmtCheckSession->bindParam(':user_id', $user['id']);
                $stmtCheckSession->execute();
                $activeSession = $stmtCheckSession->fetch(PDO::FETCH_ASSOC);
                $activeSession = false;
                if ($activeSession) {
                    $_SESSION['error_message'] = "Ya tienes una sesión activa. Por favor, cierra la sesión anterior antes de iniciar una nueva.";
                    header('Location: /login/');
                    exit;
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['user_role'] = $user['role_id'];
                    $_SESSION['rol_nombre'] = $user['rol_nombre'];
                    $_SESSION['empresa_id'] = $user['empresa_id'];
                    $_SESSION['empresa_nombre'] = $user['empresa_nombre'];
                    $_SESSION['empresa_logo'] = $user['empresa_logo'];
                    $_SESSION['user_nombre'] = $user['first_name'] . ' ' . $user['last_name'];
                    $_SESSION['email'] = $user['email'];
                    // Verificar si el usuario es un administrativo
                    if (in_array($user['rol_nombre'], ['ASISOP', 'ASISPROG', 'ADMIN'])) {
                        $queryAdministrativo = "SELECT nombres, apellidos, correo, foto FROM administrativos WHERE id = :administrativo_id";
                        $stmtAdministrativo = $conn->prepare($queryAdministrativo);
                        $stmtAdministrativo->bindParam(':administrativo_id', $user['administrativo_id']);
                        $stmtAdministrativo->execute();
                        $administrativo = $stmtAdministrativo->fetch(PDO::FETCH_ASSOC);
                        if ($administrativo) {
                            $_SESSION['administrativo_nombres'] = $administrativo['nombres'];
                            $_SESSION['administrativo_apellidos'] = $administrativo['apellidos'];
                            $_SESSION['administrativo_correo'] = $administrativo['correo'];
                            $_SESSION['administrativo_foto'] = $administrativo['foto'];
                            $_SESSION['administrativo_id'] = $user['administrativo_id'];
                        } else {
                            $_SESSION['error_message'] = "Administrativo no encontrado.";
                            header('Location: /login/');
                            exit;
                        }
                    }
                    // Verificar si el usuario es un instructor
                    if ($user['role_id'] === 6) {
                        $queryInstructor = "SELECT id, nombres, apellidos, foto FROM instructores WHERE id = :id";
                        $stmtInstructor = $conn->prepare($queryInstructor);
                        $stmtInstructor->bindParam(':id', $user['instructor_id']);
                        $stmtInstructor->execute();
                        $instructor = $stmtInstructor->fetch(PDO::FETCH_ASSOC);
                        if ($instructor) {
                            $_SESSION['instructor_id'] = $instructor['id'];
                            $_SESSION['instructor_nombres'] = $instructor['nombres'];
                            $_SESSION['instructor_apellidos'] = $instructor['apellidos'];
                            $_SESSION['instructor_foto'] = $instructor['foto'];
                        } else {
                            $_SESSION['error_message'] = "Instructor no encontrado.";
                            header('Location: /login/');
                            exit;
                        }
                    }

                    if ((int)$user['role_id'] === 5) {

                        $queryEstudiante = "SELECT id, nombres, apellidos, foto, estado 
                        FROM estudiantes 
                        WHERE id = :estudiante_id";
                        $stmtEstudiante = $conn->prepare($queryEstudiante);
                        $stmtEstudiante->bindValue(':estudiante_id', (int)$user['estudiante_id'], PDO::PARAM_INT);
                        $stmtEstudiante->execute();
                        $estudiante = $stmtEstudiante->fetch(PDO::FETCH_ASSOC);

                        if (!$estudiante) {
                            $_SESSION['error_message'] = "Estudiante no encontrado.";
                            header('Location: /login/');
                            exit;
                        }

                        // Variables de sesión del estudiante
                        $_SESSION['estudiante_id']        = (int)$user['estudiante_id'];
                        $_SESSION['estudiante_nombres']   = $estudiante['nombres'];
                        $_SESSION['estudiante_apellidos'] = $estudiante['apellidos'];
                        $_SESSION['estudiante_foto']      = $estudiante['foto'] ?? null;
                        $_SESSION['estudiante_estado']    = (string)$estudiante['estado'];
                    }

                    $sessionController->registrarIngreso($_SESSION['user_id'], $_SESSION['empresa_id']);
                    header('Location: /home/');
                    exit;
                }
            } else {
                $_SESSION['error_message'] = "Credenciales incorrectas.";
                header('Location: /login/');
                exit;
            }
        }
    }

    public function logout()
    {
        $sessionController = new RegistroSesionesController();
        $sessionController->registrarSalida($_SESSION['user_id']);
        session_unset();
        session_destroy();
        header('Location: /login/');
        exit;
    }

    public function generarCaptcha()
    {
        $captchaText = substr(str_shuffle("abcdefghijkmnopqrstuvwxyz23456789"), 0, 6);
        $_SESSION['captcha'] = $captchaText;
        $img = imagecreatetruecolor(120, 40);
        $bg = imagecolorallocate($img, 255, 255, 255);
        $textColor = imagecolorallocate($img, 0, 0, 0);
        imagefilledrectangle($img, 0, 0, 120, 40, $bg);
        $fontPath = __DIR__ . '/../views/Roboto-Font.ttf';
        if (!file_exists($fontPath)) {
            error_log("Captcha: fuente no encontrada en $fontPath");
            return null;
        }
        imagettftext($img, 20, 0, 10, 30, $textColor, $fontPath, $captchaText);
        ob_start();
        imagepng($img);
        $imageData = ob_get_clean();
        imagedestroy($img);
        return base64_encode($imageData);
    }
}
