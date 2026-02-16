<?php

function obtenerClaseEstado($estadoId, $fecha, $horaInicio, $horaFin) {

    date_default_timezone_set('America/Bogota'); // Asegurar zona horaria correcta
    $now = new DateTime();
    
    // 🔹 Asegurar que horaInicio y horaFin tienen segundos
    $horaInicio .= (strlen($horaInicio) === 5) ? ':00' : '';
    $horaFin .= (strlen($horaFin) === 5) ? ':00' : '';

    $inicioClase = DateTime::createFromFormat('Y-m-d H:i:s', "$fecha $horaInicio");
    $finClase = DateTime::createFromFormat('Y-m-d H:i:s', "$fecha $horaFin");

    // 🛠️ Depuración: Comprobar si las fechas son correctas
    if (!$inicioClase || !$finClase) {
        error_log("⚠️ Error al convertir fecha: {$fecha} {$horaInicio} - {$horaFin}");
        return ['bg-secondary text-white', 'Desconocido'];
    }

    // ✅ Calcular minutos restantes
    $minutosRestantes = ($finClase->getTimestamp() - $now->getTimestamp()) / 60;

    // 🔹 Clases programadas (futuro)
    if ($now < $inicioClase) {
        return ['bg-primary text-white', 'Programada'];
    }

    // 🟡 Clases en curso
    if ($now >= $inicioClase && $now <= $finClase) {
        if ($estadoId == 1 || $estadoId == 2) {
            return ['bg-warning text-dark', 'En progreso'];
        } elseif ($estadoId == 4 || $estadoId == 5) {
            return ['bg-danger text-white', 'Cancelada'];
        }
    }

    // ✅ Clases finalizadas o canceladas
    if ($now > $finClase) {
        if ($estadoId == 3 || $minutosRestantes <= -30) {
            return ['bg-success text-white', 'Finalizada'];
        } elseif ($estadoId == 4 || $estadoId == 5) {
            return ['bg-danger text-white', 'Cancelada'];
        } else {
            return ['bg-secondary text-white', 'Finalizada'];
        }
    }

    return ['bg-secondary text-white', 'Desconocido']; // Estado por defecto
}
