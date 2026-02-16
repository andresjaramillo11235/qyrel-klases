<?php

function obtenerCalificacionBadge($calificacion)
{
    if (!empty($calificacion)) {
        switch ($calificacion) {
            case 1:
                $badgeClass = 'badge bg-danger';
                $calificacionTexto = '1 - Muy Bajo';
                break;
            case 2:
                $badgeClass = 'badge bg-warning';
                $calificacionTexto = '2 - Bajo';
                break;
            case 3:
                $badgeClass = 'badge bg-info';
                $calificacionTexto = '3 - Regular';
                break;
            case 4:
                $badgeClass = 'badge bg-primary';
                $calificacionTexto = '4 - Bueno';
                break;
            case 5:
                $badgeClass = 'badge bg-success';
                $calificacionTexto = '5 - Excelente';
                break;
            default:
                $badgeClass = 'badge bg-secondary';
                $calificacionTexto = 'Desconocido';
                break;
        }

        return "<span class='{$badgeClass}'>{$calificacionTexto}</span>";
    } else {
        return "<span class='badge bg-secondary'>Sin calificar</span>";
    }
}


//echo obtenerCalificacionBadge($calificacion['estudiante_calificacion']);
