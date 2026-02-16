<h1 class="mt-5">Mi Cuenta</h1>

<div class="container mt-3">
    <div class="row">
        <div class="col-md-4">
            <img src="/assets/uploads/<?= htmlspecialchars($instructor['foto']) ?>" alt="Foto del Instructor" class="img-fluid">
        </div>
        <div class="col-md-8">
            <table class="table table-bordered">
                <tr>
                    <th>Nombres</th>
                    <td><?= htmlspecialchars($instructor['nombres']) ?></td>
                </tr>
                <tr>
                    <th>Apellidos</th>
                    <td><?= htmlspecialchars($instructor['apellidos']) ?></td>
                </tr>
                <tr>
                    <th>Tipo de Documento</th>
                    <td><?= htmlspecialchars($instructor['tipo_documento_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Número de Documento</th>
                    <td><?= htmlspecialchars($instructor['numero_documento']) ?></td>
                </tr>
                <tr>
                    <th>Expedición Departamento</th>
                    <td><?= htmlspecialchars($instructor['expedicion_departamento_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Expedición Ciudad</th>
                    <td><?= htmlspecialchars($instructor['expedicion_ciudad_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Fecha de Expedición</th>
                    <td><?= htmlspecialchars($instructor['fecha_expedicion']) ?></td>
                </tr>
                <tr>
                    <th>Correo</th>
                    <td><?= htmlspecialchars($instructor['correo']) ?></td>
                </tr>
                <tr>
                    <th>Celular</th>
                    <td><?= htmlspecialchars($instructor['celular']) ?></td>
                </tr>
                <tr>
                    <th>Dirección</th>
                    <td><?= htmlspecialchars($instructor['direccion']) ?></td>
                </tr>
                <tr>
                    <th>Grupo Sanguíneo</th>
                    <td><?= htmlspecialchars($instructor['grupo_sanguineo_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Género</th>
                    <td><?= htmlspecialchars($instructor['genero_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Estado Civil</th>
                    <td><?= htmlspecialchars($instructor['estado_civil_nombre']) ?></td>
                </tr>
                <tr>
                    <th>Vencimiento Licencia de Conducción</th>
                    <td><?= htmlspecialchars($instructor['vencimiento_licencia_conduccion']) ?></td>
                </tr>
                <tr>
                    <th>Vencimiento Licencia de Instructor</th>
                    <td><?= htmlspecialchars($instructor['vencimiento_licencia_instructor']) ?></td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td><?= htmlspecialchars($instructor['estado']) ?></td>
                </tr>
                <tr>
                    <th>Observaciones</th>
                    <td><?= htmlspecialchars($instructor['observaciones']) ?></td>
                </tr>
                <tr>
                    <th>Empresa</th>
                    <td><?= htmlspecialchars($instructor['empresa_nombre']) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<br><br>
