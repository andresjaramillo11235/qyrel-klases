<h1 class="my-4">Mis Matrículas</h1>

<?php if (empty($matricula)): ?>
    <div class="alert alert-info" role="alert">
        No tienes matrículas registradas.
    </div>
<?php else: ?>
    <div class="card mb-3">
        <div class="card-header">
            Matrícula ID: <?= $matricula['id'] ?>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="card-title">Información de la Matrícula</h5>
                    <p class="card-text"><strong>Fecha de Inscripción:</strong> <?= $matricula['fecha_inscripcion'] ?></p>
                    <p class="card-text"><strong>Fecha de Enrolamiento:</strong> <?= $matricula['fecha_enrolamiento'] ?></p>
                    <p class="card-text"><strong>Fecha de Vencimiento:</strong> <?= $matricula['fecha_vencimiento'] ?></p>
                    <p class="card-text"><strong>Fecha de Aprobación Teórico:</strong> <?= $matricula['fecha_aprovacion_teorico'] ?></p>
                    <p class="card-text"><strong>Fecha de Aprobación Práctico:</strong> <?= $matricula['fecha_aprovacion_practico'] ?></p>
                    <p class="card-text"><strong>Fecha de Certificación:</strong> <?= $matricula['fecha_certificacion'] ?></p>
                    <p class="card-text"><strong>Estado:</strong> <?= $matricula['estado'] ?></p>
                    <p class="card-text"><strong>Valor:</strong> <?= $matricula['valor_matricula'] ?></p>
                    <p class="card-text"><strong>Observaciones:</strong> <?= $matricula['observaciones'] ?></p>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title">Programas Asociados</h5>
                    <?php if (empty($programas)): ?>
                        <p class="card-text">No hay programas asociados a esta matrícula.</p>
                    <?php else: ?>
                        <?php foreach ($programas as $programa): ?>
                            <div class="mb-3">
                                <h6 class="card-subtitle mb-2 text-muted"><?= $programa['nombre'] ?></h6>
                                <p class="card-text"><strong>Descripción:</strong> <?= $programa['descripcion'] ?></p>
                                <p class="card-text"><strong>Valor Total:</strong> <?= $programa['valor_total'] ?></p>
                                <p class="card-text"><strong>Valor Hora:</strong> <?= $programa['valor_hora'] ?></p>
                                <p class="card-text"><strong>Valor Texto:</strong> <?= $programa['valor_texto'] ?></p>
                                <p class="card-text"><strong>Horas Prácticas:</strong> <?= $programa['horas_practicas'] ?></p>
                                <p class="card-text"><strong>Horas Teóricas:</strong> <?= $programa['horas_teoricas'] ?></p>
                                <p class="card-text"><strong>Categoría:</strong> <?= $programa['categoria'] ?></p>
                                <p class="card-text"><strong>SIET:</strong> <?= $programa['siet'] ? 'Sí' : 'No' ?></p>
                                <p class="card-text"><strong>Tipo de Servicio:</strong> <?= $programa['tipo_servicio'] ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
