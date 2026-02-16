<?php
require '../vendor/autoload.php';
include '../config/DatabaseConfig.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

ini_set('display_errors', 1);
error_reporting(E_ALL);

function safe_trim($value) {
    return is_null($value) ? '' : trim($value);
}

$db = new DatabaseConfig();
$conn = $db->getConnection();

$archivo = __DIR__ . '/matriculas.xlsx';
$documento = IOFactory::load($archivo);
$hoja = $documento->getActiveSheet();
$filas = $hoja->toArray(null, true, true, true);

$logErrores = [];

$programaMap = [
    'A1' => 29,
    'A2' => 17,
    'B1' => 18,
    'C1' => 19
];

for ($i = 2; $i <= count($filas); $i++) {
    $fila = $filas[$i];

    $numero_matricula = safe_trim($fila['A']);
    $fechaExcel = $fila['C'];
    $categoria = strtoupper(safe_trim($fila['D']));
    $valor_matricula = (int)safe_trim($fila['E']);
    $clases_recibidas = safe_trim($fila['F']);
    $numero_documento = safe_trim($fila['G']);

    $convenio_id = 13;
    $empresa_id = 19;
    $tipo_solicitud_id = 1;
    $estado = 1;

    // Procesar fecha
    if (is_numeric($fechaExcel)) {
        $fecha_inscripcion = Date::excelToDateTimeObject($fechaExcel)->format('Y-m-d');
    } elseif (preg_match('#^\d{2}/\d{2}/\d{4}$#', safe_trim($fechaExcel))) {
        [$dia, $mes, $anio] = explode('/', safe_trim($fechaExcel));
        $fecha_inscripcion = "$anio-$mes-$dia";
    } elseif (strtotime(safe_trim($fechaExcel)) !== false) {
        $fecha_inscripcion = date('Y-m-d', strtotime(safe_trim($fechaExcel)));
    } else {
        $logErrores[] = "Fila $i: Fecha inválida - '$fechaExcel'";
        continue;
    }

    // Validar existencia del estudiante
    $stmtEst = $conn->prepare("SELECT id FROM estudiantes WHERE numero_documento = ?");
    $stmtEst->execute([$numero_documento]);
    $estudiante = $stmtEst->fetch(PDO::FETCH_ASSOC);

    if (!$estudiante) {
        $logErrores[] = "Fila $i: Estudiante no encontrado - Documento: $numero_documento";
        continue;
    }

    $estudiante_id = $estudiante['id'];

    // Insertar en matriculas
    $stmtMat = $conn->prepare("INSERT INTO matriculas 
        (id, fecha_inscripcion, estudiante_id, tipo_solicitud_id, convenio_id, estado, observaciones, empresa_id, valor_matricula) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmtMat->execute([
        $numero_matricula,
        $fecha_inscripcion,
        $estudiante_id,
        $tipo_solicitud_id,
        $convenio_id,
        $estado,
        $clases_recibidas,
        $empresa_id,
        $valor_matricula
    ])) {
        $logErrores[] = "Fila $i: Error al insertar matrícula - $numero_matricula";
        continue;
    }

    // Insertar en matricula_programas
    $programa_id = $programaMap[$categoria] ?? null;

    if (!$programa_id) {
        $logErrores[] = "Fila $i: Categoría inválida - $categoria";
        continue;
    }

    $stmtProg = $conn->prepare("INSERT INTO matricula_programas (matricula_id, programa_id) VALUES (?, ?)");
    if (!$stmtProg->execute([$numero_matricula, $programa_id])) {
        $logErrores[] = "Fila $i: Error al insertar en matricula_programas - matrícula $numero_matricula";
        continue;
    }

    echo "✅ Fila $i: Matrícula creada - $numero_matricula<br>";
}

if (!empty($logErrores)) {
    $logFile = __DIR__ . '/log_errores_matriculas.txt';
    file_put_contents($logFile, implode("\n", $logErrores));
    echo "<br><br>❗ Se encontraron errores. Revisa el archivo log: log_errores_matriculas.txt";
} else {
    echo "<br><br>✅ Cargue de matrículas completado sin errores.";
}
?>
