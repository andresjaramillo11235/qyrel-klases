<?php if (isset($_SESSION['valor_creado'])) : ?>
  <script>
    Swal.fire({
      icon: 'success',
      text: '<?php echo $_SESSION['valor_creado'];
              unset($_SESSION['valor_creado']); ?>'
    });
  </script>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="/home/">Home</a></li>
          <li class="breadcrumb-item"><a href="/convenios/">Convenios</a></li>
          <li class="breadcrumb-item" aria-current="page">Gestionar Valores</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
  <!-- Formulario para agregar un nuevo valor -->
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <h5>Agregar Valor al Convenio: <?= htmlspecialchars($convenio['nombre']) ?></h5>
      </div>
      <div class="card-body">
        <form action="/convenios-guardar-valores/" method="post">
          <input type="hidden" name="convenio_id" value="<?= $convenio['id'] ?>">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label for="programa" class="form-label">Programa</label>
                <select name="programa" class="form-control" id="programa" required>
                  <option value="">Seleccione un programa</option>
                  <?php foreach ($programas as $programa) : ?>
                    <option value="<?= $programa['id'] ?>"><?= htmlspecialchars($programa['nombre']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label for="valor" class="form-label">Valor</label>
                <input type="number" name="valor" class="form-control" required>
              </div>
            </div>
          </div>
          <div class="text-end">
            <button type="submit" class="btn btn-success">Agregar Valor</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Listado de valores existentes -->
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <h5>Valores Existentes</h5>
      </div>
      <div class="card-body">
        <div class="dt-responsive table-responsive">

          <table class="table">
            <thead>
              <tr>
                <th>Programa</th>
                <th>Valor</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($valores as $valor): ?>
                <tr>
                  <td><?= htmlspecialchars($valor['programa_nombre']) ?></td>
                  <td>
                    <span class="valor-text" id="valor-text-<?= $valor['id'] ?>">
                      $ <?= number_format($valor['valor'], 0, ',', '.') ?>
                    </span>
                    <input type="number" class="form-control d-none valor-input"
                      id="valor-input-<?= $valor['id'] ?>" value="<?= htmlspecialchars($valor['valor']) ?>">
                  </td>
                  <td>
                    <button class="btn btn-warning btn-sm edit-button" onclick="toggleEdit(<?= $valor['id'] ?>)">Editar</button>
                    <button class="btn btn-success btn-sm d-none save-button" onclick="saveValor(<?= $valor['id'] ?>)">Guardar</button>
                    <button class="btn btn-secondary btn-sm d-none cancel-button" onclick="toggleEdit(<?= $valor['id'] ?>, true)">Cancelar</button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>

          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  function toggleEdit(id, cancel = false) {
    const valorText = document.getElementById(`valor-text-${id}`);
    const valorInput = document.getElementById(`valor-input-${id}`);
    const editButton = document.querySelector(`#valor-text-${id}`).parentElement.nextElementSibling.children[0];
    const saveButton = editButton.nextElementSibling;
    const cancelButton = saveButton.nextElementSibling;

    if (cancel) {
      valorInput.value = valorText.textContent.trim(); // Restaurar el valor original si se cancela
    }

    valorText.classList.toggle('d-none');
    valorInput.classList.toggle('d-none');
    editButton.classList.toggle('d-none');
    saveButton.classList.toggle('d-none');
    cancelButton.classList.toggle('d-none');
  }

  function saveValor(id) {
    const valorInput = document.getElementById(`valor-input-${id}`);
    const valor = valorInput.value;

    // Enviar la actualización al servidor
    fetch(`/convenios-valores-update/`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          id: id,
          valor: valor
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          document.getElementById(`valor-text-${id}`).textContent = valor;
          toggleEdit(id, true);
          Swal.fire('Guardado', 'El valor ha sido actualizado con éxito.', 'success');
        } else {
          Swal.fire('Error', 'Hubo un problema al guardar el valor.', 'error');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Hubo un problema al guardar el valor.', 'error');
      });
  }
</script>