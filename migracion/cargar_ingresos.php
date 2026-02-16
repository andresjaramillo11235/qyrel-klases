<?php
require '../vendor/autoload.php';
include '../config/DatabaseConfig.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

ini_set('display_errors', 1);
error_reporting(E_ALL);

function safe_trim($value)
{
    return is_null($value) ? '' : trim($value);
}

$db = new DatabaseConfig();
$conn = $db->getConnection();

$archivo = __DIR__ . '/ingresos.xlsx';
$documento = IOFactory::load($archivo);
$hoja = $documento->getActiveSheet();
$filas = $hoja->toArray(null, true, true, true);

$logErrores = [];

$tipoPagoMap = [
    'EFECTIVO' => 1,
    'TC' => 2,
    'TRANSFERENCIA' => 2
];

for ($i = 2; $i <= count($filas); $i++) {
    $fila = $filas[$i];

    $numero_recibo = safe_trim($fila['A']);
    $valor = (int)safe_trim($fila['B']);
    $excelFecha = $fila['C'];

    $fecha = null;

    if (is_numeric($excelFecha)) {
        // Fecha como serial de Excel
        $fecha = Date::excelToDateTimeObject($excelFecha)->format('Y-m-d');
    } else {
        $fechaLimpia = safe_trim($excelFecha);

        // Intentar convertir formato DD/MM/YYYY manualmente
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $fechaLimpia)) {
            $partes = explode('/', $fechaLimpia);
            $fecha = "{$partes[2]}-{$partes[1]}-{$partes[0]}"; // YYYY-MM-DD
        } elseif (strtotime($fechaLimpia) !== false) {
            $fecha = date('Y-m-d', strtotime($fechaLimpia));
        }
    }

    if (!$fecha) {
        $logErrores[] = "Fila $i: Fecha inválida o vacía - '$excelFecha'";
        continue;
    }

    $matricula_id = safe_trim($fila['D']);
    $tipo_pago = strtoupper(safe_trim($fila['E']));
    $observaciones = safe_trim($fila['F']);

    $motivo_ingreso_id = 1;
    $empresa_id = 19;

    $tipo_ingreso_id = $tipoPagoMap[$tipo_pago] ?? null;

    if (!$tipo_ingreso_id) {
        $logErrores[] = "Fila $i: Tipo de pago inválido - $tipo_pago";
        continue;
    }

    // Validar existencia de matrícula
    $stmtMat = $conn->prepare("SELECT id FROM matriculas WHERE id = ?");
    $stmtMat->execute([$matricula_id]);
    if (!$stmtMat->fetch()) {
        $logErrores[] = "Fila $i: Matrícula no encontrada - $matricula_id";
        continue;
    }

    // Validar que no exista el número de recibo
    $stmtRec = $conn->prepare("SELECT COUNT(*) FROM financiero_ingresos WHERE numero_recibo = ?");
    $stmtRec->execute([$numero_recibo]);
    if ($stmtRec->fetchColumn() > 0) {
        $logErrores[] = "Fila $i: Número de recibo duplicado - $numero_recibo";
        continue;
    }

    // Insertar ingreso
    $stmtIng = $conn->prepare("INSERT INTO financiero_ingresos 
        (matricula_id, valor, motivo_ingreso_id, tipo_ingreso_id, observaciones, fecha, numero_recibo, empresa_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmtIng->execute([$matricula_id, $valor, $motivo_ingreso_id, $tipo_ingreso_id, $observaciones, $fecha, $numero_recibo, $empresa_id])) {
        $logErrores[] = "Fila $i: Error al insertar ingreso - Recibo: $numero_recibo";
        continue;
    }

    echo "✅ Fila $i: Ingreso registrado - Recibo: $numero_recibo<br>";
}

if (!empty($logErrores)) {
    $logFile = __DIR__ . '/log_errores_ingresos.txt';
    file_put_contents($logFile, implode("\n", $logErrores));
    echo "<br><br>❗ Se encontraron errores. Revisa el archivo log: log_errores_ingresos.txt";
} else {
    echo "<br><br>✅ Cargue de ingresos completado sin errores.";
}
