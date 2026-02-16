<!-- [ breadcrumb ] start -->
<div class="page-header">
  <div class="page-block">
    <div class="row align-items-center">
      <div class="col-md-12">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="/home/">Home</a></li>
          <li class="breadcrumb-item"><a href="/administrativos/">Administrativos</a></li>
          <li class="breadcrumb-item" aria-current="page">Detalle Administrativo</li>
        </ul>
      </div>
    </div>
  </div>
</div>
<!-- [ breadcrumb ] end -->

<div class="row">
  <div class="col-sm-12">
    <div class="row">
      <div class="col-lg-5 col-xxl-3">
        <div class="card overflow-hidden">
          <div class="card-body position-relative">
            <div class="text-center mt-3">
              <div class="chat-avtar d-inline-flex mx-auto">
                <?php if (!empty($administrativo['foto'])) : ?>
                  <img class="rounded-circle img-fluid wid-180 img-thumbnail" src="/files/fotos_administrativos/<?php echo $administrativo['foto']; ?>"
                    alt="<?php echo htmlspecialchars($administrativo['nombres']); ?>"
                    class="img-thumbnail mt-2 wid-40">
                <?php else : ?>
                  <img class="rounded-circle img-fluid wid-90 img-thumbnail" src="/assets/images/user/avatar-1.jpg" alt="<?php echo htmlspecialchars($administrativo['nombres']); ?>" class="img-radius wid-40">
                <?php endif; ?>
                <i class="chat-badge bg-success me-2 mb-2"></i>
              </div>
              <h5 class="mb-0"><?= htmlspecialchars($administrativo['nombres']) ?> <?= htmlspecialchars($administrativo['apellidos']) ?></h5>
              <p class="text-muted text-sm"><?= htmlspecialchars($administrativo['rol_description']) ?></p>
            </div>
          </div>
          <div class="nav flex-column nav-pills list-group list-group-flush account-pills mb-0" id="user-set-tab"
            role="tablist" aria-orientation="vertical">
            <a class="nav-link list-group-item list-group-item-action active" id="user-set-profile-tab"
              data-bs-toggle="pill" href="#user-set-profile" role="tab" aria-controls="user-set-profile"
              aria-selected="true">
              <span class="f-w-500"><i class="ph-duotone ph-user-circle m-r-10"></i>Información personal</span>
            </a>
          </div>
        </div>
      </div>
      <div class="col-lg-7 col-xxl-9">
        <div class="tab-content" id="user-set-tabContent">
          <div class="tab-pane fade show active" id="user-set-profile" role="tabpanel"
            aria-labelledby="user-set-profile-tab">
            <div class="card">
              <div class="card-header">
                <h5>Información personal</h5>
              </div>
              <div class="card-body">
                <ul class="list-group list-group-flush">
                  <li class="list-group-item px-0 pt-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Nombres</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['nombres']) ?></p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Apellidos</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['apellidos']) ?></p>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item px-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Nombre de Usuario</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['username']) ?></p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Tipo de Documento</p>
                        <p class="mb-0"><?= htmlspecialchars($paramTiposDocumentos[array_search($administrativo['tipo_documento'], array_column($paramTiposDocumentos, 'id'))]['nombre']) ?></p>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item px-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Número de Documento</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['numero_documento']) ?></p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Expedición Departamento</p>
                        <p class="mb-0"><?= htmlspecialchars($paramDepartamentos[array_search($administrativo['expedicion_departamento'], array_column($paramDepartamentos, 'id'))]['nombre']) ?></p>
                      </div>
                    </div>
                  </li>

                  <li class="list-group-item px-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Expedición Ciudad</p>
                        <p class="mb-0">
                          <?= htmlspecialchars($paramCiudades[array_search($administrativo['expedicion_ciudad'], array_column($paramCiudades, 'id'))]['nombre']) ?>
                        </p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Fecha de Expedición</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['fecha_expedicion']) ?></p>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item px-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Correo</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['correo']) ?></p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Celular</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['celular']) ?></p>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item px-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Dirección</p>
                        <p class="mb-0"><?= htmlspecialchars($administrativo['direccion']) ?>
                        </p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Grupo Sanguíneo</p>
                        <p class="mb-0">
                          <?= htmlspecialchars($paramGrupoSanguineo[array_search($administrativo['grupo_sanguineo'], array_column($paramGrupoSanguineo, 'id'))]['nombre']) ?>
                        </p>
                      </div>
                    </div>
                  </li>

                  <li class="list-group-item px-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Género</p>
                        <p class="mb-0">
                          <?= htmlspecialchars($paramGenero[array_search($administrativo['genero'], array_column($paramGenero, 'id'))]['nombre']) ?>
                        </p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Estado Civil</p>
                        <p class="mb-0">
                          <?= htmlspecialchars($paramEstadoCivil[array_search($administrativo['estado_civil'], array_column($paramEstadoCivil, 'id'))]['nombre']) ?>
                        </p>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item px-0">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Rol</p>
                        <p class="mb-0">
                          <?= htmlspecialchars($administrativo['rol_description']) ?>
                        </p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1 text-muted">Estado</p>
                        <p class="mb-0">
                          <?= $administrativo['estado'] == 1 ? 'Activo' : 'Inactivo' ?>
                        </p>
                      </div>
                    </div>
                  </li>
                  <li class="list-group-item px-0 pb-0">
                    <p class="mb-1 text-muted">Observaciones</p>
                    <p class="mb-0"><?= htmlspecialchars($administrativo['observaciones']) ?></p>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>