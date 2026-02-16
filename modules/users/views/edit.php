
<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/users/">Usuarios</a></li>
                    <li class="breadcrumb-item" aria-current="page">Editar usuario</li>
                </ul>
            </div>
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0">Editar usuario</h2>
                </div>
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
                <h5>Información básica</h5>
            </div>

            <div class="card-body">
                <form method="post" class="validate-me" id="edit-user-form" action="/usersedit/<?= $user['id'] ?>" data-validate>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nombre de usuario <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="username" name="username" value="<?= $user['username'] ?>" disabled>
                                <input type="hidden" name="username" value="<?= $user['username'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= strtolower($user['email']) ?>" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Contraseña <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="password" class="form-control" id="password" name="password">
                                <small class="form-text text-muted">Dejar en blanco para mantener la contraseña actual</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="first_name" class="form-label">Nombres <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?= strtoupper($user['first_name']) ?>" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="last_name" class="form-label">Apellidos <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?= strtoupper($user['last_name']) ?>" oninput="this.value = this.value.toUpperCase()" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Teléfono móvil <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="phone" name="phone" value="<?= $user['phone'] ?>" pattern="^\d{10}$" required>
                                <small class="form-text text-muted">Debe contener 10 dígitos.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="address" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?= strtoupper($user['address']) ?>" oninput="this.value = this.value.toUpperCase()">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Estado</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" <?= $user['status'] == 1 ? 'selected' : '' ?>>ACTIVO</option>
                                    <option value="0" <?= $user['status'] == 0 ? 'selected' : '' ?>>INACTIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role_id" class="form-label">Rol</label>
                                <select class="form-control" id="role_id" name="role_id">
                                    <?php foreach ($roles as $role) : ?>
                                        <option value="<?= $role['id'] ?>" <?= $user['role_id'] == $role['id'] ? 'selected' : '' ?>><?= $role['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php if ($userUtils->isSuperAdmin($_SESSION['user_id'])) : ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="empresa_id" class="form-label">Empresa</label>
                                    <select class="form-control" id="empresa_id" name="empresa_id">
                                        <?php foreach ($empresas as $empresa) : ?>
                                            <option value="<?= $empresa['id'] ?>" <?= $user['empresa_id'] == $empresa['id'] ? 'selected' : '' ?>><?= $empresa['nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php else : ?>
                            <input type="hidden" name="empresa_id" value="<?= $_SESSION['empresa_id'] ?>">
                        <?php endif; ?>

                        <div class="col-md-12 text-end">
                            <input type="submit" class="btn btn-primary" value="Actualizar">
                            <a href="/users/" class="btn btn-secondary">Cancelar</a>
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
document.addEventListener('DOMContentLoaded', function() {
    var bouncer = new Bouncer('[data-validate]');

    document.addEventListener(
        'bouncerFormValid',
        function (event) {
            event.preventDefault(); // Prevenir el envío predeterminado
            var form = event.target;
            
            if (bouncer.validateAll(form)) {
                // Recopilar datos del formulario
                var formData = new FormData(form);

                // Enviar el formulario usando fetch
                fetch(form.action, {
                    method: form.method,
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok ' + response.statusText);
                    }
                    return response.json(); // Suponiendo que la respuesta es JSON
                })
                .then(data => {
                    if (data.success) {
                        // Mostrar SweetAlert después de la actualización exitosa del usuario
                        Swal.fire({
                            title: '¡Éxito!',
                            text: 'Usuario actualizado exitosamente.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = '/users/'; // Redirigir a la lista de usuarios
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: 'Hubo un problema al actualizar el usuario.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Hubo un problema con el envío del formulario:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema con el envío del formulario.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            }
        },
        false
    );
});
</script>
