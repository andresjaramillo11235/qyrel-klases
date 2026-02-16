<?php
// al inicio de la vista (una sola vez)
if (!function_exists('e')) {
    function e($v)
    {
        return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
?>

<style>
    table td {
        padding: 0;
    }
</style>

<style>
    .stacked-cards {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .cal-card {
        font-size: 12px;
        line-height: 1.25;
        padding: 8px 10px;
        border-radius: 8px;
        box-shadow: 0 1px 0 rgba(0, 0, 0, .05);
    }

    .cal-card .title {
        font-weight: 600;
        margin-bottom: 2px;
        display: block;
    }

    .cal-card .line {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .calendar-cell {
        position: relative;
    }

    .calendar-cell::before {
        content: attr(data-label);
        position: absolute;
        top: 6px;
        left: 8px;
        font-size: 11px;
        color: #6b7280;
        opacity: .35;
        pointer-events: none;
    }
</style>

<?php $routes = include '../config/Routes.php'; ?>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['success_message']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (!empty($_SESSION['error_message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un problema',
                text: <?= json_encode($_SESSION['error_message'], JSON_UNESCAPED_UNICODE) ?>,
                confirmButtonText: 'Aceptar',
                confirmButtonColor: '#dc3545'
            });
        });
    </script>
<?php unset($_SESSION['error_message']);
endif; ?>


<?php
$currentDate = isset($date) ? $date : date('Y-m-d');
$previousWeek = date('Y-m-d', strtotime('-1 week', strtotime($currentDate)));
$nextWeek = date('Y-m-d', strtotime('+1 week', strtotime($currentDate)));
?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/clases_teoricas/">Clases Teóricas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Cronograma</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <!-- Botón de "Calendario Anterior" alineado a la izquierda -->
                <a href="<?= $routes['clases_teoricas_calendariodos']; ?><?= $previousWeek ?>" class="btn btn-primary">
                    <i class="ti ti-arrow-left"></i> Calendario Anterior
                </a>

                <!-- Título centrado -->
                <h5 class="text-center flex-grow-1">Calendario Clases Teóricas</h5>

                <!-- Botón de "Siguiente Calendario" alineado a la derecha -->
                <a href="<?= $routes['clases_teoricas_calendariodos']; ?><?= $nextWeek ?>" class="btn btn-primary">
                    Siguiente Calendario <i class="ti ti-arrow-right"></i>
                </a>
            </div>

            <div class="card-body">
                <div class="dt-responsive table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <?php
                                $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                                $months = [
                                    '01' => 'Enero',
                                    '02' => 'Febrero',
                                    '03' => 'Marzo',
                                    '04' => 'Abril',
                                    '05' => 'Mayo',
                                    '06' => 'Junio',
                                    '07' => 'Julio',
                                    '08' => 'Agosto',
                                    '09' => 'Septiembre',
                                    '10' => 'Octubre',
                                    '11' => 'Noviembre',
                                    '12' => 'Diciembre'
                                ];
                                $aulaColors = [
                                    'Principal' => '#ff7f7f',
                                    'Master' => '#7f7fff',
                                ];

                                $startOfWeek = strtotime('monday this week', strtotime($currentDate));  // Ajuste aquí
                                for ($i = 0; $i < 7; $i++) {
                                    $currentDate = $startOfWeek + ($i * 86400);
                                    $dayName = $daysOfWeek[date('N', $currentDate) - 1];
                                    $day = date('d', $currentDate);
                                    $month = $months[date('m', $currentDate)];
                                    echo "<th>$dayName, $day $month</th>";
                                }
                                ?>
                            </tr>
                        </thead>

                        <tbody>
                            <?php
                            $startHour = 6;
                            $endHour   = 22;

                            // Control de rowspan por día (7 columnas)
                            $rowspanRemaining = array_fill(0, 7, 0);

                            // Mapeo de colores por estado_id (mantiene tu criterio)
                            $estadoColors = [
                                1 => '#0d6efd', // PROGRAMADA
                                2 => '#ffc107', // EN PROGRESO
                                3 => '#20c997', // FINALIZADA CALIFICADA
                                4 => '#343a40', // FINALIZADA SIN CALIFICAR
                                5 => '#dc3545', // CANCELADA
                            ];

                            for ($hour = $startHour; $hour < $endHour; $hour++):
                                $hourStart = sprintf('%02d:00', $hour);
                                $hourEnd   = sprintf('%02d:00', $hour + 1);
                            ?>
                                <tr>
                                    <td><?= $hourStart ?> - <?= $hourEnd ?></td>

                                    <?php for ($i = 0; $i < 7; $i++): ?>

                                        <?php
                                        // Si hay una celda anterior ocupando este slot por rowspan, saltar
                                        if ($rowspanRemaining[$i] > 0) {
                                            $rowspanRemaining[$i]--;
                                            continue;
                                        }

                                        $dayTs  = $startOfWeek + ($i * 86400);
                                        $slotTs = strtotime(date('Y-m-d', $dayTs) . ' ' . $hourStart);

                                        // Clases que COMIENZAN exactamente en este bloque
                                        $filtered = array_filter($clases, function ($c) use ($slotTs) {
                                            $startTs = strtotime($c['fecha'] . ' ' . $c['hora_inicio']);
                                            return $startTs == $slotTs;
                                        });

                                        if (!empty($filtered)) {
                                            // 1) rowspan máximo del grupo
                                            $maxRowSpan = 1;
                                            $cardsHtml  = '';

                                            foreach ($filtered as $class) {
                                                $tIni = strtotime($class['fecha'] . ' ' . $class['hora_inicio']);
                                                $tFin = strtotime($class['fecha'] . ' ' . $class['hora_fin']);
                                                $diffMinutes = max(60, ($tFin - $tIni) / 60); // mínimo 60 min
                                                $rowSpan = (int)ceil($diffMinutes / 60);
                                                if ($rowSpan > $maxRowSpan) $maxRowSpan = $rowSpan;

                                                // Color por estado
                                                $color = $estadoColors[$class['estado_id']] ?? '#6c757d';

                                                // Contenido de la card
                                                $contenido = '
                                                    <span class="title">' . e($class['programa_nombre']) . '</span>
                                                    <div class="line">' . htmlspecialchars($class['tema_nombre']) . '</div>
                                                    <div class="line">' . htmlspecialchars($class['aula_nombre']) . '</div>
                                                    <div class="line">' . htmlspecialchars(ucwords($class['instructor_nombre_completo'])) . '</div>
                                                    <div>' . date('H:i', strtotime($class['hora_inicio'])) . ' - ' . date('H:i', strtotime($class['hora_fin'])) . '</div>
                                                ';

                                                $cardsHtml .= '
                                                    <div class="cal-card" style="background:' . $color . ';color:#fff;">' . $contenido . '</div>
                                                ';
                                            }

                                            // 2) Un solo <td> con todas las cards apiladas
                                            echo '
                                            <td rowspan="' . (int)$maxRowSpan . '">
                                                <div class="stacked-cards">' . $cardsHtml . '</div>
                                            </td>
                                            ';

                                            // 3) Marcar filas ocupadas por el rowspan máximo
                                            $rowspanRemaining[$i] = $maxRowSpan - 1;
                                        } else {
                                            // Celda vacía con marca de agua "Miércoles, 03 Septiembre 8:00"
                                            $dayName   = $daysOfWeek[(int)date('N', $dayTs) - 1];
                                            $dayNum    = date('d', $dayTs);
                                            $monthName = $months[date('m', $dayTs)];
                                            $hourNice  = date('G:i', $slotTs); // sin cero a la izquierda en hora
                                            $label     = "{$dayName}, {$dayNum} {$monthName} {$hourNice}";

                                            echo '<td class="calendar-cell"
                                            data-date="' . date('Y-m-d', $slotTs) . '"
                                            data-hour="' . $hourStart . '"
                                            data-label="' . htmlspecialchars($label) . '"></td>';
                                        }
                                        ?>

                                    <?php endfor; ?>
                                </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Modal for Crear Clase -->
<div class="modal fade" id="createClaseModal" tabindex="-1" aria-labelledby="createClaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClaseModalLabel">Crear Clase Teórica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="createClaseFormContainer">
                    <?php include '../modules/clases/views/clases_teoricas/create.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>


<script src="/assets/js/calendarioClases.js"></script>