<?php

class ErrorController
{
    public function permissionDenied()
    {
        ob_start();
        include '../modules/errors/views/permission_denied.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function error404()
    {
        ob_start();
        include '../modules/errors/views/error404.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function connectionLost()
    {
        ob_start();
        include '../modules/errors/views/connection_lost.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

}
?>
