<?php
// Asegura rutas disponibles
$routes = $routes ?? include '../config/Routes.php';

// Helper de escape
if (!function_exists('e')) {
    function e($v)
    {
        return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

// $aula debe venir desde el controlador edit($id)
$aula = $aula ?? ['id' => null, 'nombre' => '', 'descripcion' => '', 'capacidad' => 1];
?>

<?php if (!empty($error_message)) : ?>
    <div class="alert alert-danger" role="alert">
        <?= e($error_message) ?>
    </div>
<?php endif; ?>

<?php if (!empty($_SESSION['error_message'])): ?>
    <div class="alert alert-danger" role="alert">
        <?= e($_SESSION['error_message']);
        unset($_SESSION['error_message']); ?>
    </div>
<?php endif; ?>

<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?= $routes['aulas_index'] ?? '/aulas/' ?>">Aulas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Editar Aula</li>
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
                <h5>Editar aula</h5>
            </div>

            <div class="card-body">
                <form method="post"
                    class="validate-me"
                    id="form-aula-edit"
                    action="<?= ($routes['aulas_update']) . urlencode((string)$aula['id']) ?>"
                    data-validate>
                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">
                                    Nombre del aula <i class="ph-duotone ph-asterisk" title="Obligatorio"></i>
                                </label>
                                <input type="text"
                                    class="form-control"
                                    id="nombre"
                                    name="nombre"
                                    maxlength="100"
                                    value="<?= e($aula['nombre']) ?>"
                                    required>
                                <small class="form-text text-muted">Ej.: Principal, Master, Sala 101…</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="capacidad" class="form-label">
                                    Capacidad (número de estudiantes) <i class="ph-duotone ph-asterisk" title="Obligatorio"></i>
                                </label>
                                <input type="number"
                                    class="form-control"
                                    id="capacidad"
                                    name="capacidad"
                                    min="1" step="1"
                                    value="<?= max(1, (int)$aula['capacidad']) ?>"
                                    required>
                                <small class="form-text text-muted">Debe ser un entero ≥ 1.</small>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="descripcion" class="form-label">Descripción (opcional)</label>
                                <textarea class="form-control"
                                    id="descripcion"
                                    name="descripcion"
                                    rows="3"><?= e($aula['descripcion']) ?></textarea>
                            </div>
                        </div>

                        <div class="col-md-12 text-end">
                            <a href="<?= $routes['aulas_index'] ?? '/aulas/' ?>" class="btn btn-outline-secondary me-2">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        </div>

                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
<!-- [ Main Content ] end -->

<!-- Validación en cliente (opcional) -->
<script src="../assets/js/plugins/bouncer.min.js"></script>
<script src="../assets/js/pages/form-validation.js"></script>
<script>
    // Mayúsculas en nombre
    document.getElementById('nombre').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });

    // Asegurar capacidad >= 1 y entera + mensaje de validación amigable
    const cap = document.getElementById('capacidad');

    cap.addEventListener('input', function(e) {
        let v = e.target.value.replace(/[^0-9]/g, '');
        if (v === '') v = '1';
        v = String(parseInt(v, 10));
        if (parseInt(v, 10) < 1) v = '1';
        e.target.value = v;
        e.target.setCustomValidity('');
    });

    cap.addEventListener('invalid', function(e) {
        if (e.target.validity.rangeUnderflow || e.target.validity.valueMissing) {
            e.target.setCustomValidity('La capacidad es obligatoria y debe ser un entero mayor o igual a 1.');
        }
    });

    cap.addEventListener('change', function(e) {
        e.target.setCustomValidity('');
    });
</script>