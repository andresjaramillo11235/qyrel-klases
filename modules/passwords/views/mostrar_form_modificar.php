<!-- Verifica si hay un token en la URL -->
<?php
// Obtener la ruta actual
$ruta = trim($_SERVER['REQUEST_URI'], '/');

// Dividir la ruta por "/"
$partesRuta = explode('/', $ruta);

// Buscar el token en la última parte de la URL
$token = end($partesRuta);

// Validar que el token tenga el formato correcto
if (!$token || !preg_match('/^[a-f0-9]{64}$/', $token)) {
    header("Location: /login/");
    exit;
}
?>

<?php

if (isset($_SESSION['error_message'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: '" . $_SESSION['error_message'] . "',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>";

    // Eliminar el mensaje de sesión para que no se muestre nuevamente
    unset($_SESSION['error_message']);
}
?>

<div class="auth-main v1">
    <div class="auth-wrapper">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">

                    <form action="/procesar-cambio-password/" method="POST" id="resetPasswordForm">

                        <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                        <div class="text-center">
                            <img src="../assets/images/authentication/img-auth-reset-password.png" alt="images" class="img-fluid mb-3">
                            <h4 class="f-w-500 mb-1">Cambiar contraseña</h4>
                            <p class="mb-3"><a href="/login/" class="link-primary ms-1">Volver a la página de ingreso</a></p>
                        </div>

                        <!-- Nueva contraseña -->
                        <div class="mb-3 position-relative">
                            <label class="form-label">Ingrese la nueva contraseña</label>
                            <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Nueva contraseña" required>
                            <small class="d-block text-end mt-1">
                                <a href="#" class="toggle-password" data-target="new_password">
                                    <i class="ti ti-eye"></i> <span>Mostrar contraseña</span>
                                </a>
                            </small>
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="mb-3 position-relative">
                            <label class="form-label">Confirme la nueva contraseña</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirme la nueva contraseña" required>
                            <small class="d-block text-end mt-1">
                                <a href="#" class="toggle-password" data-target="confirm_password">
                                    <i class="ti ti-eye"></i> <span>Mostrar contraseña</span>
                                </a>
                            </small>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Cambiar contraseña</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validaciones con JavaScript -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("resetPasswordForm");
        const passwordField = document.getElementById("new_password");
        const confirmPasswordField = document.getElementById("confirm_password");

        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Evitar que se envíe el formulario sin validaciones

            const password = passwordField.value.trim();
            const confirmPassword = confirmPasswordField.value.trim();

            // Validar longitud mínima
            if (password.length < 5) {
                Swal.fire({
                    icon: "warning",
                    title: "Contraseña demasiado corta",
                    text: "La contraseña debe tener al menos 5 caracteres."
                });
                return;
            }

            // Validar coincidencia de contraseñas
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: "error",
                    title: "Las contraseñas no coinciden",
                    text: "Asegúrate de que ambas contraseñas sean iguales."
                });
                return;
            }

            // Enviar formulario si todo está correcto
            form.submit();
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".toggle-password").forEach(link => {
            link.addEventListener("click", function(event) {
                event.preventDefault(); // Evitar que el enlace haga scroll hacia arriba

                const targetId = this.getAttribute("data-target");
                const inputField = document.getElementById(targetId);
                const icon = this.querySelector("i");
                const text = this.querySelector("span");

                if (inputField.type === "password") {
                    inputField.type = "text";
                    text.innerHTML = " Ocultar contraseña";
                    icon.classList.replace("ti-eye", "ti-eye-off");
                } else {
                    inputField.type = "password";
                    text.innerHTML = " Mostrar contraseña";
                    icon.classList.replace("ti-eye-off", "ti-eye");
                }
            });
        });
    });
</script>