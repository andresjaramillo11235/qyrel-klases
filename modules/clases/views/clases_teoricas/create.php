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
<?php unset($_SESSION['error_message']);?>
<?php endif; ?>



<style>
    input,
    textarea {
        text-transform: uppercase;
    }
</style>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/clases_teoricas/">Clases Teóricas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Nueva Clase Teórica</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content ] start -->
<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h5>Datos de la nueva clase teórica: los campos con
                    <i class="ph-duotone ph-asterisk"></i> son obligatorios.
                </h5>
            </div>

            <div class="card-body">

                <form method="post" action="/clasesteoricasstore/" class="validate-me" data-validate>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="programa_id" class="form-label">Programa <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="programa_id" name="programa_id" required>
                                    <option value="">Seleccione un programa</option>
                                    <?php foreach ($programas as $programa) : ?>
                                        <option value="<?= $programa['id'] ?>"><?= $programa['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="instructor_id" class="form-label">Instructor <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="instructor_id" name="instructor_id" required>
                                    <?php foreach ($instructores as $instructor) : ?>
                                        <option value="<?= $instructor['id'] ?>"><?= strtoupper($instructor['nombres'] . ' ' . $instructor['apellidos']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tema_id" class="form-label">Tema <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="tema_id" name="tema_id" required>
                                    <option value="">Seleccione un tema</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha" name="fecha" required value="<?= isset($_GET['fecha']) ? $_GET['fecha'] : '' ?>" min="<?= date('Y-m-d'); ?>">
                            </div>
                            <div id="hora_inicio_error" class="invalid-feedback" style="display: none; color: red; font-size: 0.9em;">
                                <!-- Mensajes de error se configuran dinámicamente desde el script -->
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_inicio" class="form-label">Hora Inicio <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" required value="<?= isset($_GET['hora_inicio']) ? $_GET['hora_inicio'] : '' ?>">
                                <div id="hora_inicio_error" class="invalid-feedback" style="display: none;">
                                    No puede seleccionar una hora de inicio en el pasado.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_fin" class="form-label">Hora Fin</label>
                                <input
                                    type="time"
                                    class="form-control"
                                    id="hora_fin"
                                    name="hora_fin"
                                    required
                                    readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="duracion" class="form-label">Duración <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="duracion" name="duracion" required>
                                    <option value="">Seleccione la duración</option>
                                    <?php for ($i = 1; $i <= 6; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> hora<?= $i > 1 ? 's' : '' ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aula_id" class="form-label">Aula <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="aula_id" name="aula_id" required>
                                    <?php foreach ($aulas as $aula) : ?>
                                        <option value="<?= $aula['id'] ?>"><?= $aula['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary" id="submit_button">Enviar</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<script>
    document.getElementById('programa_id').addEventListener('change', function() {
        var programaId = this.value;
        console.log('Programa seleccionado: ' + programaId); // Mensaje de depuración
        fetch('/clases_teoricas/getTemasByPrograma/' + programaId)
            .then(response => response.text())
            .then(text => {
                console.log('Respuesta del servidor:', text); // Imprimir la respuesta completa
                var data = JSON.parse(text); // Analizar el JSON manualmente
                if (data.error) {
                    console.error('Error:', data.error);
                    alert(data.error); // Muestra un mensaje de alerta si hay un error
                } else {
                    var temaSelect = document.getElementById('tema_id');
                    temaSelect.innerHTML = '<option value="">Seleccione un tema</option>';
                    data.forEach(function(tema) {
                        var option = document.createElement('option');
                        option.value = tema.id;
                        option.textContent = tema.nombre;
                        temaSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });

    document.getElementById('tema_id').addEventListener('change', function() {
        var temaId = this.value;
        console.log('Tema seleccionado: ' + temaId); // Mensaje de depuración
        fetch('/clases_teoricas/getClasesByTema/' + temaId)
            .then(response => response.text())
            .then(text => {
                console.log('Respuesta del servidor:', text); // Imprimir la respuesta completa
                var data = JSON.parse(text); // Analizar el JSON manualmente
                if (data.error) {
                    console.error('Error:', data.error);
                    alert(data.error); // Muestra un mensaje de alerta si hay un error
                } else {
                    var claseSelect = document.getElementById('clase_teorica_programa_id');
                    claseSelect.innerHTML = '<option value="">Seleccione una clase</option>';
                    data.forEach(function(clase) {
                        var option = document.createElement('option');
                        option.value = clase.id;
                        option.textContent = clase.nombre;
                        claseSelect.appendChild(option);
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
    });
</script>

<script>
    // Bootstrap validation
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>

<script>
    document.getElementById('fecha').addEventListener('change', validateDateTime);
    document.getElementById('hora_inicio').addEventListener('change', validateDateTime);

    function validateDateTime() {
        var selectedDate = document.getElementById('fecha').value;
        var selectedTime = document.getElementById('hora_inicio').value;
        var submitButton = document.getElementById('submit_button');
        var errorMessage = document.getElementById('hora_inicio_error');

        if (selectedDate && selectedTime) { // Asegúrese de que ambos, fecha y hora, estén seleccionados
            var selectedDateTime = new Date(selectedDate + 'T' + selectedTime);
            var now = new Date();

            if (selectedDateTime < now) {
                errorMessage.style.display = 'block'; // Mostrar el mensaje de error
                document.getElementById('hora_inicio').classList.add('is-invalid'); // Añadir clase de error
                submitButton.disabled = true; // Deshabilitar el botón de envío
            } else {
                errorMessage.style.display = 'none'; // Ocultar el mensaje de error
                document.getElementById('hora_inicio').classList.remove('is-invalid'); // Remover clase de error
                submitButton.disabled = false; // Habilitar el botón de envío
            }
        } else {
            errorMessage.style.display = 'none'; // Asegúrate de que no se muestre el error si los campos no están llenos
            document.getElementById('hora_inicio').classList.remove('is-invalid');
            submitButton.disabled = false;
        }
    }
</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

<script>
    document.getElementById('fecha').addEventListener('change', validateDateTime);
    document.getElementById('hora_inicio').addEventListener('change', validateDateTime);

    function validateDateTime() {
        var selectedDate = document.getElementById('fecha').value;
        var selectedTime = document.getElementById('hora_inicio').value;
        var submitButton = document.getElementById('submit_button');
        var errorMessage = document.getElementById('hora_inicio_error');

        if (selectedDate && selectedTime) {
            // Convertir la fecha y hora seleccionadas a la zona horaria de Colombia
            var selectedDateTime = new Date(selectedDate + 'T' + selectedTime);
            var now = new Date();

            // Ajustar `now` a la zona horaria de Colombia (America/Bogota)
            var colombiaOffset = -5 * 60; // UTC-5 en minutos
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset() - colombiaOffset);

            if (selectedDateTime < now) {
                errorMessage.style.display = 'block';
                errorMessage.textContent = 'La fecha y hora seleccionadas están en el pasado.';
                document.getElementById('hora_inicio').classList.add('is-invalid');
                submitButton.disabled = true;
            } else {
                errorMessage.style.display = 'none';
                errorMessage.textContent = '';
                document.getElementById('hora_inicio').classList.remove('is-invalid');
                submitButton.disabled = false;
            }

        } else {
            errorMessage.style.display = 'none';
            errorMessage.textContent = '';
            document.getElementById('hora_inicio').classList.remove('is-invalid');
            submitButton.disabled = false;
        }
    }
</script>


<script>
    document.getElementById('duracion').addEventListener('change', function() {
        const horaInicio = document.getElementById('hora_inicio').value;
        const duracion = parseInt(this.value, 10);
        const horaFinInput = document.getElementById('hora_fin');

        if (horaInicio && duracion) {
            const [hours, minutes] = horaInicio.split(':').map(Number);
            const date = new Date();
            date.setHours(hours, minutes, 0, 0);
            date.setHours(date.getHours() + duracion);

            const horaFin = date.toTimeString().split(':').slice(0, 2).join(':');
            horaFinInput.value = horaFin;
        } else {
            horaFinInput.value = ''; // Limpiar el campo si no hay valores válidos
        }
    });

    // Limpiar el campo hora_fin si cambia la hora de inicio
    document.getElementById('hora_inicio').addEventListener('change', function() {
        document.getElementById('hora_fin').value = '';
        document.getElementById('duracion').value = '';
    });
</script>