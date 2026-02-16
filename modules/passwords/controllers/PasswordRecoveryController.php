<?php
require_once '../modules/auditoria/controllers/AuditoriaController.php';
require_once '../config/DatabaseConfig.php';
$settings = include '../config/Settings.php';

class PasswordRecoveryController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    /**
     * 📌 Genera y almacena un token para la recuperación de contraseña
     */
    public function generarTokenRecuperacion()
    {
        $settings = include '../config/Settings.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $username = trim($_POST['username']);

            // Verificar si el usuario existe
            $query = "SELECT id, email FROM users WHERE username = :username";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                $_SESSION['error_message'] = "El usuario no existe.";
                header('Location: /recuperar-password/');
                exit;
            }

            $userId = $user['id'];
            $email = $user['email'];

            // 🔹 Generar un token seguro (usamos bin2hex para mayor seguridad)
            $token = bin2hex(random_bytes(32));
            $fechaExpiracion = date('Y-m-d H:i:s', strtotime('+30 minutes'));

            // Eliminar tokens previos de este usuario
            $queryDelete = "DELETE FROM recuperacion_tokens WHERE user_id = :user_id";
            $stmtDelete = $this->conn->prepare($queryDelete);
            $stmtDelete->bindParam(':user_id', $userId);
            $stmtDelete->execute();

            // Insertar nuevo token en la base de datos
            $queryInsert = "
                INSERT INTO recuperacion_tokens (user_id, token, fecha_expiracion, estado)
                VALUES (:user_id, :token, :fecha_expiracion, 'activo')
            ";

            $stmtInsert = $this->conn->prepare($queryInsert);
            $stmtInsert->bindParam(':user_id', $userId);
            $stmtInsert->bindParam(':token', $token);
            $stmtInsert->bindParam(':fecha_expiracion', $fechaExpiracion);

            if ($stmtInsert->execute()) {

                require_once '../modules/mail/MailController.php';

                $mailController = new MailController();

                // Plantilla de correo
                $plantilla = '../modules/mail/views/plantilla_recuperacion_password.php';

                // Variables a reemplazar en la plantilla
                $variables = [
                    'nombre_usuario' => $username,
                    'codigo_recuperacion' => $token,
                    'enlace_recuperacion' => $settings['dominio'] . "/update-password/$token",
                    'nombre_empresa' => $_SESSION['empresa_nombre']
                ];

                // Cargar y personalizar la plantilla
                try {
                    $contenidoHtml = $mailController->cargarPlantilla($plantilla, $variables);

                    // Enviar el correo
                    if ($mailController->sendMail($email, $username, "Recuperación de Contraseña", $contenidoHtml)) {
                        $_SESSION['success_message'] = "Se ha enviado un enlace de recuperación a su correo. Puede cerrar esta página.";
                        header('Location: /reset-password/');
                        exit;
                    } else {
                        error_log("Hubo un error al enviar el correo.");
                        $_SESSION['error_message'] = "Hubo un problema al enviar el correo.";
                    }

                } catch (Exception $e) {
                    error_log("Error: " . $e->getMessage());
                }
                
            } else {
                $_SESSION['error_message'] = "Error al generar el token de recuperación.";
            }

            header('Location: /recuperar-password/');
            exit;
        }
    }

    public function mostrarFormUsuario()
    {
        // Renderizar la vista de lista de ingresos
        ob_start();
        include '../modules/passwords/views/mostrar_form_usuario.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function mostrarFormModificar()
    {
        // Renderizar la vista de lista de ingresos
        ob_start();
        include '../modules/passwords/views/mostrar_form_modificar.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function cambiarPassword()
    {
        $_SESSION = array();

        // Establecer la zona horaria a Bogotá, Colombia
        date_default_timezone_set('America/Bogota');

        // Obtener la fecha y hora actual en Bogotá
        $fecha_actual = date('Y-m-d H:i:s');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $token = $_POST['token'] ?? '';
            $nuevoPassword = $_POST['new_password'] ?? '';
            $confirmarPassword = $_POST['confirm_password'] ?? '';

            // Validar que las contraseñas coincidan
            if ($nuevoPassword !== $confirmarPassword) {
                $_SESSION['error_message'] = 'Las contraseñas no coinciden.';
                header('Location: /update-password/' . urlencode($token));
                exit;
            }

            // Validar el formato de la contraseña
            if (strlen($nuevoPassword) < 5) {
                $_SESSION['error_message'] = 'La contraseña debe tener al menos 5 caracteres.';
                header('Location: /update-password/' . urlencode($token));
                exit;
            }

            // Buscar el token en la base de datos y verificar si aún es válido
            $query = "SELECT user_id, fecha_expiracion 
                FROM recuperacion_tokens 
                WHERE token = :token 
                AND fecha_expiracion > :fecha_actual 
                AND estado = 'activo'";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':token', $token);
            $stmt->bindParam(':fecha_actual', $fecha_actual);
            $stmt->execute();
            $resetData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$resetData) {
                $_SESSION['error_message'] = 'El token es inválido o ha expirado.';
                $url = "/update-password/" . urlencode($token);
                header('Location:' . $url);
                exit;
            }

            // Actualizar la contraseña
            $hashedPassword = password_hash($nuevoPassword, PASSWORD_DEFAULT);
            $queryUpdate = "UPDATE users SET password = :password WHERE id = :user_id";
            $stmtUpdate = $this->conn->prepare($queryUpdate);
            $stmtUpdate->bindParam(':password', $hashedPassword);
            $stmtUpdate->bindParam(':user_id', $resetData['user_id']);

            if ($stmtUpdate->execute()) {

                // Eliminar el token después de usarlo
                $queryDelete = "DELETE FROM recuperacion_tokens WHERE token = :token";
                $stmtDelete = $this->conn->prepare($queryDelete);
                $stmtDelete->bindParam(':token', $token);
                $stmtDelete->execute();

                // 🔎 Registrar auditoría
                $descripcion = "El usuario con ID " . $resetData['user_id'] . " cambió su contraseña.";
                $auditoriaController = new AuditoriaController();
                $auditoriaController->registrar($resetData['user_id'], 'Actualizar', 'Usuarios', $descripcion, $_SESSION['empresa_id']);

                $_SESSION['success_message'] = 'Contraseña actualizada correctamente. Inicia sesión con tu nueva contraseña.';
                header('Location: /login/');
                exit;
            } else {
                $_SESSION['error_message'] = 'Hubo un error al actualizar la contraseña.';
                header('Location: /reset-password?token=' . urlencode($token));
                exit;
            }
        }
    }

    public function verFormResetPassword()
    {
        // Renderizar la vista de lista de ingresos
        ob_start();
        include '../modules/passwords/views/form_reset_password.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }
}
