<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/matriculas/">Matrículas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Nueva matrícula</li>
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
                <h5>Datos de la nueva matrícula: los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</h5>
            </div>

            <div class="card-body">

                <form id="matriculaForm" action="/matriculas/store" method="post" class="needs-validation" novalidate>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estudiante_nombre" class="form-label">Estudiante <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="estudiante_nombre" name="estudiante_nombre" readonly data-bs-toggle="modal" data-bs-target="#buscarEstudianteModal" required>
                                <input type="hidden" id="estudiante_id" name="estudiante_id" required>
                                <div class="invalid-feedback">Seleccione un estudiante.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="programas" class="form-label">Programas <i class="ph-duotone ph-asterisk"></i></label>

                                <select class="form-control" id="programas" name="programas" required>
                                    <option value="">Seleccione un programa</option>
                                    <?php foreach ($programas as $programa) : ?>
                                        <option value="<?= $programa['id'] ?>" data-valor="<?= $programa['valor_total'] ?>">
                                            <?= strtoupper($programa['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="invalid-feedback">Seleccione un programa.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inscripcion" class="form-label">Fecha Inscripción <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha_inscripcion" name="fecha_inscripcion" required value="<?= date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_solicitud_id" class="form-label">Tipo Solicitud <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="tipo_solicitud_id" name="tipo_solicitud_id" required>
                                    <?php foreach ($tiposSolicitud as $tipo) : ?>
                                        <option value="<?= $tipo['id'] ?>"><?= $tipo['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado_id" class="form-label">Estado de Matrícula <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="estado_id" name="estado_id" required>
                                    <?php foreach ($estadosMatricula as $estado) : ?>
                                        <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="convenio_id" class="form-label">Convenio <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="convenio_id" name="convenio_id" required>
                                    <option value="">Seleccione un convenio</option>
                                    <?php foreach ($convenios as $convenio) : ?>
                                        <option value="<?= $convenio['id'] ?>"><?= htmlspecialchars($convenio['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>

                                <div class="invalid-feedback">Seleccione un convenio.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="valor_matricula" class="form-label">Valor de la Matrícula</label>
                                <input type="text" class="form-control" id="valor_matricula_display" placeholder="$0" required>
                                <input type="hidden" id="valor_matricula" name="valor_matricula">
                                <div class="invalid-feedback">Ingrese un valor.</div>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="hidden" name="empresa_id" value="<?= $_SESSION['empresa_id'] ?>">

                            <button type="submit" id="submit_button" class="btn btn-primary">
                                <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true" id="spinner"></span>
                                Enviar
                            </button>

                        </div>

                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Modal para buscar y seleccionar un estudiante -->
<div class="modal fade" id="buscarEstudianteModal" tabindex="-1" aria-labelledby="buscarEstudianteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="buscarEstudianteLabel">Buscar Estudiante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="buscarEstudianteInput" class="form-control mb-3" placeholder="Buscar por cédula, código o nombre">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre</th>
                            <th>Seleccionar</th>
                        </tr>
                    </thead>
                    <tbody id="estudianteResultados">
                        <!-- Aquí se llenarán los resultados de la búsqueda -->
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script src="../assets/js/plugins/bouncer.min.js"></script>
<script src="../assets/js/pages/form-validation.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>



<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("matriculaForm");
        const submitButton = document.getElementById("submit_button");
        const spinner = document.getElementById("spinner");

        form.addEventListener("submit", function(e) {
            // Si ya fue deshabilitado (doble clic rápido), no sigue
            if (submitButton.disabled) {
                e.preventDefault();
                return;
            }

            // Desactiva el botón y muestra el spinner
            submitButton.disabled = true;
            spinner.classList.remove("d-none");
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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    $(document).ready(function() {
        $('#programas').on('change', function() {
            var programaId = $(this).val();
            var estudianteId = $('#estudiante_id').val(); // Obtener ID del estudiante

            // Validar si el estudiante fue seleccionado primero
            if (!estudianteId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Selecciona un estudiante primero',
                    text: 'Por favor, selecciona un estudiante antes de elegir un programa.',
                    confirmButtonText: 'Ok'
                });
                $(this).val(''); // Reinicia el select de programas
                return;
            }

            // Formar la URL con los parámetros
            var url = `/0uVwXyZaBc/${estudianteId}/${programaId}`;

            $.ajax({
                url: url, // URL con estudiante_id y programa_id en la ruta
                type: 'GET', // Usa GET si los parámetros van en la URL
                dataType: 'json',
                success: function(response) {
                    if (response.exists) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Ya matriculado',
                            text: 'El estudiante ya está matriculado en este programa.',
                            confirmButtonText: 'Ok'
                        });
                        $('#programas').val(''); // Resetea la selección
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al validar el programa.',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectPrograma = document.getElementById('programas');
        const selectConvenio = document.getElementById('convenio_id');
        const inputValorMatricula = document.getElementById('valor_matricula');
        const inputValorMatriculaDisplay = document.getElementById('valor_matricula_display');

        function actualizarValorMatricula() {
            const programaId = selectPrograma.value;
            const convenioId = selectConvenio.value;

            // Validar si ambos campos están seleccionados
            if (!programaId || !convenioId) {
                inputValorMatricula.value = '';
                inputValorMatriculaDisplay.value = '$0';
                return;
            }

            // Construir la URL para la solicitud
            const url = `/5678AbCdEf/${programaId}/${convenioId}`;

            // Realizar la solicitud al backend
            fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Actualizar los campos con el valor obtenido
                        const valor = parseFloat(data.valor).toLocaleString('es-CO', {
                            style: 'currency',
                            currency: 'COP'
                        });

                        inputValorMatricula.value = data.valor;
                        inputValorMatriculaDisplay.value = valor;
                    } else {
                        inputValorMatricula.value = '';
                        inputValorMatriculaDisplay.value = '$0';
                    }
                })
                .catch(error => {
                    console.error("Error al obtener el valor del convenio:", error);
                    inputValorMatricula.value = '';
                    inputValorMatriculaDisplay.value = '$0';
                });
        }

        // Agregar eventos a los selects
        selectPrograma.addEventListener('change', actualizarValorMatricula);
        selectConvenio.addEventListener('change', actualizarValorMatricula);
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const buscarInput = document.getElementById('buscarEstudianteInput');
        const resultados = document.getElementById('estudianteResultados');
        const estudianteNombre = document.getElementById('estudiante_nombre');
        const estudianteId = document.getElementById('estudiante_id');

        // Buscar estudiantes usando AJAX
        buscarInput.addEventListener('input', function() {
            const query = buscarInput.value.trim();

            if (query.length >= 2) { // Realizar la búsqueda si hay al menos 2 caracteres
                fetch('/estudiantes/buscar/', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            termino: query
                        })
                    })
                    .then(response => {
                        return response.text(); // Obtener el texto en bruto para inspección
                    })
                    .then(text => {

                        try {
                            const data = JSON.parse(text); // Intentar convertir el texto en JSON

                            if (data.error) {
                                resultados.innerHTML = '<tr><td colspan="4" class="text-center">No se encontraron estudiantes</td></tr>';
                            } else {
                                resultados.innerHTML = data.map(est => `
                                <tr>
                                    <td>${est.numero_documento}</td>
                                    <td>${est.nombres} ${est.apellidos}</td>
                                    <td><input type="radio" name="seleccionar_estudiante" value="${est.id}" data-nombre="${est.nombres} ${est.apellidos}"></td>
                                </tr>
                            `).join('');
                                console.log('Resultados renderizados correctamente.'); // Confirmar que la lista se renderizó
                            }
                        } catch (e) {
                            alert('Error al procesar la respuesta del servidor. Verifica la consola para más detalles.');
                        }
                    })
                    .catch(error => {
                        resultados.innerHTML = '<tr><td colspan="4" class="text-center">Error en la búsqueda</td></tr>';
                    });
            } else {
                resultados.innerHTML = '<tr><td colspan="4" class="text-center">Ingrese al menos 2 caracteres para buscar</td></tr>';
            }
        });

        // Manejar la selección del estudiante
        resultados.addEventListener('change', function(e) {
            if (e.target.name === 'seleccionar_estudiante') {
                const nombreSeleccionado = e.target.getAttribute('data-nombre');
                console.log('Estudiante seleccionado:', nombreSeleccionado);
                estudianteNombre.value = nombreSeleccionado;
                estudianteId.value = e.target.value;
                // Cerrar el modal
                bootstrap.Modal.getInstance(document.getElementById('buscarEstudianteModal')).hide();
                console.log('Modal cerrado y datos actualizados.');
            }
        });

        // Validar el formulario antes de enviarlo
        const matriculaForm = document.getElementById('matriculaForm');


        matriculaForm.addEventListener('submit', function(event) {
            const estudianteIdValue = estudianteId.value.trim();
            const programasSelected = document.getElementById('programas').selectedOptions.length;

            if (!estudianteIdValue || programasSelected === 0) {
                event.preventDefault(); // Prevenir el envío del formulario
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Por favor, Ingrese los campos obligatorios.'
                });
            }
        });

        // Convertir a mayúsculas mientras el usuario escribe en el campo observaciones
        document.getElementById('observaciones').addEventListener('input', function(e) {
            e.target.value = e.target.value.toUpperCase();
        });

    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const programasSelect = document.getElementById("programas");
        const valorMatriculaDisplay = document.getElementById("valor_matricula_display");
        const valorMatriculaHidden = document.getElementById("valor_matricula");

        // Función para actualizar el valor de la matrícula
        function actualizarValorMatricula() {
            let totalValor = 0;

            // Iterar sobre las opciones seleccionadas
            Array.from(programasSelect.selectedOptions).forEach(option => {
                totalValor += parseInt(option.getAttribute("data-valor")) || 0;
            });

            // Formatear el valor total para mostrarlo
            let formattedValue = new Intl.NumberFormat('es-CO').format(totalValor); // Formato con puntos
            valorMatriculaDisplay.value = `$${formattedValue}`; // Mostrar el valor formateado
            valorMatriculaHidden.value = totalValor; // Guardar el valor sin formato en el campo oculto
        }

        // Evento al cambiar la selección de programas
        programasSelect.addEventListener("change", actualizarValorMatricula);

        // Evento para formatear manualmente el campo de entrada si el usuario lo edita directamente
        valorMatriculaDisplay.addEventListener("input", function(e) {
            let inputVal = e.target.value.replace(/\D/g, ''); // Eliminar caracteres no numéricos
            let formattedValue = new Intl.NumberFormat('es-CO').format(inputVal); // Formato con puntos
            valorMatriculaDisplay.value = `$${formattedValue}`; // Mostrar el valor formateado
            valorMatriculaHidden.value = inputVal; // Guardar el valor sin formato en el campo oculto
        });
    });
</script>