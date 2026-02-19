<?php $routes = include '../config/Routes.php'; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/"><i class="ti ti-home"></i> <?= LabelHelper::get('menu_inicio') ?></a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="/estudiantes/"><?= LabelHelper::get('menu_estudiantes') ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-lg-5 col-xxl-3">
                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h5><?= LabelHelper::get('menu_estudiante') ?></h5>
                    </div>

                    <div class="card-body position-relative">
                        <div class="text-center mt-3">
                            <div class="chat-avtar d-inline-flex mx-auto">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#lightboxModal">
                                    <img class="img-fluid wid-90 img-thumbnail"
                                        src="/files/fotos_estudiantes/<?= $estudiante['foto'] ?>"
                                        alt="Foto del Estudiante"
                                        style="width: 150px; height: auto;">
                                </a>
                                <i class=" chat-badge bg-success me-2 mb-2"></i>
                            </div><br><br>
                            <h5 class="mb-0"><?= $estudiante['nombres'] ?> <?= $estudiante['apellidos'] ?></h5>
                        </div>
                    </div>

                    <div class="nav flex-column nav-pills list-group list-group-flush account-pills mb-0"
                        id="user-set-tab"
                        role="tablist"
                        aria-orientation="vertical">

                        <a class="nav-link list-group-item list-group-item-action active"
                            id="perfil-estudiante-tab"
                            data-bs-toggle="pill"
                            href="#perfil-estudiante"
                            role="tab"
                            aria-controls="perfil-estudiante"
                            aria-selected="true">
                            Perfil
                        </a>
                    </div>
                </div>

                <div class="card overflow-hidden">
                    <div class="card-header">
                        <h5>Matrículas (Programa)</h5>
                    </div>

                    <?php /** [ Listado de matrículas ] **/ ?>

                    <div class="nav flex-column nav-pills list-group list-group-flush account-pills mb-0"
                        id="user-set-tab"
                        role="tablist"
                        aria-orientation="vertical">

                        <?php
                        // Contar cuántas matrículas hay
                        $totalMatriculas = count($matriculas);
                        ?>

                        <?php for ($i = 0; $i < $totalMatriculas; $i++): ?>
                            <?php
                            // Determinar el texto y la clase de estilo según el estado
                            switch ($matriculas[$i]['estado']) {
                                case 1:
                                    $estadoTexto = 'Activo';
                                    $estadoClase = 'badge bg-success'; // Estilo para Activo
                                    break;
                                case 2:
                                    $estadoTexto = 'Pendiente';
                                    $estadoClase = 'badge bg-warning'; // Estilo para Pendiente
                                    break;
                                case 3:
                                    $estadoTexto = 'Eliminado';
                                    $estadoClase = 'badge bg-danger'; // Estilo para Eliminado
                                    break;
                                case 4:
                                    $estadoTexto = 'Bloqueado';
                                    $estadoClase = 'badge bg-secondary'; // Estilo para Bloqueado
                                    break;
                                case 5:
                                    $estadoTexto = 'Finalizado';
                                    $estadoClase = 'badge bg-primary'; // Estilo para Finalizado
                                    break;
                                default:
                                    $estadoTexto = 'Desconocido';
                                    $estadoClase = 'badge bg-dark'; // Estilo para Desconocido
                                    break;
                            }
                            ?>

                            <a class="nav-link list-group-item list-group-item-action"
                                id="matricula-tab-<?= htmlspecialchars($matriculas[$i]['id']) ?>"
                                data-bs-toggle="pill"
                                href="#matricula-content-<?= htmlspecialchars($matriculas[$i]['id']) ?>"
                                role="tab"
                                aria-controls="matricula-content-<?= htmlspecialchars($matriculas[$i]['id']) ?>"
                                aria-selected="<?= $i === 0 ? 'true' : 'false' ?>">
                                <span class="d-flex justify-content-between align-items-center">
                                    <span><?= htmlspecialchars($matriculas[$i]['id']) ?> (<?= htmlspecialchars($matriculas[$i]['programas_nombre']) ?>)</span>
                                    <span class="<?= $estadoClase ?>"><?= $estadoTexto ?></span>
                                </span>
                            </a>
                        <?php endfor; ?>

                    </div>
                </div>
            </div>

            <?php /** [ PERFIL DEL ESTUDIANTE ] */ ?>

            <div class="col-lg-7 col-xxl-9">
                <div class="tab-content" id="user-set-tabContent">

                    <div class="tab-pane fade show active"
                        id="perfil-estudiante"
                        role="tabpanel"
                        aria-labelledby="perfil-estudiante-tab">

                        <div class="card">
                            <div class="card-header">
                                <h5>Perfil del estudiante</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item px-0 pt-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Nombre Completo</p>
                                                <p class="mb-0"><?= $estudiante['nombres'] ?> <?= $estudiante['apellidos'] ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Documento</p>
                                                <p class="mb-0"><?= $estudiante['tipo_documento_sigla'] ?> <?= $estudiante['numero_documento'] ?></p>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-group-item px-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Correo</p>
                                                <p class="mb-0"><?= $estudiante['correo'] ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Celular</p>
                                                <p class="mb-0"><?= $estudiante['celular'] ?></p>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-group-item px-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Dirección de Residencia</p>
                                                <p class="mb-0"><?= $estudiante['direccion_residencia'] ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Grupo Sanguíneo</p>
                                                <p class="mb-0"><?= $estudiante['grupo_sanguineo_nombre'] ?></p>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-group-item px-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Fecha de Nacimiento</p>
                                                <p class="mb-0"><?= $estudiante['fecha_nacimiento'] ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Género</p>
                                                <p class="mb-0"><?= $estudiante['genero_nombre'] ?></p>
                                            </div>
                                        </div>
                                    </li>

                                     <li class="list-group-item px-0">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Nombre del Contacto</p>
                                                <p class="mb-0"><?= $estudiante['nombre_contacto'] ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1 text-muted">Teléfono del Contacto</p>
                                                <p class="mb-0"><?= $estudiante['telefono_contacto'] ?></p>
                                            </div>
                                        </div>
                                    </li>

                                    <li class="list-group-item px-0 pb-0">
                                        <p class="mb-1 text-muted">Observaciones</p>
                                        <p class="mb-0"><?= $estudiante['observaciones'] ?></p>
                                    </li>

                                </ul>
                            </div>
                        </div>
                    </div>

                    <?php /** [ DETALLES DE MATRÍCULAS ] **/ ?>

                    <?php
                    // Contar cuántas matrículas hay
                    $totalMatriculas = count($matriculas);
                    ?>

                    <?php for ($i = 0; $i < $totalMatriculas; $i++): ?>

                        <div class="tab-pane fade"
                            id="matricula-content-<?= htmlspecialchars($matriculas[$i]['id']) ?>"
                            role="tabpanel"
                            aria-labelledby="matricula-tab-<?= htmlspecialchars($matriculas[$i]['id']) ?>">

                            <?php /** INICIO ABONOS */ ?>

                            <div class="card">
                                <div class="card-header">
                                    <h5>
                                        Abonos matrícula: <?= htmlspecialchars($matriculas[$i]['id']) ?>
                                        | Programa: <?= htmlspecialchars($matriculas[$i]['programas_nombre']) ?>
                                        | Total matrícula: $<?= number_format($matriculas[$i]['valor_matricula'], 0, ',', '.') ?>
                                        | Total abonos: $<?= number_format($matriculas[$i]['total_abonos'], 0, ',', '.') ?>
                                        | Saldo: $<?= number_format($matriculas[$i]['valor_matricula'] - $matriculas[$i]['total_abonos'], 0, ',', '.') ?>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($matriculas[$i]['abonos'])): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($matriculas[$i]['abonos'] as $abono): ?>
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <p class="mb-1 text-muted">Fecha</p>
                                                            <p class="mb-0"><?= htmlspecialchars($abono['fecha']) ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p class="mb-1 text-muted">Motivo</p>
                                                            <p class="mb-0"><?= htmlspecialchars($abono['motivo_ingreso']) ?></p>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p class="mb-1 text-muted">Valor</p>
                                                            <p class="mb-0">$<?= number_format($abono['valor'], 0, ',', '.') ?></p>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                        <div class="mt-3">
                                            <p><strong>Total Abonos:</strong> $<?= number_format($matriculas[$i]['total_abonos'], 0, ',', '.') ?></p>
                                        </div>
                                    <?php else: ?>
                                        <p>No hay abonos registrados para esta matrícula.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php /** DETALLE DE MATRÍCULA */ ?>

                            <div class="card">
                                <div class="card-header">
                                    <h5>Matrícula: <?= htmlspecialchars($matriculas[$i]['id']) ?> - Programa: <?= htmlspecialchars($matriculas[$i]['programas_nombre']) ?></h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">

                                        <li class="list-group-item px-0 pt-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">ID</p>
                                                    <p class="mb-0"><?= htmlspecialchars($matriculas[$i]['id']) ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Fecha Inscripción</p>
                                                    <p class="mb-0"><?= htmlspecialchars($matriculas[$i]['fecha_inscripcion']) ?></p>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="list-group-item px-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Fecha Enrolamiento</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['fecha_enrolamiento'] ?: 'No disponible' ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Fecha Vencimiento</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['fecha_vencimiento'] ?: 'No disponible' ?></p>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="list-group-item px-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Fecha Aprobación Teórico</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['fecha_aprovacion_teorico'] ?: 'No disponible' ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Fecha Aprobación Práctico</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['fecha_aprovacion_practico'] ?: 'No disponible' ?></p>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="list-group-item px-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Fecha Certificación</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['fecha_certificacion'] ?: 'No disponible' ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Tipo de Solicitud</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['tipo_solicitud_nombre'] ?></p>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="list-group-item px-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Convenio</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['convenio_nombre'] ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Estado</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['estado'] == 1 ? 'Activo' : 'Inactivo' ?></p>
                                                </div>
                                            </div>
                                        </li>

                                        <li class="list-group-item px-0">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Valor Matrícula</p>
                                                    <p class="mb-0"><?= "$" . number_format($matriculas[$i]['valor_matricula'], 0, ',', '.') ?></p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p class="mb-1 text-muted">Observaciones</p>
                                                    <p class="mb-0"><?= $matriculas[$i]['observaciones'] ?: 'Sin observaciones' ?></p>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <?php /** INICIO CLASES PRACTICAS */ ?>

                            <div class="card">
                                <div class="card-header">
                                    <h5>Clases Prácticas</h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($matriculas[$i]['clases_practicas'])): ?>
                                        <ul class="list-group list-group-flush">
                                            <?php foreach ($matriculas[$i]['clases_practicas'] as $clase): ?>
                                                <li class="list-group-item px-0">
                                                    <div class="row">
                                                        <div class="col-md-3">
                                                            <p class="mb-1 text-muted">Nombre</p>
                                                            <p class="mb-0"><?= htmlspecialchars($clase['nombre']) ?></p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <p class="mb-1 text-muted">Fecha</p>
                                                            <p class="mb-0"><?= htmlspecialchars($clase['fecha']) ?></p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <p class="mb-1 text-muted">Horario</p>
                                                            <p class="mb-0"><?= htmlspecialchars($clase['hora_inicio']) ?> - <?= htmlspecialchars($clase['hora_fin']) ?></p>
                                                        </div>
                                                        <div class="col-md-3">
                                                            <p class="mb-1 text-muted">Instructor</p>
                                                            <p class="mb-0"><?= htmlspecialchars($clase['instructor_nombre']) ?></p>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <p class="mb-1 text-muted">Vehículo</p>
                                                            <p class="mb-0"><?= htmlspecialchars($clase['vehiculo_placa']) ?: 'No asignado' ?></p>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p>No hay clases prácticas registradas para esta matrícula.</p>
                                    <?php endif; ?>
                                </div>
                            </div>


                            <?php /** FIN ABONOS **/ ?>


                            <?php /** DOCUMENTOS */ ?>

                            <div class="card">
                                <div class="card-header">
                                    <h5>Documentos</h5>
                                </div>
                                <div class="card-body">
                                    <div class="list-group list-group-flush">
                                        <!-- Contrato -->
                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="mb-0 text-muted">Contrato:</p>
                                                <small>Descargue el contrato relacionado con la matrícula.</small>
                                            </div>
                                            <a href="<?= $routes['documento_contrato_pdf'] . htmlspecialchars($matriculas[$i]['id']) ?>" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="ph-duotone ph-download"></i> Descargar
                                            </a>
                                        </div>

                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="mb-0 text-muted">Control Clases Teóricas:</p>
                                                <small>Acceda al documento de control de clases teóricas.</small>
                                            </div>
                                            <a href="<?= $routes['documento_control_clases_teoricas_pdf'] . htmlspecialchars($matriculas[$i]['id']) ?>" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="ph-duotone ph-download"></i> Descargar
                                            </a>
                                        </div>

                                        <div class="list-group-item d-flex align-items-center justify-content-between">
                                            <div>
                                                <p class="mb-0 text-muted">Control Clases Prácticas:</p>
                                                <small>Descargue el documento de control de clases prácticas.</small>
                                            </div>
                                            <a href="<?= $routes['documento_control_clases_practicas_pdf'] . htmlspecialchars($matriculas[$i]['id']) ?>" class="btn btn-primary btn-sm" target="_blank">
                                                <i class="ph-duotone ph-download"></i> Descargar
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    <?php endfor; ?>

                    <?php /** [ FIN  DETALLES DE MATRÍCULAS ] **/ ?>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabLinks = document.querySelectorAll('[data-bs-toggle="pill"]');
        const tabContents = document.querySelectorAll('.tab-pane');

        // Ocultar todas las pestañas excepto "perfil-estudiante"
        tabContents.forEach((content) => {
            if (content.id !== "perfil-estudiante") {
                content.classList.remove('show', 'active');
            }
        });

        tabLinks.forEach((tab) => {
            tab.addEventListener('click', function(e) {
                e.preventDefault(); // Prevenir comportamiento por defecto

                // Eliminar clases `show active` de todos los tabs y su contenido
                tabLinks.forEach((link) => link.classList.remove('active'));
                tabContents.forEach((content) => content.classList.remove('show', 'active'));

                // Agregar clases `show active` al tab seleccionado
                const target = document.querySelector(this.getAttribute('href'));
                this.classList.add('active');
                target.classList.add('show', 'active');
            });
        });

        // Asegurar que solo el "perfil-estudiante" está visible al cargar la página
        document.getElementById("perfil-estudiante").classList.add('show', 'active');
    });
</script>