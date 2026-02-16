<?php

// Se esta utilizando en los logos del contrato 2024-11-28


function calcularDimensionesProporcionadas($rutaImagen, $maxWidth, $maxHeight)
{
    // Obtener las dimensiones originales de la imagen
    list($originalWidth, $originalHeight) = getimagesize($rutaImagen);

    // Calcular la proporción de la imagen original
    $aspectRatio = $originalWidth / $originalHeight;

    // Variables para almacenar el nuevo ancho y alto
    $newWidth = $originalWidth;
    $newHeight = $originalHeight;

    // Redimensionar manteniendo la proporción
    if ($originalWidth > $originalHeight) {
        // Si la imagen es más ancha
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $aspectRatio;
    } else {
        // Si la imagen es más alta o cuadrada
        $newHeight = $maxHeight;
        $newWidth = $maxHeight * $aspectRatio;
    }

    // Ajustar si excede los límites máximos permitidos
    if ($newWidth > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = $maxWidth / $aspectRatio;
    }

    if ($newHeight > $maxHeight) {
        $newHeight = $maxHeight;
        $newWidth = $maxHeight * $aspectRatio;
    }

    // Retornar las dimensiones ajustadas
    return [
        'width' => $newWidth,
        'height' => $newHeight
    ];
}
