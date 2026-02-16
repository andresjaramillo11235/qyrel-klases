<?php

require_once '../config/DatabaseConfig.php';

class ContratosController
{
    private $conn;
    private $routes;

    public function __construct()
    {
        $dbConfig = new DatabaseConfig();
        $this->conn = $dbConfig->getConnection();
        $this->routes = include '../config/Routes.php';
    }

    // Método para mostrar todos los contratos
    public function index()
    {
        // Obtener todas las secciones del contrato, incluyendo el encabezado
        $query = "SELECT * FROM documentos_contratos WHERE empresa_id = :empresa_id ORDER BY orden ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $_SESSION['empresa_id']);
        $stmt->execute();
        $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/documentos/views/contratos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Método para mostrar el formulario de creación de contrato
    public function create()
    {
        ob_start();
        include '../modules/documentos/views/contratos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Método para almacenar un nuevo contrato
    public function store()
    {
        $routes = include '../config/Routes.php';

        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido'];
        $orden = $_POST['orden'];
        $empresa_id = $_SESSION['empresa_id'];

        $query = "INSERT INTO documentos_contratos (empresa_id, titulo_seccion, contenido, orden) 
                  VALUES (:empresa_id, :titulo, :contenido, :orden)";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresa_id);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':orden', $orden);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Sección del contrato creada correctamente.';
            header('Location: ' . $routes['documento_contrato_index']);
            exit;
        } else {
            $_SESSION['error_message'] = 'Error al crear el contrato';
            header('Location: ' . $routes['documento_contrato_index']);
            exit;
        }
    }

    // Método para mostrar el formulario de edición de contrato
    public function edit($id)
    {
        $query = "SELECT * FROM documentos_contratos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $contrato = $stmt->fetch(PDO::FETCH_ASSOC);

        ob_start();
        include '../modules/documentos/views/contratos/edit.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    // Método para actualizar un contrato
    public function update()
    {
        $routes = include '../config/Routes.php';

        $id = $_POST['id'];
        $titulo = $_POST['titulo'];
        $contenido = $_POST['contenido'];
        $orden = $_POST['orden'];

        $query = "UPDATE documentos_contratos SET titulo_seccion = :titulo, contenido = :contenido, orden = :orden WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':titulo', $titulo);
        $stmt->bindParam(':contenido', $contenido);
        $stmt->bindParam(':orden', $orden);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Sección del contrato actualizada correctamente.';
            header('Location: ' . $this->routes['documento_contrato_index']);
            exit;
        } else {
            $_SESSION['error_message'] = 'Error al actualizar el contrato';
            header('Location: ' . $this->routes['documento_contrato_index']);
            exit;
        }
    }

    // Método para eliminar un contrato
    public function delete($id)
    {
        $query = "DELETE FROM documentos_contratos WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Sección eliminada correctamente.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el contrato';
        }
        header('Location: ' . $this->routes['documento_contrato_index']);
        exit;
    }

    public function generatePDFContract($matriculaId)
    {
        require_once '../vendor/autoload.php';
        require_once '../shared/utils/CalcularDimensionesProporcionadasImagen.php';

        $empresaId = $_SESSION['empresa_id'];

        // Conectar a la base de datos
        $dbConfig = new DatabaseConfig();
        $this->conn = $dbConfig->getConnection();

        $matriculaData = $this->getMatriculaById($matriculaId);
        $fechaInscripcion = $matriculaData['fecha_inscripcion'];
        $codigoMatricula = $matriculaData['id'];
        $valorMatricula = "$" . number_format($matriculaData['valor_matricula'], 0, ',', '.');

        $programa = $this->getProgramasByMatriculaId($matriculaId);
        $programaNombre = $programa[0]['nombre'];
        $programaTipoServicio = $programa[0]['tipo_servicio'];
        $programaHorasPracticas = $programa[0]['horas_practicas'];
        $programaHorasTeoricas = $programa[0]['horas_teoricas'];

        // Datos del estudiante
        $estudianteNombres = $matriculaData['estudiante_nombres'];
        $estudianteApellidos = $matriculaData['estudiante_apellidos'];
        $estudianteDocumento = $matriculaData['estudiante_documento'];

        // Paso 2: Consulta para obtener las secciones del contrato según empresa_id
        $query = "
            SELECT titulo_seccion, contenido, orden
            FROM documentos_contratos
            WHERE empresa_id = :empresa_id
            ORDER BY orden ASC
        ";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':empresa_id', $empresaId, PDO::PARAM_INT);
        $stmt->execute();
        $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // COMIENZO PDF ---------------------------------------------------------------------------

        // Configuración del documento PDF
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false); // Desactiva el encabezado
        $pdf->setPrintFooter(false); // Desactiva el pie de página
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Tu Empresa');
        $pdf->SetTitle('Contrato');
        $pdf->SetMargins(10, 10, 10);
        $pdf->AddPage();

        ## INICIO LOGOS ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## 

        $pageWidth = $pdf->getPageWidth(); // Ancho total de la página

        // Restar los márgenes izquierdo y derecho
        $usableWidth = $pageWidth - $pdf->getMargins()['left'] - $pdf->getMargins()['right'];

        // Dividir el ancho disponible entre el número de celdas que quieres
        $numCeldas = 3; // Por ejemplo, tres celdas de igual ancho
        $cellWidth = $usableWidth / $numCeldas;

        // Rutas de los logos
        $logoEmpresaPath = '../assets/uploads/logos_empresas/image.png'; // Ruta del logo de la empresa
        $logoCentroPath = '../assets/uploads/logos_empresas/min-transporte-logo.png'; // Ruta del logo central
        $logoVigiladoPath = '../assets/uploads/logos_empresas/logo-supertransporte-edited.png'; // Ruta del logo de "Vigilado"

        $dimensionesLogoUno = calcularDimensionesProporcionadas($logoEmpresaPath, 40, 20);
        $dimensionesLogoDos = calcularDimensionesProporcionadas($logoCentroPath, 60, 20);
        $dimensionesLogoTres = calcularDimensionesProporcionadas($logoVigiladoPath, 40, 20);

        // Celda de la izquierda: Logo de la empresa
        $pdf->SetY(15); // Ajusta la posición vertical si es necesario
        $pdf->Cell(
            $cellWidth,
            max($dimensionesLogoUno['height'], $dimensionesLogoDos['height'], $dimensionesLogoTres['height']), // Altura máxima para alineación
            $pdf->Image($logoEmpresaPath, $pdf->GetX(), $pdf->GetY(), $dimensionesLogoUno['width'], $dimensionesLogoUno['height']),
            0,
            0,
            'L',
            false
        );

        // Celda del centro: Logo central
        $pdf->Cell(
            $cellWidth,
            max($dimensionesLogoUno['height'], $dimensionesLogoDos['height'], $dimensionesLogoTres['height']), // Altura máxima para alineación
            $pdf->Image($logoCentroPath, $pdf->GetX() + ($cellWidth / 2) - ($dimensionesLogoDos['width'] / 2), $pdf->GetY(), $dimensionesLogoDos['width'], $dimensionesLogoDos['height']),
            0,
            0,
            'C',
            false
        );

        // Celda de la derecha: Logo de "Vigilado"
        $pdf->Cell(
            $cellWidth,
            max($dimensionesLogoUno['height'], $dimensionesLogoDos['height'], $dimensionesLogoTres['height']), // Altura máxima para alineación
            $pdf->Image($logoVigiladoPath, $pdf->GetX() + ($cellWidth - $dimensionesLogoTres['width']), $pdf->GetY(), $dimensionesLogoTres['width'], $dimensionesLogoTres['height']),
            0,
            0,
            'R',
            false
        );

        $pdf->Ln(20); // Salto de línea

        ## DATOS DEL CONTRATO ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##

        // Definir ancho de cada celda, dividiendo el ancho total de la página entre 6
        // Configuración de la fuente y tamaños de celdas
        $pdf->SetFont('helvetica', '', 8); // Establecemos el estilo regular para los valores
        $cellWidth = ($pdf->GetPageWidth() - 20) / 6; // Ajustar el ancho de la celda

        // Títulos y valores en la misma línea
        // Fecha
        $pdf->SetFont('helvetica', 'B', 10); // Negrita solo para el título
        $pdf->Cell($cellWidth, 8, 'Fecha:', 0, 0, 'L'); // Celda sin borde
        $pdf->SetFont('helvetica', '', 10); // Regular para el valor
        $pdf->Cell($cellWidth, 8, $fechaInscripcion, 0, 0, 'L'); // Celda sin borde

        // Contrato
        $pdf->SetFont('helvetica', 'B', 10); // Negrita solo para el título
        $pdf->Cell($cellWidth, 8, 'Contrato:', 0, 0, 'L'); // Celda sin borde
        $pdf->SetFont('helvetica', '', 10); // Regular para el valor
        $pdf->Cell($cellWidth, 8, $codigoMatricula, 0, 0, 'L'); // Celda sin borde

        // Valor del Contrato
        $pdf->SetFont('helvetica', 'B', 10); // Negrita solo para el título
        $pdf->Cell($cellWidth, 8, 'Valor Contrato:', 0, 0, 'L'); // Celda sin borde
        $pdf->SetFont('helvetica', '', 10); // Regular para el valor
        $pdf->Cell($cellWidth, 8, $valorMatricula, 0, 0, 'L');

        $pdf->Ln(5);

        ## DATOS DEL ESTUDIANTE ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##

        // Configurar fuente y estilo para los títulos
        $pdf->SetFont('helvetica', 'B', 9);

        // Primera celda: "Nombre"
        $pdf->Cell(22, 10, 'Nombre:', 0, 0, 'L');

        // Configurar fuente y estilo para el valor
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(90, 10, $estudianteNombres . ' ' . $estudianteApellidos, 0, 0, 'L'); // Reducir el ancho si es necesario

        // Volver a configurar fuente y estilo para el título de "Identificación"
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell(30, 10, 'Identificación No.: ', 0, 0, 'L'); // Ampliar espacio antes del número

        // Configurar fuente y estilo para el valor de identificación
        $pdf->SetFont('helvetica', '', 9);
        $pdf->Cell(30, 10, $estudianteDocumento, 0, 0, 'C'); // '1' hace salto de línea

        $pdf->Ln(7);

        ## DATOS DEL PROGRAMA ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ## ##
        // Configurar el tamaño de la fuente
        $pdf->SetFont('helvetica', 'B', 10);

        // Definir el ancho de cada celda, dividiendo el ancho de la página para acomodar las 4 celdas
        $cellWidth = ($pdf->GetPageWidth() - 20) / 8; // Resta márgenes

        // Celda para el Programa
        $pdf->Cell($cellWidth - 5, 8, 'Programa:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10); // Cambiar a texto normal
        $pdf->Cell($cellWidth + 25, 8, strtoupper($programaNombre), 0, 0, 'L');

        // Celda para el Servicio
        $pdf->SetFont('helvetica', 'B', 10); // Volver a negrita
        $pdf->Cell($cellWidth - 5, 8, 'Servicio:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10); // Cambiar a texto normal
        $pdf->Cell($cellWidth, 8, $programaTipoServicio, 0, 0, 'L'); // Saltar línea después de esta celda

        // Segunda línea con Horas Prácticas y Horas Teóricas
        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell($cellWidth + 5, 8, 'Horas Prácticas:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell($cellWidth - 10, 8, $programaHorasPracticas, 0, 0, 'L');

        $pdf->SetFont('helvetica', 'B', 10);
        $pdf->Cell($cellWidth, 8, 'Horas Teóricas:', 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell($cellWidth - 5, 8, $programaHorasTeoricas, 0, 1, 'C'); // Saltar línea después de esta celda

        // Paso 3: Estructura de contenido en el PDF
        $pdf->SetFont('helvetica', '', 10);

        // Línea horizontal para separar las secciones, excepto antes de la primera sección
        $pdf->SetDrawColor(0, 0, 0); // Color negro para la línea
        $pdf->SetLineWidth(0.2); // Grosor de la línea
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Dibuja la línea horizontal
        $pdf->Ln(2); // Espacio después de la línea

        // Define el espacio entre secciones
        $espacioEntreSecciones = 1; // Ajusta este valor según el espacio que necesites

        foreach ($sections as $section) {
            // Encabezado especial si el orden es 0 y hay título
            if ($section['orden'] == 0 && !empty($section['titulo_seccion'])) {
                $pdf->SetFont('helvetica', '', 9);
                $pdf->MultiCell(0, 6, $section['contenido'], 0, 'J', false, 1); // Solo contenido
                $pdf->Ln(2); // Espacio controlado
            } else {
                // Título de la sección para órdenes diferentes de 0
                if (!empty($section['titulo_seccion'])) {
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->MultiCell(0, 5, $section['titulo_seccion'], 0, 'L', false, 1);
                    $pdf->Ln($espacioEntreSecciones); // Espacio controlado después del título
                }

                // Contenido de la sección
                if (!empty($section['contenido'])) {
                    $pdf->SetFont('helvetica', '', 9);
                    $pdf->MultiCell(0, 6, $section['contenido'], 0, 'J', false, 1); // Cambiado a 'J' para justificación
                    $pdf->Ln($espacioEntreSecciones);
                }
            }
        }

        $pdf->Ln(6);

        ## FIRMA Y HUELLA
        // Definir el ancho y alto de las celdas
        $cellHeight = 30; // Altura de las celdas
        $pageWidth = $pdf->getPageWidth() - $pdf->getMargins()['left'] - $pdf->getMargins()['right']; // Ancho utilizable
        $cellWidthLeft = $pageWidth * 0.7; // 60% del ancho para la celda izquierda
        $cellWidthRight = $pageWidth * 0.3; // 40% del ancho para la celda derecha

        // Calcular la posición inicial de ambas celdas
        $startX = $pdf->GetX();
        $startY = $pdf->GetY();

        // Celda izquierda: Texto y línea para la fecha
        $pdf->SetFont('helvetica', '', 8);
        $pdf->SetXY($startX, $startY); // Mover a la posición inicial
        $pdf->MultiCell(
            $cellWidthLeft,  // Ancho de la celda
            6,               // Altura mínima por línea
            "Este Contrato se firma el: __/__/____\n\n\n\n\n\n_____________________________\n" . $estudianteNombres . ' ' . $estudianteApellidos, // Texto con línea
            0,               // Borde
            'L',             // Alineado a la izquierda
            false            // Sin fondo
        );

        // Celda derecha: Espacio para la huella
        $pdf->SetXY($startX + $cellWidthLeft, $startY); // Mover a la posición derecha
        $pdf->Cell(
            $cellWidthRight, // Ancho de la celda
            $cellHeight,     // Altura de la celda
            "",        // Texto
            1,               // Borde
            0,               // Sin salto de línea
            'C',             // Centrado
            false            // Sin fondo
        );

        // Salto de línea después de estas celdas (opcional)
        $pdf->Ln(10);

        // Paso 4: Generación del PDF
        $pdf->Output('contrato.pdf', 'I');
    }

    public function getMatriculaById($matriculaId)
    {
        $query = "
            SELECT m.*, 
                e.nombres AS estudiante_nombres,
                e.apellidos AS estudiante_apellidos,
                e.numero_documento AS estudiante_documento
            FROM matriculas m
            LEFT JOIN estudiantes e ON m.estudiante_id = e.id
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
