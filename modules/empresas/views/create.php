<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/empresas/">Empresas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Nueva empresa</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="card-header">
                <h5>Datos de la nueva empresa: los campos con <i class="ph-duotone ph-asterisk"></i> son obligatorios.</h5>
            </div>

            <div class="card-body">
                <form action="/empresas-store/" method="POST" enctype="multipart/form-data">

                    <div class="row">

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre de la empresa <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="identificacion" class="form-label">Identificación <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="number" class="form-control" id="identificacion" name="identificacion" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="direccion" class="form-label">Dirección <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="direccion" name="direccion" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ciudad" class="form-label">Ciudad <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="tel" class="form-control" id="telefono" name="telefono" pattern="\d*" required>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_ingreso" class="form-label">Fecha de Ingreso <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="inicio_facturacion" class="form-label">Inicio de Facturación <i class="ph-duotone ph-asterisk"></i></label>
                                <input type="date" class="form-control" id="inicio_facturacion" name="inicio_facturacion" value="<?= isset($empresa['inicio_facturacion']) ? htmlspecialchars($empresa['inicio_facturacion']) : '' ?>" required>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="estado" name="estado" required>
                                    <?php foreach ($estadosEmpresas as $estado) : ?>
                                        <option value="<?= $estado['id'] ?>" <?= isset($empresa['estado']) && $empresa['estado'] == $estado['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($estado['nombre']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>



                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nivel" class="form-label">Nivel <i class="ph-duotone ph-asterisk"></i></label>
                                <select class="form-control" id="nivel" name="nivel" required>
                                    <option value="1" <?= isset($empresa['nivel']) && $empresa['nivel'] == '1' ? 'selected' : '' ?>>1</option>
                                    <option value="2" <?= isset($empresa['nivel']) && $empresa['nivel'] == '2' ? 'selected' : '' ?>>2</option>
                                    <option value="3" <?= isset($empresa['nivel']) && $empresa['nivel'] == '3' ? 'selected' : '' ?>>3</option>
                                </select>
                            </div>
                        </div>

                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="dominio" class="form-label">Dominio</label>
                                <input type="text" class="form-control" id="dominio" name="dominio">
                            </div>
                        </div> -->

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="logo" class="form-label">Logo</label>
                                <input type="file" class="form-control" id="logo" name="logo">
                            </div>
                        </div>


                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="notas" class="form-label">Notas</label>
                                <textarea class="form-control" id="notas" name="notas" rows="1"></textarea>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('input[type="text"], input[type="email"], input[type="tel"]');

        inputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        });
    });
</script>