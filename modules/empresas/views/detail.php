<!-- [ breadcrumb ] start -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/empresas/">Empresas</a></li>
                    <li class="breadcrumb-item" aria-current="page">Detalle de Empresa</li>
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
                <h5>Detalle de la Empresa: <?= $empresa['nombre'] ?></h5>
            </div>

            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-0 pt-0">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">ID</p>
                                <p class="mb-0"><?= $empresa['id'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Código</p>
                                <p class="mb-0"><?= $empresa['codigo'] ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item px-0">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Nombre</p>
                                <p class="mb-0"><?= $empresa['nombre'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Identificación</p>
                                <p class="mb-0"><?= $empresa['identificacion'] ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item px-0">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Dirección</p>
                                <p class="mb-0"><?= $empresa['direccion'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Ciudad</p>
                                <p class="mb-0"><?= $empresa['ciudad'] ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item px-0">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Correo</p>
                                <p class="mb-0"><?= $empresa['correo'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Teléfono</p>
                                <p class="mb-0"><?= $empresa['telefono'] ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item px-0">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Dominio</p>
                                <p class="mb-0"><?= $empresa['dominio'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Logo</p>
                                <p class="mb-0"><?= $empresa['logo'] ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item px-0">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Estado</p>
                                <p class="mb-0"><?= $empresa['estado'] == 1 ? 'Activo' : 'Inactivo' ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Fecha de Ingreso</p>
                                <p class="mb-0"><?= $empresa['fecha_ingreso'] ?></p>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item px-0">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Nivel</p>
                                <p class="mb-0"><?= $empresa['nivel'] ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1 text-muted">Inicio Fecha Facturación</p>
                                <p class="mb-0"><?= $empresa['inicio_facturacion'] ?></p>
                            </div>
                        </div>
                    </li>

                    
                    <li class="list-group-item px-0 pb-0">
                        <p class="mb-1 text-muted">Notas</p>
                        <p class="mb-0"><?= $empresa['notas'] ?></p>
                    </li>
                </ul>
            </div>

            <div class="card-footer text-end">
                <a href="/empresas/" class="btn btn-secondary">Regresar</a>
            </div>
        </div>
    </div>
</div>
<!-- [ Main Content ] end -->
