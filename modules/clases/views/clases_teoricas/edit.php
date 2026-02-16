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
                    <li class="breadcrumb-item" aria-current="page">Modificar Clase Teórica</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ breadcrumb ] end -->

<!-- [ Main Content - card ] start -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>Modificar Clase Teórica</h5>
            </div>

            <div class="card-body">

                <form method="post" action="/clases_teoricas_update/<?= $clase['id'] ?>">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="programa_id" class="form-label">Programa</label>
                                <select class="form-select" id="programa_id" name="programa_id" required>
                                    <?php foreach ($programas as $programa) : ?>
                                        <option value="<?= $programa['id'] ?>" <?= $clase['programa_id'] == $programa['id'] ? 'selected' : '' ?>><?= $programa['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Tema (agregamos required) -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tema_id" class="form-label">Tema</label>
                                <select class="form-select" id="tema_id" name="tema_id" required>
                                    <?php if (!empty($temas)) : ?>
                                        <option value="">-- Seleccione un tema --</option>
                                        <?php foreach ($temas as $tema) : ?>
                                            <option value="<?= (int)$tema['id'] ?>"
                                                <?= isset($clase['tema_id']) && (int)$clase['tema_id'] === (int)$tema['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($tema['nombre']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <option value="">-- No hay temas para el programa seleccionado --</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aula_id" class="form-label">Aula</label>
                                <select class="form-select" id="aula_id" name="aula_id" required>
                                    <?php foreach ($aulas as $aula) : ?>
                                        <option value="<?= $aula['id'] ?>" <?= $clase['aula_id'] == $aula['id'] ? 'selected' : '' ?>><?= $aula['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="instructor_id" class="form-label">Instructor</label>
                                <select class="form-select" id="instructor_id" name="instructor_id" required>
                                    <?php foreach ($instructores as $instructor) : ?>
                                        <option value="<?= $instructor['id'] ?>" <?= $clase['instructor_id'] == $instructor['id'] ? 'selected' : '' ?>><?= $instructor['nombres'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="fecha" name="fecha" value="<?= $clase['fecha'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_inicio" class="form-label">Hora Inicio</label>
                                <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="<?= $clase['hora_inicio'] ?>" required>
                                <div class="invalid-feedback">La hora de inicio es obligatoria.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="hora_fin" class="form-label">Hora Fin</label>
                                <input type="time" class="form-control" id="hora_fin" name="hora_fin" value="<?= $clase['hora_fin'] ?>" required>
                                <div class="invalid-feedback">La hora de fin debe ser mayor que la hora de inicio.</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="1" <?= $clase['estado_id'] == '1' ? 'selected' : '' ?>>PROGRAMADA</option>
                                    <option value="2" <?= $clase['estado_id'] == '2' ? 'selected' : '' ?>>CANCELADA</option>
                                    <option value="3" <?= $clase['estado_id'] == '3' ? 'selected' : '' ?>>FINALIZADA</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"><?= $clase['observaciones'] ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        </div>
                    </div>

                </form>


            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('form-edit-clase-teorica');
        const programa = document.getElementById('programa_id');
        const tema = document.getElementById('tema_id');
        const aula = document.getElementById('aula_id');
        const instructor = document.getElementById('instructor_id');
        const fecha = document.getElementById('fecha');
        const horaInicio = document.getElementById('hora_inicio');
        const horaFin = document.getElementById('hora_fin');

        const parseTimeToMinutes = (t) => {
            if (!t) return null;
            const [h, m] = t.split(':').map(Number);
            return h * 60 + m;
        };

        const setMinHoraFin = () => {
            if (horaInicio.value) {
                horaFin.min = horaInicio.value;
            } else {
                horaFin.removeAttribute('min');
            }
        };

        // Quita marcas de error al modificar
        [programa, tema, aula, instructor, fecha, horaInicio, horaFin].forEach(el => {
            el.addEventListener('input', () => el.classList.remove('is-invalid'));
            el.addEventListener('change', () => el.classList.remove('is-invalid'));
        });

        horaInicio.addEventListener('change', setMinHoraFin);
        setMinHoraFin();

        form.addEventListener('submit', (e) => {
            // Primero: validación nativa HTML5 (required, type="date/time", etc.)
            if (!form.checkValidity()) {
                e.preventDefault();
                form.reportValidity();
                return;
            }

            // Luego: validación de congruencia de horas
            const ini = parseTimeToMinutes(horaInicio.value);
            const fin = parseTimeToMinutes(horaFin.value);

            if (ini !== null && fin !== null && fin <= ini) {
                e.preventDefault();
                horaInicio.classList.add('is-invalid');
                horaFin.classList.add('is-invalid');

                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Horario inválido',
                        text: 'La hora de fin debe ser mayor que la hora de inicio.'
                    });
                } else {
                    alert('La hora de fin debe ser mayor que la hora de inicio.');
                }
                return;
            }

            // (Opcional) Validación explícita por si Tema tiene placeholder vacío
            if (tema && tema.required && (!tema.value || tema.value === '')) {
                e.preventDefault();
                tema.classList.add('is-invalid');
                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Falta el Tema',
                        text: 'Selecciona un tema.'
                    });
                } else {
                    alert('Selecciona un tema.');
                }
                return;
            }
        });
    });
</script>