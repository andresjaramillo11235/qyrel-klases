<?php
function formatearFechaHumana($fecha) {
    // Convertir la fecha a un objeto DateTime
    $date = DateTime::createFromFormat('Y-m-d', $fecha);

    // Verificar si la fecha es válida
    if (!$date) {
        return "Fecha no válida";
    }

    // Mapas para días y meses en español
    $dias = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];

    // Obtener los componentes de la fecha
    $diaSemana = $dias[$date->format('w')];
    $diaMes = $date->format('d');
    $mes = $meses[$date->format('n') - 1];
    $anio = $date->format('Y');

    // Construir el formato deseado
    return "{$diaSemana}, {$diaMes} de {$mes} del {$anio}";
}



?>