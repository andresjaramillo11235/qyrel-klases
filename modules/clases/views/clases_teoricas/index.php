<?php
$routes = include '../config/Routes.php';
include '../shared/utils/InsertarSaltosDeLinea.php';
?>

<?php if (isset($_SESSION['success_message'])) : ?>
    <script>
        const successMessage = <?php echo json_encode($_SESSION['success_message']); ?>;
        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: successMessage
        });
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])) : ?>
    <script>
        const errorMessage = <?php echo json_encode($_SESSION['error_message']); ?>;
        if (window.Swal) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: errorMessage
            });
        } else {
            alert(errorMessage);
        }
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<div class="page-header">
    <div class="page-block">
        <div class="row align-items-center">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/home/">Inicio</a></li>
                    <li class="breadcrumb-item" aria-current="page">Clases Teóricas</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php /** ==== Filtro por fechas: Clases Teóricas ==== */ ?>

<div class="card">
    <div class="card-header bg-light text-dark border-bottom">
        <div class="d-sm-flex align-items-center justify-content-between">
            <h5 class="mb-3 mb-sm-0">
                <i class="ti ti-filter me-2"></i> Filtro de clases teóricas por intervalo de tiempo
            </h5>
        </div>
    </div>
    <div class="card-body">

        <form id="form-filtros" method="POST" action="/clases_teoricas/">
            <div class="row align-items-end">
                <div class="col-md-6">
                    <div class="mb-2">
                        <label for="fecha_inicial" class="form-label">
                            <i class="ti ti-calendar"></i> Fecha Inicial:
                        </label>
                        <input
                            type="date"
                            id="fecha_inicial"
                            name="fecha_inicial"
                            class="form-control form-control-sm"
                            value="<?= htmlspecialchars($fecha_inicial) ?>">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-2">
                        <label for="fecha_final" class="form-label">
                            <i class="ti ti-calendar"></i> Fecha Final:
                        </label>
                        <input
                            type="date"
                            id="fecha_final"
                            name="fecha_final"
                            class="form-control form-control-sm"
                            value="<?= htmlspecialchars($fecha_final) ?>">
                    </div>
                </div>

                <div class="col-12 d-flex gap-2 justify-content-end">
                    <!-- 🔹 Botón Filtrar -->
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="ti ti-filter"></i> Filtrar
                    </button>

                    <!-- 🔹 Botón Hoy -->
                    <button type="button" id="btn-hoy" class="btn btn-sm btn-success">
                        <i class="ti ti-calendar-event"></i> Hoy
                    </button>

                    <!-- 🔹 Botón Limpiar -->
                    <a href="/clases_teoricas/" class="btn btn-sm btn-outline-secondary">
                        <i class="ti ti-refresh"></i> Limpiar
                    </a>
                </div>
            </div>
        </form>

        <!-- 🔹 Script para botón “Hoy” -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const btnHoy = document.getElementById('btn-hoy');
                const fechaInicial = document.getElementById('fecha_inicial');
                const fechaFinal = document.getElementById('fecha_final');
                const form = document.getElementById('form-filtros');

                btnHoy.addEventListener('click', function() {
                    // 🔑 Fecha actual en horario local (Bogotá)
                    const hoy = new Date().toLocaleDateString('en-CA');

                    fechaInicial.value = hoy;
                    fechaFinal.value = hoy;
                    form.submit();
                });
            });
        </script>

    </div>
</div>

<?php /** ==== Tabla ==== */ ?>

<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-header">
                <div class="d-sm-flex align-items-center justify-content-between">
                    <h5 class="mb-3 mb-sm-0">Listado de Clases Teóricas</h5>
                    <div>
                        <a href="<?= $routes['clases_teoricas_calendariodos']; ?>" class="btn btn-primary">Calendario</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="dt-responsive table-responsive">

                    <table class="table table-striped data-table" id="pc-dt-simple">
                        <thead>
                            <tr>
                                <th>Programa</th>
                                <th>Nombre Clase</th>
                                <th>Aula</th>
                                <th>Instructor</th>
                                <th>Fecha</th>
                                <th>Hora<br> Inicio</th>
                                <th>Hora<br> Fin</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clasesTeoricas as $clase) : ?>
                                <tr>
                                    <td><?= $clase['programa_nombre'] ?></td>
                                    <td><?= $clase['tema_nombre'] ?></td>
                                    <td><?= $clase['aula_nombre'] ?></td>
                                    <td><?= strtoupper($clase['instructor_nombre_completo']) ?></td>
                                    <td><?= $clase['fecha'] ?></td>
                                    <td><?= date('H:i', strtotime($clase['hora_inicio'])) ?></td>
                                    <td><?= date('H:i', strtotime($clase['hora_fin'])) ?></td>
                                    <td>
                                        <?php if (empty($_SESSION['instructor_id'])): ?>

                                            <!-- Botón editar solo si NO es instructor -->
                                            <a href="<?= $routes['clases_teoricas_edit']; ?><?= urlencode($clase['id']) ?>"
                                                class="btn-link-secondary" title="Editar">
                                                <i class="ti ti-edit f-20"></i>
                                            </a>

                                            <!-- Eliminar -->

                                            <!--
                                            <a href="#"
                                                class="btn-link-secondary ms-2 btn-delete-clase"
                                                data-delete-url="<?= $routes['clases_teoricas_delete']; ?><?= urlencode($clase['id']) ?>"
                                                title="Eliminar">
                                                <i class="ti ti-trash f-20"></i>
                                            </a>
                                            -->

                                            <a href="#"
                                                class="btn-link-secondary ms-2 btn-delete-clase"
                                                data-delete-url="<?= $routes['clases_teoricas_delete_ajax']; ?>"
                                                data-id="<?= $clase['id'] ?>"
                                                title="Eliminar">
                                                <i class="ti ti-trash f-20"></i>
                                            </a>



                                        <?php endif; ?>

                                        <!-- Botón listado de estudiantes -->
                                        <a href="#"
                                            class="btn-link-secondary ms-2 btn-listado-estudiantes"
                                            data-id="<?= $clase['id'] ?>"
                                            title="Listado de estudiantes">
                                            <i class="ti ti-users f-20"></i>
                                        </a>



                                        <!-- Botón de informe Excel -->
                                        <a href="<?= $routes['clases_teoricas_informe_clase']; ?><?= urlencode($clase['id']) ?>"
                                            class="btn-link-secondary ms-2" title="Exportar informe Excel de esta clase">
                                            <i class="ti ti-download f-20"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Form oculto para DELETE por POST -->
<form id="form-delete-clase" method="POST" style="display:none;"></form>


<!-- ----------------------------------------------------------
     🔹 Modal Listado de Estudiantes
----------------------------------------------------------- -->
<div class="modal fade" id="modalListadoEstudiantes" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header cc-card-header">
                <h5 class="modal-title fw-bold">Listado de estudiantes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Se llena dinámicamente -->
                <div id="modalListadoContent"></div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener('hidden.bs.modal', function() {

        // Restaurar scroll del body
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');

        // Eliminar cualquier backdrop residual
        document.querySelectorAll('.modal-backdrop').forEach(bd => bd.remove());
    });
</script>

<script>
    // ----------------------------------------------------------
    // 🔹 ELIMINAR CLASE TEÓRICA POR AJAX
    // ----------------------------------------------------------
    document.addEventListener('click', async (e) => {

        // Buscar si presionó el botón de eliminar
        const btn = e.target.closest('.btn-delete-clase');
        if (!btn) return;

        e.preventDefault();
        e.stopPropagation();

        const id = btn.dataset.id;
        const url = btn.dataset.deleteUrl;

        if (!id || !url) {
            console.error("Falta ID o URL en el botón");
            return;
        }

        // ------------------------------------------------------
        // 🔹 Confirmación con SweetAlert2
        // ------------------------------------------------------
        const result = await Swal.fire({
            icon: 'warning',
            title: '¿Eliminar esta clase?',
            text: 'Esta acción no se puede deshacer.',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });

        if (!result.isConfirmed) return;

        // ------------------------------------------------------
        // 🔹 Preparar datos del POST
        // ------------------------------------------------------
        const formData = new FormData();
        formData.append('id', id);

        // ------------------------------------------------------
        // 🔹 Enviar petición AJAX
        // ------------------------------------------------------
        const resp = await fetch(url, {
            method: 'POST',
            body: formData
        });

        // Recibir la respuesta como texto (para debug)
        const respText = await resp.text();
        console.log("Respuesta del servidor:", respText);
        // 🔥 MOSTRAR EN PANTALLA el contenido REAL que envía el servidor
        console.log("🔎 RESPUESTA RAW DEL SERVIDOR:");
        console.log(respText);

        let data;
        try {
            data = JSON.parse(respText); // intentar interpretar como JSON
        } catch (e) {
            console.error("NO ES JSON:", respText);
            Swal.fire("Error", "Respuesta inválida del servidor.", "error");
            return;
        }

        // ------------------------------------------------------
        // 🔹 Procesar respuesta
        // ------------------------------------------------------
        if (data.status === 'ok') {
            Swal.fire("Eliminada", data.msg, "success");

            // Quitar la fila de la tabla sin recargar
            const fila = btn.closest('tr');
            if (fila) fila.remove();

        } else {
            Swal.fire("Error", data.msg, "error");
        }
    });
</script>

<script>
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-listado-estudiantes');
        if (!btn) return;

        e.preventDefault();
        const id = btn.dataset.id;

        // URL AJAX
        const url = `/x3Y8z0A1Bc/${id}`;

        // Cargar contenido
        fetch(url)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modalListadoContent').innerHTML = html;
                const modal = new bootstrap.Modal(document.getElementById('modalListadoEstudiantes'));
                modal.show();
            })
            .catch(err => {
                console.error(err);
                alert("Error cargando el listado");
            });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const formDelete = document.getElementById('form-delete-clase');

        document.querySelectorAll('.btn-delete-clase').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const url = btn.getAttribute('data-delete-url');

                const confirmar = () => {
                    formDelete.setAttribute('action', url);
                    formDelete.submit();
                };

                if (window.Swal) {
                    Swal.fire({
                        icon: 'warning',
                        title: '¿Eliminar clase?',
                        text: 'Esta acción no se puede deshacer. Si la clase tiene estudiantes asignados no se eliminará.',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar'
                    }).then(res => {
                        if (res.isConfirmed) confirmar();
                    });
                } else {
                    if (confirm('¿Eliminar esta clase?')) confirmar();
                }
            });
        });
    });
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="../assets/js/plugins/dataTables.min.js"></script>
<script src="../assets/js/plugins/dataTables.bootstrap5.min.js"></script>
<!-- <script src="../assets/js/datatables-config.js"></script> -->


<script>
    $(document).ready(function() {
        $('.data-table').DataTable({
            responsive: true,
            ordering: true,
            order: [],
            pageLength: 20, // 👈 cantidad por defecto
            language: {
                decimal: ",",
                thousands: ".",
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros por página",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Mostrando 0 a 0 de 0 registros",
                infoFiltered: "(filtrado de _MAX_ registros totales)",
                loadingRecords: "Cargando...",
                zeroRecords: "No se encontraron registros coincidentes",
                emptyTable: "No hay datos disponibles en la tabla",
                aria: {
                    sortAscending: ": activar para ordenar la columna ascendente",
                    sortDescending: ": activar para ordenar la columna descendente"
                }
            }
        });
    });
</script>




<script>
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-listado-estudiantes');
        if (!btn) return;

        e.preventDefault(); // ← IMPORTANTE
        e.stopPropagation();

        const id = btn.dataset.id;
        const url = `/x3Y8z0A1Bc/${id}`;

        fetch(url)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modalListadoContent').innerHTML = html;

                const modal = new bootstrap.Modal(
                    document.getElementById('modalListadoEstudiantes')
                );
                modal.show();
            })
            .catch(err => {
                console.error(err);
                alert("Error cargando el listado");
            });
    });
</script>

<script>
    // ----------------------------------------------------------
    // 🔹 Desagendar alumno desde el modal (Event Delegation)
    // ----------------------------------------------------------
    document.addEventListener('click', (ev) => {

        const btn = ev.target.closest('.btn-unsubscribe-student');
        if (!btn) return; // No es el botón → salir

        ev.preventDefault();
        ev.stopPropagation();

        const cteId = btn.dataset.cteId;
        const alumno = btn.dataset.alumno;
        const claseId = btn.dataset.claseId;

        // Confirmación con SweetAlert2
        if (window.Swal) {
            Swal.fire({
                icon: 'warning',
                title: '¿Desagendar alumno?',
                html: `Vas a desagendar a <b>${alumno}</b> de esta clase.`,
                showCancelButton: true,
                confirmButtonText: 'Sí, desagendar',
                cancelButtonText: 'Cancelar'
            }).then(result => {
                if (result.isConfirmed) {
                    enviarDesagendar(cteId, claseId);
                }
            });
        } else {
            if (confirm(`¿Desagendar a ${alumno}?`)) {
                enviarDesagendar(cteId, claseId);
            }
        }
    });

    // ----------------------------------------------------------
    // 🔹 Función para enviar el formulario de desagendar
    // ----------------------------------------------------------
    function enviarDesagendar(cteId, claseId) {

        // Creamos un <form> dinámico (mejor que usar uno oculto)
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/clases_teoricas/desagendar_estudiante_admin';

        // Campo cte_id
        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'cte_id';
        input1.value = cteId;

        // Campo clase_id
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'clase_id';
        input2.value = claseId;

        form.appendChild(input1);
        form.appendChild(input2);

        document.body.appendChild(form);
        form.submit();
    }
</script>

<script>
    // ----------------------------------------------------------
    // 🔹 Función para recargar completamente el contenido del modal
    // ----------------------------------------------------------
    function recargarModalListado(claseId) {
        fetch(`/x3Y8z0A1Bc/${claseId}`)
            .then(res => res.text())
            .then(html => {
                document.getElementById('modalListadoContent').innerHTML = html;
            })
            .catch(err => {
                console.error('Error recargando modal:', err);
            });
    }
</script>

<script>
    // ----------------------------------------------------------
    // 🔹 Desagendar alumno usando el MISMO método del sistema
    //    pero sin recargar la página (fetch ignora redirect)
    // ----------------------------------------------------------
    document.addEventListener('click', (ev) => {
        const btn = ev.target.closest('.btn-unsubscribe-student');
        if (!btn) return;

        ev.preventDefault();
        ev.stopPropagation();

        const cteId = btn.dataset.cteId;
        const alumno = btn.dataset.alumno;
        const claseId = btn.dataset.claseId;

        Swal.fire({
            icon: 'warning',
            title: '¿Desagendar alumno?',
            html: `Vas a desagendar a <b>${alumno}</b>.`,
            showCancelButton: true,
            confirmButtonText: 'Sí, desagendar',
            cancelButtonText: 'Cancelar'
        }).then(async (result) => {

            if (!result.isConfirmed) return;

            // 🔹 Enviar al método original SIN recargar página
            const formData = new URLSearchParams();
            formData.append('cte_id', cteId);
            formData.append('clase_id', claseId);

            // ESTE endpoint es el mismo que usan instructores, NO LO TOCAMOS
            const url = '<?= $routes["clases_teoricas_unsubscribe_estudiante_admin"] ?>';

            await fetch(url, {
                method: 'POST',
                body: formData,
            });

            // ✔️ El alumno ya fue removido en el backend
            // ✔️ El redirect NO afecta porque fetch no recarga la página

            // 🔹 Volvemos a recargar el modal COMPLETO
            recargarModalListado(claseId);

            Swal.fire('Listo', 'Alumno desagendado.', 'success');
        });
    });
</script>