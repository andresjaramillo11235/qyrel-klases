<div class="container mt-5">
    <h2>Detalle del Programa</h2>
    
    <div class="row">
        <!-- Información del Programa -->
        <div class="col-md-6">
            <h4>Información del Programa</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Nombre</th>
                    <td><?= $programa['nombre'] ?></td>
                </tr>
                <tr>
                    <th>Descripción</th>
                    <td><?= $programa['descripcion'] ?></td>
                </tr>
                <tr>
                    <th>Valor Total</th>
                    <td><?= $programa['valor_total'] ?></td>
                </tr>
                <tr>
                    <th>Valor Hora</th>
                    <td><?= $programa['valor_hora'] ?></td>
                </tr>
                <tr>
                    <th>Valor Texto</th>
                    <td><?= $programa['valor_texto'] ?></td>
                </tr>
            </table>
        </div>

        <!-- Información Adicional -->
        <div class="col-md-6">
            <h4>Información Adicional</h4>
            <table class="table table-bordered">
                <tr>
                    <th>Horas Prácticas</th>
                    <td><?= $programa['horas_practicas'] ?></td>
                </tr>
                <tr>
                    <th>Horas Teóricas</th>
                    <td><?= $programa['horas_teoricas'] ?></td>
                </tr>
                <tr>
                    <th>SIET</th>
                    <td><?= $programa['siet'] ? 'Sí' : 'No' ?></td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td><?= $programa['estado'] ? 'Activo' : 'Inactivo' ?></td>
                </tr>
                <tr>
                    <th>Tipo de Servicio</th>
                    <td><?= $programa['tipo_servicio'] ?></td>
                </tr>
                <tr>
                    <th>Empresa</th>
                    <td><?= $programa['empresa_nombre'] ?></td>
                </tr>
                <tr>
                    <th>Categoría</th>
                    <td><?= $programa['categoria_nombre'] ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Clases del Programa -->
    <div class="mt-5">
        <h4>Clases del Programa</h4>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre de la Clase</th>
                    <th>Duración (horas)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clases as $clase) : ?>
                    <tr>
                        <td><?= $clase['id'] ?></td>
                        <td><?= $clase['nombre_clase'] ?></td>
                        <td><?= $clase['numero_horas'] ?></td> <!-- Duración en horas -->
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <a href="<?= $_SERVER['HTTP_REFERER'] ?>" class="btn btn-secondary mt-3">Volver</a>
</div>
