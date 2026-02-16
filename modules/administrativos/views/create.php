<?php
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];
unset($_SESSION['form_data']);
?>

<?php if (isset($_SESSION['username_error'])) : ?>
  <script>
    const errorMessage = <?php echo json_encode($_SESSION['username_error']); ?>;
    Swal.fire({
      icon: 'error',
      text: errorMessage
    });
  </script>
  <?php unset($_SESSION['username_error']); ?>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="/home/">Home</a></li>
          <li class="breadcrumb-item"><a href="/administrativos/">Administrativos</a></li>
          <li class="breadcrumb-item" aria-current="page">Nuevo administrativo</li>
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
        <h5>Datos del nuevo administrativo: los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</h5>
      </div>

      <div class="card-body">

        <form method="post" action="/administrativos-store/" class="validate-me" id="validate-me" enctype="multipart/form-data" data-validate>

          <div class="row">

            <div class="col-md-6">
              <div class="mb-3">
                <label for="nombres" class="form-label">Nombres <i class="ph-duotone ph-asterisk"></i></label>
                <input type="text" class="form-control" id="nombres" name="nombres"
                  value="<?= isset($form_data['nombres']) ? htmlspecialchars($form_data['nombres']) : '' ?>" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="apellidos" class="form-label">Apellidos <i class="ph-duotone ph-asterisk"></i></label>
                <input type="text" class="form-control" id="apellidos" name="apellidos"
                  value="<?= isset($form_data['apellidos']) ? htmlspecialchars($form_data['apellidos']) : '' ?>" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="username" class="form-label">Nombre de Usuario <i class="ph-duotone ph-asterisk"></i></label>
                <input type="text" class="form-control" id="username" name="username"
                  value="<?= isset($form_data['username']) ? htmlspecialchars($form_data['username']) : '' ?>" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="tipo_documento" class="form-label">Tipo de Documento <i class="ph-duotone ph-asterisk"></i></label>
                <select class="form-select" id="tipo_documento" name="tipo_documento" required>
                  <?php foreach ($paramTiposDocumentos as $tipo) : ?>
                    <option value="<?= $tipo['id'] ?>" <?= isset($form_data['tipo_documento']) && $form_data['tipo_documento'] == $tipo['id'] ? 'selected' : '' ?>>
                      <?= $tipo['nombre'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>


            <div class="col-md-6">
              <div class="mb-3">
                <label for="numero_documento" class="form-label">Número de Documento <i class="ph-duotone ph-asterisk"></i></label>
                <input type="number" class="form-control" id="numero_documento" name="numero_documento"
                  value="<?= isset($form_data['numero_documento']) ? htmlspecialchars($form_data['numero_documento']) : '' ?>" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="expedicion_departamento" class="form-label">Expedición Departamento <i class="ph-duotone ph-asterisk"></i></label>
                <select class="form-select" id="expedicion_departamento" name="expedicion_departamento" required>
                  <?php foreach ($paramDepartamentos as $departamento) : ?>
                    <option value="<?= $departamento['id'] ?>"
                      <?= isset($form_data['expedicion_departamento']) && $form_data['expedicion_departamento'] == $departamento['id'] ? 'selected' : '' ?>>
                      <?= $departamento['nombre'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="expedicion_ciudad" class="form-label">Expedición Ciudad <i class="ph-duotone ph-asterisk"></i></label>
                <select class="form-select" id="expedicion_ciudad" name="expedicion_ciudad" required>
                  <?php foreach ($paramCiudades as $ciudad) : ?>
                    <option value="<?= $ciudad['id'] ?>"
                      <?= isset($form_data['expedicion_ciudad']) && $form_data['expedicion_ciudad'] == $ciudad['id'] ? 'selected' : '' ?>>
                      <?= $ciudad['nombre'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="fecha_expedicion" class="form-label">Fecha de Expedición <i class="ph-duotone ph-asterisk"></i></label>
                <input type="date" class="form-control" id="fecha_expedicion" name="fecha_expedicion"
                  value="<?= isset($form_data['fecha_expedicion']) ? htmlspecialchars($form_data['fecha_expedicion']) : '' ?>" required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="correo" class="form-label">Correo <i class="ph-duotone ph-asterisk"></i></label>
                <input type="email" class="form-control" id="correo" name="correo"
                  value="<?= isset($form_data['correo']) ? htmlspecialchars($form_data['correo']) : '' ?>"
                  data-bouncer-message="Correo electrónico inválido." required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="celular" class="form-label">Celular <i class="ph-duotone ph-asterisk"></i></label>
                <input type="tel" class="form-control" id="celular" name="celular"
                  value="<?= isset($form_data['celular']) ? htmlspecialchars($form_data['celular']) : '' ?>"
                  pattern="\d{10}" required>
                <div id="celular-feedback" class="invalid-feedback">
                  El número de celular debe tener 10 dígitos.
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="direccion" class="form-label">Dirección <i class="ph-duotone ph-asterisk"></i></label>
                <input type="text" class="form-control" id="direccion" name="direccion"
                  value="<?= isset($form_data['direccion']) ? htmlspecialchars($form_data['direccion']) : '' ?>"
                  required>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="grupo_sanguineo" class="form-label">Grupo Sanguíneo <i class="ph-duotone ph-asterisk"></i></label>
                <select class="form-select" id="grupo_sanguineo" name="grupo_sanguineo" required>
                  <?php foreach ($paramGrupoSanguineo as $grupo) : ?>
                    <option value="<?= $grupo['id'] ?>"
                      <?= isset($form_data['grupo_sanguineo']) && $form_data['grupo_sanguineo'] == $grupo['id'] ? 'selected' : '' ?>>
                      <?= $grupo['nombre'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="genero" class="form-label">Género </label>
                <select class="form-select" id="genero" name="genero" required>
                  <?php foreach ($paramGenero as $genero) : ?>
                    <option value="<?= $genero['id'] ?>"
                      <?= isset($form_data['genero']) && $form_data['genero'] == $genero['id'] ? 'selected' : '' ?>>
                      <?= $genero['nombre'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="estado_civil" class="form-label">Estado Civil <i class="ph-duotone ph-asterisk"></i></label>
                <select class="form-select" id="estado_civil" name="estado_civil" required>
                  <?php foreach ($paramEstadoCivil as $estado) : ?>
                    <option value="<?= $estado['id'] ?>"
                      <?= isset($form_data['estado_civil']) && $form_data['estado_civil'] == $estado['id'] ? 'selected' : '' ?>>
                      <?= $estado['nombre'] ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="rol" class="form-label">Rol <i class="ph-duotone ph-asterisk"></i></label>
                <select class="form-control" id="rol" name="rol" required>
                  <?php foreach ($roles as $rol) : ?>
                    <option value="<?= $rol['id'] ?>"
                      <?= isset($form_data['rol']) && $form_data['rol'] == $rol['id'] ? 'selected' : '' ?>>
                      <?= htmlspecialchars($rol['description']) ?> (<?= htmlspecialchars($rol['name']) ?>)
                    </option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="estado" name="estado"
                    <?= isset($form_data['estado']) && $form_data['estado'] == 1 ? 'checked' : '' ?>>
                  <label class="form-check-label" id="estado-label" for="estado">
                    <?= isset($form_data['estado']) && $form_data['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                  </label>
                </div>
              </div>
            </div>

            <div class="col-md-6">
              <div class="mb-3">
                <label for="observaciones" class="form-label">Observaciones</label>
                <textarea class="form-control" id="observaciones" name="observaciones"><?= isset($form_data['observaciones']) ? htmlspecialchars($form_data['observaciones']) : '' ?></textarea>
              </div>
            </div>

            <div class="col-md-12">
              <div class="mb-3">
                <label for="foto" class="form-label">Foto</label>
                <input type="file" class="form-control" id="foto" name="foto">
                <?php if (isset($form_data['foto']) && !empty($form_data['foto'])) : ?>
                  <div class="mt-2">
                    <img src="/files/fotos_administrativos/<?= htmlspecialchars($form_data['foto']) ?>"
                      alt="Foto actual" class="img-thumbnail" width="100">
                  </div>
                <?php endif; ?>
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

    document.getElementById('nombres').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('apellidos').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('direccion').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('correo').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('observaciones').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });

  });
</script>