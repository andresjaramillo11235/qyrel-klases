<?php $routes = include '../config/Routes.php'; ?>

<?php if (isset($_SESSION['success_message'])): ?>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			Swal.fire({
				icon: 'success',
				title: '¡Éxito!',
				text: "<?php echo $_SESSION['success_message']; ?>"
			});
		});
	</script>
	<?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
	<script>
		document.addEventListener("DOMContentLoaded", function() {
			Swal.fire({
				icon: 'error',
				title: 'Error',
				text: "<?php echo $_SESSION['error_message']; ?>"
			});
		});
	</script>
	<?php unset($_SESSION['error_message']); ?>
<?php endif; ?>


<div class="container-fluid py-3">
	<h4 class="mb-3">Cuentas y Subcuentas de Egreso</h4>

	<?php if (!empty($_SESSION['flash_error'])): ?>
		<div class="alert alert-danger"><?= htmlspecialchars($_SESSION['flash_error']);
										unset($_SESSION['flash_error']); ?></div>
	<?php endif; ?>
	<?php if (!empty($_SESSION['flash_success'])): ?>
		<div class="alert alert-success"><?= htmlspecialchars($_SESSION['flash_success']);
											unset($_SESSION['flash_success']); ?></div>
	<?php endif; ?>

	<div class="d-flex justify-content-end mb-2 gap-2">
		<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNuevaCuenta">
			<i class="fas fa-plus-circle"></i> Nueva cuenta
		</button>
	</div>

	<div class="card">
		<div class="card-body">
			<?php if (!empty($cuentas)): ?>
				<div class="table-responsive">
					<table class="table table-sm align-middle">
						<thead class="table-light">
							<tr>
								<th style="width:45%;">Cuenta</th>
								<th style="width:15%;">Tipo</th>
								<th style="width:39%;">Subcuentas</th>
								<th style="width:1px; white-space:nowrap;">Acciones</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($cuentas as $c):
								$sub = $mapSub[$c['id']] ?? [];
							?>
								<tr>
									<td class="fw-semibold"><?= htmlspecialchars($c['nombre']) ?></td>

									<!-- Tipo de egreso -->
									<td><?= htmlspecialchars($c['tipo_nombre'] ?? ($mapTipos[(int)$c['tipo_egreso_id']] ?? '')) ?></td>

									<!-- Subcuentas -->
									<td>
										<?php if (!empty($sub)): ?>
											<ul class="mb-0 ps-3">
												<?php foreach ($sub as $s): ?>
													<li class="mb-1 d-flex align-items-center justify-content-between">
														<div><?= htmlspecialchars($s['nombre']) ?></div>
														<div class="btn-group btn-group-sm">
															<!-- Editar subcuenta -->
															<button
																class="btn"
																title="Editar"
																data-bs-toggle="modal"
																data-bs-target="#modalEditarSubcuenta"
																data-id="<?= (int)$s['id'] ?>"
																data-nombre="<?= htmlspecialchars($s['nombre']) ?>"
																data-cuenta="<?= (int)$c['id'] ?>">
																<i class="ti ti-edit"></i>
															</button>
															<!-- Eliminar subcuenta -->
															<form action="<?php echo $routes['egresos_subcuentas_egreso_delete'] ?>" method="post" class="d-inline"
																onsubmit="return confirm('¿Eliminar la subcuenta «<?= htmlspecialchars($s['nombre']) ?>»?');">
																<input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
																<button type="submit" class="btn" title="Eliminar">
																	<i class="ti ti-trash"></i>
																</button>
															</form>
														</div>
													</li>
												<?php endforeach; ?>
											</ul>
										<?php else: ?>
											<span class="text-muted">— Sin subcuentas —</span>
										<?php endif; ?>

										<div class="mt-2">
											<button
												class="btn btn-sm btn-outline-success"
												data-bs-toggle="modal"
												data-bs-target="#modalNuevaSubcuenta"
												data-cuenta="<?= (int)$c['id'] ?>"
												data-cuenta-nombre="<?= htmlspecialchars($c['nombre']) ?>">
												<i class="fas fa-plus"></i> Agregar subcuenta
											</button>
										</div>
									</td>

									<!-- Acciones cuenta -->
									<td class="text-end">
										<div class="btn-group btn-group-sm">

											<!-- boton editar cuenta -->
											<button
												class="btn"
												data-bs-toggle="modal"
												data-bs-target="#modalEditarCuenta"
												data-id="<?= (int)$c['id'] ?>"
												data-nombre="<?= htmlspecialchars($c['nombre']) ?>">
												<i class="ti ti-edit"></i>
											</button>

											<!-- Eliminar cuenta -->
											<form action="<?php echo $routes['egresos_cuentas_egreso_delete'] ?>" method="post" class="d-inline"
												onsubmit="return confirm('¿Eliminar la cuenta «<?= htmlspecialchars($c['nombre']) ?>»? Solo se eliminará si no tiene subcuentas ni egresos asociados.');">
												<input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
												<button type="submit" class="btn" title="Eliminar">
													<i class="ti ti-trash"></i>
												</button>
											</form>

										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else: ?>
				<div class="text-muted">Aún no hay cuentas registradas.</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- ============ MODALES ============ -->

<!-- Nueva Cuenta -->
<div class="modal fade" id="modalNuevaCuenta" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<form action="<?php echo $routes['egresos_cuentas_egreso_store'] ?>" method="post" class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Nueva cuenta de egreso</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label class="form-label">Tipo de egreso</label>
					<select name="tipo_egreso_id" class="form-select" required>
						<option value="">Seleccione...</option>
						<?php foreach ($tiposEgreso as $t): ?>
							<option value="<?= (int)$t['id'] ?>"><?= htmlspecialchars($t['nombre']) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Nombre de la cuenta</label>
					<input type="text" name="nombre" class="form-control" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary">Guardar</button>
			</div>
		</form>
	</div>
</div>

<!-- Editar Cuenta -->
<div class="modal fade" id="modalEditarCuenta" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<form action="<?php echo $routes['egresos_cuentas_egreso_update'] ?>" method="post" class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Editar cuenta</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="id" id="editCuentaId">
				<div class="mb-3">
					<label class="form-label">Nombre</label>
					<input type="text" name="nombre" id="editCuentaNombre" class="form-control" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary">Actualizar</button>
			</div>
		</form>
	</div>
</div>

<!-- Nueva Subcuenta -->
<div class="modal fade" id="modalNuevaSubcuenta" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<form action="<?php echo $routes['egresos_subcuentas_egreso_store'] ?>" method="post" class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Nueva subcuenta</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="cuenta_egreso_id" id="newSubCuentaId">
				<div class="mb-2 text-muted small">Cuenta: <span id="newSubCuentaNombre"></span></div>
				<div class="mb-3">
					<label class="form-label">Nombre</label>
					<input type="text" name="nombre" class="form-control" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-success">Guardar</button>
			</div>
		</form>
	</div>
</div>

<!-- Editar Subcuenta -->
<div class="modal fade" id="modalEditarSubcuenta" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<form action="<?php echo $routes['egresos_subcuentas_egreso_update'] ?>" method="post" class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Editar subcuenta</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
			</div>
			<div class="modal-body">
				<input type="hidden" name="id" id="editSubId">
				<div class="mb-3">
					<label class="form-label">Nombre</label>
					<input type="text" name="nombre" id="editSubNombre" class="form-control" required>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary">Actualizar</button>
			</div>
		</form>
	</div>
</div>





<script>
	document.addEventListener('DOMContentLoaded', function() {
		var modalEditarCuenta = document.getElementById('modalEditarCuenta');
		if (!modalEditarCuenta) return;
		modalEditarCuenta.addEventListener('show.bs.modal', function(event) {
			var btn = event.relatedTarget;
			document.getElementById('editCuentaId').value = btn.getAttribute('data-id');
			document.getElementById('editCuentaNombre').value = btn.getAttribute('data-nombre');
		});
	});
</script>


<script>
	document.addEventListener('DOMContentLoaded', function() {

		var modalNuevaSubcuenta = document.getElementById('modalNuevaSubcuenta');
		if (modalNuevaSubcuenta) {
			modalNuevaSubcuenta.addEventListener('show.bs.modal', function(event) {
				var btn = event.relatedTarget;
				document.getElementById('newSubCuentaId').value = btn.getAttribute('data-cuenta');
				document.getElementById('newSubCuentaNombre').textContent = btn.getAttribute('data-cuenta-nombre');
			});
		}

		var modalEditarSubcuenta = document.getElementById('modalEditarSubcuenta');
		if (modalEditarSubcuenta) {
			modalEditarSubcuenta.addEventListener('show.bs.modal', function(event) {
				var btn = event.relatedTarget;
				document.getElementById('editSubId').value = btn.getAttribute('data-id');
				document.getElementById('editSubNombre').value = btn.getAttribute('data-nombre');
				var cuentaId = btn.getAttribute('data-cuenta');
				var sel = document.getElementById('editSubCuentaSelect');
				if (sel) {
					sel.value = cuentaId;
				}
			});
		}
	});
</script>