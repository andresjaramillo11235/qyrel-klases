<?php

require '../vendor/autoload.php';
require_once '../shared/utils/ConvertirNumeroALetras.php';

class DocumentosController
{
  public function __construct() {}

  // Método para generar el PDF del recibo de pago
  public function generarReciboPago($ingreso_id)
  {
    // Incluir la configuración de la base de datos
    require_once '../config/DatabaseConfig.php';
    $dbConfig = new DatabaseConfig();
    $conn = $dbConfig->getConnection();

    // Consulta principal para obtener los datos del ingreso
    $queryIngreso = "
          SELECT 
            fi.id AS ingreso_id,
            fi.matricula_id,
            fi.valor,
            fi.numero_recibo,
            fi.observaciones,
            fi.fecha,
            pmfi.nombre AS motivo_ingreso,
            ptfi.nombre AS tipo_ingreso,
            m.id AS matricula_id,
            e.nombres AS estudiante_nombres,
            e.apellidos AS estudiante_apellidos,
            e.numero_documento AS estudiante_documento,
            p.nombre AS programa_nombre,
            cl.nombre AS categoria_licencia,
            c.nombre AS convenio_nombre
        FROM 
            financiero_ingresos fi
        INNER JOIN 
            param_motivos_financiero_ingresos pmfi ON fi.motivo_ingreso_id = pmfi.id
        INNER JOIN 
            param_tipos_financiero_ingresos ptfi ON fi.tipo_ingreso_id = ptfi.id
        INNER JOIN 
            matriculas m ON fi.matricula_id = m.id
        INNER JOIN 
            convenios c ON c.id = m.convenio_id   -- 👈 JOIN CONVENIOS
        INNER JOIN 
            estudiantes e ON m.estudiante_id = e.id
        LEFT JOIN 
            matricula_programas mp ON mp.matricula_id = fi.matricula_id
        LEFT JOIN 
            programas p ON p.id = mp.programa_id
        LEFT JOIN 
            categorias_licencia cl ON cl.id = p.categoria
        WHERE 
            fi.id = :ingreso_id;

    ";

    // Preparar y ejecutar la consulta
    $stmtIngreso = $conn->prepare($queryIngreso);
    $stmtIngreso->bindParam(':ingreso_id', $ingreso_id, PDO::PARAM_INT);
    $stmtIngreso->execute();
    $ingresoData = $stmtIngreso->fetch(PDO::FETCH_ASSOC);

    // Verificar si se encontró el ingreso
    if (!$ingresoData) {
      echo "Error: No se encontró el ingreso con ID " . htmlspecialchars($ingreso_id);
      exit;
    }

    // Sumar todos los abonos previos para la matrícula
    $queryAbonos = "SELECT SUM(valor) FROM financiero_ingresos WHERE matricula_id = :matricula_id";
    $stmtAbonos = $conn->prepare($queryAbonos);
    $stmtAbonos->bindParam(':matricula_id', $ingresoData['matricula_id']);
    $stmtAbonos->execute();
    $totalAbonos = $stmtAbonos->fetchColumn();
    $totalAbonos = $totalAbonos ? $totalAbonos : 0;

    // Obtener valor de la matrícula
    $queryMatricula = "SELECT valor_matricula FROM matriculas WHERE id = :matricula_id";
    $stmtMatricula = $conn->prepare($queryMatricula);
    $stmtMatricula->bindParam(':matricula_id', $ingresoData['matricula_id']);
    $stmtMatricula->execute();
    $valorMatricula = $stmtMatricula->fetchColumn();
    $contrato = "$" . number_format($valorMatricula, 0, ',', '.');

    // Calcular saldo restante
    $saldoRestante = $valorMatricula - $totalAbonos;
    $saldo = "$" . number_format($saldoRestante, 0, ',', '.');

    // Formatear el valor del abono actual
    $abono = "$" . number_format($ingresoData['valor'], 0, ',', '.');

    // Datos de la empresa *******************************************
    // Obtener el ID de la empresa desde la sesión
    $empresa_id = $_SESSION['empresa_id'];

    // Consulta para obtener los datos de la empresa
    $queryEmpresa = "
        SELECT nombre, direccion, ciudad, telefono, correo, logo 
        FROM empresas 
        WHERE id = :empresa_id
    ";

    $stmtEmpresa = $conn->prepare($queryEmpresa);
    $stmtEmpresa->bindParam(':empresa_id', $empresa_id, PDO::PARAM_INT);
    $stmtEmpresa->execute();
    $empresaData = $stmtEmpresa->fetch(PDO::FETCH_ASSOC);

    // Asignar los datos obtenidos a variables para su uso en el PDF
    $nombreEmpresa = $empresaData['nombre'];
    $direccion = $empresaData['direccion'] . ', ' . $empresaData['ciudad'];
    $contacto = $empresaData['telefono'];
    $correo = $empresaData['correo'];
    $logoPath = '../files/logos_empresas/' . $empresaData['logo'];
    // Fin de los datos de la empresa *********************************

    // Extraer los datos obtenidos
    $matricula = $ingresoData['matricula_id'];
    $valor = $ingresoData['valor'];

    $valorEnLetras = convertirNumeroALetras($ingresoData['valor']);
    $motivoIngreso = $ingresoData['motivo_ingreso'];
    $tipoIngreso = $ingresoData['tipo_ingreso'];
    $observaciones = $ingresoData['observaciones'];
    $numeroRecibo = $ingresoData['numero_recibo'];
    $estudianteNombreCompleto = $ingresoData['estudiante_nombres'] . ' ' . $ingresoData['estudiante_apellidos'];
    $estudianteDocumento = $ingresoData['estudiante_documento'];
    $programaNombre = $ingresoData['programa_nombre'];
    $categoriaLicencia = $ingresoData['categoria_licencia'];
    $convenioNombre = $ingresoData['convenio_nombre'] ?? '';

    // Crear nueva instancia del PDF
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->AddPage();

    $datos = array(
      'fecha' => '25/09/2024',
      'cc' => '111839521',
      'nombre_empresa' => $nombreEmpresa,
      'direccion' => $direccion,
      'contacto' => $contacto,
      'correo' => $correo,
      'matricula' => $matricula,
      'valor' => $valor,
      'valor_en_letras' => $valorEnLetras,
      'motivo_ingreso' => $motivoIngreso,
      'tipo_ingreso' => $tipoIngreso,
      'contrato' => $contrato,
      'abono' => $abono,
      'saldo' => $saldo,
      'estudiante_nombre_completo' => $estudianteNombreCompleto,
      'estudiante_documento' => $estudianteDocumento,
      'observaciones' => $observaciones,
      'logo_path' => $logoPath,
      'fecha' => $ingresoData['fecha'],
      'numero_recibo' => $numeroRecibo,
      'empresa_id' => $empresa_id,
      'ingreso_id' => $ingreso_id,
      'programa_nombre' => $programaNombre,
      'categoria_licencia' => $categoriaLicencia,
      'convenio_nombre' => $convenioNombre
    );

    $this->imprimirRecibo($pdf, 10, $datos);

    // Estilo de línea punteada (2mm línea, 2mm espacio)
     $style = array(
       'width' => 0.2,
       'cap' => 'butt',
       'join' => 'miter',
       'dash' => '2,2', // alterna 2 unidades línea, 2 unidades espacio
       'color' => array(0, 0, 0) // negro
     );

    // Posición horizontal: de 10 a 200 mm (ajusta al tamaño de tu recibo)
    // Posición vertical: por ejemplo, a la mitad de la hoja
    $y = 140; // posición vertical en mm (ajústalo según tu layout)

    // Dibujar la línea
    $pdf->Line(10, $y, 200, $y, $style);

    // Restaurar estilo de línea sólida antes del segundo recibo
    $pdf->SetLineStyle([
      'width' => 0.2,
      'cap' => 'butt',
      'join' => 'miter',
      'dash' => 0, // sin dash
      'color' => [0, 0, 0]
    ]);

    $this->imprimirRecibo($pdf, 150, $datos);

    // Guardar y mostrar el PDF
    $pdf->Output('recibo_pago.pdf', 'I');
  }

  public function imprimirRecibo($pdf, $yStart, $datos)
  {
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Academia de Conducción');
    $pdf->SetTitle('Recibo de Caja');
    $pdf->SetMargins(10, 10, 10); // Márgenes de 1 cm en cada lado

    // Ajustes iniciales de fuente
    $pdf->SetFont('helvetica', '', 10);
    $pdf->SetXY(10, $yStart); // 10 es el margen izquierdo

    // Calcular anchos para las secciones de la primera línea
    $pageWidth = $pdf->getPageWidth() - 20; // Ancho total sin márgenes
    $cellWidth = $pageWidth / 3;

    // Colocar el logo en la primera celda
    $pdf->Image($datos['logo_path'], 20, $pdf->GetY(), $cellWidth, 20, '', '', 'C', true, 300, '', false, false, 0, 'C', false);
    $pdf->Cell($cellWidth, 20, '', 1, 0, 'C'); // Celda vacía para el logo

    // Configurar colores para encabezados
    $pdf->SetFillColor(211, 211, 211); // Gris claro

    // Encabezados "Fecha" y "Recibo de Caja"
    $pdf->Cell($cellWidth, 10, 'FECHA', 1, 0, 'C', 1);
    $pdf->Cell($cellWidth, 10, 'RECIBO DE CAJA', 1, 1, 'C', 1);

    // Valores para "Fecha" y "Número de Recibo"
    $pdf->SetTextColor(0, 0, 0); // Restablecer color a negro
    $pdf->SetX($cellWidth + 10);
    $pdf->Cell($cellWidth, 10, $datos['fecha'], 1, 0, 'C', 0);
    $pdf->Cell($cellWidth, 10, $datos['numero_recibo'], 1, 1, 'C', 0);

    // matricula, programa y categoria
    $pdf->SetFont('helvetica', '', 9); // Tamaño de letra reducido para títulos
    $cellWidth = $pageWidth / 6;

    $pdf->Cell($cellWidth, 10, 'MATRÍCULA', 1, 0, 'C', 1);
    $pdf->Cell($cellWidth, 10, $datos['matricula'], 1, 0, 'C', 0);
    $pdf->Cell($cellWidth, 10, 'PROGRAMA', 1, 0, 'C', 1);
    $pdf->Cell($cellWidth, 10, $datos['programa_nombre'], 1, 0, 'C', 0);
    $pdf->Cell($cellWidth, 10, 'CATEGORIA', 1, 0, 'C', 1);
    $pdf->Cell($cellWidth, 10, $datos['categoria_licencia'], 1, 1, 'C', 0);


    // ----------------------------------------------------------
    // Línea intermedia - Convenio
    // ----------------------------------------------------------
    $pdf->SetFont('helvetica', '', 9);

    $pdf->Cell($pageWidth * 0.20, 10, 'CONVENIO', 1, 0, 'C', 1);
    $pdf->Cell(
      $pageWidth * 0.80,
      10,
      $datos['convenio_nombre'] ?? '',
      1,
      1,
      'L',
      0
    );

    // Tercera línea - Recibí de
    $titleWidth = $pageWidth * 0.15;  // Ancho para "RECIBI DE:"
    $nameWidth = $pageWidth * 0.55;   // Ancho para nombre
    $ccWidth = $pageWidth * 0.10;     // Ancho para "CC"
    $idWidth = $pageWidth * 0.20;     // Ancho para documento

    $pdf->Cell($titleWidth, 10, 'RECIBI DE:', 1, 0, 'C', 1);
    $pdf->Cell($nameWidth, 10, $datos['estudiante_nombre_completo'], 1, 0, 'C', 0);
    $pdf->Cell($ccWidth, 10, 'CC', 1, 0, 'C', 1);
    $pdf->Cell($idWidth, 10, $datos['estudiante_documento'], 1, 1, 'C', 0);

    // Cuarta línea - La suma de
    $valorRecibo = "$" . number_format((float)$datos['valor'], 0, ',', '.') . " " . strtoupper($datos['valor_en_letras']);

    // Ancho de la página sin márgenes
    $totalWidth = $pdf->getPageWidth() - 20;

    // Anchos de cada celda
    $titleWidth = $pdf->GetStringWidth('LA SUMA DE: ') + 5.2; // Ancho suficiente para el título
    $formattedValueWidth = 40; // Ajusta el ancho de esta celda para la cifra en formato
    $textValueWidth = $totalWidth - $titleWidth - $formattedValueWidth; // El resto del espacio para el valor en texto

    // Texto en formato y en letras
    $formattedValue = "$" . number_format((float)$datos['valor'], 0, ',', '.'); // Cifra en formato
    $textValue = $datos['valor_en_letras']; // Cifra en letras

    // ** Generar las celdas ** //

    // Primera celda: Título "LA SUMA DE:"
    $pdf->Cell($titleWidth, 10, 'LA SUMA DE:', 1, 0, 'C', true);

    // Segunda celda: Valor en formato (por ejemplo, "$158.000")
    $pdf->Cell($formattedValueWidth, 10, $formattedValue, 1, 0, 'C', false);

    // Tercera celda: Valor en letras (por ejemplo, "CIENTO CINCUENTA Y OCHO MIL PESOS")
    $pdf->Cell($textValueWidth, 10, $textValue, 1, 1, 'C', false); // 'C' para centrar

    // Quinta línea - Concepto y Forma de Pago
    $conceptoTitleWidth = $pdf->GetStringWidth('POR CONCEPTO DE: ') + 5;
    $formaPagoTitleWidth = $pdf->GetStringWidth('FORMA DE PAGO: ') + 5;
    $valueWidth = ($pageWidth - $conceptoTitleWidth - $formaPagoTitleWidth) / 2;

    $pdf->Cell($conceptoTitleWidth, 10, 'POR CONCEPTO DE:', 1, 0, 'C', true);
    $pdf->Cell($valueWidth, 10, $datos['motivo_ingreso'], 1, 0, 'C', false);
    $pdf->Cell($formaPagoTitleWidth, 10, 'FORMA DE PAGO:', 1, 0, 'C', true);
    $pdf->Cell($valueWidth, 10, $datos['tipo_ingreso'], 1, 1, 'C', false);

    // Sexta línea - Valores de contrato, recibo y saldo
    $pdf->SetFont('helvetica', '', 9); // Tamaño de letra reducido para títulos
    $cellWidth = $pageWidth / 6;

    $pdf->Cell($cellWidth, 10, 'V/CONTRATO', 1, 0, 'C', 1);
    $pdf->Cell($cellWidth, 10, $datos['contrato'], 1, 0, 'C', 0);
    $pdf->Cell($cellWidth, 10, 'V/RECIBO', 1, 0, 'C', 1);
    $pdf->Cell($cellWidth, 10, $datos['abono'], 1, 0, 'C', 0);
    $pdf->Cell($cellWidth, 10, 'SALDO', 1, 0, 'C', 1);
    $pdf->Cell($cellWidth, 10, $datos['saldo'], 1, 1, 'C', 0);

    $pdf->SetFont('helvetica', '', 10); // Restaurar tamaño de fuente

    // Última línea - Dos celdas (Recibí y Dirección)
    $halfWidth = $pageWidth / 2;

    $pdf->SetFillColor(240, 240, 240); // Fondo gris claro para "Recibí"
    $pdf->MultiCell($halfWidth, 40, "RECIBI", 1, 'C', true, 0, '', '', true, 0, false, true, 40, 'B', true);

    $pdf->SetFont('helvetica', '', 9);
    $pdf->MultiCell($halfWidth, 40, $datos['nombre_empresa'] . "\n" . $datos['direccion'] . "\n" . $datos['contacto'] . "\n" . $datos['correo'], 1, 'C', false, 1, '', '', true, 0, false, true, 40, 'M', true);
  }
}
