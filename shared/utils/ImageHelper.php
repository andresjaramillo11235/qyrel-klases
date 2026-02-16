<?php

class ImageHelper
{
    public static function getThumbnailUrl($imagePath, $width = 50, $height = 50)
    {
        // Generar la URL dinámica
        $baseUrl = '/files/fotos_instructores/';
        $imageUrl = $baseUrl . $imagePath;

        // Ajustar el tamaño de la imagen usando HTML
        return "<img src='{$imageUrl}' width='{$width}' height='{$height}' alt='Foto'>";
    }
}
?>
