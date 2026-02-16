<style>
    .estado-cuadro {
        width: 16px;
        height: 16px;
        border-radius: 4px;
        /* usa 50% si prefieres círculos */
        display: inline-block;
    }

    .bg-primary {
        background-color: #00b3ff;
        /* azul similar al de tu imagen */
    }

    .bg-warning {
        background-color: #ffc107;
    }

    .bg-success {
        background-color: #28a745;
    }

    .bg-secondary {
        background-color: #6c757d;
    }

    .bg-danger {
        background-color: #dc3545;
    }
</style>

<style>
    /* CODIGO SCROLL SUPERIOR */
    .scroll-top-wrapper {
        overflow-x: auto;
        overflow-y: hidden;
        height: 20px;
    }

    #scroll-top {
        height: 1px;
    }

    .calendar-wrapper {
        overflow-x: auto;
    }

    /* FIN CODIGO SCROLL SUPERIOR */
    #tablaCronograma {
        table-layout: fixed;
        /* Fuerza a la tabla a respetar los anchos de las columnas */
        width: 100%;
        /* Asegura que ocupe todo el espacio */
    }

    #tablaCronograma th,
    #tablaCronograma td {
        width: 120px;
        /* Fija el ancho de cada celda */
        min-width: 120px;
        /* Evita que se reduzca más allá de este tamaño */
        max-width: 120px;
        /* Mantiene la consistencia */
        white-space: nowrap;
        /* Evita que el texto se divida en varias líneas */
        overflow: hidden;
        text-overflow: ellipsis;
        /* Si hay texto largo, lo corta con "..." */
    }

    .celda-pasada {
        background-color: rgba(200, 200, 200, 0.5) !important;
        /* Gris claro con transparencia */
        pointer-events: none;
        /* Evita clics en estas celdas */
    }

    .bg-primary {
        background-color: #007bff !important;
        /* Clases futuras */
    }

    .bg-warning {
        background-color: #ffc107 !important;
        /* Clases en marcha */
    }

    .bg-danger {
        background-color: #dc3545 !important;
        /* Clases pasadas */
    }

    .text-white {
        color: white !important;
    }

    .text-dark {
        color: black !important;
    }

    /* Aplica el mismo fondo a la columna de instructores */
    .instructor-columna {
        background-color: #f0f0f0;
        /* Mismo fondo que la fila de horas */
        color: #333;
        text-align: center;
        font-weight: bold;
        padding: 10px;
        border-right: 1px solid #ccc;
        /* Separador si es necesario */
    }

    /* Fijar la fila de horas */
    #tablaInstructores thead th {
        position: sticky;
        top: 0;
        background-color: #f9f9f9;
        z-index: 2;
    }

    /* Marca de agua en celdas vacías */
    .hora-celda span {
        pointer-events: none;
    }

    /* Asegurar que cada celda tenga la misma altura y anchura para formar cuadrados */
    .hora-celda {
        width: 60px;
        /* Ajusta el ancho de la celda */
        height: 60px;
        /* Ajusta la altura de la celda */
        padding: 0;
        /* Eliminar el relleno para lograr un cuadrado perfecto */
        vertical-align: middle;
        /* Alinear el contenido verticalmente */
        text-align: center;
        /* Centrar el contenido horizontalmente */
        border: 1px solid #ddd;
        /* Bordes para definir bien cada celda */
    }

    /* Ajustar el texto dentro de las celdas de clase */
    .clase-asignada {
        background-color: #007bff;
        /* Fondo azul para clases prácticas */
        color: white;
        /* Texto en blanco */
        text-align: center;
        /* Alinear el texto al centro */
        font-size: 0.7em;
        /* Reducir el tamaño del texto para que no se desborde */
        line-height: 1.1;
        /* Reducir la altura de línea */
        padding: 2px;
        /* Reducir el relleno del contenido */
        white-space: nowrap;
        /* Evitar saltos de línea innecesarios */
        overflow: hidden;
        /* Ocultar el texto que se desborda */
        text-overflow: ellipsis;
        /* Añadir puntos suspensivos si el texto no cabe */
    }

    /* Asegurar que las celdas fusionadas mantengan el tamaño correcto */
    .clase-asignada[colspan] {
        display: table-cell;
        /* Mantener el comportamiento de tabla */
        vertical-align: middle;
        /* Centrar verticalmente el contenido */
    }

    /* Ajustar el encabezado de la tabla */
    .table thead th {
        text-align: center;
        /* Centrar el texto en el encabezado */
        padding: 5px;
        /* Relleno pequeño para no romper el diseño */
        font-weight: bold;
        /* Hacer que el encabezado sea más prominente */
        font-size: 0.85em;
        /* Reducir un poco el tamaño del texto del encabezado */
    }

    #encabezadoInstructorHorario {
        font-size: 0.7em;
        /* Reducir el tamaño de la fuente */
        text-align: center;
        /* Centrar el texto */
        line-height: 1.2;
        /* Ajustar la altura de línea para mayor claridad */
        background-color: #e0e0e0;
        /* Un gris más oscuro que el de las horas */
    }

    #encabezadoInstructorHorario div {
        display: block;
        /* Asegurarse de que cada parte del encabezado esté en su propia línea */
        padding: 0;
        /* Eliminar cualquier relleno extra */
        margin: 0;
        /* Eliminar márgenes */
    }

    #hora-encabezado {
        font-size: 0.7em;
        /* Reducir el tamaño de la fuente */
        background-color: #f0f0f0;
        /* Fondo gris claro */
        color: #333;
        /* Color del texto para mejor visibilidad */
        padding: 10px;
        /* Agregar algo de padding para darle espacio al texto */
    }

    #tablaInstructores td {
        padding: 2px;
        /* Reducir el padding para cada celda */
    }

    .bg-verde-reserva {
        background-color: #32CD32 !important;
        /* Verde esmeralda */
        color: #fff !important;
    }
</style>


<div class="container mt-5">
    <!-- Formulario de selección de fecha -->
    <form id="fechaForm" method="get" action="">
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="date" class="form-control" id="fechaInput" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Seleccionar fecha</button>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-secondary" id="hoyBtn">Hoy</button>
            </div>
        </div>
    </form>

    <!-- Botones de navegación para los días -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button type="button" class="btn btn-outline-secondary" id="anteriorBtn">Día Anterior</button>
        <h4 id="tituloCronograma">Cronograma de Clases Prácticas</h4>
        <button type="button" class="btn btn-outline-secondary" id="siguienteBtn">Día Siguiente</button>
    </div>

    <div class="d-flex justify-content-start align-items-center gap-3 mb-3 flex-wrap">
        <div class="d-flex align-items-center">
            <span class="estado-cuadro bg-primary me-2"></span> Clase programada
        </div>
        <div class="d-flex align-items-center">
            <span class="estado-cuadro bg-warning me-2"></span> Clase en progreso
        </div>
        <div class="d-flex align-items-center">
            <span class="estado-cuadro bg-success me-2"></span> Clase finalizada calificada
        </div>
        <div class="d-flex align-items-center">
            <span class="estado-cuadro bg-secondary me-2"></span> Clase finalizada sin calificar
        </div>
        <div class="d-flex align-items-center">
            <span class="estado-cuadro bg-danger me-2"></span> Clase cancelada
        </div>
    </div>

    <!-- Tabla del cronograma -->
    <div class="table-responsive">

        <div class="scroll-top-wrapper">
            <div id="scroll-top"></div>
        </div>

        <div class="calendar-wrapper" id="scroll-bottom">
            <table class="table table-bordered text-center" id="tablaCronograma">
                <thead>
                    <tr>
                        <th class="align-middle" id="encabezadoInstructorHorario">
                            <div>HORARIO</div>
                            <div>INSTRUCTOR</div>
                        </th>

                        <?php for ($hora = 6; $hora <= 21; $hora++): ?>
                            <th class="align-middle" id="hora-encabezado" style="width: 200px;"><?= sprintf('%02d:00', $hora) ?></th>
                        <?php endfor; ?>

                    </tr>
                </thead>
                <tbody id="tablaInstructores">
                    <?php foreach ($instructores as $instructor): ?>
                        <tr>
                            <td class="align-middle instructor-nombre"> <?= $instructor['nombres'] ?> <?= $instructor['apellidos'] ?></td>

                            <?php for ($hora = 6; $hora <= 21; $hora++): ?>
                                <td class="align-middle hora-celda" style="width: 150px; height: 100px;" data-hora="<?= $hora ?>" data-instructor-id="<?= $instructor['id'] ?>">
                                    <!-- Vacío inicialmente -->
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include 'modal_agregar_clase.php'; ?>
<?php include 'modal_editar_clase.php'; ?>
<?php include 'modal_clase_activa.php'; ?>
<?php include 'modal_eliminar_clase_reserva.php'; ?>

<!-- Incluir jQuery antes de los scripts personalizados -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="../assets/js/clases_practicas_cronograma/cronograma.js"></script>
<script src="../assets/js/clases_practicas_cronograma/eventosCronograma.js"></script>
<script src="../assets/js/clases_practicas_cronograma/busqueda_estudiante.js"></script>

<script>
    $(document).ready(function() {
        // Obtener la fecha actual en la zona horaria de Colombia
        var hoy = new Date().toLocaleDateString("es-CO", {
            timeZone: "America/Bogota"
        });

        console.warn("Fecha obtenida con `toLocaleDateString()`:", hoy);

        // Verificar si el formato es correcto
        var partes = hoy.split('/');

        if (partes.length === 3) {
            var dia = partes[0].padStart(2, '0'); // Asegurar que tenga dos dígitos
            var mes = partes[1].padStart(2, '0'); // Asegurar que tenga dos dígitos
            var anio = partes[2];

            var fechaColombia = `${anio}-${mes}-${dia}`; // Convertir a formato YYYY-MM-DD

            console.warn("Fecha convertida a `YYYY-MM-DD`:", fechaColombia);

            // Establecer la fecha en el input y cargar el cronograma
            $('#fechaInput').val(fechaColombia);
            cargarCronograma(fechaColombia);
        } else {
            console.error("Error: El formato de la fecha obtenida no es válido:", hoy);
        }
    });
</script>

<script>
    const topScroll = document.querySelector('.scroll-top-wrapper');
    const bottomScroll = document.querySelector('.calendar-wrapper');

    const tablaCronograma = document.getElementById('tablaCronograma');
    document.getElementById('scroll-top').style.width = tablaCronograma.scrollWidth + 'px';

    topScroll.addEventListener('scroll', () => {
        bottomScroll.scrollLeft = topScroll.scrollLeft;
    });

    bottomScroll.addEventListener('scroll', () => {
        topScroll.scrollLeft = bottomScroll.scrollLeft;
    });
</script>