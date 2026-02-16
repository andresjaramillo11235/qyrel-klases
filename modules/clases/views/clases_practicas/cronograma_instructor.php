<style>
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

    #instructor-nombre {
        font-size: 0.8em;
        /* Ajustar el tamaño de la letra */
        background-color: #f0f0f0;
        /* Mismo color de fondo que las horas */
        color: #333;
        /* Texto con buen contraste */
        text-align: center;
        /* Centrar el texto */
        padding: 15px;
        /* Ajustar el padding si es necesario */
        line-height: 1.2em;
        /* Ajustar el espacio entre líneas */

        /* Forzar que los nombres se muestren en dos líneas */
        word-wrap: break-word;
        /* Permitir el salto de línea */
    }

    #tablaInstructores td {
        padding: 2px;
        /* Reducir el padding para cada celda */
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
        <h4>Cronograma de Clases Prácticas</h4>
        <button type="button" class="btn btn-outline-secondary" id="siguienteBtn">Día Siguiente</button>
    </div>

    <!-- Tabla del cronograma -->
    <div class="table-responsive">

        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th class="align-middle" id="encabezadoInstructorHorario">
                        <div>HORARIO</div>
                        <div>INSTRUCTOR</div>
                    </th>

                    <?php for ($hora = 6; $hora <= 21; $hora++): ?>
                        <th class="align-middle" id="hora-encabezado" style="width: 150px;"><?= sprintf('%02d:00', $hora) ?></th>
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

<?php include 'modal_detalle_clase_instructor.php'; ?>

<!-- Incluir jQuery antes de los scripts personalizados -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="../assets/js/clases_practicas_cronograma/cronograma_instructor.js"></script>
<script src="../assets/js/clases_practicas_cronograma/eventosCronograma.js"></script>
<script src="../assets/js/clases_practicas_cronograma/busqueda_estudiante.js"></script>

<script>
    $(document).ready(function() {
        // Al cargar la página, establecer la fecha actual y cargar el cronograma
        var hoy = new Date().toISOString().split('T')[0];
        $('#fechaInput').val(hoy);
        cargarCronograma(hoy);
    });
</script>