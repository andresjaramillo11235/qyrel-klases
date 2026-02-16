<?php $routes = include '../config/Routes.php'; ?>

<!-- Consulta Rápida -->
<li class="pc-item">
    <a href="<?php echo $routes['consulta_rapida_index'] ?>" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-magnifying-glass"></i> 
        </span>
        <span class="pc-mtext">Consulta Estudiante</span> 
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
        <li class="pc-item"><a class="pc-link" href="/estudiantescreate/">Crear</a></li>
    </ul>
</li>

<li class="pc-item">
    <a href="/users/" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-users"></i>
        </span>
        <span class="pc-mtext">Usuarios</span>
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

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-projector-screen-chart"></i>
        </span>
        <span class="pc-mtext">Clases Teóricas</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="/clases_teoricas/">Listado</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_teoricas_calendariodos']; ?>">Calendario</a></li>
        <li class="pc-item"><a class="pc-link" href="/clasesteoricascreate/">Crear</a></li>
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
        <li class="pc-item"><a class="pc-link" href="/clasespracticascronograma/">Calendario</a></li>
    </ul>
</li>

<li class="pc-item">
    <a href="/convenios/" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-handshake"></i>
        </span>
        <span class="pc-mtext">Convenios</span>
    </a>
</li>

<li class="pc-item">
    <a href="/administrativos/" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-user-list"></i>
        </span>
        <span class="pc-mtext">Administrativos</span>
    </a>
</li>

<!-- VEHÍCULOS -->
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ti ti-car"></i> <!-- Ícono alternativo de vehículo -->
        </span>
        <span class="pc-mtext">Vehículos</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?= $routes['vehiculos_index']; ?>">Listado</a></li>
        <li class="pc-item"><a class="pc-link" href="/vehiculos/create/">Crear</a></li>
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