<h1 class="mt-5">Calendario de Clases Teóricas (PHP)</h1>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Hora</th>
            <th>Lunes</th>
            <th>Martes</th>
            <th>Miércoles</th>
            <th>Jueves</th>
            <th>Viernes</th>
            <th>Sábado</th>
            <th>Domingo</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $timeslots = [
            '06:00 - 07:00',
            '07:00 - 08:00',
            '08:00 - 09:00',
            '09:00 - 10:00',
            '10:00 - 11:00',
            '11:00 - 12:00',
            '12:00 - 13:00',
            '13:00 - 14:00',
            '14:00 - 15:00',
            '15:00 - 16:00',
            '16:00 - 17:00',
            '17:00 - 18:00',
            '18:00 - 19:00',
            '19:00 - 20:00',
            '20:00 - 21:00'
        ];

        foreach ($timeslots as $timeslot) {
            echo "<tr>";
            echo "<td>$timeslot</td>";

            for ($day = 0; $day < 7; $day++) {
                $found = false;

                foreach ($classes as $class) {
                    $class_day = date('w', strtotime($class['start']));
                    $class_start = date('H:i', strtotime($class['hora_inicio']));
                    $class_end = date('H:i', strtotime($class['hora_fin']));
                    $timeslot_start = explode(' - ', $timeslot)[0];
                    $timeslot_end = explode(' - ', $timeslot)[1];

                    if ($day == $class_day && $timeslot_start == $class_start) {
                        $rowspan = (strtotime($class_end) - strtotime($class_start)) / 3600;

                        echo "<td rowspan='$rowspan' style='background-color: #007bff; color: white;'>
                                <strong>{$class['title']}</strong><br>
                                {$class['instructor']}<br>
                                {$class['aula']}<br>
                                {$class['hora_inicio']} - {$class['hora_fin']}
                              </td>";
                        $found = true;
                        break;
                    }
                }

                if (!$found) {
                    echo "<td></td>";
                }
            }

            echo "</tr>";
        }
        ?>
    </tbody>
</table>