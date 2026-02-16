<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    

    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- ABLE ------------------------------------------------------------------ -->

    <!-- [Tabler Icons] https://tablericons.com -->
    <link rel="stylesheet" href="../able/assets/fonts/tabler-icons.min.css">

    <!-- [Feather Icons] https://feathericons.com -->
    <link rel="stylesheet" href="../able/assets/fonts/feather.css">

    <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
    <link rel="stylesheet" href="../able/assets/fonts/fontawesome.css">

    <!-- [Material Icons] https://fonts.google.com/icons -->
    <link rel="stylesheet" href="../able/assets/fonts/material.css">

    <!-- [Template CSS Files] -->
    <link rel="stylesheet" href="../able/assets/css/style.css" id="main-style-link">
    <link rel="stylesheet" href="../able/assets/css/style-preset.css">

    <title>TecnoAcademias</title>
</head>

<body data-pc-preset="preset-1" data-pc-sidebar-theme="light" data-pc-sidebar-caption="true" data-pc-direction="ltr" data-pc-theme="light">
    <?php
    /*
    if (isset($_SESSION['user_id'])) {
        $role = $_SESSION['rol_nombre'];
        switch ($role) {
            case 'ROOT':
                include 'navbar.php';
                break;
            case 'ADMIN':
                include 'navbar_admin.php';
                break;
            case 'ASISOP':
                include 'navbar_asisop.php';
                break;
            case 'EST':
                include 'navbar_est.php';
                break;
            case 'INST':
                include 'navbar_inst.php';
                break;
                // Añadir más casos según los roles que tengas
            default:
                include 'navbar_default.php'; // Un navbar por defecto si no se encuentra el rol
                break;
        }
    */
    ?>

    <?php include 'sidebar.php' ?>
    
    <?php include 'body.php' ?>

    <?php echo $content; ?>

    <script>layout_change('light');</script>




<script>layout_sidebar_change('light');</script>



<script>change_box_container('false');</script>


<script>layout_caption_change('true');</script>




<script>layout_rtl_change('false');</script>


<script>preset_change("preset-1");</script>

    
    <script src="../able/assets/js/plugins/popper.min.js"></script>
    <script src="../able/assets/js/plugins/simplebar.min.js"></script>
    <script src="../able/assets/js/plugins/bootstrap.min.js"></script>
    <script src="../able/assets/js/fonts/custom-font.js"></script>
    <script src="../able/assets/js/pcoded.js"></script>
    <script src="../able/assets/js/plugins/feather.min.js"></script>

</body>

</html>