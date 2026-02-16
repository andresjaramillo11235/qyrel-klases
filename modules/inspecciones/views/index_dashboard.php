<div class="container-fluid py-3">
  <h4 class="mb-3">Dashboard de Inspecciones</h4>

  <div class="text-muted mb-3">
    <small>
      Hoy: <strong><?= htmlspecialchars($dashboard['rango']['hoy']) ?></strong> &nbsp;|&nbsp;
      Mes: <strong><?= htmlspecialchars($dashboard['rango']['mes']['inicio']) ?> → <?= htmlspecialchars($dashboard['rango']['mes']['fin']) ?></strong>
    </small>
  </div>

  <div class="row g-3">
    <!-- ====== COLUMNA IZQUIERDA (HOY y MES) ====== -->
    <div class="col-12 col-lg-5 d-flex flex-column gap-3">
      <!-- HOY -->
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <span>Inspecciones realizadas <strong>HOY</strong></span>
          <span class="text-muted small">Inspección por vehículo por día</span>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted small mb-1">Vehículos</div>
                <div class="display-6 fw-bold"><?= (int)$dashboard['hoy']['vehiculos'] ?></div>
                <div class="small text-muted">Inspecciones Vehículos</div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted small mb-1">Motos</div>
                <div class="display-6 fw-bold"><?= (int)$dashboard['hoy']['motos'] ?></div>
                <div class="small text-muted">Inspecciones Motos</div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100 bg-light">
                <div class="text-muted small mb-1">Total inspecciones HOY</div>
                <div class="display-6 fw-bold"><?= (int)$dashboard['hoy']['total'] ?></div>
                <div class="small text-muted">= Vehículos + Motos</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- MES EN CURSO -->
      <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
          <span>Inspecciones del <strong>MES en curso</strong></span>
          <span class="text-muted small">Rango: <?= htmlspecialchars($dashboard['rango']['mes']['inicio']) ?> → <?= htmlspecialchars($dashboard['rango']['mes']['fin']) ?></span>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted small mb-1">Vehículos</div>
                <div class="display-6 fw-bold"><?= (int)$dashboard['mes']['vehiculos'] ?></div>
                <div class="small text-muted">Inspecciones Vehículos</div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100">
                <div class="text-muted small mb-1">Motos</div>
                <div class="display-6 fw-bold"><?= (int)$dashboard['mes']['motos'] ?></div>
                <div class="small text-muted">Inspecciones Motos</div>
              </div>
            </div>
            <div class="col-12 col-md-4">
              <div class="border rounded p-3 h-100 bg-light">
                <div class="text-muted small mb-1">Total inspecciones del MES</div>
                <div class="display-6 fw-bold"><?= (int)$dashboard['mes']['total'] ?></div>
                <div class="small text-muted">= Vehículos + Motos</div>
              </div>
            </div>
          </div>
          <div class="text-muted mt-3">
            <small>*El total mensual es la suma de inspecciones registradas en el mes, separadas por día y vehículo.</small>
          </div>
        </div>
      </div>
    </div>

    <!-- ====== COLUMNA DERECHA (FALTANTES HOY) ====== -->
    <div class="col-12 col-lg-7">
      <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between">
          <div>
            Vehículos con clases <strong>HOY</strong> <span class="text-muted">sin inspección registrada</span>
          </div>
          <span class="badge bg-warning text-dark fs-6">
            <?= (int)$dashboard['hoy']['faltantes']['cantidad'] ?> faltantes
          </span>
        </div>
        <div class="card-body">
          <?php if (!empty($dashboard['hoy']['faltantes']['vehiculos'])): ?>
            <div class="table-responsive" style="max-height:70vh; overflow:auto;">
              <table class="table table-sm align-middle">
                <thead class="table-light">
                  <tr>
                    <th style="width: 120px;">Placa</th>
                    <th style="width: 100px;">Tipo</th>
                    <th style="width: 120px;">Modelo</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($dashboard['hoy']['faltantes']['vehiculos'] as $v): ?>
                    <tr>
                      <td class="fw-semibold"><?= htmlspecialchars($v['placa']) ?></td>
                      <td><?= (int)$v['tipo_vehiculo_id'] === 2 ? 'Moto' : 'Vehículo' ?></td>
                      <td><?= htmlspecialchars($v['modelo'] ?? '') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <div class="text-muted"><small>No hay faltantes hoy.</small></div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>
