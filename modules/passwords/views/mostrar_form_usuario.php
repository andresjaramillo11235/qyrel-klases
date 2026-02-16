<div class="auth-main v1">
    <div class="auth-wrapper">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">
                    <form action="/reset-password/" method="POST" id="resetPasswordForm">
                        <div class="text-center">
                            <img src="../assets/images/authentication/img-auth-fporgot-password.png" alt="images" class="img-fluid mb-3">
                            <h4 class="f-w-500 mb-1">Restablecer contraseña</h4>
                            <p class="mb-3"><a href="/login/" class="link-primary ms-1">Volver a la página de ingreso</a></p>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ingrese su nombre de usuario</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Nombre de usuario" required>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Enviar correo de restablecimiento</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Verificar si hay un mensaje de éxito en sesión y mostrar SweetAlert -->
<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                icon: 'success',
                title: 'Correo enviado',
                text: "<?= $_SESSION['success_message']; ?>",
                confirmButtonText: 'Cerrar',
                allowOutsideClick: false
            }).then(() => {
                document.getElementById('username').disabled = true;
                document.getElementById('submitBtn').disabled = true;
            });
        });
    </script>
    <?php unset($_SESSION['success_message']); // Limpiar el mensaje después de mostrarlo ?>
<?php endif; ?>
