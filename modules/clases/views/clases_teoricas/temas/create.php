<?php $routes = include '../config/Routes.php'; ?>

<!-- [ Breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $routes['programas_index'] ?>">Programas</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo $routes['clases_teoricas_temas_index'] . $idPrograma ?>"><?php echo htmlspecialchars($programaNombre); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nuevo tema</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- [ Breadcrumb ] end -->

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    Nuevo Tema Programa: <?php echo htmlspecialchars($programaNombre); ?>
                    <br>
                    <small class="text-muted">Los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</small>
                </h5>
            </div>

            <div class="card-body">
                <form action="<?php echo $routes['clases_teoricas_temas_store']; ?>" method="POST">
                    <input type="hidden" name="clase_teorica_programa_id" value="<?php echo htmlspecialchars($idPrograma); ?>">

                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del tema <span class="text-danger">*</span></label>
                        <input 
                            type="text" 
                            class="form-control" 
                            id="nombre" 
                            name="nombre" 
                            required 
                            style="text-transform: uppercase;">
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea 
                            class="form-control" 
                            id="descripcion" 
                            name="descripcion" 
                            rows="4" 
                            style="text-transform: uppercase;"></textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="ti ti-check"></i> Guardar tema
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
