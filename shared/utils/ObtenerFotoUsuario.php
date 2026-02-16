<?php
function obtenerFotoUsuario($tipoUsuario, $foto, $rutaBase, $anchoMax = 35, $altoMax = 35)
{
    // Validar si la foto tiene un nombre válido
    if (!empty($foto) && file_exists($rutaBase . $foto) && is_file($rutaBase . $foto)) {
        $imgPath = $rutaBase . $foto;

        // Validar si la imagen es válida
        if (exif_imagetype($imgPath)) {
            // Ajustar las dimensiones de la imagen
            list($imgWidth, $imgHeight) = ajustarImagen($imgPath, $anchoMax, $altoMax);
        } else {
            // Usar imagen predeterminada si no es una imagen válida
            $imgPath = "../assets/images/user/avatar-2.jpg";
            $imgWidth = $anchoMax;
            $imgHeight = $altoMax;
        }
    } else {
        // Usar imagen predeterminada si no existe la foto o no es válida
        $imgPath = "../assets/images/user/avatar-2.jpg";
        $imgWidth = $anchoMax;
        $imgHeight = $altoMax;
    }

    // Generar el código HTML para la imagen
    return "<img src=\"$imgPath\" alt=\"user-image\" style=\"width: {$imgWidth}px; height: {$imgHeight}px; height: auto; display: block; object-fit: cover;\" />";
}
