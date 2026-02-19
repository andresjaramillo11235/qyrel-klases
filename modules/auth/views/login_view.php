<?php
if (isset($_SESSION['success_message'])) {
    $successMsg = json_encode($_SESSION['success_message'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire({icon:'success',title:'Exito!',text:$successMsg,confirmButtonText:'Aceptar'});});</script>";
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $errorMsg = json_encode($_SESSION['error_message'], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    echo "<script>document.addEventListener('DOMContentLoaded',function(){Swal.fire({icon:'error',text:$errorMsg,confirmButtonText:'Aceptar'});});</script>";
    unset($_SESSION['error_message']);
}
?>

<div class="auth-main v1">
    <div class="auth-wrapper">
        <div class="auth-form">
            <div class="card my-5">
                <div class="card-body">
                    <div class="text-center">
                        <img src="../assets/images/kqa.png" alt="KLASES.QYREL" class="img-fluid mb-3">
                        <h4 class="f-w-500 mb-1">Digite sus credenciales de acceso.</h4>
                    </div>

                    <form id="loginForm" action="/login/" method="post" novalidate>
                        <div class="mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Usuario" required>
                            <div class="invalid-feedback">Por favor ingrese su nombre de usuario</div>
                        </div>
                        <div class="mb-3">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                            <div class="invalid-feedback">Por favor ingrese su contraseña</div>
                        </div>

                        <div class="saprator my-3">
                            <span>CAPTCHA</span>
                        </div>

                        <div class="mb-3">
                            <?php if ($imgCaptcha): ?>
                                <img src="data:image/png;base64,<?= $imgCaptcha ?>" alt="CAPTCHA" class="mb-2">
                                <input type="text" name="captcha" placeholder="Escribe el texto de la imagen" class="form-control" required>
                                <small class="form-text text-muted">Escribe exactamente el texto de la imagen.</small>
                                <div class="invalid-feedback">Por favor escriba el texto que aparece en la imagen</div>
                            <?php else: ?>
                                <p style="color:red;">No se pudo generar CAPTCHA</p>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex mt-1 justify-content-between align-items-center">
                            <div class="form-check"></div>
                            <a href="/reset-password/" target="_blank">
                                <h6 class="text-secondary f-w-400 mb-0">¿Olvidó su contraseña?</h6>
                            </a>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary" id="submitBtn">Iniciar Sesión</button>
                        </div>
                    </form>
                    <script>
                        (() => {
                            'use strict';
                            const form = document.getElementById('loginForm');
                            form.addEventListener('submit', event => {
                                if (!form.checkValidity()) {
                                    event.preventDefault();
                                    event.stopPropagation();
                                }
                                form.classList.add('was-validated');
                            }, false);
                        })();
                    </script>
                </div>
            </div>
        </div>
    </div>
</div>
