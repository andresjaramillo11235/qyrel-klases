<?php
$routes = include '../config/Routes.php';
require_once '../shared/utils/enlaces.php';
?>

<li class="pc-item">
    <a href="<?php echo $routes['consulta_rapida_index'] ?>" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-magnifying-glass"></i>
        </span>
        <span class="pc-mtext">Consulta Estudiante</span>
    </a>
</li>

<?php /** MATRICULAS */ ?>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-subtitles"></i>
        </span>
        <span class="pc-mtext">Matrículas</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="/matriculas/">Listado</a></li>
        <li class="pc-item"><a class="pc-link" href="/matriculascreate/">Crear</a></li>
    </ul>
</li>

<?php /** ESTUDIANTES */ ?>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-student"></i>
        </span>
        <span class="pc-mtext"><?= LabelHelper::get('menu_estudiantes') ?></span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="/estudiantes/">Listado</a></li>
        <li class="pc-item"><a class="pc-link" href="/estudiantescreate/">Nuevo</a></li>
    </ul>
</li>

<?php /** INSTRUCTORES */ ?>
<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-chalkboard-teacher"></i>
        </span>
        <span class="pc-mtext"><?= LabelHelper::get('menu_instructores') ?></span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="/instructores/">Listado</a></li>
        <li class="pc-item"><a class="pc-link" href="/instructorescreate/">Nuevo</a></li>
    </ul>
</li>

<?php /** CLASES TEORICAS */ ?>
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
        <!--<li class="pc-item"><a class="pc-link" href="/clasesteoricascreate/">Crear</a></li>-->
        <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_teoricas_informe_general']; ?>">Informe General</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['aulas_index']; ?>">Aulas</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_teoricas_no_asistidas']; ?>">Control</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['clases_teoricas_creacion_multiple_form']; ?>">Cargue Masivo</a></li>
    </ul>
</li>

<?php /** CLASES PRACTICAS */ ?>
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
        <li class="pc-item"><a class="pc-link" href="<?= $routes['calificaciones_index'] ?>">Calificaciones</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['novedades_index'] ?>">Novedades</a></li>
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


<?php /* INGRESOS */ ?>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-currency-dollar"></i>
        </span>
        <span class="pc-mtext">Ingresos</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?= $routes['ingresos_index']; ?>">Ingresos</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['ingresos_informe']; ?>">Informe</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['ingresos_informe_cartera']; ?>">Informe Cartera</a></li>
    </ul>
</li>


<?php /* EGRESOS */ ?>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-hand-coins"></i>
        </span>
        <span class="pc-mtext">Egresos</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?= $routes['egresos_index']; ?>">Egresos</a></li>
    </ul>
</li>


<?php /* CAJA */ ?>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-vault"></i>
        </span>
        <span class="pc-mtext">Caja</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?= $routes['cajas_index']; ?>">Cajas</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['movimientos_caja_index']; ?>">Movimientos Caja</a></li>
        <li class="pc-item"><a class="pc-link" href="<?= $routes['caja_diaria']; ?>">Caja Diaria</a></li>
    </ul>
</li>



<li class="pc-item">
    <a href="<?php echo $routes['programas_index']; ?>" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-stack"></i>
        </span>
        <span class="pc-mtext">Programas</span>
    </a>
</li>


<?php /* INSPECCIONES */ ?>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-clipboard-text"></i>
        </span>
        <span class="pc-mtext">Inspecciones</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['inspecciones_dashboard']; ?>">Dashboard</a></li>
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['inspecciones_vehiculos_index']; ?>">Inspecciones Automóviles</a></li>
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['inspecciones_motos_index']; ?>">Inspecciones Motos</a></li>
    </ul>
</li>


<?php /* INFORMES */ ?>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-file-text"></i>
        </span>
        <span class="pc-mtext">Informes</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>
    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['informe_siet_index']; ?>">SIET</a></li>
    </ul>
</li>


<?php /* CONFIGURACION */ ?>

<li class="pc-item pc-hasmenu">
    <a href="#!" class="pc-link">
        <span class="pc-micon">
            <i class="ph-duotone ph-gear"></i>
        </span>
        <span class="pc-mtext">Configuración</span>
        <span class="pc-arrow"><i data-feather="chevron-right"></i></span>
    </a>

    <ul class="pc-submenu">
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['egresos_cuentas_egreso_index']; ?>">Cuentas Egresos</a></li>
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['documento_contrato_index']; ?>">Personalizar Contrato</a></li>
        <li class="pc-item"><a class="pc-link" href="<?php echo $routes['auditoria_index']; ?>">Control de Auditoria</a></li>
    </ul>
</li>