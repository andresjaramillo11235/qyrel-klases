<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/AjustarImagen.php';

class ControlClasesTeoriaController
{
    private $conn;
    private $routes;

    public function __construct()
    {
        $dbConfig = new DatabaseConfig();
        $this->conn = $dbConfig->getConnection();
        $this->routes = include '../config/Routes.php';
    }

    public function index()
    {
        ob_start();
        include '../modules/documentos/views/contratos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function generarPdfControlClasesTeoria($matriculaId)
    {
        require_once '../vendor/autoload.php';

        $empresaId = $_SESSION['empresa_id'];

        // Conectar a la base de datos
        $dbConfig = new DatabaseConfig();
        $this->conn = $dbConfig->getConnection();

        $matriculaData = $this->getMatriculaById($matriculaId);
        $fechaInscripcion = $matriculaData['fecha_inscripcion'];
        $codigoMatricula = $matriculaData['id'];
        $observacionesMatricula = $matriculaData['observaciones'];

        $programa = $this->getProgramasByMatriculaId($matriculaId);
        $programaNombre = $programa[0]['nombre'];

        // Datos del estudiante
        $estudianteNombres = $matriculaData['estudiante_nombres'];
        $estudianteApellidos = $matriculaData['estudiante_apellidos'];
        $estudianteDocumento = $matriculaData['estudiante_documento'];
        $estudianteFoto = $matriculaData['estudiante_foto'];
        $estudianteGrupoSanguineo = $matriculaData['estudiante_grupo_sanguineo'];
        $estudianteTipoDocumento = $matriculaData['estudiante_tipo_documento'];

        $temas = $this->getTemasDinamicos($codigoMatricula);

        // COMIENZO PDF ---------------------------------------------------------------------------

        // Configuración del documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false); // Desactiva el encabezado
        $pdf->setPrintFooter(false); // Desactiva el pie de página
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CeaCloud');
        $pdf->SetTitle('Control Clases Teoria');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        // INICIO LOGOS

        // Ruta de los logos
        $logoEmpresaPath = '../assets/uploads/logos_empresas/image.png'; // Ruta del logo de la empresa
        $logoVigiladoPath = '../assets/uploads/logos_empresas/logo-super-transporte.png'; // Ruta del logo de "Vigilado"

        // Ancho total de la página menos márgenes
        $pageWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];

        // Ancho de cada celda (33% de la página para tres celdas)
        $cellWidth = $pageWidth / 3;

        // Posicionar el cursor al inicio de la línea
        $pdf->SetY(15);
        $pdf->SetX($pdf->getMargins()['left']);

        // Celda de la izquierda: Logo de la empresa
        $pdf->Cell(
            $cellWidth,
            22,
            $pdf->Image($logoEmpresaPath, $pdf->GetX(), $pdf->GetY(), 40),
            0,
            0,
            'L',
            false
        );

        // Guardar la posición actual para posicionar el texto central
        $centerX = $pdf->GetX();
        $centerY = $pdf->GetY();

        // Celda del centro: Espacio para el título y código de matrícula
        $pdf->Cell($cellWidth + 20, 22, '', 0, 0, 'C', false);

        // Celda de la derecha: Logo de "Vigilado"
        $logoAncho = 40; // Ancho deseado en mm
        $logoAlto = 20;  // Alto deseado en mm
        $pdf->Cell(
            $cellWidth + 20,
            22,
            $pdf->Image($logoVigiladoPath, $pdf->GetX(), $pdf->GetY(), $logoAncho, $logoAlto),
            0,
            0,
            'R',
            false
        );

        // Volver a la posición del centro para agregar el texto
        $pdf->SetXY($centerX, $centerY);

        // Texto para "MATRICULA" y su valor
        $matriculaTexto = 'MATRICULA:';
        $matriculaCompleta = $matriculaTexto . ' ' . $codigoMatricula;

        // Establecer fuente y tamaño para el título
        $pdf->SetFont('helvetica', 'B', 14);

        // Escribir el título centrado
        $pdf->Cell($cellWidth, 10, 'CONTROL CLASES TEORIA', 0, 2, 'C', false);

        // Reducir el tamaño de fuente para el código de matrícula
        $pdf->SetFont('helvetica', '', 10);

        // Escribir el código de matrícula centrado
        $pdf->Cell($cellWidth, 10, $matriculaCompleta, 0, 0, 'C', false);

        // Salto de línea después de completar la línea de logos y título
        $pdf->Ln(15);

        // Línea horizontal para separar las secciones, excepto antes de la primera sección
        $pdf->SetDrawColor(0, 0, 0); // Color negro para la línea
        $pdf->SetLineWidth(0.1); // Grosor de la línea
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Dibuja la línea horizontal
        $pdf->Ln(2); // Espacio después de la línea

        ## DATOS Y FOTO ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##

        // Definir proporciones de ancho
        $leftSectionWidth = $pdf->getPageWidth() * 0.7; // 70% para las celdas de texto
        $rightSectionWidth = $pdf->getPageWidth() * 0.3; // 30% para la foto
        $cellHeight = 7; // Altura de las celdas

        // Asegúrate de calcular $cellWidthFull antes de usarlo
        $cellWidthFull = $pdf->getPageWidth() - $pdf->GetMargins()['left'] - $pdf->GetMargins()['right'];

        // Configurar el ancho de la celda para la foto
        $cellWidthRight = $cellWidthFull * 0.3; // 30% del ancho de la página
        $cellHeightPhoto = $cellHeight * 5; // Ajustar el alto según el número de filas

        // Ruta de la foto (esto debe ser dinámico)
        $fotoPath = '../files/fotos_estudiantes/' . $estudianteFoto;

        // Verificar si la foto existe antes de insertarla
        if (file_exists($fotoPath)) {

            $pdf->SetX($cellWidthFull * 0.7); // Posicionar al 70% del ancho total

            list($newWidth, $newHeight) = ajustarImagen($fotoPath, $cellWidthRight, $cellHeightPhoto);

            $pdf->Cell(
                $cellWidthRight,
                $cellHeightPhoto,
                $pdf->Image($fotoPath, $pdf->GetX() + $cellWidthRight - $newWidth - 5, $pdf->GetY(), $newWidth, $newHeight),
                0, // Borde de la celda
                0, // Sin salto de línea
                'R', // Alinea el contenido de la celda a la derecha
                false // No es fondo
            );
        } else {
            // Si no hay foto, mostrar una celda vacía con mensaje
            $pdf->SetX($cellWidthFull * 0.7);


            $pdf->SetFont('helvetica', 'I', 9);



            $pdf->Cell(
                $cellWidthRight,
                $cellHeightPhoto,
                'Sin Foto',
                0,
                0,
                'C',
                false
            );
        }

        //--------------------------------------------------------

        // Celdas del lado izquierdo
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->SetX(10); // Alinear al margen izquierdo

        // Primera celda: Fecha
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, 'Fecha:', 0, 0, 'L'); // Texto "Fecha"
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, $fechaInscripcion, 0, 0, 'L'); // Valor de la fecha

        // Segunda celda: Categoría
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, 'Categoria:', 0, 0, 'L'); // Texto "Categoría"
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, $programaNombre, 0, 0, 'L'); // Valor de la categoría

        // Salto de línea después de esta fila
        $pdf->Ln($cellHeight);

        //--------------------------------------------------------

        // Celdas de Nombres y Apellidos
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->SetX(10); // Alinear al margen izquierdo

        // Definir proporciones
        $cellWidthLeft = ($pdf->getPageWidth() - $rightSectionWidth - 20) / 2; // Dividir el espacio restante (dejando 30% para la foto) en 2 celdas

        // Primera celda: Nombres y Apellidos (Título)
        $pdf->Cell($cellWidthLeft, $cellHeight, 'Nombres y Apellidos:', 0, 0, 'L');

        // Segunda celda: Nombres y Apellidos (Valor dinámico)
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell($cellWidthLeft, $cellHeight, $estudianteNombres . ' ' . $estudianteApellidos, 0, 0, 'L');

        // Salto de línea después de esta fila
        $pdf->Ln($cellHeight);

        //--------------------------------------------------------

        // Ajuste de celdas para Documento de Identidad y RH
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->SetX(10); // Posicionar al margen izquierdo

        // Primera celda: Documento de Identidad (Título)
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, 'Documento:', 0, 0, 'L'); // Texto "Fecha"

        // Segunda celda: Documento de Identidad (Valor dinámico)
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell(             // Crea una nueva celda
            $cellWidthLeft / 2, // Define el ancho de la celda 
            $cellHeight,        // Define el alto de la celda
            $estudianteTipoDocumento . ' ' . $estudianteDocumento, // Define el contenido de la celda
            0,                  // Define el borde de la celda. 0 significa que la celda no tendrá bordes.
            0,                  // Define si hay un salto de línea después de la celda.
            'L'                 // Define la alineación del contenido dentro de la celda.
        );

        // Tercera celda: RH (Título)
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->Cell($cellWidthLeft / 6, $cellHeight, 'RH:', 0, 0, 'L');

        // Cuarta celda: RH (Valor dinámico)
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell($cellWidthLeft / 6, $cellHeight, $estudianteGrupoSanguineo, 0, 0, 'L');

        // Salto de línea para la próxima fila
        $pdf->Ln($cellHeight);

        //--------------------------------------------------------

        // Ajuste de celdas para Dirección y Barrio
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->SetX(10); // Posicionar al margen izquierdo

        // Primera celda: Dirección (Título)
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, 'Dirección:', 0, 0, 'L');

        // Segunda celda: Dirección (Valor dinámico)
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, 'Calle 56 Sur #28-43', 0, 0, 'L');

        // Tercera celda: Barrio (Título)
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->Cell($leftSectionWidth / 10, $cellHeight, 'Barrio:', 0, 0, 'L');

        // Cuarta celda: Barrio (Valor dinámico)
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell($leftSectionWidth / 6, $cellHeight, 'San Vicente', 0, 0, 'L');

        //--------------------------------------------------------

        // Salto de línea para la próxima fila
        $pdf->Ln($cellHeight);

        //--------------------------------------------------------

        // Ajuste de celdas para Observaciones
        $pdf->SetFont('helvetica', 'B', 10); // Texto en negrita
        $pdf->SetX(10); // Posicionar al margen izquierdo

        // Primera celda: Observaciones (Título)
        $pdf->Cell($leftSectionWidth / 4, $cellHeight, 'Observaciones:', 0, 0, 'L');

        // Segunda celda: Observaciones (Valor dinámico)
        $pdf->SetFont('helvetica', '', 10); // Texto normal
        $pdf->Cell($cellWidthLeft - ($cellWidthLeft / 3.5), $cellHeight, $observacionesMatricula, 0, 0, 'L');

        // Salto de línea para la próxima fila
        $pdf->Ln($cellHeight + 4);

        $temas = $this->getTemasDinamicos($codigoMatricula);

        $nombreAlumno = trim($estudianteNombres . ' ' . $estudianteApellidos);
        $this->renderTablaTemasTeoricos($pdf, $temas, $nombreAlumno);

        // Paso 4: Generación del PDF
        $pdf->Output('control-clases-teoria-' . $codigoMatricula . '.pdf', 'I');
    }

    private function renderTablaTemasTeoricos(
        TCPDF $pdf,
        array $temas,
        string $nombreAlumno
    ): void {
        // ==========================================================
        // TABLA CONTROL CLASES TEÓRICAS
        // ==========================================================

        // Ancho útil
        $pageWidth = $pdf->getPageWidth()
            - $pdf->getMargins()['left']
            - $pdf->getMargins()['right'];

        // Columnas
        $wNum   = $pageWidth * 0.04;
        $wTema  = $pageWidth * 0.40;
        $wFecha = $pageWidth * 0.10;
        $wIni   = $pageWidth * 0.08;
        $wFin   = $pageWidth * 0.08;
        $wAlum  = $pageWidth * 0.15;
        $wInst  = $pageWidth * 0.15;

        $rowH = 7;

        // ----------------------------------------------------------
        // ENCABEZADO
        // ----------------------------------------------------------
        $pdf->SetFillColor(200, 200, 200);
        $pdf->SetFont('helvetica', 'B', 8);

        $pdf->Cell($wNum,   $rowH, '#',     1, 0, 'C', true);
        $pdf->Cell($wTema,  $rowH, 'TEMA',  1, 0, 'L', true);
        $pdf->Cell($wFecha, $rowH, 'FECHA', 1, 0, 'C', true);
        $pdf->Cell($wIni,   $rowH, 'INI',   1, 0, 'C', true);
        $pdf->Cell($wFin,   $rowH, 'FIN',   1, 0, 'C', true);
        $pdf->Cell($wAlum,  $rowH, 'ALUMNO', 1, 0, 'C', true);
        $pdf->Cell($wInst,  $rowH, 'INST',  1, 1, 'C', true);

        // ----------------------------------------------------------
        // FILAS
        // ----------------------------------------------------------
        $contador = 1;

        // Ajustar altura de fila (doble línea)
        $rowH = 14;

        // 🔹 CORRECCIÓN CLAVE: separar nombre y apellido DESDE EL PARÁMETRO
        $partesAlumno = explode(' ', trim($nombreAlumno), 2);
        $nombreAlumnoCompleto   = $partesAlumno[0] ?? '';
        $apellidoAlumnoCompleto = $partesAlumno[1] ?? '';

        foreach ($temas as $tema) {

            // Columnas fijas
            $pdf->SetFont('helvetica', '', 8);
            $pdf->Cell($wNum,  $rowH, $contador, 1, 0, 'C');

            // ------------------------------------------------------
            // TEMA (dividido en 2 líneas)
            // ------------------------------------------------------
            $xTema = $pdf->GetX();
            $yTema = $pdf->GetY();

            $textoTema = $this->dividirTextoPorPalabras($tema['tema_nombre'], 3);

            $pdf->SetFont('helvetica', '', 8);
            $pdf->SetXY($xTema, $yTema + 3); // padding vertical fino

            $pdf->MultiCell(
                $wTema,
                ($rowH - 3) / 2,
                $textoTema,
                0,
                'L',
                false
            );

            // Dibujar borde completo de la celda
            $pdf->SetXY($xTema, $yTema);
            $pdf->Cell($wTema, $rowH, '', 1, 0);

            // Volver al flujo normal
            $pdf->SetXY($xTema + $wTema, $yTema);

            if (!empty($tema['fecha'])) {

                $pdf->Cell($wFecha, $rowH, $tema['fecha'], 1, 0, 'C');
                $pdf->Cell($wIni,   $rowH, substr($tema['hora_inicio'], 0, 5), 1, 0, 'C');
                $pdf->Cell($wFin,   $rowH, substr($tema['hora_fin'], 0, 5),    1, 0, 'C');

                // ------------------------------------------------------
                // ALUMNO (nombre arriba, apellido abajo)
                // ------------------------------------------------------
                $xAlumno = $pdf->GetX();
                $yAlumno = $pdf->GetY();

                $paddingVertical = 3;

                $pdf->SetFont('times', 'I', 7);
                $pdf->SetXY($xAlumno, $yAlumno + $paddingVertical);

                $pdf->MultiCell(
                    $wAlum,
                    ($rowH - $paddingVertical) / 2,
                    $nombreAlumnoCompleto . "\n" . $apellidoAlumnoCompleto,
                    0,
                    'C',
                    false
                );

                // Dibujar borde completo de la celda
                $pdf->SetXY($xAlumno, $yAlumno);
                $pdf->Cell($wAlum, $rowH, '', 1, 0);

                // Volver al flujo normal
                $pdf->SetXY($xAlumno + $wAlum, $yAlumno);

                // ------------------------------------------------------
                // INSTRUCTOR (nombre arriba, apellido abajo)
                // ------------------------------------------------------
                $nombreInstructor = '';
                $apellidoInstructor = '';

                if (!empty($tema['instructor_nombre'])) {
                    $partesInstructor = explode(' ', trim($tema['instructor_nombre']), 2);
                    $nombreInstructor = $partesInstructor[0] ?? '';
                    $apellidoInstructor = $partesInstructor[1] ?? '';
                }

                $xInst = $pdf->GetX();
                $yInst = $pdf->GetY();

                $pdf->SetFont('times', 'I', 7);
                $pdf->SetXY($xInst, $yInst + $paddingVertical);

                $pdf->MultiCell(
                    $wInst,
                    ($rowH - $paddingVertical) / 2,
                    $nombreInstructor . "\n" . $apellidoInstructor,
                    0,
                    'C',
                    false
                );

                // Dibujar borde completo
                $pdf->SetXY($xInst, $yInst);
                $pdf->Cell($wInst, $rowH, '', 1, 1);
            } else {

                // Clase NO realizada
                $pdf->Cell($wFecha, $rowH, '', 1, 0);
                $pdf->Cell($wIni,   $rowH, '', 1, 0);
                $pdf->Cell($wFin,   $rowH, '', 1, 0);
                $pdf->Cell($wAlum,  $rowH, '', 1, 0);
                $pdf->Cell($wInst,  $rowH, '', 1, 1);
            }

            $contador++;
        }
    }

    function dividirTextoPorPalabras(string $texto, int $maxPalabras = 3): string
    {
        $palabras = preg_split('/\s+/', trim($texto));

        if (count($palabras) <= $maxPalabras) {
            return $texto;
        }

        $linea1 = array_slice($palabras, 0, $maxPalabras);
        $linea2 = array_slice($palabras, $maxPalabras);

        return implode(' ', $linea1) . "\n" . implode(' ', $linea2);
    }

    public function getTemasDinamicos($matriculaId)
    {
        // ==========================================================
        // 1. TODOS los temas del programa (catálogo)
        // ==========================================================
        $sqlTemas = "
        SELECT
            t.id     AS tema_id,
            t.nombre AS tema_nombre
        FROM clases_teoricas_temas t
        INNER JOIN matricula_programas mp
            ON mp.programa_id = t.clase_teorica_programa_id
        WHERE mp.matricula_id = :matricula_id
        ORDER BY t.id ASC
    ";

        $stmt = $this->conn->prepare($sqlTemas);
        $stmt->execute([
            ':matricula_id' => $matriculaId
        ]);

        $temas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ==========================================================
        // 2. Clases reales que tomó el alumno (por matrícula)
        // ==========================================================
        $sqlClases = "
        SELECT
            ct.tema_id,
            ct.fecha,
            ct.hora_inicio,
            ct.hora_fin,
            CONCAT(i.nombres, ' ', i.apellidos) AS instructor_nombre,
            cte.asistencia
        FROM clases_teoricas_estudiantes cte
        INNER JOIN clases_teoricas ct
            ON ct.id = cte.clase_teorica_id
        LEFT JOIN instructores i
            ON i.id = ct.instructor_id
        WHERE cte.matricula_id = :matricula_id
    ";

        $stmt = $this->conn->prepare($sqlClases);
        $stmt->execute([
            ':matricula_id' => $matriculaId
        ]);

        $clases = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ==========================================================
        // 3. Indexar clases por tema_id (clave)
        // ==========================================================
        $clasesPorTema = [];

        foreach ($clases as $clase) {
            // Si por alguna razón hay más de una, nos quedamos con la primera
            if (!isset($clasesPorTema[$clase['tema_id']])) {
                $clasesPorTema[$clase['tema_id']] = $clase;
            }
        }

        // ==========================================================
        // 4. Merge final: 1 fila por tema (limpio)
        // ==========================================================
        foreach ($temas as &$tema) {

            $temaId = $tema['tema_id'];

            if (isset($clasesPorTema[$temaId])) {

                $tema['fecha']             = $clasesPorTema[$temaId]['fecha'];
                $tema['hora_inicio']       = $clasesPorTema[$temaId]['hora_inicio'];
                $tema['hora_fin']          = $clasesPorTema[$temaId]['hora_fin'];
                $tema['instructor_nombre'] = $clasesPorTema[$temaId]['instructor_nombre'];
                $tema['asistencia']        = $clasesPorTema[$temaId]['asistencia'];
            } else {

                // Tema NO visto aún
                $tema['fecha']             = null;
                $tema['hora_inicio']       = null;
                $tema['hora_fin']          = null;
                $tema['instructor_nombre'] = null;
                $tema['asistencia']        = null;
            }
        }

        return $temas;
    }















    public function getMatriculaById($matriculaId)
    {
        $query = "
            SELECT m.*, 
                e.nombres AS estudiante_nombres,
                e.apellidos AS estudiante_apellidos,
                e.numero_documento AS estudiante_documento,
                e.foto AS estudiante_foto,
                gs.nombre AS estudiante_grupo_sanguineo,
                td.sigla AS estudiante_tipo_documento
            FROM matriculas m
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
            LEFT JOIN param_grupo_sanguineo gs ON e.grupo_sanguineo = gs.id
            LEFT JOIN param_tipo_documento td ON e.tipo_documento = td.id
            WHERE m.id = :matricula_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProgramasByMatriculaId($matriculaId)
    {
        // Consulta para obtener los datos de los programas asociados a la matrícula
        $query = "
            SELECT p.nombre, 
                p.tipo_servicio, 
                p.horas_practicas, 
                p.horas_teoricas
            FROM matricula_programas mp
            INNER JOIN programas p ON mp.programa_id = p.id
            WHERE mp.matricula_id = :matricula_id
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':matricula_id', $matriculaId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
