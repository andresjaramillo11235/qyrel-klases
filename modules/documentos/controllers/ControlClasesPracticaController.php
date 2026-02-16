<?php

require_once '../config/DatabaseConfig.php';
require_once '../shared/utils/AjustarImagen.php';

class ControlClasesPracticaController
{
    private $conn;

    public function __construct()
    {
        $dbConfig = new DatabaseConfig();
        $this->conn = $dbConfig->getConnection();
    }

    public function generatePDFControlClasesPractica($matriculaId)
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

        $clases = $this->getClasesPracticas($codigoMatricula);

        // Configuración del documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false); // Desactiva el encabezado
        $pdf->setPrintFooter(false); // Desactiva el pie de página
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('CeaCloud');

        function limpiarTextoPdf($texto)
        {
            return preg_replace('/[^A-Za-z0-9\- ]/', '', $texto);
        }

        $tituloPdf = sprintf(
            'Control-Clases-Practicas-%s-%s-%s',
            limpiarTextoPdf($programaNombre),
            limpiarTextoPdf($estudianteNombres),
            limpiarTextoPdf($estudianteApellidos)
        );

        $pdf->SetTitle($tituloPdf);

        $pdf->SetMargins(8, 8, 8);
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
        $pdf->Cell($cellWidth, 10, 'Control de Clases Prácticas', 0, 2, 'C', false);

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

        ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
        ## DATOS Y FOTO   ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##

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

        //---------------------------------------------------------------------
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

        // ==========================================================
        // ENCABEZADO TABLA CLASES PRÁCTICAS
        // ==========================================================

        $pdf->SetX($pdf->GetMargins()['left']);

        // Ancho útil
        $pageWidth = $pdf->getPageWidth()
            - $pdf->getMargins()['left']
            - $pdf->getMargins()['right'];

        // Columnas
        $col0Width = $pageWidth * 0.03; // #
        $col1Width = $pageWidth * 0.30; // TEMAS
        $col2Width = $pageWidth * 0.08; // FECHA
        $col3Width = $pageWidth * 0.08; // HORA
        $col4Width = $pageWidth * 0.08; // N CLASES
        $col5Width = $pageWidth * 0.08; // PLACA
        $col6Width = $pageWidth * 0.17; // ALUMNO
        $col7Width = $pageWidth * 0.18; // INSTRUCTOR

        $rowHeight = 14;

        // Encabezado
        $pdf->SetFillColor(192, 192, 192);
        $pdf->SetFont('helvetica', 'B', 6);

        $pdf->Cell($col0Width, $rowHeight, '#', 1, 0, 'C', true);
        $pdf->Cell($col1Width, $rowHeight, 'TEMAS VISTOS', 1, 0, 'C', true);
        $pdf->Cell($col2Width, $rowHeight, 'FECHA', 1, 0, 'C', true);
        $pdf->Cell($col3Width, $rowHeight, 'HORA INICIO', 1, 0, 'C', true);
        $pdf->Cell($col4Width, $rowHeight, 'N CLASES', 1, 0, 'C', true);
        $pdf->Cell($col5Width, $rowHeight, 'PLACA', 1, 0, 'C', true);
        $pdf->Cell($col6Width, $rowHeight, 'ALUMNO', 1, 0, 'C', true);
        $pdf->Cell($col7Width, $rowHeight, 'INSTRUCTOR', 1, 1, 'C', true);

        // ----------------------------------------------------------
        // FILAS
        // ----------------------------------------------------------

        $pdf->SetFont('helvetica', '', 7);

        $nombreAlumno   = trim($estudianteNombres);
        $apellidoAlumno = trim($estudianteApellidos);

        $i = 1;

        foreach ($clases as $clase) {

            $pdf->Cell($col0Width, $rowHeight, $i, 1, 0, 'C');

            // ------------------------------------------------------
            // NOMBRE DE LA CLASE (2 líneas controladas)
            // ------------------------------------------------------
            $xClase = $pdf->GetX();
            $yClase = $pdf->GetY();

            $textoClase = $this->dividirTextoPorPalabras($clase['clase_nombre'], 3);

            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetXY($xClase, $yClase + 3); // padding vertical fino

            $pdf->MultiCell(
                $col1Width,
                ($rowHeight - 3) / 2,
                $textoClase,
                0,
                'L',
                false
            );

            // Dibujar borde completo
            $pdf->SetXY($xClase, $yClase);
            $pdf->Cell($col1Width, $rowHeight, '', 1, 0);

            // Volver al flujo normal
            $pdf->SetXY($xClase + $col1Width, $yClase);

            if (!empty($clase['fecha'])) {

                $pdf->Cell($col2Width, $rowHeight, $clase['fecha'], 1, 0, 'C');
                $pdf->Cell($col3Width, $rowHeight, substr($clase['hora_inicio'], 0, 5), 1, 0, 'C');
                $pdf->Cell($col4Width, $rowHeight, $clase['numero_horas'], 1, 0, 'C');
                $pdf->Cell($col5Width, $rowHeight, $clase['placa'] ?? '', 1, 0, 'C');

                // ======================================================
                // ALUMNO (nombre arriba / apellido abajo)
                // ======================================================
                $xAlumno = $pdf->GetX();
                $yAlumno = $pdf->GetY();
                $paddingVertical = 3;

                $pdf->SetFont('times', 'I', 7);
                $pdf->SetXY($xAlumno, $yAlumno + $paddingVertical);

                $pdf->MultiCell(
                    $col6Width,
                    ($rowHeight - $paddingVertical) / 2,
                    $nombreAlumno . "\n" . $apellidoAlumno,
                    0,
                    'C'
                );

                // Borde alumno
                $pdf->SetXY($xAlumno, $yAlumno);
                $pdf->Cell($col6Width, $rowHeight, '', 1, 0);

                // Volver al flujo
                $pdf->SetXY($xAlumno + $col6Width, $yAlumno);

                // ======================================================
                // INSTRUCTOR (nombre arriba / apellido abajo)
                // ======================================================
                $nombreInstructor   = '';
                $apellidoInstructor = '';

                if (!empty($clase['instructor_nombre'])) {
                    $partesInstructor = explode(' ', trim($clase['instructor_nombre']), 2);
                    $nombreInstructor   = $partesInstructor[0] ?? '';
                    $apellidoInstructor = $partesInstructor[1] ?? '';
                }

                $xInst = $pdf->GetX();
                $yInst = $pdf->GetY();

                $pdf->SetFont('times', 'I', 7);
                $pdf->SetXY($xInst, $yInst + $paddingVertical);

                $pdf->MultiCell(
                    $col7Width,
                    ($rowHeight - $paddingVertical) / 2,
                    $nombreInstructor . "\n" . $apellidoInstructor,
                    0,
                    'C'
                );

                // Borde instructor
                $pdf->SetXY($xInst, $yInst);
                $pdf->Cell($col7Width, $rowHeight, '', 1, 1);

                $pdf->SetFont('helvetica', '', 7);
            } else {

                // Clase NO realizada
                $pdf->Cell($col2Width, $rowHeight, '', 1, 0);
                $pdf->Cell($col3Width, $rowHeight, '', 1, 0);
                $pdf->Cell($col4Width, $rowHeight, '', 1, 0);
                $pdf->Cell($col5Width, $rowHeight, '', 1, 0);
                $pdf->Cell($col6Width, $rowHeight, '', 1, 0);
                $pdf->Cell($col7Width, $rowHeight, '', 1, 1);
            }

            $i++;
        }

        // Salida del PDF
        $pdf->Output('ControlClasesPracticas.pdf', 'I');
    }

    function dividirTextoPorPalabras(string $texto, int $maxPalabras = 3): string
    {
        $palabras = preg_split('/\s+/', trim($texto));

        if (count($palabras) <= $maxPalabras) {
            return $texto;
        }

        return implode(' ', array_slice($palabras, 0, $maxPalabras))
            . "\n" .
            implode(' ', array_slice($palabras, $maxPalabras));
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

    public function getClasesPracticas(int $matriculaId): array
    {
        $sql = "
            SELECT
                programa_clase.id            AS clase_programa_id,
                programa_clase.nombre_clase  AS clase_nombre,
                programa_clase.orden         AS orden,
                programa_clase.numero_horas  AS numero_horas,

                clase_real.fecha             AS fecha,
                clase_real.hora_inicio       AS hora_inicio,
                clase_real.hora_fin          AS hora_fin,

                vehiculo.placa               AS placa,

                CONCAT(instructor.nombres, ' ', instructor.apellidos)
                                            AS instructor_nombre

            FROM matriculas matricula

            INNER JOIN matricula_programas matricula_programa
                ON matricula_programa.matricula_id = matricula.id

            INNER JOIN clases_programas programa_clase
                ON programa_clase.programa_id = matricula_programa.programa_id

            LEFT JOIN clases_practicas clase_real
                ON clase_real.clase_programa_id = programa_clase.id
                AND clase_real.matricula_id = matricula.id

            LEFT JOIN instructores instructor
                ON instructor.id = clase_real.instructor_id

            LEFT JOIN vehiculos vehiculo
                ON vehiculo.id = clase_real.vehiculo_id

            WHERE matricula.id = :matricula_id

            ORDER BY programa_clase.orden ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':matricula_id' => $matriculaId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
}
