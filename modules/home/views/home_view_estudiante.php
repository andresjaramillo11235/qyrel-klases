<?php if (!empty($_SESSION['flash_success'])): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="ph-duotone ph-check-circle me-2"></i>
        <?= htmlspecialchars($_SESSION['flash_success']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>
    <?php unset($_SESSION['flash_success']); ?>
<?php endif; ?>

<?php if ($_SESSION['estudiante_estado'] == 5) { ?>
    <div class="container my-4">
        <div class="p-4 p-lg-5 rounded-4 shadow-sm border bg-primary-subtle position-relative overflow-hidden">
            <div class="row align-items-center g-4">
                <div class="col-auto">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-white shadow-sm"
                        style="width: 72px; height: 72px;">
                        <i class="ph-duotone ph-user-circle-gear" style="font-size: 38px; color: var(--bs-primary);"></i>
                    </div>
                </div>
                <div class="col">
                    <h2 class="h4 h-lg-3 fw-bold mb-2 text-primary-emphasis">¡Bienvenido! Completa tu registro</h2>
                    <p class="mb-3 lead text-primary-emphasis">
                        Para activar tu cuenta y acceder a todos los servicios, por favor completa tu información.
                        <span class="fw-semibold">Toma 3–5 minutos.</span>
                    </p>
                    <ul class="list-inline text-primary-emphasis small mb-4">
                        <li class="list-inline-item me-3">
                            <i class="ph ph-check-circle me-1"></i> Datos de contacto
                        </li>
                        <li class="list-inline-item me-3">
                            <i class="ph ph-check-circle me-1"></i> Dirección
                        </li>
                        <li class="list-inline-item me-3">
                            <i class="ph ph-check-circle me-1"></i> Fecha de nacimiento
                        </li>
                    </ul>

                    <a href="/estudiantesedit/<?= $_SESSION['estudiante_id'] ?>" class="btn btn-success btn-lg px-4">
                        <i class="ph-duotone ph-pencil-line me-2"></i>
                        Completar registro ahora
                    </a>

                    <span class="ms-3 align-middle badge rounded-pill text-bg-warning">
                        Pendiente de completar
                    </span>
                </div>
            </div>
        </div>
    </div>
<?php } ?>