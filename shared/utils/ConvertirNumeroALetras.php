<?php
function convertirNumeroALetras($numero)
{
    $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
    $centenas = ['', 'CIENTO', 'DOSCIENTOS', 'TRESCIENTOS', 'CUATROCIENTOS', 'QUINIENTOS', 'SEISCIENTOS', 'SETECIENTOS', 'OCHOCIENTOS', 'NOVECIENTOS'];

    if ($numero == 0) {
        return "CERO PESOS";
    }

    $numeroEnLetras = '';

    // Manejar millones
    if ($numero >= 1000000) {
        $millones = intval($numero / 1000000);
        $numeroEnLetras .= $millones == 1 ? 'UN MILLÓN ' : $unidades[$millones] . ' MILLONES ';
        $numero -= $millones * 1000000;
    }

    // Manejar miles
    if ($numero >= 1000) {
        $miles = intval($numero / 1000);
        $numeroEnLetras .= $miles == 1 ? 'MIL ' : convertirNumeroALetras($miles) . ' MIL ';
        $numero -= $miles * 1000;
    }

    // Manejar centenas
    $cientos = intval($numero / 100);
    if ($cientos > 0) {
        if ($cientos == 1 && $numero % 100 == 0) {
            $numeroEnLetras .= 'CIEN';
        } else {
            $numeroEnLetras .= $centenas[$cientos] . ' ';
        }
        $numero -= $cientos * 100;
    }

    // Manejar decenas y unidades
    // Manejar decenas y unidades
    if ($numero > 0) {
        if ($numero < 10) {
            $numeroEnLetras .= $unidades[$numero];
        } elseif ($numero >= 11 && $numero <= 19) {
            $especiales = ['ONCE', 'DOCE', 'TRECE', 'CATORCE', 'QUINCE', 'DIECISEIS', 'DIECISIETE', 'DIECIOCHO', 'DIECINUEVE'];
            $numeroEnLetras .= $especiales[$numero - 11];
        } else {
            $dec = intval($numero / 10);
            $unidad = $numero % 10;
            $numeroEnLetras .= $decenas[$dec];
            if ($unidad > 0) {
                $numeroEnLetras .= ' Y ' . $unidades[$unidad];
            }
        }
    }


    return trim($numeroEnLetras) . ' PESOS';
}
