<?php $routes = include '../config/Routes.php'; ?>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-student"></i>
        </span>
        <span class="pc-mtext">Mi Cuenta</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?= $routes['estudiantes_cuenta'] ?>">Mi cuenta</a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-car"></i>
        </span>
        <span class="pc-mtext">Clases Prácticas</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_practicas_listado_estudiante'] ?>">Listado</a></li>
        <li class="pc-item"><a class="pc-link" href="<?=
                                                        $routes['clases_practicas_cronograma_estudiante'] ?>">Calendario</a></li>
    </ul>
</li>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-car"></i>
        </span>
        <span class="pc-mtext">Clases Teóricas</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="/estudiantes-agenda-teoricas/">Clases teóricas calendario</a></li>
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['estudiantes_progreso_teorico'] ?>">Clases teóricas progreso</a></li>
    </ul>
</li>