<?php require_once '../shared/utils/AjustarImagen.php' ?>
<?php require_once '../shared/utils/ObtenerFotoUsuario.php' ?>
<?php $routes = include '../config/Routes.php'; ?>

<div class="ms-auto">
    <ul class="list-unstyled">
        <li class="dropdown pc-h-item header-user-profile">

            <a class="pc-head-link dropdown-toggle arrow-none me-0"
                data-bs-toggle="dropdown"
                href="#"
                role="button"
                aria-haspopup="false"
                data-bs-auto-close="outside"
                aria-expanded="false"
                style="display: inline-block; width: 50px; height: 50px; overflow: hidden; border-radius: 0;"> <!-- Eliminamos la forma redonda -->

                <?php
                if (isset($_SESSION['administrativo_id'])) {
                    echo obtenerFotoUsuario(
                        'administrativo',
                        $_SESSION['administrativo_foto'],
                        "../files/fotos_administrativos/"
                    );
                } elseif (isset($_SESSION['estudiante_id'])) {
                    echo obtenerFotoUsuario(
                        'estudiante',
                        $_SESSION['estudiante_foto'],
                        "../files/fotos_estudiantes/"
                    );
                } elseif (isset($_SESSION['instructor_id'])) {
                    echo obtenerFotoUsuario(
                        'instructor',
                        $_SESSION['instructor_foto'],
                        "../files/fotos_instructores/"
                    );
                } else {
                    echo obtenerFotoUsuario(
                        'default',
                        "avatar-2.jpg",
                        "../assets/images/user/"
                    );
                }
                ?>
            </a>

            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
                <div class="dropdown-header d-flex align-items-center justify-content-between">
                    <h5 class="m-0">Perfil</h5>
                </div>
                <div class="dropdown-body">
                    <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                        <ul class="list-group list-group-flush w-100">
                            <li class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">

                                        <?php
                                        if (isset($_SESSION['administrativo_id'])) {
                                            echo obtenerFotoUsuario(
                                                'administrativo',
                                                $_SESSION['administrativo_foto'],
                                                "../files/fotos_administrativos/"
                                            );
                                        } elseif (isset($_SESSION['estudiante_id'])) {
                                            echo obtenerFotoUsuario(
                                                'estudiante',
                                                $_SESSION['estudiante_foto'],
                                                "../files/fotos_estudiantes/"
                                            );
                                        } elseif (isset($_SESSION['instructor_id'])) {
                                            echo obtenerFotoUsuario(
                                                'instructor',
                                                $_SESSION['instructor_foto'],
                                                "../files/fotos_instructores/"
                                            );
                                        } else {
                                            echo obtenerFotoUsuario(
                                                'default',
                                                "avatar-2.jpg", // Imagen predeterminada
                                                "../assets/images/user/"
                                            );
                                        }
                                        ?>

                                    </div>
                                    <div class="flex-grow-1 mx-3">
                                        <h5 class="mb-0"><?php echo strtoupper($_SESSION['user_nombre']) ?></h5>
                                        <a class="link-primary" href="mailto:<?= $_SESSION['email'] ?>"><?= $_SESSION['email'] ?></a>
                                        <h5 class="mb-0"><?php echo strtoupper($_SESSION['empresa_nombre']) ?></h5>
                                    </div>
                                    <span class="badge bg-primary"><?php echo $_SESSION['rol_nombre']; ?></span>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <a href="<?php echo $routes['reset_password']; ?>" class="dropdown-item">
                                    <span class="d-flex align-items-center">
                                        <i class="ti ti-menu-2"></i>
                                        <span>Cambiar contraseña</span>
                                    </span>
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="/logout/" class="dropdown-item">
                                    <span class="d-flex align-items-center">
                                        <i class="ph-duotone ph-power"></i>
                                        <span>Salir</span>
                                    </span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
    </ul>
</div>