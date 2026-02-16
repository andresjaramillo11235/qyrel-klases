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
                    <li class="breadcrumb-item" aria-current="page">Modificar instructor</li>
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
                <h5>Modificar datos del instructor.</h5>
            </div>

            <div class="card-body">
                <form method="post" action="/instructores/update/<?= $instructor['id'] ?>" enctype="multipart/form-data">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                                <?php if (!empty($instructor['foto'])) : ?>
                                    <img src="../files/fotos_instructores/<?= $instructor['foto'] ?>" alt="Foto del instructor" class="img-thumbnail mt-2" style="max-width: 150px;">
                                    <input type="hidden" name="existing_foto" value="<?= $instructor['foto'] ?>">
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" value="<?= $instructor['nombres'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= $instructor['apellidos'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_documento" class="form-label">Tipo de Documento</label>
                                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                                    <?php foreach ($paramTiposDocumentos as $tipo) : ?>
                                        <option value="<?= $tipo['id'] ?>" <?= $instructor['tipo_documento'] == $tipo['id'] ? 'selected' : '' ?>><?= $tipo['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>

                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_documento" class="form-label">Número de Documento</label>
                                <input type="text" class="form-control" id="numero_documento" name="numero_documento" value="<?= $instructor['numero_documento'] ?>" readonly>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expedicion_departamento" class="form-label">Expedición Departamento</label>
                                <select class="form-select" id="expedicion_departamento" name="expedicion_departamento" required>
                                    <?php foreach ($paramDepartamentos as $departamento) : ?>
                                        <option value="<?= $departamento['id_departamento'] ?>"
                                            <?= $instructor['expedicion_departamento'] == $departamento['id_departamento'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($departamento['departamento']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="expedicion_ciudad" class="form-label">Expedición Ciudad</label>
                                <select class="form-select" id="expedicion_ciudad" name="expedicion_ciudad" required>
                                    <?php foreach ($paramCiudades as $ciudad) : ?>
                                        <option value="<?= $ciudad['id_municipio'] ?>" <?= $instructor['expedicion_ciudad'] == $ciudad['id_municipio'] ? 'selected' : '' ?>>
                                            <?= $ciudad['municipio'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_expedicion" class="form-label">Fecha de Expedición</label>
                                <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion" value="<?= $instructor['fecha_expedicion'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?= $instructor['correo'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="celular" class="form-label">Celular</label>
                                <input type="text" class="form-control" id="celular" name="celular" value="<?= $instructor['celular'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección</label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="<?= $instructor['direccion'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grupo_sanguineo" class="form-label">Grupo Sanguíneo</label>
                                <select class="form-select" id="grupo_sanguineo" name="grupo_sanguineo" required>
                                    <?php foreach ($paramGrupoSanguineo as $grupo) : ?>
                                        <option value="<?= $grupo['id'] ?>" <?= $instructor['grupo_sanguineo'] == $grupo['id'] ? 'selected' : '' ?>><?= $grupo['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="genero" class="form-label">Género</label>
                                <select class="form-select" id="genero" name="genero" required>
                                    <?php foreach ($paramGenero as $genero) : ?>
                                        <option value="<?= $genero['id'] ?>" <?= $instructor['genero'] == $genero['id'] ? 'selected' : '' ?>><?= $genero['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado_civil" class="form-label">Estado Civil</label>
                                <select class="form-select" id="estado_civil" name="estado_civil" required>
                                    <?php foreach ($paramEstadoCivil as $estado) : ?>
                                        <option value="<?= $estado['id'] ?>" <?= $instructor['estado_civil'] == $estado['id'] ? 'selected' : '' ?>><?= $estado['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vencimiento_licencia_conduccion" class="form-label">Vencimiento Licencia de Conducción</label>
                                <input type="date" class="form-control" id="vencimiento_licencia_conduccion" name="vencimiento_licencia_conduccion" value="<?= $instructor['vencimiento_licencia_conduccion'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vencimiento_licencia_instructor" class="form-label">Vencimiento Licencia de Instructor</label>
                                <input type="date" class="form-control" id="vencimiento_licencia_instructor" name="vencimiento_licencia_instructor" value="<?= $instructor['vencimiento_licencia_instructor'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="estado" name="estado" <?= $instructor['estado'] == '1' ? 'checked' : '' ?>>
                                    <label class="form-check-label" id="estado-label" for="estado"><?= $instructor['estado'] == '1' ? 'Activo' : 'Inactivo' ?></label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control" id="observaciones" name="observaciones"><?= $instructor['observaciones'] ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="categorias_conduccion" class="form-label">Categorías de Conducción</label>
                                <select multiple class="form-select" id="categorias_conduccion" name="categorias_conduccion[]" required>
                                    <?php foreach ($paramCategoriasConduccion as $categoria) : ?>
                                        <option value="<?= $categoria['id'] ?>" <?= in_array($categoria['id'], $instructorCategoriasConduccion) ? 'selected' : '' ?>><?= $categoria['nombre'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="submit" class="btn btn-primary" value="Modificar">
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('El script está cargado.');

        // Obtener el select de departamento
        const departamentoSelect = document.getElementById('expedicion_departamento');
        const municipioSelect = document.getElementById('expedicion_ciudad'); // Select de municipios

        // Validar si el select existe
        if (!departamentoSelect) {
            console.error('El elemento "expedicion_departamento" no existe.');
            return;
        }

        // Agregar el evento change al select de departamento
        departamentoSelect.addEventListener('change', function() {
            const departamentoId = this.value.trim(); // Obtener el valor del departamento seleccionado
            console.log('Departamento seleccionado:', departamentoId);

            // Validar si el ID del departamento es válido
            if (!departamentoId || isNaN(departamentoId)) {
                console.warn('El ID del departamento seleccionado no es válido.');
                municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                return;
            }

            // Mostrar mensaje mientras se cargan los datos
            municipioSelect.innerHTML = '<option value="">Cargando municipios...</option>';

            // Construir la URL del endpoint
            const url = `<?= $routes['listado_municipios_por_departamento'] ?>${departamentoId}`;
            console.log('Ruta generada:', url);

            // Realizar la solicitud al backend
            fetch(url)
                .then(response => {
                    console.log('Respuesta del servidor:', response);
                    if (!response.ok) {
                        throw new Error(`Error en la respuesta del servidor: ${response.status} ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Datos recibidos:', data);

                    // Validar que se reciban datos válidos
                    if (!Array.isArray(data) || data.length === 0) {
                        municipioSelect.innerHTML = '<option value="">No hay municipios disponibles</option>';
                        console.warn('No se encontraron municipios para el departamento seleccionado.');
                        return;
                    }

                    // Limpiar y llenar el select de municipios
                    municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                    data.forEach(municipio => {
                        municipioSelect.innerHTML += `<option value="${municipio.id_municipio}">${municipio.municipio}</option>`;
                    });
                    console.log('Municipios cargados correctamente.');
                })
                .catch(error => {
                    console.error('Error al cargar los municipios:', error);
                    municipioSelect.innerHTML = '<option value="">Seleccione un municipio</option>';
                    alert('Hubo un problema al cargar los municipios. Por favor, intente nuevamente.');
                });



            // Aquí puedes agregar lógica adicional, como cargar los municipios
        });
    });
</script>