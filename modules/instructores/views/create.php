<?php $routes = include '../config/Routes.php'; ?>

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
                    <li class="breadcrumb-item"><a href="/instructores/">Instructores</a></li>
                    <li class="breadcrumb-item" aria-current="page">Nuevo instructor</li>
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
                <h5>Datos del nuevo instructor: los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</h5>
            </div>

            <div class="card-body">

                <form method="post" action="/instructoresstore/" class="validate-me" id="validate-me" enctype="multipart/form-data" data-validate>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombres" class="form-label">Nombres <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_documento" class="form-label">Tipo de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                    <?php foreach ($paramTiposDocumentos as $tipo) : ?>
                                        <option value="<?= $tipo['id'] ?>"><?= $tipo['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_documento" class="form-label">Número de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="number" class="form-control" id="numero_documento" name="numero_documento" required>
                                <div id="documento-feedback" class="invalid-feedback">
                                    El número de documento ya está en uso.
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expedicion_departamento" class="form-label">Expedición Departamento <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="expedicion_departamento" name="expedicion_departamento" required>
                                    <?php foreach ($paramDepartamentos as $departamento) : ?>
                                        <option value="<?= $departamento['id_departamento'] ?>"><?= $departamento['departamento'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expedicion_ciudad" class="form-label">
                                    Expedición Ciudad <i class="ph-duotone ph-asterisk"></i>
                                </label>
                                <select class="form-select" id="expedicion_ciudad" name="expedicion_ciudad" required>
                                    <option value="">Seleccione un municipio</option>
                                </select>
                            </div>
                        </div>




                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expedicion_ciudad" class="form-label">Expedición Ciudad <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="expedicion_ciudad" name="expedicion_ciudad" required>
                                    <?php foreach ($paramCiudades as $ciudad) : ?>
                                        <option value="<?= $ciudad['id'] ?>"><?= $ciudad['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div> -->

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_expedicion" class="form-label">Fecha de Expedición <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="email" class="form-control" id="email" name="correo" data-bouncer-message="Correo electrónico inválido." required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="celular" class="form-label">Celular <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="phone" name="celular" pattern="^\d{10}$" required>
                                <small class="form-text text-muted">El número debe tener exactamente 10 dígitos</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grupo_sanguineo" class="form-label">Grupo Sanguíneo <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="grupo_sanguineo" name="grupo_sanguineo" required>
                                    <?php foreach ($paramGrupoSanguineo as $grupo) : ?>
                                        <option value="<?= $grupo['id'] ?>"><?= $grupo['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="genero" class="form-label">Género </label>
                                <select class="form-select" id="genero" name="genero" required>
                                    <?php foreach ($paramGenero as $genero) : ?>
                                        <option value="<?= $genero['id'] ?>"><?= $genero['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado_civil" class="form-label">Estado Civil</label>
                                <select class="form-select" id="estado_civil" name="estado_civil">
                                    <?php foreach ($paramEstadoCivil as $estado) : ?>
                                        <option value="<?= $estado['id'] ?>"><?= $estado['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vencimiento_licencia_conduccion" class="form-label">Vencimiento Licencia de Conducción <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="vencimiento_licencia_conduccion" name="vencimiento_licencia_conduccion" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vencimiento_licencia_instructor" class="form-label">Vencimiento Licencia de Instructor <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="vencimiento_licencia_instructor" name="vencimiento_licencia_instructor" required>
                            </div>
                        </div>

                        <!-- Campo Estado con Switch -->
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="estado" name="estado" checked>
                                    <label class="form-check-label" id="estado-label" for="estado">Activo</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorias_conduccion" class="form-label">Categorías de Conducción <i class="ph-duotone ph-asterisk"></i></label>
                                <select multiple class="form-select" id="categorias_conduccion" name="categorias_conduccion[]" required>
                                    <?php foreach ($paramCategoriasConduccion as $categoria) : ?>
                                        <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorias_instructor" class="form-label">Categorías de Instructor <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-select" id="categorias_instructor" name="categorias_instructor[]" multiple required>
                                    <?php foreach ($categoriasInstructor as $categoria) : ?>
                                        <option value="<?= $categoria['id'] ?>"><?= $categoria['nombre'] ?></option>
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

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" id="submit_button" class="btn btn-primary">Enviar</button>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<script src="../assets/js/plugins/bouncer.min.js"></script>
<script src="../assets/js/pages/form-validation.js"></script>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

<script>
    $(document).ready(function() {

        $('#estado').on('change', function() {
            if ($(this).is(':checked')) {
                $('#estado-label').text('Activo');
            } else {
                $('#estado-label').text('Inactivo');
            }
        });


        $('#numero_documento').on('blur', function() {
            var numero_documento = $(this).val();

            if (numero_documento.length > 0) {
                $.ajax({
                    url: '/instructores_verificar_documento/',
                    type: 'POST',
                    data: {
                        numero_documento: numero_documento
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'exists') {
                            $('#numero_documento').addClass('is-invalid');
                            $('#documento-feedback').show();
                            $('#submit_button').prop('disabled', true); // Deshabilitar el botón de envío
                        } else {
                            $('#numero_documento').removeClass('is-invalid');
                            $('#documento-feedback').hide();
                            $('#submit_button').prop('disabled', false); // Habilitar el botón de envío
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.log("Error en la solicitud AJAX: ", textStatus, errorThrown);
                    }
                });
            } else {
                $('#numero_documento').removeClass('is-invalid');
                $('#documento-feedback').hide();
                $('#submit_button').prop('disabled', false); // Habilitar el botón de envío si el campo está vacío
            }
        });
    });
</script>


<script>
    // Agregar un evento 'change' al select de departamentos
    document.getElementById('expedicion_departamento').addEventListener('change', function() {
        const departamentoId = this.value.trim(); // Obtener el ID del departamento seleccionado y eliminar espacios en blanco
        const municipioSelect = document.getElementById('expedicion_ciudad'); // Obtener el select de municipios

        console.log('Departamento seleccionado:', departamentoId); // Depuración: mostrar ID del departamento seleccionado

        // Validar si el valor del departamento es válido
        if (!departamentoId || isNaN(departamentoId)) {
            console.warn('El ID del departamento seleccionado no es válido.');
            municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
            return;
        }

        // Mostrar mensaje mientras se cargan los datos
        municipioSelect.innerHTML = '<option value="">Cargando municipios...</option>';

        // Construir la ruta del endpoint
        const url = `<?= $routes['listado_municipios_por_departamento'] ?>${departamentoId}`;
        console.log('Ruta generada:', url); // Depuración: mostrar la URL generada

        // Realizar la petición al backend
        fetch(url)
            .then(response => {
                console.log('Respuesta del servidor:', response); // Depuración: mostrar respuesta completa
                // Verificar si la respuesta es exitosa
                if (!response.ok) {
                    throw new Error(`Error en la respuesta del servidor: ${response.status} ${response.statusText}`);
                }
                return response.json(); // Convertir la respuesta a formato JSON
            })
            .then(data => {
                console.log('Datos recibidos:', data); // Depuración: mostrar los datos recibidos

                // Validar si se recibieron datos válidos
                if (!Array.isArray(data) || data.length === 0) {
                    console.warn('No se encontraron municipios para el departamento seleccionado.');
                    municipioSelect.innerHTML = '<option value="">No hay municipios disponibles</option>';
                    return;
                }

                // Limpiar y llenar el select de municipios
                municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                data.forEach(municipio => {
                    const option = document.createElement('option');
                    option.value = municipio.id_municipio; // Asignar el ID del municipio como valor
                    option.textContent = municipio.municipio; // Asignar el nombre del municipio como texto
                    municipioSelect.appendChild(option); // Agregar la opción al select
                });
                console.log('Municipios cargados correctamente.'); // Confirmación en la consola
            })
            .catch(error => {
                // Manejar errores durante la solicitud
                municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>'; // Restablecer el select
                console.error('Error al cargar los municipios:', error); // Mostrar error en consola
                alert('Hubo un problema al cargar los municipios. Por favor, intente nuevamente.');
            });
    });
</script>