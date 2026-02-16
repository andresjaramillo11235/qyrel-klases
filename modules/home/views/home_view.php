<?php
if ($_SESSION['rol_nombre'] == 'ADMIN') { ?>
    <?php include "dashboard_administrador.php"; ?>
<?php } elseif ($_SESSION['rol_nombre'] == 'INST') {    ?>
    <?php include "home_view_instructor.php"; ?>
<?php } elseif ($_SESSION['rol_nombre'] == 'EST') {    ?>
    <?php include "home_view_estudiante.php"; ?>
<?php } ?>