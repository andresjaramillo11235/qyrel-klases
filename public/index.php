<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

date_default_timezone_set('America/Bogota');

require './../routes/router.php';
require './../routes/routes.php';

