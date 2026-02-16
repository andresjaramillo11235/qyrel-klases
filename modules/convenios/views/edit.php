<?php if (!empty($error_message)) : ?>
    <div class="alert alert-danger" role="alert">
        <?= $error_message ?>
    </div>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/convenios/">Convenios</a></li>
                    <li class="breadcrumb-item" aria-current="page">Editar convenio</li>
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
                <h5>Información del convenio</h5>
            </div>

            <div class="card-body">
                <form method="post" class="validate-me" id="validate-me" action="/convenios-update/<?= $convenio['id'] ?>" data-validate>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre del convenio <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?= htmlspecialchars($convenio['nombre']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="documento" class="form-label">Documento <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="documento" name="documento" value="<?= htmlspecialchars($convenio['documento']) ?>" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?= htmlspecialchars($convenio['telefono']) ?>" pattern="^\d{10}$" required>
                                <small class="form-text text-muted">Debe contener 10 dígitos.</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="tipo_convenio" class="form-label">Tipo de convenio <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="tipo_convenio" name="tipo_convenio" required>
                                    <?php foreach ($tiposConvenio as $tipo) : ?>
                                        <option value="<?= $tipo['id'] ?>" <?= $convenio['tipo_convenio'] == $tipo['id'] ? 'selected' : '' ?>>
                                            <?= $tipo['nombre'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <input type="submit" class="btn btn-primary" value="Actualizar">
                        </div>
                </form>
            </div>

        </div>
    </div>
    <!-- [ sample-page ] end -->
</div>
<!-- [ Main Content ] end -->

<script src="../assets/js/plugins/bouncer.min.js"></script>
<script src="../assets/js/pages/form-validation.js"></script>
<script>
    // Convertir a mayúsculas mientras el usuario escribe
    document.getElementById('nombre').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    document.getElementById('documento').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Validación de solo números en el campo de teléfono
    document.getElementById('telefono').addEventListener('input', function(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    });
</script>
