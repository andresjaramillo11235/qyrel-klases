<div class="container mt-3">
        <h1 class="mt-5">Cronograma Semanal del Instructor</h1>
        <form id="formSeleccionarFecha" class="mb-3">
            <div class="row">
                <div class="col-md-3">
                    <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary" id="btnMostrarCronograma">Mostrar Cronograma</button>
                </div>
            </div>
        </form>

        <div id="cronogramaSemanal" style="display: none;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <?php for ($i = 0; $i < 7; $i++): ?>
                            <th id="dia-<?= $i ?>"></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($hora = 6; $hora <= 21; $hora++): ?>
                        <tr>
                            <th><?= sprintf('%02d:00', $hora) ?></th>
                            <?php for ($dia = 0; $dia < 7; $dia++): ?>
                                <td id="celda-<?= $hora ?>-<?= $dia ?>"></td>
                            <?php endfor; ?>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include the modal -->
    <?php include 'modal_detalle_clase.php'; ?>

    <!-- Include the specific JavaScript files for managing the instructor's class modal -->
    <script src="../../assets/js/instructores/cargarEstados.js"></script>
    <script src="../../assets/js/instructores/mostrarCronograma.js"></script>
    <script src="../../assets/js/instructores/obtenerDetalleClase.js"></script>
    <script src="../../assets/js/instructores/actualizarClase.js"></script>
    <script src="../../assets/js/instructores/init.js"></script>

    <script>
        $(document).ready(function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('updated')) {
                Swal.fire({
                    title: 'Éxito',
                    text: 'La clase se ha actualizado correctamente.',
                    icon: 'success'
                });
            }
        });
    </script>