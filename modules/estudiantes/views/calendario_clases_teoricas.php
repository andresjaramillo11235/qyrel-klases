<?php $routes = include '../config/Routes.php'; ?>

<style>
    .stacked-cards {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .cal-card {
        font-size: 12px;
        line-height: 1.2;
        padding: 8px 10px;
        border-radius: 8px;
        box-shadow: 0 1px 0 rgba(0, 0, 0, .05);
    }

    .cal-card .title {
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }

    .cal-card .line {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cal-card.agendada {
        background: #28a745;
        color: #fff;
        cursor: pointer;
        position: relative;
    }

    .cal-card.disponible {
        background: #fff;
        color: #111;
        border: 1px solid #e5e7eb;
        cursor: pointer;
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

<h1 class="mt-5">Calendario de Clases Teóricas</h1>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['success_message'];
                    unset($_SESSION['success_message']); ?>'
        });
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])) : ?>
    <script>
        Swal.fire({
            icon: 'success',
            text: '<?php echo $_SESSION['error_message'];
                    unset($_SESSION['error_message']); ?>'
        });
    </script>
<?php endif; ?>

<?php if ($programa_id === false) : ?>
    <div class="alert alert-warning">
        No estás inscrito en ningún programa que incluya clases teóricas. Por favor, contacta con administración para más información.
    </div>
<?php else : ?>

    <?php
    $currentDate = isset($date) ? $date : date('Y-m-d');
    $previousWeek = date('Y-m-d', strtotime('-1 week', strtotime($currentDate)));
    $nextWeek = date('Y-m-d', strtotime('+1 week', strtotime($currentDate)));
    ?>

    <div class="d-flex justify-content-between mb-3">
        <a href="/estudiantes-agenda-teoricas/<?= $previousWeek ?>" class="btn btn-primary">Anterior</a>
        <a href="/estudiantes-agenda-teoricas/<?= $nextWeek ?>" class="btn btn-primary">Siguiente</a>
    </div>

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

                $startOfWeek = strtotime('monday this week', strtotime($currentDate));
                for ($i = 0; $i < 7; $i++) {
                    $currentDay = $startOfWeek + ($i * 86400);
                    $dayName = $daysOfWeek[date('N', $currentDay) - 1];
                    $day = date('d', $currentDay);
                    $month = $months[date('m', $currentDay)];
                    echo "<th>$dayName, $day $month</th>";
                }
                ?>
            </tr>
        </thead>

        <tbody>

            <?php
            $startHour = 6;
            $endHour   = 22;

            // control de rowspan por día de la semana (7 columnas)
            $rowspanRemaining = array_fill(0, 7, 0);

            for ($hour = $startHour; $hour < $endHour; $hour++) :
                $hourStart = sprintf('%02d:00', $hour);
                $hourEnd   = sprintf('%02d:00', $hour + 1);
            ?>
                <tr>
                    <td><?= $hourStart ?> - <?= $hourEnd ?></td>
                    <?php for ($i = 0; $i < 7; $i++) :

                        if ($rowspanRemaining[$i] > 0) {
                            $rowspanRemaining[$i]--;
                            continue;
                        }

                        $dayTs   = $startOfWeek + ($i * 86400);
                        $slotTs  = strtotime(date('Y-m-d', $dayTs) . ' ' . $hourStart);

                        // Clases que COMIENZAN exactamente en este bloque
                        $filteredClasses = array_filter($clases, function ($class) use ($slotTs) {
                            $classStartTs = strtotime($class['fecha'] . ' ' . $class['hora_inicio']);
                            return $classStartTs == $slotTs;
                        });

                        if (!empty($filteredClasses)) {
                            // 1) calcular rowspan máximo del grupo
                            $maxRowSpan = 1;
                            $cardsHtml  = '';

                            foreach ($filteredClasses as $class) {

                                // Dentro del foreach de $filteredClasses...
                                $inicioTs  = strtotime($class['fecha'] . ' ' . $class['hora_inicio']);
                                $hoy0      = strtotime('today'); // medianoche de hoy

                                // Colores Bootstrap
                                $COLOR_INSCRITO   = '#28a745'; // success (verde)
                                $COLOR_VISTO      = '#0d6efd'; // primary (azul)
                                $COLOR_NO_ASISTIO = '#dc3545'; // danger (rojo)
                                $COLOR_DISPONIBLE = '#FFFFFF'; // blanco (no inscrito)

                                // Texto por defecto
                                $textColor = 'black';

                                // Si el estudiante está agendado a esta clase:
                                if (!empty($class['agendado'])) {
                                    // ¿clase futura o de hoy sin empezar? => INSCRITO (verde)
                                    if ($inicioTs >= $hoy0 && (int)($class['asistencia'] ?? 0) === 0) {
                                        $color = $COLOR_INSCRITO;   // VERDE
                                        $textColor = 'white';
                                    } else {
                                        // Clase ya pasada: usa asistencia real
                                        if ((int)($class['asistencia'] ?? 0) === 1) {
                                            $color = $COLOR_VISTO;      // AZUL (asistió)
                                            $textColor = 'white';
                                        } elseif ((int)($class['asistencia'] ?? 0) === 2) {
                                            $color = $COLOR_NO_ASISTIO; // ROJO (no asistió)
                                            $textColor = 'white';
                                        } else {
                                            // Pasada y sin marcar (caso raro): gris suave o el que prefieras
                                            $color = '#6c757d';
                                            $textColor = 'white';
                                        }
                                    }
                                } else {
                                    // No agendado (disponible para inscribirse)
                                    $color = $COLOR_DISPONIBLE;
                                    $textColor = 'black';
                                }

                                $tIni = strtotime($class['fecha'] . ' ' . $class['hora_inicio']);
                                $tFin = strtotime($class['fecha'] . ' ' . $class['hora_fin']);
                                $diffMinutes = max(60, ($tFin - $tIni) / 60); // mínimo 60 min
                                $rowSpan = (int)ceil($diffMinutes / 60);
                                if ($rowSpan > $maxRowSpan) $maxRowSpan = $rowSpan;

                                // contenido común
                                $contenido = '
                                <div class="line">' . htmlspecialchars($class['programa_nombre']) . '</div>
                                <div class="line">' . htmlspecialchars($class['tema_nombre']) . '</div>
                                <div class="line">' . htmlspecialchars($class['aula_nombre']) . '</div>
                                <div class="line">' . htmlspecialchars(ucwords($class['instructor_nombre_completo'])) . '</div>
                                <div>' . date('H:i', strtotime($class['hora_inicio'])) . ' - ' . date('H:i', strtotime($class['hora_fin'])) . '</div>';

                                if (!empty($class['agendado'])) {
                                    // Tarjeta VERDE (desinscribir clicando toda la card)
                                    $matri = ($matriculaId ?? '') ?: ($class['matricula_asignada'] ?? '');
                                    $cardsHtml .= '

                                    <div class="p-2 mb-2 cronograma-card"
                                        data-agendado="1"
                                        data-clase-id="' . (int)$class['clase_id'] . '"
                                        style="background-color: ' . $color . '; color: ' . $textColor . '; border-radius:6px; position:relative;"
                                        title="Haz clic para desinscribirte de esta clase">
                                        ' . htmlspecialchars($class['programa_nombre']) . '<br>
                                        ' . htmlspecialchars($class['tema_nombre']) . '<br>
                                        ' . htmlspecialchars($class['aula_nombre']) . '<br>
                                        ' . htmlspecialchars(ucwords($class['instructor_nombre_completo'])) . '<br>
                                        ' . date('H:i', strtotime($class['hora_inicio'])) . ' - ' . date('H:i', strtotime($class['hora_fin'])) . '
                                    </div>';
                                } else {

                                    // Tarjeta BLANCA (inscribirse via modal)
                                    $cardsHtml .= '
                                    <div class="cal-card disponible clickable-class"
                                        data-bs-toggle="modal"
                                        data-bs-target="#createClaseModal"
                                        data-clase-id="' . (int)$class['clase_id'] . '"
                                        data-calendario-fecha="' . htmlspecialchars($class['fecha']) . '"
                                        data-clase-start="' . htmlspecialchars($class['fecha'] . 'T' . $class['hora_inicio']) . '">
                                        ' . $contenido . '
                                    </div>
                                    ';
                                }
                            }

                            // 2) Pintar UN SOLO <td> con las N cards apiladas dentro
                            echo '
                            <td rowspan="' . (int)$maxRowSpan . '">
                                <div class="stacked-cards">' . $cardsHtml . '</div>
                            </td>
                            ';

                            // 3) Marcar filas ocupadas por el rowspan máximo
                            $rowspanRemaining[$i] = $maxRowSpan - 1;
                        } else {

                            // Celda vacía con marca de agua (Día, dd Mes hh:mm sin cero a la izquierda)
                            $dayName   = $daysOfWeek[(int)date('N', $dayTs) - 1];
                            $dayNum    = date('d', $dayTs);
                            $monthName = $months[date('m', $dayTs)];
                            $hourNice  = date('G:i', $slotTs); // 0–23 sin cero a la izquierda

                            $label = "{$dayName}, {$dayNum} {$monthName} {$hourNice}";

                            echo '<td class="calendar-cell"
                            data-date="' . date('Y-m-d', $slotTs) . '"
                            data-hour="' . $hourStart . '"
                            data-label="' . htmlspecialchars($label) . '"></td>';
                        }

                    endfor; ?>
                </tr>
            <?php endfor; ?>

        </tbody>
    </table>

<?php endif; // fin de if programaId 
?>



<!-- Modal for Crear Clase -->
<div class="modal fade" id="createClaseModal" tabindex="-1" aria-labelledby="createClaseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createClaseModalLabel">Registro a Clase Teórica</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="createClaseFormContainer">
                    <?php include '../modules/estudiantes/views/form_clases_teoricas_estudiantes.php'; ?>
                </div>
            </div>
        </div>
    </div>
</div>



<form id="form-unsubscribe" method="POST" style="display:none;">
    <input type="hidden" name="matricula_id" value="<?= htmlspecialchars($matriculaId ?? '', ENT_QUOTES) ?>">
</form>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classes = document.querySelectorAll('.clickable-class');
        classes.forEach(function(clase) {
            clase.addEventListener('click', function() {
                const claseId = this.getAttribute('data-clase-id');
                const claseStartTime = new Date(this.getAttribute('data-clase-start'));

                // Comprueba si la clase ya ha comenzado o finalizado
                if (claseStartTime < new Date()) {
                    // Solo muestra el mensaje de error y evita abrir el modal
                    Swal.fire({
                        icon: 'error',
                        title: 'Clase Pasada',
                        text: 'No puedes inscribirte en una clase que ya ha comenzado o finalizado.'
                    });
                } else {
                    // Configurar el ID de clase en el formulario del modal
                    document.getElementById('inputClaseId').value = claseId;
                    // Abre el modal si la clase está en el futuro
                    $('#createClaseModal').modal('show');
                }
            });
        });
    });
</script>



<script>
    function handlePastClass() {
        Swal.fire({
            icon: 'error',
            title: 'Clase Pasada',
            text: 'No puedes inscribirte en una clase que ya ha comenzado o finalizado.'
        });
    }
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const createClaseModal = document.getElementById('createClaseModal');

        createClaseModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const claseId = button.getAttribute('data-clase-id');
            const fecha = button.getAttribute('data-calendario-fecha'); // ✅ aquí defines la variable

            // Realizar una petición AJAX para obtener los detalles de la clase
            fetch(`/estudiante_obtener_detalle_clase/${claseId}`)
                .then(response => response.json())
                .then(data => {
                    // Rellenar los campos del formulario con los datos de la clase
                    document.getElementById('clase_teorica_id').value = claseId;
                    document.getElementById('calendario_fecha').value = fecha; // ✅ ahora ya está definida
                    document.getElementById('programa_id').value = data.programa_id;
                    document.getElementById('programa_nombre').value = data.programa_nombre;
                    document.getElementById('tema_nombre').value = data.tema_nombre;
                    document.getElementById('aula_nombre').value = data.aula_nombre;
                    document.getElementById('instructor_nombre').value = data.instructor_nombre_completo;
                    document.getElementById('fecha_hora').value = `${data.fecha} ${data.hora_inicio} - ${data.hora_fin}`;
                    document.getElementById('instructor_foto').src = `/files/fotos_instructores/${data.instructor_foto}`;
                })
                .catch(error => console.error('Error al cargar los detalles de la clase:', error));
        });
    });
</script>