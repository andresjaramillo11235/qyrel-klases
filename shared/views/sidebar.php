<?php require_once '../shared/utils/AjustarImagen.php' ?>
<?php require_once '../shared/utils/ObtenerFotoUsuario.php' ?>

<!-- [ Sidebar Menu ] start -->
<nav class="pc-sidebar">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/home/" class="b-brand text-primary">

                <?php
                // Ruta base donde están los logos
                $baseLogoPath = "../files/logos_empresas/";

                // Verificar si existe el logo en la sesión
                $sessionLogo = isset($_SESSION['empresa_logo']) && !empty($_SESSION['empresa_logo'])
                    ? $_SESSION['empresa_logo']
                    : 'logo-ceacloud-original.png';

                // Ruta completa del logo
                $path = $baseLogoPath . $sessionLogo;

                // Verificar si el archivo existe, si no usar el logo por defecto
                if (!file_exists($path)) {
                    $path = $baseLogoPath . 'logo-ceacloud-original.png';
                }

                // Ajustar tamaño de la imagen
                list($newWidth, $newHeight) = ajustarImagen($path, 150, 100); // Ajuste a 150x100 máximo
                ?>

                <!-- Mostrar la imagen -->
                <img src="<?= $path ?>" alt="logo image" class="logo-lg" width="<?= $newWidth ?>" height="<?= $newHeight ?>" />

                <span class="badge bg-brand-color-2 rounded-pill ms-2 theme-version">v0.9.25</span>
            </a>
        </div>
        <div class="navbar-content">

            <ul class="pc-navbar">

                <li class="pc-item">
                    <a href="/home/" class="pc-link">
                        <span class="pc-micon">
                            <i class="ph-duotone ph-house"></i>
                        </span>
                        <span class="pc-mtext">Inicio</span>
                    </a>
                </li>

                <?php
                switch ($_SESSION['rol_nombre']) {
                    case 'ROOT':
                        include 'navbar.php';
                        break;
                    case 'ADMIN':
                        include 'navbar_admin.php';
                        break;
                    case 'ASISOP':
                        include 'navbar_asisop.php';
                        break;
                    case 'ASISPROG':
                        include 'navbar_asisprog.php';
                        break;
                    case 'EST':
                        include 'navbar_est.php';
                        break;
                    case 'INST':
                        include 'navbar_inst.php';
                        break;
                    case 'AUDITOR':
                        include 'navbar_auditor.php';
                        break;
                    default:
                        echo 'Rol no reconocido';
                        break;
                }
                ?>
            </ul>

        </div>

        <div class="card pc-user-card">
            <div class="card-body">
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
                    <div class="flex-grow-1 ms-3">
                        <div class="dropdown">
                            <a href="#" class="arrow-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" data-bs-offset="0,20">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 me-2">
                                        <h6 class="mb-0"><?php echo ucwords($_SESSION['user_nombre']); ?></h6>
                                        <small><?php echo ucwords($_SESSION['rol_nombre']); ?></small>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <div class="btn btn-icon btn-link-secondary avtar">
                                            <i class="ph-duotone ph-windows-logo"></i>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <div class="dropdown-menu">
                                <ul>
                                    <li>
                                        <a href="/logout/" class="pc-user-links">
                                            <i class="ph-duotone ph-power"></i>
                                            <span>Salir</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>