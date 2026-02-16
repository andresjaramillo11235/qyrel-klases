<?php $routes = include '../config/Routes.php'; ?>

<?php if (!empty($_SESSION['flash_error'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const msg = <?= json_encode($_SESSION['flash_error'], JSON_UNESCAPED_UNICODE) ?>;
            Swal.fire({
                icon: 'error',
                title: 'No se pudo completar la acción',
                text: msg,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#0d6efd' // opcional, para matchear Bootstrap
            });
        });
    </script>
<?php unset($_SESSION['flash_error']);
endif; ?>


<div class="card">
    <div class="card-header bg-light text-dark border-bottom">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="mb-3 mb-sm-0">
                <i class="ti ti-filter me-2"></i> Crear Nuevo cliente / estudiante
            </h5>
        </div>
    </div>

    <div class="card-body">

        <div class="container-fluid py-3">

            <?php if (!empty($_SESSION['flash_error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_SESSION['flash_error']);
                    unset($_SESSION['flash_error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <form action="<?= $routes['clientes_nuevos_store'] ?>" method="post" class="card-body needs-validation" novalidate>
                    <div class="row g-3">

                        <!-- Número de documento -->
                        <div class="col-md-3">
                            <label class="form-label">Número de documento</label>
                            <input
                                type="text"
                                name="cedula"
                                class="form-control"
                                inputmode="numeric"
                                pattern="^\d{6,12}$"
                                minlength="6"
                                maxlength="12"
                                required
                                oninput="this.value = this.value.replace(/\D/g,'').slice(0,12);">
                            <div class="form-text">Solo números (6 a 12 dígitos).</div>
                            <div class="invalid-feedback">Ingrese un documento válido (6 a 12 dígitos).</div>
                        </div>

                        <!-- Nombres -->
                        <div class="col-md-3">
                            <label class="form-label">Nombres</label>
                            <input
                                type="text"
                                name="nombres"
                                class="form-control"
                                minlength="2"
                                maxlength="100"
                                required
                                style="text-transform: uppercase;"
                                pattern="^[A-ZÁÉÍÓÚÜÑ\s'\-]{2,100}$"
                                oninput="this.value=this.value.toUpperCase().replace(/[^A-ZÁÉÍÓÚÜÑ\s'-]/g,'').slice(0,100);">
                            <div class="form-text">Solo letras, espacios, ' y -, mínimo 2 caracteres.</div>
                            <div class="invalid-feedback">Ingrese los nombres (mínimo 2 letras).</div>
                        </div>

                        <!-- Apellidos -->
                        <div class="col-md-3">
                            <label class="form-label">Apellidos</label>
                            <input
                                type="text"
                                name="apellidos"
                                class="form-control"
                                minlength="2"
                                maxlength="100"
                                required
                                style="text-transform: uppercase;"
                                pattern="^[A-ZÁÉÍÓÚÜÑ\s'\-]{2,100}$"
                                oninput="this.value=this.value.toUpperCase().replace(/[^A-ZÁÉÍÓÚÜÑ\s'-]/g,'').slice(0,100);">
                            <div class="form-text">Solo letras, espacios, ' y -, mínimo 2 caracteres.</div>
                            <div class="invalid-feedback">Ingrese los apellidos (mínimo 2 letras).</div>
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input
                                type="text"
                                name="telefono"
                                class="form-control"
                                inputmode="numeric"
                                pattern="^\d{10}$"
                                minlength="10"
                                maxlength="10"
                                required
                                oninput="this.value = this.value.replace(/\D/g,'').slice(0,10);">
                            <div class="form-text">Exactamente 10 dígitos, sin espacios ni símbolos.</div>
                            <div class="invalid-feedback">Ingrese un teléfono de 10 dígitos.</div>
                        </div>

                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="<?= htmlspecialchars($routes['clientes_nuevos_index'] ?? '#') ?>" class="btn btn-light">Cancelar</a>
                        <button type="submit" class="btn btn-success">
                            <i class="ph-duotone ph-check me-1"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Activar validación Bootstrap -->
<script>
    (function() {
        'use strict';
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    })();
</script>