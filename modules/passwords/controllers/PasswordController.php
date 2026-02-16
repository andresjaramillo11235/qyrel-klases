<?php
require_once '../config/DatabaseConfig.php';
require_once '../modules/auditoria/controllers/AuditoriaController.php';

class PasswordController
{
    private $conn;

    public function __construct()
    {
        $config = new DatabaseConfig();
        $this->conn = $config->getConnection();
    }

    /**
     * 🔹 Muestra el formulario de cambio de contraseña
     */
    public function mostrarFormulario()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login/');
            exit;
        }

        ob_start();
        include '../modules/passwords/views/cambiar_password.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    /**
     * 🔹 Procesa el cambio de contraseña (usuario autenticado)
     */
    public function cambiarPasswordAutenticado()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login/');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /cambiar-password/');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $nuevoPassword = $_POST['new_password'] ?? '';
        $confirmarPassword = $_POST['confirm_password'] ?? '';

        // ----------------------------------------------------------
        // 🔹 Validaciones backend (OBLIGATORIAS)
        // ----------------------------------------------------------
        if ($nuevoPassword !== $confirmarPassword) {
            $_SESSION['error_message'] = 'Las contraseñas no coinciden.';
            header('Location: /cambiar-password/');
            exit;
        }

        if (strlen(trim($nuevoPassword)) < 5) {
            $_SESSION['error_message'] = 'La contraseña debe tener al menos 5 caracteres.';
            header('Location: /cambiar-password/');
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 Hash de la nueva contraseña
        // ----------------------------------------------------------
        $hashedPassword = password_hash($nuevoPassword, PASSWORD_DEFAULT);

        // ----------------------------------------------------------
        // 🔹 Actualizar contraseña
        // (password_updated_at debe existir; si no, elimina esa línea)
        // ----------------------------------------------------------
        $query = "
            UPDATE users
            SET password = :password,
                updated_at = NOW()
            WHERE id = :id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':id', $userId);

        if ($stmt->execute()) {

            // ----------------------------------------------------------
            // 🔹 Auditoría
            // ----------------------------------------------------------
            $auditoriaController = new AuditoriaController();
            $auditoriaController->registrar(
                $userId,
                'Actualizar',
                'Usuarios',
                'Cambio de contraseña desde sesión activa',
                $_SESSION['empresa_id']
            );

            // ----------------------------------------------------------
            // 🔹 Cierre de sesión por seguridad
            // ----------------------------------------------------------
            session_destroy();

            session_start();
            $_SESSION['success_message'] = 'Contraseña actualizada correctamente. Inicia sesión nuevamente.';
            header('Location: /login/');
            exit;
        }

        // ----------------------------------------------------------
        // 🔹 Error general
        // ----------------------------------------------------------
        $_SESSION['error_message'] = 'Ocurrió un error al actualizar la contraseña.';
        header('Location: /cambiar-password/');
        exit;
    }
}
