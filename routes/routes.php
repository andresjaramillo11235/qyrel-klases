<?php

require_once '../modules/auth/controllers/AuthMiddleware.php';
$routes = include '../config/Routes.php';

$router = new Router();

$router->getDirectory('/assets/fonts', __DIR__ . '/../assets/fonts');
$router->getDirectory('/assets/fonts/fontawesome', __DIR__ . '/../assets/fonts/fontawesome');
$router->getDirectory('/assets/fonts/tabler', __DIR__ . '/../assets/fonts/tabler');
$router->getDirectory('/assets/fonts/feather', __DIR__ . '/../assets/fonts/feather');
$router->getDirectory('/assets/fonts/material', __DIR__ . '/../assets/fonts/material');
$router->getDirectory('/assets/fonts/phosphor', __DIR__ . '/../assets/fonts/phosphor');
$router->getDirectory('/assets/fonts/phosphor/duotone', __DIR__ . '/../assets/fonts/phosphor/duotone');

$router->getDirectory('/assets/css', __DIR__ . '/../assets/css');
$router->getDirectory('/assets/css/plugins', __DIR__ . '/../assets/css/plugins');

$router->getDirectory('/assets/js', __DIR__ . '/../assets/js');
$router->getDirectory('/assets/js/plugins', __DIR__ . '/../assets/js/plugins');
$router->getDirectory('/assets/js/fonts', __DIR__ . '/../assets/js/fonts');
$router->getDirectory('/assets/js/pages', __DIR__ . '/../assets/js/pages');
$router->getDirectory('/assets/js/helpers', __DIR__ . '/../assets/js/helpers');
$router->getDirectory('/assets/js/clases_practicas_cronograma', __DIR__ . '/../assets/js/clases_practicas_cronograma');

$router->getDirectory('/assets/images', __DIR__ . '/../assets/images');
$router->getDirectory('/assets/images/user', __DIR__ . '/../assets/images/user');
$router->getDirectory('/assets/images/authentication', __DIR__ . '/../assets/images/authentication');
$router->getDirectory('/assets/images/layout', __DIR__ . '/../assets/images/layout');
$router->getDirectory('/assets/images/widget', __DIR__ . '/../assets/images/widget');

$router->getDirectory('/modules/clases/views/js', __DIR__ . '/../modules/clases/views/js');
$router->getDirectory('/modules/financiero/views/js', __DIR__ . '/../modules/financiero/views/js');
$router->getDirectory('/modules/consulta_rapida/js', __DIR__ . '/../modules/consulta_rapida/js');
$router->getDirectory('/modules/seguimiento/assets/js', __DIR__ . '/../modules/seguimiento/assets/js');

$router->middleware('/^(?!\/(login|register|logout|reset-password|update-password|procesar-cambio-password)).*$/', 'AuthMiddleware::handle');

## Rutas de Autenticación (Exentas del Middleware)
$router->get('/login/', 'modules\\auth\\controllers\\AuthController@showLoginForm');
$router->post('/login/', 'modules\\auth\\controllers\\AuthController@login');
$router->get('/logout/', 'modules\\auth\\controllers\\AuthController@logout');
$router->get('/reset-password/', 'modules\\passwords\\controllers\\PasswordRecoveryController@mostrarFormUsuario');
$router->get('/reset-password/([a-fA-F0-9]{64})', 'modules\\passwords\\controllers\\PasswordRecoveryController@mostrarFormUsuario');
$router->post('/reset-password/', 'modules\\passwords\\controllers\\PasswordRecoveryController@generarTokenRecuperacion');
$router->get('/update-password/([a-fA-F0-9]{64})',  'modules\\passwords\\controllers\\PasswordRecoveryController@mostrarFormModificar');
$router->get('/update-password/',  'modules\\passwords\\controllers\\PasswordRecoveryController@mostrarFormModificar');
$router->post('/procesar-cambio-password/', 'modules\\passwords\\controllers\\PasswordRecoveryController@cambiarPassword');

$router->get('/capcha/',  'modules\\auth\\controllers\\AuthController@generarCaptcha');

## Rutas protegidas *********************************
$router->get('/home/', 'modules\\home\\controllers\\HomeController@index');

// Administración de roles
$router->get('/roles/', 'modules\\roles\\controllers\\RolesController@index');
$router->get('/roles/create', 'modules\\roles\\controllers\\RolesController@create');
$router->post('/roles/store', 'modules\\roles\\controllers\\RolesController@store');
$router->get('/roles/edit/{id}', 'modules\\roles\\controllers\\RolesController@edit');
$router->post('/roles/update/{id}', 'modules\\roles\\controllers\\RolesController@update');
$router->get('/roles/detail/{id}', 'modules\\roles\\controllers\\RolesController@detail');
$router->get('/roles/delete/{id}', 'modules\\roles\\controllers\\RolesController@delete');

// Administración de permisos
$router->get('/permissions/', 'modules\\permissions\\controllers\\PermissionsAdminController@index');
$router->get('/permissionscreate/', 'modules\\permissions\\controllers\\PermissionsAdminController@create');
$router->post('/createpermission/', 'modules\\permissions\\controllers\\PermissionsAdminController@createPermission');
$router->get('/permissionslist/', 'modules\\permissions\\controllers\\PermissionsAdminController@listPermission');
$router->get('/permissionupdate/(\d+)', 'modules\\permissions\\controllers\\PermissionsAdminController@update');
$router->post('/updatepermission/', 'modules\\permissions\\controllers\\PermissionsAdminController@updatePermission');
$router->get('/permissiondelete/(\d+)', 'modules\\permissions\\controllers\\PermissionsAdminController@delete');

// Administración de asignaciones de permisos a roles
$router->get('/rolepermissions/', 'modules\\role_permissions\\controllers\\RolePermissionsAdminController@index');
$router->get('/rolepermissionscreate/', 'modules\\role_permissions\\controllers\\RolePermissionsAdminController@create');
$router->post('/rolepermissionsstore/', 'modules\\role_permissions\\controllers\\RolePermissionsAdminController@store');
$router->get('/rolepermissionsupdate/(\d+)', 'modules\\role_permissions\\controllers\\RolePermissionsAdminController@update');
$router->post('/rolepermissionsupdate/(\d+)', 'modules\\role_permissions\\controllers\\RolePermissionsAdminController@updateRolePermission');
$router->get('/rolepermissionsdelete/(\d+)', 'modules\\role_permissions\\controllers\\RolePermissionsAdminController@delete');

## PROGRAMAS ##
$router->get($routes['programas_index'], 'modules\\programas\\controllers\\ProgramasController@index');
$router->get($routes['programas_create'], 'modules\\programas\\controllers\\ProgramasController@create');
$router->get($routes['programas_edit'] . '([0-9]+)', 'modules\\programas\\controllers\\ProgramasController@edit');
$router->get($routes['programas_delete'] . '([0-9]+)', 'modules\\programas\\controllers\\ProgramasController@delete');
$router->post('/programas/store', 'modules\\programas\\controllers\\ProgramasController@store');
$router->post('/programas/update/{id}', 'modules\\programas\\controllers\\ProgramasController@update');
$router->get('/programas/delete/{id}', 'modules\\programas\\controllers\\ProgramasController@delete');
$router->get('/programas/detail/([a-zA-Z0-9]+)', 'modules\\programas\\controllers\\ProgramasController@detail');
$router->get('/programasclases/([a-zA-Z0-9]+)', 'modules\\programas\\controllers\\ProgramasController@getClasesPorPrograma');
$router->get('/clasesporprograma/([0-9]+)', 'modules\\programas\\controllers\\ProgramasController@getClasesPorPrograma');
$router->get($routes['get_programas'], 'modules\\programas\\controllers\\ProgramasController@getProgramas');

$router->get('/clasesPendientesPorMatricula/([0-9]+)', 'modules\\programas\\controllers\\ProgramasController@getClasesPendientesPorMatricula');

### EMPRESAS ###
$router->get('/empresas/', 'modules\\empresas\\controllers\\EmpresasController@index');
$router->get('/empresas-create/', 'modules\\empresas\\controllers\\EmpresasController@create');
$router->post('/empresas-store/', 'modules\\empresas\\controllers\\EmpresasController@store');
$router->get('/empresas-edit/([0-9]+)', 'modules\\empresas\\controllers\\EmpresasController@edit');
$router->post('/empresas-update/([0-9]+)', 'modules\\empresas\\controllers\\EmpresasController@update');
$router->get('/empresas/delete/{id}', 'modules\\empresas\\controllers\\EmpresasController@delete');
$router->get('/empresas-admin/([0-9]+)', 'modules\\users\\controllers\\UsersController@getUserAdmin');
$router->get('/empresas-detail/([0-9]+)', 'modules\\empresas\\controllers\\EmpresasController@detail');

### DOCUMENTOS RECIBO PAGO ###
$router->get($routes['pdf_generar_recibo_pago'] . '([0-9]+)', 'modules\\documentos\\controllers\\DocumentosController@generarReciboPago');

## DOCUMENTOS CONTRATOS ##
$router->get($routes['documento_contrato_index'], 'modules\\documentos\\controllers\\ContratosController@index');
$router->post($routes['documento_contrato_store'], 'modules\\documentos\\controllers\\ContratosController@store');
$router->post($routes['documento_contrato_update'], 'modules\\documentos\\controllers\\ContratosController@update');
$router->get($routes['documento_contrato_delete'] . '([0-9]+)', 'modules\\documentos\\controllers\\ContratosController@delete');
$router->get($routes['documento_contrato_pdf'] . '([a-zA-Z0-9]+)', 'modules\\documentos\\controllers\\ContratosController@generatePDFContract');

## DOCUMENTOS CONTROL CLASES TEORICAS
$router->get($routes['documento_control_clases_teoricas_pdf'] . '([a-zA-Z0-9]+)', 'modules\\documentos\\controllers\\ControlClasesTeoriaController@generarPdfControlClasesTeoria');

## DOCUMENTOS CONTROL CLASES PRACTICAS
$router->get($routes['documento_control_clases_practicas_pdf'] . '([a-zA-Z0-9]+)', 'modules\\documentos\\controllers\\ControlClasesPracticaController@generatePDFControlClasesPractica');

## USUARIOS ###
$router->get('/users/', 'modules\\users\\controllers\\UsersController@index');
$router->post('/users-store/', 'modules\\users\\controllers\\UsersController@store');
$router->get('/users-create/([0-9]+)', 'modules\\users\\controllers\\UsersController@create');
$router->get('/usersedit/([a-zA-Z0-9]+)', 'modules\\users\\controllers\\UsersController@edit');
$router->post('/usersedit/([a-zA-Z0-9]+)', 'modules\\users\\controllers\\UsersController@edit');
$router->get('/usersdelete/([a-zA-Z0-9]+)', 'modules\\users\\controllers\\UsersController@delete');

/* ESTUDIANTES */
$router->get('/estudiantes/', 'modules\\estudiantes\\controllers\\EstudiantesController@index');
$router->get('/estudiantescreate/', 'modules\\estudiantes\\controllers\\EstudiantesController@create');
$router->post('/estudiantesstore/', 'modules\\estudiantes\\controllers\\EstudiantesController@store');
$router->get('/estudiantesedit/{id}', 'modules\\estudiantes\\controllers\\EstudiantesController@edit');
$router->post('/estudiantesupdate/{id}', 'modules\\estudiantes\\controllers\\EstudiantesController@update');
$router->get('/estudiantesdelete/{id}', 'modules\\estudiantes\\controllers\\EstudiantesController@delete');
$router->get('/estudiantesdetail/([a-zA-Z0-9]+)', 'modules\\estudiantes\\controllers\\EstudiantesController@detail');
$router->get($routes['estudiantes_cuenta'], 'modules\\estudiantes\\controllers\\EstudiantesController@cuenta');
$router->get('/estudiantes/matricula/', 'modules\\estudiantes\\controllers\\EstudiantesController@matricula');
$router->post('/estudiantes/search/', 'modules\\estudiantes\\controllers\\EstudiantesController@search');

//$router->get('/estudiantesbuscar/{termino}', 'modules\\estudiantes\\controllers\\EstudiantesController@buscar');
$router->post('/estudiantesbuscar/', 'modules\\estudiantes\\controllers\\EstudiantesController@buscar');

$router->post('/buscar-estudiante-por-nombre/', 'modules\\estudiantes\\controllers\\EstudiantesController@buscarEstudiantePorNombre');
$router->get('/estudiantesdetalle/([a-zA-Z0-9]+)', 'modules\\estudiantes\\controllers\\EstudiantesController@detalle');
$router->get('/programasdetalle/([a-zA-Z0-9]+)/([a-zA-Z0-9]+)', 'modules\\estudiantes\\controllers\\EstudiantesController@detallePrograma');
$router->get('/estudiantes/seguimiento_clases/', 'modules\\estudiantes\\controllers\\EstudiantesController@seguimientoClases');
$router->get('/estudiantes-agenda-teoricas/([a-zA-Z0-9-]+)', 'modules\\estudiantes\\controllers\\EstudiantesController@calendarioClasesTeoricas');
$router->get('/estudiantes-agenda-teoricas/', 'modules\\estudiantes\\controllers\\EstudiantesController@calendarioClasesTeoricas');
$router->post('/estudiantes_agendar_teorica/', 'modules\\estudiantes\\controllers\\EstudiantesController@agendarClaseTeorica');
$router->post('/estudiantes_verificar_documento/', 'modules\\estudiantes\\controllers\\EstudiantesController@verificarDocumento');
$router->post('/estudiantes_verificar_correo/', 'modules\\estudiantes\\controllers\\EstudiantesController@verificarCorreo');
$router->get('/estudiante_obtener_detalle_clase/([a-zA-Z0-9]+)', 'modules\\estudiantes\\controllers\\EstudiantesController@obtenerDetalleClase');
$router->post('/estudiantes/buscar/', 'modules\\estudiantes\\controllers\\EstudiantesController@buscarEstudiantes');

$router->get($routes['estudiantes_progreso_teorico'], 'modules\\estudiantes\\controllers\\EstudiantesController@progresoTeorico');
$router->post($routes['estudiantes_progreso_teorico'], 'modules\\estudiantes\\controllers\\EstudiantesController@progresoTeorico');
$router->post($routes['clases_teoricas_estudiante_unsuscribe'], 'modules\\estudiantes\\controllers\\EstudiantesController@estudianteDesinscribir');


## MATRICULAS ##
$router->get('/matriculas/([0-9]+)/([0-9]+)', 'modules\\matriculas\\controllers\\MatriculasController@index');
$router->get('/matriculas/', 'modules\\matriculas\\controllers\\MatriculasController@index');

$router->post('/matriculas/', 'modules\\matriculas\\controllers\\MatriculasController@index');
$router->get('/matriculascreate/', 'modules\\matriculas\\controllers\\MatriculasController@create');
$router->post('/matriculas/store', 'modules\\matriculas\\controllers\\MatriculasController@store');
$router->get('/matriculasedit/([a-zA-Z0-9]+)', 'modules\\matriculas\\controllers\\MatriculasController@edit');
$router->post('/matriculasupdate/', 'modules\\matriculas\\controllers\\MatriculasController@update');
$router->get($routes['matriculas_delete'] . '([a-zA-Z0-9-]+)', 'modules\\matriculas\\controllers\\MatriculasController@delete');
$router->get('/matriculasdetail/([a-zA-Z0-9]+)', 'modules\\matriculas\\controllers\\MatriculasController@detail');
$router->get('/matriculas/activate/([a-zA-Z0-9]+)', 'modules\\matriculas\\controllers\\MatriculasController@activate');


$router->GET($routes['matriculas_validar_programa_estudiante'] . '([0-9]+)/([0-9]+)', 'modules\\matriculas\\controllers\\MatriculasController@validarProgramaEstudiante');
$router->get($routes['matriculas_dashboard'], 'modules\\matriculas\\controllers\\MatriculasController@matriculasDashboard');

### clases prácticas ###
$router->get('/clases/index/([a-zA-Z0-9]+)', 'modules\\clases\\controllers\\ClasesController@index');
$router->get('/clases/create/([a-zA-Z0-9]+)', 'modules\\clases\\controllers\\ClasesController@create');
$router->get('/clases/edit/([a-zA-Z0-9]+)', 'modules\\clases\\controllers\\ClasesController@edit');
$router->get('/clases/detail/([a-zA-Z0-9]+)', 'modules\\clases\\controllers\\ClasesController@detail');
$router->post('/clases/store/', 'modules\\clases\\controllers\\ClasesController@store');
$router->post('/clases/update/([a-zA-Z0-9]+)', 'modules\\clases\\controllers\\ClasesController@update');

### INSTRUCTORES ###
$router->get('/instructores/', 'modules\\instructores\\controllers\\InstructoresController@index');
$router->get('/instructoresdetail/([a-zA-Z0-9]+)', 'modules\\instructores\\controllers\\InstructoresController@detail');
$router->get('/instructoresedit/([a-zA-Z0-9]+)', 'modules\\instructores\\controllers\\InstructoresController@edit');
$router->get('/instructorescreate/', 'modules\\instructores\\controllers\\InstructoresController@create');
$router->post('/instructores/update/([a-zA-Z0-9]+)', 'modules\\instructores\\controllers\\InstructoresController@update');
$router->post('/instructoresstore/', 'modules\\instructores\\controllers\\InstructoresController@store');
$router->get('/instructores/cuenta/', 'modules\\instructores\\controllers\\InstructoresController@cuenta');
$router->post('/instructores/cronograma_semanal/', 'modules\\instructores\\controllers\\InstructoresController@cronogramaSemanal');
$router->post('/instructores/obtener_detalle_clase/', 'modules\\instructores\\controllers\\InstructoresController@obtenerDetalleClase');
$router->post('/instructores/actualizar_clase/', 'modules\\instructores\\controllers\\InstructoresController@actualizarClase');
$router->get('/instructores/cronograma_semanal/', 'modules\\instructores\\controllers\\InstructoresController@cronogramaSemanal');
$router->get('/instructores/obtener_estados_clase/', 'modules\\instructores\\controllers\\InstructoresController@obtenerEstadosClase');
$router->get('/instructores/cronograma_diario/', 'modules\\instructores\\controllers\\InstructoresController@cronogramaDiario');
$router->post('/instructores/clases_del_dia/', 'modules\\instructores\\controllers\\InstructoresController@clasesDelDia');
$router->get('/instructores/administracion/', 'modules\\instructores\\controllers\\InstructoresController@administracion');
$router->post('/instructores_verificar_documento/', 'modules\\instructores\\controllers\\InstructoresController@verificarDocumento');
$router->get($routes['get_instructores'], 'modules\\instructores\\controllers\\InstructoresController@getInstructores');



## CLASES TEORICAS TEMAS ##
$router->get($routes['clases_teoricas_temas_index'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasTemasController@index');
$router->get($routes['clases_teoricas_temas_edit'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasTemasController@edit');
$router->get($routes['clases_teoricas_temas_create'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasTemasController@create');
$router->post($routes['clases_teoricas_temas_store'], 'modules\\clases\\controllers\\ClasesTeoricasTemasController@store');
$router->post($routes['clases_teoricas_temas_update'], 'modules\\clases\\controllers\\ClasesTeoricasTemasController@update');



## CLASES TEORICAS ##
$router->get('/clases_teoricas/', 'modules\\clases\\controllers\\ClasesTeoricasController@index');
$router->post('/clases_teoricas/', 'modules\\clases\\controllers\\ClasesTeoricasController@index');
$router->get($routes['clases_teoricas_delete'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@delete');
$router->post($routes['clases_teoricas_delete'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@delete');
$router->get('/clasesteoricascreate/([a-zA-Z0-9-]+)/([a-zA-Z0-9:]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@create');
$router->get('/clasesteoricascreate/', 'modules\\clases\\controllers\\ClasesTeoricasController@create');
$router->post('/clasesteoricasstore/', 'modules\\clases\\controllers\\ClasesTeoricasController@store');
$router->post('/clases_teoricas_check_availability/', 'modules\\clases\\controllers\\ClasesTeoricasController@checkAvailability');
$router->get('/clases_teoricas/detail/([a-zA-Z0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@detail');
$router->get($routes['clases_teoricas_edit'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@edit');
$router->post('/clases_teoricas_update/([a-zA-Z0-9]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@update');


$router->get('/clases_teoricas/delete/(:num)', 'modules\\clases_teoricas\\controllers\\ClasesTeoricasController@delete');
$router->post($routes['clases_teoricas_delete_ajax'], 'modules\\clases\\controllers\\ClasesTeoricasController@deleteAjax');


$router->get('/clases_teoricas/getEvents', 'modules\\clases\\controllers\\ClasesTeoricasController@getEvents');
$router->get('/clases_teoricas/calendar', 'modules\\clases\\controllers\\ClasesTeoricasController@calendar');
$router->get($routes['clases_teoricas_calendariodos'], 'modules\\clases\\controllers\\ClasesTeoricasController@calendariodos');
$router->get($routes['clases_teoricas_calendariodos'] . '([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@calendariodos');
$router->get($routes['clases_teoricas_listado_instructores'], 'modules\\clases\\controllers\\ClasesTeoricasController@indexInstructores');
$router->get($routes['clases_teoricas_listado_estudiantes'] . '([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@listadoEstudiantes');
$router->get('/clases_teoricas/getTemasByPrograma/([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@getTemasByPrograma');
$router->get('/clases_teoricas/getClasesByTema/([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@getClasesByTema');
$router->post($routes['clases_teoricas_guardar_asistencia'], 'modules\\clases\\controllers\\ClasesTeoricasController@guardarAsistencia');
$router->get($routes['clases_teoricas_informe_general'], 'modules\\clases\\controllers\\ClasesTeoricasController@formularioInforme');
$router->post($routes['clases_teoricas_exportar_informe'], 'modules\\clases\\controllers\\ClasesTeoricasController@exportarInformeClasesTeoricas');
$router->get($routes['clases_teoricas_informe_clase'] . '([0-9-]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@exportarInformePorClase');
$router->get($routes['clases_teoricas_no_asistidas'], 'modules\\clases\\controllers\\ClasesNoAsistidasController@index');
$router->post($routes['clases_teoricas_marcar_no_visto'], 'modules\\clases\\controllers\\ClasesNoAsistidasController@marcarComoNoVisto');
$router->post($routes['clases_teoricas_unsubscribe'] . '([0-9-]+)', 'modules\\estudiantes\\controllers\\EstudiantesController@desagendarClaseTeorica');
$router->get($routes['clases_teoricas_unsubscribe']  . '([0-9-]+)', 'modules\\estudiantes\\controllers\\EstudiantesController@desagendarClaseTeorica');
$router->post($routes['clases_teoricas_unsubscribe_estudiante_admin'], 'modules\\clases\\controllers\\ClasesTeoricasController@unsubscribeEstudianteAdmin');
$router->get($routes['clases_teoricas_carga_masiva_form'], 'modules\\clases\\controllers\\ClasesTeoricasController@cargaMasivaForm');
$router->post($routes['clases_teoricas_carga_masiva_process'], 'modules\\clases\\controllers\\ClasesTeoricasController@cargaMasivaProcess');
$router->post($routes['clases_teoricas_get_asociables'], 'modules\\clases\\controllers\\ClasesTeoricasController@getAsociables');
$router->get($routes['clases_teoricas_creacion_multiple_form'], 'modules\\clases\\controllers\\ClasesTeoricasController@mostrarFormAlmacenamientoMultiple');
$router->post($routes['clases_teoricas_creacion_multiple_store'], 'modules\\clases\\controllers\\ClasesTeoricasController@storeMultiple');

$router->get($routes['clases_teoricas_listado_temas_by_programa'] . '([0-9-]+)', 'modules\\programas\\controllers\\TemasController@listadoTemasTeoricosByPrograma');
$router->get($routes['clases_teoricas_listado_estudiantes_modal'] . '([0-9-]+)', 'modules\\clases\\controllers\\ClasesTeoricasController@ajaxListadoEstudiantes');


### CLASES PRACTICAS ###
$router->get($routes['clases_practicas_listado_admin'], 'modules\\clases\\controllers\\ClasesPracticasController@index');
$router->post($routes['clases_practicas_listado_admin'], 'modules\\clases\\controllers\\ClasesPracticasController@index');

$router->get($routes['clases_practicas_listado_estudiante'], 'modules\\clases\\controllers\\ClasesPracticasController@listarClasesEstudiante');
$router->get($routes['clases_practicas_estudiante_calificar'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasController@calificarClaseEstudiante');
$router->get($routes['clases_practicas_instructor_calificar'] . '([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasController@calificarClaseInstructor');

$router->get($routes['clases_practicas_listado_instructor'], 'modules\\clases\\controllers\\ClasesPracticasController@listarClasesInstructor');
$router->post($routes['clases_practicas_listado_instructor'], 'modules\\clases\\controllers\\ClasesPracticasController@listarClasesInstructor');

$router->post($routes['clases_practicas_estudiante_calificar_store'], 'modules\\clases\\controllers\\ClasesPracticasController@calificarClaseEstudianteStore');
$router->post($routes['clases_practicas_instructor_calificar_store'], 'modules\\clases\\controllers\\ClasesPracticasController@calificarClaseInstructorStore');
$router->get($routes['clases_practicas_eliminar_clase']  . '([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasController@eliminarClasePractica');
$router->post($routes['clases_practicas_editar_clase']  . '([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasController@editarClasePractica');

$router->post($routes['clases_practicas_cambiar_estado'], 'modules\\clases\\controllers\\ClasesPracticasController@cambiarEstadoClasePractica');

$router->get($routes['clases_practicas_obtener_calificacion_estudiante']  . '([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasController@obtenerCalificacionClase');
$router->get($routes['clases_practicas_detalle']  . '([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasController@detalleClasePractica');
$router->get($routes['clases_practicas_seguimiento']  . '([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasSeguimientoController@mapaSeguimiento');
$router->get($routes['clases_practicas_cronograma_estudiante'], 'modules\\clases\\controllers\\ClasesPracticasController@cronogramaEstudiante');
$router->get($routes['clases_practicas_cronograma_estudiante'] . '([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesPracticasController@cronogramaEstudiante');
$router->get($routes['clases_practicas_cronograma_instructor'], 'modules\\clases\\controllers\\ClasesPracticasController@cronogramaInstructor');
$router->get($routes['clases_practicas_cronograma_instructor'] . '([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesPracticasController@cronogramaInstructor');
$router->get('/clasespracticascronograma/', 'modules\\clases\\controllers\\ClasesPracticasController@cronograma');
$router->get('/clasespracticasajax/([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesPracticasController@getClasesByFecha');

$router->get($routes['clases_practicas_cargar_clases_estudiante'] . '([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesPracticasController@getClasesEstudianteByFecha');
$router->get($routes['clases_practicas_cargar_clases_instructor'] . '([a-zA-Z0-9-]+)', 'modules\\clases\\controllers\\ClasesPracticasController@getClasesInstructorByFecha');

$router->get('/clases_practicas/getClasesByFecha', 'modules\\clases\\controllers\\ClasesPracticasController@getClasesByFecha');
$router->post('/clases_practicas/create/', 'modules\\clases\\controllers\\ClasesPracticasController@create');
$router->get('/clasespracticasdetalle/([0-9]+)', 'modules\\clases\\controllers\\ClasesPracticasController@getClasePracticaDetalle');
$router->post('/clasespracticasguardar', 'modules\\clases\\controllers\\ClasesPracticasController@storeClasePractica');
$router->post('/obtenerInstructoresDisponibles/', 'modules\\clases\\controllers\\ClasesPracticasController@obtenerInstructoresDisponibles');

$router->post($routes['home_instructor_historial_estudiante'], 'modules\\home\\controllers\\HomeController@getHistorialClases');

### AULAS ###
$router->get($routes['aulas_index'], 'modules\\aulas\\controllers\\AulasController@index');
$router->get($routes['aulas_create'], 'modules\\aulas\\controllers\\AulasController@create');
$router->post($routes['aulas_store'], 'modules\\aulas\\controllers\\AulasController@store');
$router->get($routes['aulas_edit'] . '([0-9]+)', 'modules\\aulas\\controllers\\AulasController@edit');
$router->post($routes['aulas_update'] . '([0-9]+)', 'modules\\aulas\\controllers\\AulasController@update');
$router->post($routes['aulas_delete'] . '([0-9]+)', 'modules\\aulas\\controllers\\AulasController@delete');
$router->get($routes['get_aulas'], 'modules\\aulas\\controllers\\AulasController@getAulas');

### CONVENIOS ###
$router->get('/convenios/', 'modules\\convenios\\controllers\\ConveniosController@index');
$router->get('/convenios-create/', 'modules\\convenios\\controllers\\ConveniosController@create');
$router->post('/convenios-store/', 'modules\\convenios\\controllers\\ConveniosController@store');
$router->get('/convenios-edit/([0-9]+)', 'modules\\convenios\\controllers\\ConveniosController@edit');
$router->post('/convenios-update/([0-9]+)', 'modules\\convenios\\controllers\\ConveniosController@update');
$router->post('/convenios-guardar-valores/', 'modules\\convenios\\controllers\\ConveniosController@guardarValores');
$router->post('/convenios-valores-update/', 'modules\\convenios\\controllers\\ConveniosController@updateValor');
$router->get('/convenios-valores/([0-9]+)', 'modules\\convenios\\controllers\\ConveniosController@gestionarValores');
$router->get($routes['valor_convenio_programa'] . '([0-9]+)/([0-9]+)', 'modules\\convenios\\controllers\\ConveniosController@getValorConvenioPorPrograma');

### ADMINISTRATIVOS ###
$router->get('/administrativos/', 'modules\\administrativos\\controllers\\AdministrativosController@index');
$router->get('/administrativos-create/', 'modules\\administrativos\\controllers\\AdministrativosController@create');
$router->post('/administrativos-store/', 'modules\\administrativos\\controllers\\AdministrativosController@store');
$router->get('/administrativos-edit/([0-9]+)', 'modules\\administrativos\\controllers\\AdministrativosController@edit');
$router->post('/administrativos-update/([0-9]+)', 'modules\\administrativos\\controllers\\AdministrativosController@update');
$router->get('/administrativos-detail/([0-9]+)', 'modules\\administrativos\\controllers\\AdministrativosController@detail');

### VEHICULOS ### 
$router->get('/vehiculosdisponibles/([0-9]{4}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}:[0-9]{2})/([0-9]+)', 'modules\\vehiculos\\controllers\\VehiculosController@getVehiculosDisponibles');
$router->post('/vehiculosdisponibles/', 'modules\\vehiculos\\controllers\\VehiculosController@traerVehiculosDisponibles');
$router->get($routes['vehiculos_index'], 'modules\\vehiculos\\controllers\\VehiculosController@index');
$router->get($routes['vehiculos_create'], 'modules\\vehiculos\\controllers\\VehiculosController@create');
$router->post($routes['vehiculos_store'], 'modules\\vehiculos\\controllers\\VehiculosController@store');
$router->get($routes['vehiculos_detail'] . '([0-9]+)', 'modules\\vehiculos\\controllers\\VehiculosController@detail');
$router->get($routes['vehiculos_edit'] . '([0-9]+)', 'modules\\vehiculos\\controllers\\VehiculosController@edit');
$router->post($routes['vehiculos_update'], 'modules\\vehiculos\\controllers\\VehiculosController@update');
$router->get($routes['vehiculos_verificar_placa_unica'] . '([a-zA-Z0-9-]+)', 'modules\\vehiculos\\controllers\\VehiculosController@verificarPlaca');

### DISPOSITIVOS GPS ###
$router->get($routes['dispositivos_gps_index'], 'modules\\gps\\controllers\\DispositivosGpsController@index');
$router->get($routes['dispositivos_gps_create'], 'modules\\gps\\controllers\\DispositivosGpsController@create');
$router->post($routes['dispositivos_gps_store'], 'modules\\gps\\controllers\\DispositivosGpsController@store');

### SEGUIMIENTO ###
$router->get($routes['seguimiento_index'], 'modules\\seguimiento\\controllers\\SeguimientoController@index');
$router->get($routes['seguimiento_get_posicion_vehiculos'], 'modules\\seguimiento\\controllers\\SeguimientoController@getPosicionVehiculos');

### INGRESOS ###
$router->get($routes['ingresos_index'], 'modules\\financiero\\controllers\\IngresosController@index');
$router->get($routes['ingresos_index'] . '([A-Za-z0-9=+/]+)', 'modules\\financiero\\controllers\\IngresosController@index');
$router->get($routes['ingresos_informe_cartera'], 'modules\\financiero\\controllers\\IngresosController@informeCartera');
$router->get($routes['ingresos_informe'], 'modules\\financiero\\controllers\\IngresosController@informe');
$router->get($routes['ingresos_informe'] . '([A-Za-z0-9=+/]+)', 'modules\\financiero\\controllers\\IngresosController@informe');
$router->post($routes['ingresos_informe_serverside'], 'modules\\financiero\\controllers\\IngresosController@informeServerSide');
$router->post($routes['ingresos_informe_excel'], 'modules\\financiero\\controllers\\IngresosController@exportarExcelFiltro');

$router->get($routes['ingresos_create'], 'modules\\financiero\\controllers\\IngresosController@create');
$router->get($routes['ingresos_edit'] . '([0-9]+)', 'modules\\financiero\\controllers\\IngresosController@edit');
$router->get($routes['ingresos_delete'] . '([0-9]+)', 'modules\\financiero\\controllers\\IngresosController@delete');
$router->post($routes['ingresos_store'], 'modules\\financiero\\controllers\\IngresosController@store');
$router->post($routes['ingresos_update'], 'modules\\financiero\\controllers\\IngresosController@update');
$router->post($routes['ingresos_traer_abonos_matricula'], 'modules\\financiero\\controllers\\IngresosController@traer_abonos_matricula');

### EGRESOS ###
$router->get($routes['egresos_index'] . '([0-9]+)/([0-9]+)', 'modules\\financiero\\controllers\\EgresosController@index');
$router->get($routes['egresos_index'], 'modules\\financiero\\controllers\\EgresosController@index');
$router->post($routes['egresos_index'], 'modules\\financiero\\controllers\\EgresosController@index');

$router->get($routes['egresos_create'], 'modules\\financiero\\controllers\\EgresosController@create');
$router->post($routes['egresos_store'], 'modules\\financiero\\controllers\\EgresosController@store');
$router->post($routes['egresos_update'], 'modules\\financiero\\controllers\\EgresosController@update');
$router->post($routes['egresos_delete'], 'modules\\financiero\\controllers\\EgresosController@delete');
$router->get($routes['egresos_cuentas_egreso_index'], 'modules\\financiero\\controllers\\CuentasEgresoController@index');
$router->post($routes['egresos_cuentas_egreso_store'], 'modules\\financiero\\controllers\\CuentasEgresoController@storeCuenta');
$router->post($routes['egresos_cuentas_egreso_update'], 'modules\\financiero\\controllers\\CuentasEgresoController@updateCuenta');
$router->post($routes['egresos_cuentas_egreso_delete'], 'modules\\financiero\\controllers\\CuentasEgresoController@deleteCuenta');
$router->post($routes['egresos_subcuentas_egreso_store'], 'modules\\financiero\\controllers\\CuentasEgresoController@storeSubcuenta');
$router->post($routes['egresos_subcuentas_egreso_update'], 'modules\\financiero\\controllers\\CuentasEgresoController@updateSubcuenta');
$router->post($routes['egresos_subcuentas_egreso_delete'], 'modules\\financiero\\controllers\\CuentasEgresoController@deleteSubcuenta');

### CLIENTES NUEVOS ###
$router->get($routes['clientes_nuevos_index'], 'modules\\estudiantes\\controllers\\ClientesNuevosController@index');
$router->get($routes['clientes_nuevos_create'], 'modules\\estudiantes\\controllers\\ClientesNuevosController@create');
$router->post($routes['clientes_nuevos_store'], 'modules\\estudiantes\\controllers\\ClientesNuevosController@store');
$router->post($routes['clientes_nuevos_send_wa'], 'modules\\estudiantes\\controllers\\ClientesNuevosController@sendWhatsappTemplate');

### PROGRAMAS TEMAS ###
$router->get($routes['programas_temas_index'] . '([0-9]+)', 'modules\\programas\\controllers\\TemasController@index');
$router->get('/clasesprogramaslistado/([0-9]+)', 'modules\\programas\\controllers\\TemasController@listadoTemasByPrograma');
$router->post($routes['programas_temas_store'], 'modules\\programas\\controllers\\TemasController@store');
$router->get($routes['programas_temas_edit'] . '([0-9]+)', 'modules\\programas\\controllers\\TemasController@edit');
$router->post($routes['programas_temas_update'], 'modules\\programas\\controllers\\TemasController@update');
$router->post($routes['programas_temas_delete'] . '([0-9]+)', 'modules\\programas\\controllers\\TemasController@destroy');


### INSPECCIONES ###
$router->get($routes['inspecciones_vehiculos_index'], 'modules\\inspecciones\\controllers\\InspeccionesVehiculosController@index');
$router->get($routes['inspecciones_vehiculos_create'], 'modules\\inspecciones\\controllers\\InspeccionesVehiculosController@create');
$router->get($routes['inspecciones_vehiculos_view'] . '([0-9]+)', 'modules\\inspecciones\\controllers\\InspeccionesVehiculosController@view');
$router->post($routes['inspecciones_vehiculos_store'], 'modules\\inspecciones\\controllers\\InspeccionesVehiculosController@store');


### INSPECCIONES MOTOS ###
$router->get($routes['inspecciones_motos_index'], 'modules\\inspecciones\\controllers\\InspeccionesMotosController@index');
$router->post($routes['inspecciones_motos_index'], 'modules\\inspecciones\\controllers\\InspeccionesMotosController@index');
$router->get($routes['inspecciones_motos_create'], 'modules\\inspecciones\\controllers\\InspeccionesMotosController@create');
$router->post($routes['inspecciones_motos_store'], 'modules\\inspecciones\\controllers\\InspeccionesMotosController@store');
$router->get($routes['inspecciones_motos_view'] . '([0-9]+)', 'modules\\inspecciones\\controllers\\InspeccionesMotosController@view');

### INSPECCIONES DASHBOARD ###
$router->get($routes['inspecciones_dashboard'], 'modules\\inspecciones\\controllers\\InspeccionesDashboarController@index');

### CALIFICACIONES ###
$router->get($routes['calificaciones_index'], 'modules\\calificaciones\\controllers\\CalificacionesController@index');
$router->get($routes['calificaciones_detail'] . '([0-9]+)', 'modules\\calificaciones\\controllers\\CalificacionesController@detail');

### MUNICIPIOS ###
$router->get($routes['listado_municipios_por_departamento'] . '([0-9]+)', 'modules\\municipios\\controllers\\MunicipiosController@getListadoMunicipiosByDepartamento');

### AUDITORIA ###
$router->get($routes['auditoria_index'], 'modules\\auditoria\\controllers\\AuditoriaController@index');

### NOVEDADES ###
$router->get($routes['novedades_index'], 'modules\\novedades\\controllers\\NovedadesController@index');
$router->post($routes['novedades_update'], 'modules\\novedades\\controllers\\NovedadesController@update');

### CAJAS ###
$router->get($routes['cajas_index'], 'modules\\financiero\\controllers\\CajasController@index');
$router->get($routes['cajas_create'], 'modules\\financiero\\controllers\\CajasController@create');
$router->post($routes['cajas_store'], 'modules\\financiero\\controllers\\CajasController@store');
$router->get($routes['cajas_edit'] . '([0-9]+)', 'modules\\financiero\\controllers\\CajasController@edit');
$router->post($routes['cajas_update'], 'modules\\financiero\\controllers\\CajasController@update');
$router->post($routes['cajas_delete'] . '([0-9]+)', 'modules\\financiero\\controllers\\CajasController@delete');
$router->get($routes['caja_diaria'], 'modules\\financiero\\controllers\\CajasController@mostrarFormCajaDiaria');
$router->post($routes['procesar_caja_diaria'], 'modules\\financiero\\controllers\\CajasController@procesarCajaDiaria');

### MOVIMIENTOS DE CAJA ###
$router->get($routes['movimientos_caja_index'], 'modules\\financiero\\controllers\\MovimientosCajaController@index');
$router->post($routes['movimientos_caja_index'], 'modules\\financiero\\controllers\\MovimientosCajaController@index');
$router->get($routes['movimientos_caja_detalle'] . '([0-9]+)', 'modules\\financiero\\controllers\\MovimientosCajaController@detalle');

## CONSULTA RAPIDA ##
$router->get($routes['consulta_rapida_index'], 'modules\\consulta_rapida\\controllers\\ConsultaRapidaController@index');
$router->post($routes['consulta_rapida_buscar_estudiante'], 'modules\\consulta_rapida\\controllers\\ConsultaRapidaController@buscarEstudiantesConsultaRapida');

## INFORMES ##
$router->get($routes['informe_siet_index'], 'modules\\informes\\controllers\\InformesController@informeSietIndex');
$router->post($routes['informes_siet_resultado'], 'modules\\informes\\controllers\\InformesController@informeSietResultado');

### ERRORES ###
$router->get('/permission-denied/', 'modules\\errors\\controllers\\ErrorController@permissionDenied');
$router->get('/error/', 'modules\\errors\\controllers\\ErrorController@error');
$router->get('/connection-lost/', 'modules\\errors\\controllers\\ErrorController@connectionLost');

### CAMBIO DE CONTRASEÑA ###
$router->get($routes['reset_password'], 'modules\\passwords\\controllers\\PasswordController@mostrarFormulario');
$router->post($routes['update_password'], 'modules\\passwords\\controllers\\PasswordController@cambiarPasswordAutenticado');

$router->getImg('/files/fotos_estudiantes/{filename}', __DIR__ . '/../files/fotos_estudiantes');
$router->getImg('/files/fotos_instructores/{filename}', __DIR__ . '/../files/fotos_instructores');
$router->getImg('/files/fotos_administrativos/{filename}', __DIR__ . '/../files/fotos_administrativos');
$router->getImg('/files/fotos_vehiculos/{filename}', __DIR__ . '/../files/fotos_vehiculos');
$router->getImg('/files/logos_empresas/{filename}', __DIR__ . '/../files/logos_empresas');
$router->getImg('/files/fotos_inspecciones_vehiculos/{filename}', __DIR__ . '/../files/fotos_inspecciones_vehiculos');
$router->getImg('/files/fotos_inspecciones_motos/{filename}', __DIR__ . '/../files/fotos_inspecciones_motos');
$router->getImg('/files/soportes_egresos/{filename}', __DIR__ . '/../files/soportes_egresos');
$router->getImg('/assets/images/pages/{filename}', __DIR__ . '/../assets/images/pages');

$router->getCss('/assets/css/styles.css', __DIR__ . '/../assets/css/styles.css');

$router->getCss('/assets/js/buscar_estudiantes.js', __DIR__ . '/../assets/js/buscar_estudiantes.js');
$router->getCss('/assets/js/ajustar_ruta_fecha.js', __DIR__ . '/../assets/js/ajustar_ruta_fecha.js');
$router->getCss('/assets/js/form_agregar_clase.js', __DIR__ . '/../assets/js/form_agregar_clase.js');
$router->getCss('/assets/js/gestion_modal_clases.js', __DIR__ . '/../assets/js/gestion_modal_clases.js');

$router->getCss('/assets/js/gestion_modal_clases_instructor.js', __DIR__ . '/../assets/js/gestion_modal_clases_instructor.js');

$router->getCss('/assets/js/instructores/cargarEstados.js', __DIR__ . '/../assets/js//instructores/cargarEstados.js');
$router->getCss('/assets/js/instructores/mostrarCronograma.js', __DIR__ . '/../assets/js//instructores/mostrarCronograma.js');
$router->getCss('/assets/js/instructores/obtenerDetalleClase.js', __DIR__ . '/../assets/js//instructores/obtenerDetalleClase.js');
$router->getCss('/assets/js/instructores/actualizarClase.js', __DIR__ . '/../assets/js//instructores/actualizarClase.js');
$router->getCss('/assets/js/instructores/init.js', __DIR__ . '/../assets/js//instructores/init.js');
$router->getCss('/assets/js/calendarioClases.js', __DIR__ . '/../assets/js//calendarioClases.js');
$router->getCss('/assets/js/clases_teoricas_multiple.js', __DIR__ . '/../assets/js//clases_teoricas_multiple.js');

// Manejo de la solicitud
$currentRoute = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$router->handle($currentRoute, $requestMethod);
