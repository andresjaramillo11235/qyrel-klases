<?php $routes = include '../config/Routes.php'; ?>

<li class="pc-item">
    <a href="<?php echo $routes['consulta_rapida_index'] ?>" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-magnifying-glass"></i> 
        </span>
        <span class="pc-mtext">Consulta Estudiante</span> 
    </a>
</li>

<li class="pc-item">
    <a href="/matriculas/" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-subtitles"></i>
        </span>
        <span class="pc-mtext">Matrículas</span>
    </a>
</li>

<!-- Estudiantes -->
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-student"></i>
        </span>
        <span class="pc-mtext">Estudiantes</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="/estudiantes/">Listado</a></li>
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
        <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_practicas_listado_admin'] ?>">Listado</a></li>
        <!-- <li class="pc-item"><a class="pc-link" href="/clasespracticascronograma/">Calendario</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['calificaciones_index'] ?>">Calificaciones</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['novedades_index'] ?>">Novedades</a></li> -->
    </ul>
</li>

<li class="pc-item">
    <a href="<?php echo $routes['ingresos_index']; ?>" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-currency-dollar"></i>
        </span>
        <span class="pc-mtext">Ingresos</span>
    </a>
</li>

