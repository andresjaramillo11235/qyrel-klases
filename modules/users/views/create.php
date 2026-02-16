<?php if (isset($_SESSION['error_create'])) : ?>
    <script>
        Swal.fire({
            icon: 'error',
            text: '<?php echo $_SESSION['error_create'];
                    unset($_SESSION['error_create']); ?>'
        });
    </script>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/users/">Usuarios</a></li>
                    <li class="breadcrumb-item" aria-current="page">Nuevo usuario</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h5>Información usuario administrador de la empresa</h5>
            </div>

            <div class="card-body">
                <form method="post" class="validate-me" id="validate-me" action="/users-store/">
                    <input type="hidden" name="empresa_id" value="<?= $empresaId ?>">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">Nombres <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= isset($_SESSION['form_data']['first_name']) ? htmlspecialchars($_SESSION['form_data']['first_name']) : '' ?>" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Apellidos <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= isset($_SESSION['form_data']['last_name']) ? htmlspecialchars($_SESSION['form_data']['last_name']) : '' ?>" required>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuario <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= isset($_SESSION['form_data']['username']) ? htmlspecialchars($_SESSION['form_data']['username']) : '' ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="email" class="form-control" id="email" name="email" data-bouncer-message="Correo electrónico inválido." value="<?= isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_documento" class="form-label">Tipo de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                    <?php foreach ($paramTiposDocumentos as $tipo) : ?>
                                        <option value="<?= $tipo['id'] ?>" <?= isset($form_data['tipo_documento']) && $form_data['tipo_documento'] == $tipo['id'] ? 'selected' : '' ?>>
                                            <?= $tipo['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_documento" class="form-label">Número de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="number" class="form-control" id="numero_documento" name="numero_documento"
                                    value="<?= isset($form_data['numero_documento']) ? htmlspecialchars($form_data['numero_documento']) : '' ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña <i class="ph-duotone ph-asterisk"></i></label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="ph-duotone ph-eye-slash"></i>
                                    </button>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                                <!-- Mensaje de validación debajo del input-group -->
                                <div class="invalid-feedback">
                                    Por favor ingrese una contraseña.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirmar Contraseña <i class="ph-duotone ph-asterisk"></i></label>
                                <div class="input-group">
                                    <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirm">
                                        <i class="ph-duotone ph-eye-slash"></i>
                                    </button>
                                    <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                                </div>
                                <!-- Mensaje de validación debajo del input-group -->
                                <div class="invalid-feedback">
                                    Por favor confirme su contraseña.
                                </div>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono móvil <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" name="phone" id="phone" pattern="^\d{10}$" value="<?= isset($_SESSION['form_data']['phone']) ? htmlspecialchars($_SESSION['form_data']['phone']) : '' ?>" required>
                                <small class="form-text text-muted">Debe contener 10 dígitos.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">Dirección <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="address" name="address" value="<?= isset($_SESSION['form_data']['address']) ? htmlspecialchars($_SESSION['form_data']['address']) : '' ?>" required>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="submit" class="btn btn-primary" value="Enviar">
                        </div>
                </form>
            </div>

        </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
<!-- [ Main Content ] end -->


<script src="../assets/js/plugins/bouncer.min.js"></script>
<script src="../assets/js/pages/form-validation.js"></script>
<script>
    // Convertir a mayúsculas mientras el usuario escribe
    document.getElementById('email').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('first_name').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('last_name').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('address').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Validación de solo números en el campo de teléfono móvil
    document.getElementById('phone').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
</script>

<script>
    // Funcionalidad para mostrar/ocultar contraseña
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const togglePasswordConfirm = document.querySelector('#togglePasswordConfirm');
    const passwordConfirm = document.querySelector('#password_confirm');

    togglePassword.addEventListener('click', function() {
        // Cambiar el tipo de input entre "password" y "text"
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        // Cambiar el ícono
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });

    togglePasswordConfirm.addEventListener('click', function() {
        const type = passwordConfirm.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordConfirm.setAttribute('type', type);
        this.querySelector('i').classList.toggle('bi-eye');
        this.querySelector('i').classList.toggle('bi-eye-slash');
    });
</script>


<?php
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}
?>