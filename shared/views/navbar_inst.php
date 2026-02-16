<?php $routes = include '../config/Routes.php'; ?>

<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ph-duotone ph-chalkboard-teacher"></i>
    </span>
    <span class="pc-mtext">Clases Teóricas</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_teoricas_listado_instructores']; ?>">Clases teóricas</a></li>
    <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_teoricas_calendariodos']; ?>">Clases teóricas calendario</a></li>
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
    <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_practicas_listado_instructor'] ?>">Listado</a></li>
    <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_practicas_cronograma_instructor'] ?>">Calendario</a></li>
  </ul>
</li>

<!-- INSPECCIONES -->
<li class="pc-item pc-hasmenu">
  <a href="#!" class="pc-link">
    <span class="pc-micon">
      <i class="ph-duotone ph-clipboard-text"></i>
    </span>
    <span class="pc-mtext">Inspecciones</span>
    <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
  </a>
  <ul class="pc-submenu">
    <li class="pc-item"><a class="pc-link" href="<?php echo $routes['inspecciones_vehiculos_index']; ?>">Inspecciones Automóviles</a></li>
    <li class="pc-item"><a class="pc-link" href="<?php echo $routes['inspecciones_motos_index']; ?>">Inspecciones Motos</a></li>
  </ul>
</li>