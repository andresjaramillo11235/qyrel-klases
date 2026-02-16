<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/empresas/">Empresas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Editar empresa</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h5>Editar datos de la empresa: los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</h5>
            </div>

            <div class="card-body">
                <form action="/empresas-update/<?= $empresa['id'] ?>" method="POST">
                    <input type="hidden" name="id" value="<?= $empresa['id'] ?>">
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= $empresa['nombre'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="identificacion" class="form-label">Identificación <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="number" class="form-control" id="identificacion" name="identificacion" value="<?= $empresa['identificacion'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="direccion" name="direccion" value="<?= $empresa['direccion'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ciudad" class="form-label">Ciudad <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?= $empresa['ciudad'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="email" class="form-control" id="correo" name="correo" value="<?= $empresa['correo'] ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= $empresa['telefono'] ?>" pattern="\d*" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dominio" class="form-label">Dominio</label>
                                <input type="text" class="form-control" id="dominio" name="dominio" value="<?= $empresa['dominio'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="text" class="form-control" id="logo" name="logo" value="<?= $empresa['logo'] ?>">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <?php foreach ($estadosEmpresas as $estado) : ?>
                                        <option value="<?= $estado['id'] ?>" <?= $empresa['estado'] == $estado['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_ingreso" class="form-label">Fecha de Ingreso <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" value="<?= date('Y-m-d', strtotime($empresa['fecha_ingreso'])) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea class="form-control" id="notas" name="notas" rows="4"><?= htmlspecialchars($empresa['notas']) ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <button type="submit" id="submit_button" class="btn btn-success">Guardar</button>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"]');

        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    });
    document.getElementById('notas').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>