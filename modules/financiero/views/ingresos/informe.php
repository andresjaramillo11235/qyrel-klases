<!-- ENCABEZADO DE NAVEGACIÓN -->
<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item active">Ingresos Financieros</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- FORMULARIO DE FILTRO -->
<div class="card">
    <div class="card-header bg-light text-dark border-bottom">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="mb-3 mb-sm-0">
                REPORTE INGRESOS
            </h5>
        </div>
    </div>
    <div class="card-body">
        <form id="form-filtros">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <label for="fecha_inicial" class="form-label">
                        <i class="ti ti-calendar"></i> Fecha Inicial:
                    </label>
                    <input type="date" id="fecha_inicial" name="fecha_inicial" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-6">
                    <label for="fecha_final" class="form-label">
                        <i class="ti ti-calendar"></i> Fecha Final:
                    </label>
                    <input type="date" id="fecha_final" name="fecha_final" class="form-control form-control-sm" required>
                </div>
                <div class="col-12 d-flex gap-2 justify-content-end mt-3">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ti ti-filter"></i> Filtrar
                    </button>
                    <button type="button" id="btnExportarFiltro" class="btn btn-sm btn-success">
                        <i class="ti ti-file-export"></i> Exportar Filtro a Excel
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="totales-ingresos" class="mt-4 d-none">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <div class="row" id="info-fechas" class="small fw-light"></div>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Totales Pantalla -->
                <div class="col-md-6 border-end" id="totales-pantalla">
                    <h6 class="text-primary mb-3">Totales en Pantalla</h6>
                </div>

                <!-- Totales Filtro -->
                <div class="col-md-6" id="totales-filtro">
                    <h6 class="text-primary mb-3">Totales del Filtro</h6>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLA DE RESULTADOS -->
<div class="card mt-4 d-none" id="tabla-contenedor">
    <div class="card-body">
        <div class="table-responsive">
            <table id="miTabla" class="table table-striped table-sm">
                <thead class="table-primary text-center">
                    <tr>
                        <th>FECHA</th>
                        <th>RECIBO</th>
                        <th>VALOR</th>
                        <th>DOCUMENTO</th>
                        <th>NOMBRES</th>
                        <th>APELLIDOS</th>
                        <th>MATRÍCULA</th>
                        <th>CATEGORÍA</th>
                        <th>CONVENIO</th>
                        <th>TIPO PAGO</th>
                        <th>CREADO POR</th>
                        <th>OBSERVACIONES</th>
                        <th>SALDO</th>
                        <th>ID</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Incluir jQuery y DataTables con Bootstrap -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">

<!-- DataTables y sus extensiones -->
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<!-- SCRIPT JAVASCRIPT -->
<script>
    let tabla = null;

    $(document).ready(function() {
        $('#form-filtros').on('submit', function(e) {
            e.preventDefault(); // Evitar reload de la página

            console.log('Formulario capturado'); // <- prueba aquí

            const fechaInicial = $('#fecha_inicial').val();
            const fechaFinal = $('#fecha_final').val();

            if (!fechaInicial || !fechaFinal) {
                alert("Debes seleccionar las fechas.");
                return;
            }

            // Mostrar contenedor de tabla si está oculto (opcional)
            $('#tabla-contenedor').removeClass('d-none');

            // Destruir la tabla anterior si ya existe
            if (tabla) {
                tabla.clear().destroy(); // ✅ Elimina los datos, no el DOM
                // tabla.destroy();
                // $('#miTabla').empty(); // Limpia thead y tbody
            }

            // Crear nueva tabla con AJAX hacia el backend
            tabla = $('#miTabla').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 25,
                ajax: {
                    url: '/ZXCVbnmQWEr/',
                    type: 'POST',
                    data: {
                        fecha_inicial: fechaInicial,
                        fecha_final: fechaFinal
                    },

                    dataSrc: function(json) {
                        $('#totales-ingresos').removeClass('d-none');

                        // Fechas en encabezado
                        $('#info-fechas').html(`
                            <div class="col-md-3"><strong>📅 Fecha Inicial:</strong> ${json.fecha_inicial}</div>
                            <div class="col-md-3"><strong>📅 Fecha Final:</strong> ${json.fecha_final}</div>
                            <div class="col-md-3"><strong>🏫 Academia:</strong> ${json.empresa_nombre}</div>
                            <div class="col-md-3"><strong>🧾 Registros:</strong> ${json.cantidad_registros}</div>
                        `);

                        // 🔁 Unificar todos los tipos en un solo conjunto
                        const allTipos = new Set([
                            ...Object.keys(json.totales),
                            ...Object.keys(json.totales_globales)
                        ]);

                        // Convertir a array y ordenar alfabéticamente (excepto TOTAL al final)
                        const tiposOrdenados = Array.from(allTipos).filter(t => t !== 'total').sort();

                        // 🟦 Totales en pantalla
                        let htmlPantalla = '';
                        tiposOrdenados.forEach(tipo => {
                            const valor = Number(json.totales[tipo] || 0).toLocaleString();
                            htmlPantalla += `
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>${tipo.toUpperCase()}</span>
                                <strong>$${valor}</strong>
                            </div>`;
                        });
                        // Total al final
                        htmlPantalla += `
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span><strong>TOTAL</strong></span>
                            <strong class="text-primary">$${Number(json.totales.total).toLocaleString()}</strong>
                        </div>`;
                        $('#totales-pantalla').html(`<h6 class="text-primary mb-3">Totales en Pantalla</h6>` + htmlPantalla);

                        // 🟨 Totales del filtro
                        let htmlFiltro = '';
                        tiposOrdenados.forEach(tipo => {
                            const valor = Number(json.totales_globales[tipo] || 0).toLocaleString();
                            htmlFiltro += `
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span>${tipo.toUpperCase()}</span>
                                <strong>$${valor}</strong>
                            </div>`;
                        });
                        // Total al final
                        htmlFiltro += `
                        <div class="d-flex justify-content-between border-top pt-2 mt-2">
                            <span><strong>TOTAL</strong></span>
                            <strong class="text-primary">$${Number(json.totales_globales.total).toLocaleString()}</strong>
                        </div>`;
                        $('#totales-filtro').html(`<h6 class="text-primary mb-3">Totales del Filtro</h6>` + htmlFiltro);

                        return json.data;
                    }




                },
                columns: [{
                        data: 'fecha'
                    },
                    {
                        data: 'numero_recibo'
                    },
                    {
                        data: 'valor'
                    },
                    {
                        data: 'numero_documento'
                    },
                    {
                        data: 'nombres'
                    },
                    {
                        data: 'apellidos'
                    },
                    {
                        data: 'matricula_id'
                    },
                    {
                        data: 'categoria_matricula'
                    },
                    { 
                        data: 'convenio_nombre' 
                    },
                    {
                        data: 'tipo_ingreso_nombre'
                    },
                    {
                        data: 'usuario_creacion'
                    },
                    {
                        data: 'observaciones'
                    },
                    {
                        data: 'saldo_matricula'
                    },
                    {
                        data: 'id'
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.5/i18n/es-ES.json'
                }
            });
        });
    });


    // Boton Exportar a Excel
    $('#btnExportarFiltro').on('click', function() {
        const fechaInicial = $('#fecha_inicial').val();
        const fechaFinal = $('#fecha_final').val();

        if (!fechaInicial || !fechaFinal) {
            alert('Por favor selecciona un rango de fechas válido.');
            return;
        }

        // Crear un formulario oculto para enviar POST
        const form = $('<form>', {
            method: 'POST',
            action: '/QWERTYuiopL/',
            target: '_blank'
        });

        form.append($('<input>', {
            type: 'hidden',
            name: 'fecha_inicial',
            value: fechaInicial
        }));

        form.append($('<input>', {
            type: 'hidden',
            name: 'fecha_final',
            value: fechaFinal
        }));

        // Agregar al body, enviar y eliminar
        $('body').append(form);
        form.submit();
        form.remove();
    });
</script>