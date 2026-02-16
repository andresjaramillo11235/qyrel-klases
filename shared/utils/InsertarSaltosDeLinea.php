<?php

function insertarSaltosDeLinea($texto, $palabrasPorLinea = 10) {
    // Divide el texto en palabras
    $palabras = explode(' ', $texto);

    // Inicializa variables
    $resultado = '';
    $contador = 0;

    // Recorre las palabras y añade un <br> cada cierto número de palabras
    foreach ($palabras as $palabra) {
        $resultado .= $palabra . ' ';
        $contador++;

        if ($contador >= $palabrasPorLinea) {
            $resultado .= '<br>';
            $contador = 0;
        }
    }

    return trim($resultado);
}
