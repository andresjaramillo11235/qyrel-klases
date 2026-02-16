<?php $routes = include '../config/Routes.php'; ?>

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
    unset($_SESSION['error_message']);
}

if (isset($_SESSION['success_message'])) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '" . $_SESSION['success_message'] . "',
                confirmButtonText: 'Aceptar'
            });
        });
    </script>";
    unset($_SESSION['success_message']);
}
?>

<div class="auth-main v1">
    <div class="auth-wrapper">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">

                    <form action="<?php echo $routes['update_password']; ?>"
                          method="POST"
                          id="changePasswordForm"
                          autocomplete="off">

                        <div class="text-center mb-4">
                            <h4 class="f-w-500 mb-1">Cambiar contraseña</h4>
                            <p class="mb-3">
                                Define una nueva contraseña para tu cuenta
                            </p>
                        </div>

                        <!-- Nueva contraseña -->
                        <div class="mb-3 position-relative">
                            <label class="form-label">Nueva contraseña</label>
                            <input type="password"
                                   class="form-control"
                                   id="new_password"
                                   name="new_password"
                                   placeholder="Nueva contraseña"
                                   required
                                   autocomplete="new-password">
                            <small class="d-block text-end mt-1">
                                <a href="#" class="toggle-password" data-target="new_password">
                                    <i class="ti ti-eye"></i>
                                    <span>Mostrar contraseña</span>
                                </a>
                            </small>
                        </div>

                        <!-- Confirmar contraseña -->
                        <div class="mb-3 position-relative">
                            <label class="form-label">Confirmar nueva contraseña</label>
                            <input type="password"
                                   class="form-control"
                                   id="confirm_password"
                                   name="confirm_password"
                                   placeholder="Confirmar nueva contraseña"
                                   required
                                   autocomplete="new-password">
                            <small class="d-block text-end mt-1">
                                <a href="#" class="toggle-password" data-target="confirm_password">
                                    <i class="ti ti-eye"></i>
                                    <span>Mostrar contraseña</span>
                                </a>
                            </small>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                Cambiar contraseña
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Validaciones JS -->
<script>
document.addEventListener("DOMContentLoaded", function() {

    const form = document.getElementById("changePasswordForm");
    const newPassword = document.getElementById("new_password");
    const confirmPassword = document.getElementById("confirm_password");

    form.addEventListener("submit", function(event) {
        event.preventDefault();

        const pwd = newPassword.value;
        const confirm = confirmPassword.value;

        // 1. No vacía / solo espacios
        if (!pwd.trim()) {
            Swal.fire({
                icon: "warning",
                title: "Contraseña inválida",
                text: "La contraseña no puede estar vacía."
            });
            return;
        }

        // 2. Sin espacios al inicio o final
        if (pwd !== pwd.trim()) {
            Swal.fire({
                icon: "warning",
                title: "Espacios no permitidos",
                text: "La contraseña no debe iniciar ni terminar con espacios."
            });
            return;
        }

        // 3. Longitud mínima
        if (pwd.length < 5) {
            Swal.fire({
                icon: "warning",
                title: "Contraseña muy corta",
                text: "La contraseña debe tener al menos 5 caracteres."
            });
            return;
        }

        // 4. Coincidencia
        if (pwd !== confirm) {
            Swal.fire({
                icon: "error",
                title: "Las contraseñas no coinciden",
                text: "Verifica que ambas contraseñas sean iguales."
            });
            return;
        }

        // 5. Contraseñas triviales (UX)
        const passwordsDebiles = ['12345', 'password', 'admin', 'qwerty'];

        if (passwordsDebiles.includes(pwd.toLowerCase())) {
            Swal.fire({
                icon: "warning",
                title: "Contraseña insegura",
                text: "Elige una contraseña menos obvia."
            });
            return;
        }

        form.submit();
    });

    // Mostrar / ocultar contraseña
    document.querySelectorAll(".toggle-password").forEach(link => {
        link.addEventListener("click", function(event) {
            event.preventDefault();

            const targetId = this.getAttribute("data-target");
            const input = document.getElementById(targetId);
            const icon = this.querySelector("i");
            const text = this.querySelector("span");

            if (input.type === "password") {
                input.type = "text";
                text.innerHTML = " Ocultar contraseña";
                icon.classList.replace("ti-eye", "ti-eye-off");
            } else {
                input.type = "password";
                text.innerHTML = " Mostrar contraseña";
                icon.classList.replace("ti-eye-off", "ti-eye");
            }
        });
    });

});
</script>
