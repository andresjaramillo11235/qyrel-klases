<?php if (isset($_SESSION['convenio_creado'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['convenio_creado']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage
        });
    </script>
    <?php unset($_SESSION['convenio_creado']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['convenio_modificado'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['convenio_modificado']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage
        });
    </script>
    <?php unset($_SESSION['convenio_modificado']); ?>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
          <li class="breadcrumb-item" aria-current="page">Convenios</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
  <div class="col-sm-12">
    <div class="card">
      
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Listado de convenios.</h5>
        <div>
          <a href="/convenios-create/" class="btn btn-primary">Nuevo convenio</a>
        </div>
      </div>

      <div class="card-body">
        <div class="dt-responsive table-responsive">
          <table class="table table-hover data-table" id="pc-dt-simple">
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Teléfono</th>
                <th>Tipo Convenio</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($convenios as $convenio) : ?>
                <tr>
                  <td><?= htmlspecialchars($convenio['nombre']); ?></td>
                  <td><?= htmlspecialchars($convenio['documento']); ?></td>
                  <td><?= htmlspecialchars($convenio['telefono']); ?></td>
                  <td><?= htmlspecialchars($convenio['tipo_convenio']); ?></td>

                  <td>
                    <a href="/convenios-edit/<?= $convenio['id'] ?>" class="avtar avtar-xs btn-link-secondary" title="Modificar convenio">
                      <i class="ti ti-edit f-20"></i>
                    </a>
                    <a href="/convenios-valores/<?= $convenio['id'] ?>" class="avtar avtar-xs btn-link-secondary" title="Gestionar Valores">
                      <i class="ti ti-wallet f-20"></i>
                    </a>
                    <!-- <a href="/convenios/delete/<?= $convenio['id'] ?>" class="avtar avtar-xs btn-link-secondary">
                                            <i class="ti ti-trash f-20"></i>
                                        </a> -->
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<script src="../assets/js/datatables-config.js"></script>
