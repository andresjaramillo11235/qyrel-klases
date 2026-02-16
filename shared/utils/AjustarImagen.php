<?php
function ajustarImagen($path, $maxWidth, $maxHeight) {
    // Validar que el archivo exista y sea válido antes de usar getimagesize()
    if (file_exists($path) && is_file($path) && @getimagesize($path)) {
        list($originalWidth, $originalHeight) = getimagesize($path);

        if ($originalHeight === 0 || $originalWidth === 0) {
            // Manejar casos de dimensiones inválidas
            return [$maxWidth, $maxHeight];
        }

        $aspectRatio = $originalWidth / $originalHeight;

        if ($originalWidth > $originalHeight) {
            $newWidth = $maxWidth;
            $newHeight = $maxWidth / $aspectRatio;
        } else {
            $newHeight = $maxHeight;
            $newWidth = $maxHeight * $aspectRatio;
        }

        if ($newWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = $newWidth / $aspectRatio;
        }

        if ($newHeight > $maxHeight) {
            $newHeight = $maxHeight;
            $newWidth = $newHeight * $aspectRatio;
        }

        return [$newWidth, $newHeight];
    } else {
        // Si no es un archivo válido, devolver las dimensiones máximas por defecto
        return [$maxWidth, $maxHeight];
    }
}
