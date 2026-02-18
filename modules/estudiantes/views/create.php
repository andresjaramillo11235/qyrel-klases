<?php if (!empty($error_message)) : ?>
    <div class="alert alert-danger" role="alert">
        <?= $error_message ?>
    </div>
<?php endif; ?>

<?php /** [ breadcrumb ] start */  ?>
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/estudiantes/"><?= LabelHelper::get('menu_estudiantes') ?></a></li>
                    <li class="breadcrumb-item" aria-current="page">Nuevo</li>
                </ul>
            </div>
            <div class="col-md-12">
                <div class="page-header-title">
                    <h2 class="mb-0"><?= LabelHelper::get('menu_estudiantes') ?></h2>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /** [ breadcrumb ] end */  ?>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h5>Los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.
                </h5>
            </div>

            <div class="card-body">

                <!-- Este input va FUERA del formulario -->
                <input type="text" id="scannerInput" autofocus style="position:absolute;opacity:0;">


                <form id="formAgregarEstudiante" method="post" action="/estudiantesstore/" enctype="multipart/form-data" class="validate-me" data-validate>

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_documento" class="form-label">Tipo de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="tipo_documento" name="tipo_documento" required>
                                    <?php foreach ($parametricTables['tipo_documento'] as $tipo_documento) : ?>
                                        <option value="<?php echo $tipo_documento['id']; ?>" <?= isset($form_data['tipo_documento']) && $form_data['tipo_documento'] == $tipo_documento['id'] ? 'selected' : '' ?>><?php echo $tipo_documento['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione el tipo de documento.</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="numero_documento" class="form-label">Número de Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="number" class="form-control" id="numero_documento" name="numero_documento" value="<?= htmlspecialchars($form_data['numero_documento'] ?? '') ?>" required>
                                <div id="documento-feedback" class="invalid-feedback">El número de documento ya está en uso.</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombres" class="form-label">Nombres <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control text-uppercase" id="nombres" name="nombres" value="<?= htmlspecialchars($form_data['nombres'] ?? '') ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese los nombres.</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="apellidos" class="form-label">Apellidos <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control text-uppercase" id="apellidos" name="apellidos" value="<?= htmlspecialchars($form_data['apellidos'] ?? '') ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese los apellidos.</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" value="<?= htmlspecialchars($form_data['fecha_nacimiento'] ?? '') ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese la fecha de nacimiento.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grupo_sanguineo" class="form-label">Grupo Sanguíneo <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="grupo_sanguineo" name="grupo_sanguineo" required>
                                    <?php foreach ($parametricTables['grupo_sanguineo'] as $grupo_sanguineo) : ?>
                                        <option value="<?php echo $grupo_sanguineo['id']; ?>" <?= isset($form_data['grupo_sanguineo']) && $form_data['grupo_sanguineo'] == $grupo_sanguineo['id'] ? 'selected' : '' ?>><?php echo $grupo_sanguineo['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione el grupo sanguíneo.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="genero" class="form-label">Género <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="genero" name="genero" required>
                                    <?php foreach ($parametricTables['genero'] as $genero) : ?>
                                        <option value="<?php echo $genero['id']; ?>" <?= isset($form_data['genero']) && $form_data['genero'] == $genero['id'] ? 'selected' : '' ?>><?php echo $genero['nombre']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="invalid-feedback">Por favor, seleccione el género.</div>
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo<i class="ph-duotone ph-asterisk"></i></label>
                                <input type="email" class="form-control text-lowercase" id="correo" name="correo" value="<?= htmlspecialchars($form_data['correo'] ?? '') ?>" required>
                                <div class="invalid-feedback">Por favor, ingrese el correo.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="celular" class="form-label">Celular <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="number" class="form-control" id="celular" name="celular" value="<?= htmlspecialchars($form_data['celular'] ?? '') ?>" minlength="10" maxlength="10" required>
                                <div class="invalid-feedback">Por favor, ingrese un número de celular de 10 dígitos.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre_contacto" class="form-label">Nombre del Contacto</label>
                                <input type="text" class="form-control text-uppercase" id="nombre_contacto" name="nombre_contacto" value="<?= htmlspecialchars($form_data['nombre_contacto'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono_contacto" class="form-label">Teléfono del Contacto</label>
                                <input type="text" class="form-control" id="telefono_contacto" name="telefono_contacto" value="<?= htmlspecialchars($form_data['telefono_contacto'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="observaciones" class="form-label">Observaciones</label>
                                <textarea class="form-control text-uppercase" id="observaciones" name="observaciones"><?= htmlspecialchars($form_data['observaciones'] ?? '') ?></textarea>
                            </div>
                        </div>

                        <?php if ($this->userUtils->isSuperAdmin($_SESSION['user_id'])) : ?>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="empresa_id" class="form-label">Empresa</label>
                                    <select class="form-control" id="empresa_id" name <select class="form-control" id="empresa_id" name="empresa_id">
                                        <?php foreach ($parametricTables['empresa'] as $empresa) : ?>
                                            <option value="<?php echo $empresa['id']; ?>" <?= isset($form_data['empresa_id']) && $form_data['empresa_id'] == $empresa['id'] ? 'selected' : '' ?>><?php echo $empresa['nombre']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto</label>
                                <input type="file" class="form-control" id="foto" name="foto">
                            </div>
                        </div>

                    </div>

                    <div class="col-md-12 text-end">
                        <button type="submit" id="submit_button" class="btn btn-primary">Enviar</button>
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
    $(document).ready(function() {

        // Convertir los campos a mayúsculas
        $('input.text-uppercase, textarea.text-uppercase').on('input', function() {
            this.value = this.value.toUpperCase();
        });

        var celularInput = document.getElementById('celular');

        // Custom validation message
        celularInput.oninvalid = function(event) {
            event.target.setCustomValidity('Por favor, ingrese un número de celular de 10 dígitos.');
        }

        // Remove custom validation message on input
        celularInput.oninput = function(event) {
            event.target.setCustomValidity('');
            var celular = event.target.value;

            // Eliminar caracteres no numéricos
            celular = celular.replace(/\D/g, '');
            event.target.value = celular;

            if (celular.length !== 10) {
                $(event.target).addClass('is-invalid');
                $('#submit_button').prop('disabled', true); // Deshabilitar el botón de envío
            } else {
                $(event.target).removeClass('is-invalid');
                $('#submit_button').prop('disabled', false); // Habilitar el botón de envío
            }
        };

        // Validación adicional al enviar el formulario
        $('#formulario_estudiante').on('submit', function(event) {
            var celular = $('#celular').val();
            if (celular.length !== 10) {
                $('#celular').addClass('is-invalid');
                event.preventDefault(); // Prevenir el envío del formulario
            }
        });
    });
</script>

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
                    url: '/estudiantes_verificar_documento/',
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
    $(document).ready(function() {
        $('#correo').on('blur', function() {
            var correo = $(this).val().trim(); // Eliminar espacios en blanco

            // Validar que el correo no esté vacío
            if (correo.length === 0) {
                $('#correo').removeClass('is-invalid');
                $('#correo-feedback').remove();
                $('#submit_button').prop('disabled', false); // Habilitar el botón si el campo está vacío
                return;
            }

            // Validar el formato del correo electrónico
            var correoRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!correoRegex.test(correo)) {
                $('#correo').addClass('is-invalid');
                $('#correo-feedback').remove(); // Evitar duplicados
                $('#correo').after('<div id="correo-feedback" class="invalid-feedback">El formato del correo es inválido.</div>');
                $('#submit_button').prop('disabled', true); // Deshabilitar el botón
                return;
            }

            // Realizar la solicitud AJAX si el formato del correo es válido
            $.ajax({
                url: '/estudiantes_verificar_correo/', // Ruta al controlador
                type: 'POST',
                data: {
                    correo: correo
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'exists') {
                        $('#correo').addClass('is-invalid');
                        $('#correo-feedback').remove(); // Evitar duplicados
                        $('#correo').after('<div id="correo-feedback" class="invalid-feedback">El correo ya está registrado.</div>');
                        $('#submit_button').prop('disabled', true); // Deshabilitar el botón de envío
                    } else if (response.status === 'available') {
                        $('#correo').removeClass('is-invalid');
                        $('#correo-feedback').remove();
                        $('#submit_button').prop('disabled', false); // Habilitar el botón de envío
                    } else if (response.status === 'error') {
                        console.error(response.message); // Mostrar el mensaje de error del backend
                        alert('Hubo un error al validar el correo. Intente nuevamente.');
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error en la solicitud AJAX: ", textStatus, errorThrown);
                    alert('Error al conectar con el servidor. Por favor, intente más tarde.');
                }
            });
        });
    });
</script>