<?php

require_once '../config/DatabaseConfig.php';
require_once '../modules/permissions/controllers/PermissionController.php';

class ClientesNuevosController
{
    private $conn;

    public function __construct()
    {
        $db = new DatabaseConfig();
        $this->conn = $db->getConnection();
    }

    public function index()
    {
        $empresa_id = (int)$_SESSION['empresa_id'];

        // Listado de pendientes (activos)
        $rows = $this->fetchAll("
                SELECT cn.*, e.numero_documento, e.nombres, e.apellidos
                FROM clientes_nuevos cn
                INNER JOIN estudiantes e ON e.id = cn.estudiante_id
                WHERE cn.empresa_id = :empresa_id AND cn.estado = 1
                ORDER BY cn.created_at DESC
            ", [':empresa_id' => $empresa_id]);

        ob_start();
        include '../modules/estudiantes/views/clientes_nuevos/index.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }

    public function create()
    {
        ob_start();
        include '../modules/estudiantes/views/clientes_nuevos/create.php';
        $content = ob_get_clean();
        include '../shared/views/layout.php';
    }



    public function store()
    {
        $routes     = include '../config/Routes.php';
        $empresa_id = (int) $_SESSION['empresa_id'];
        $user_id    = (int) $_SESSION['user_id'];

        // === Datos del formulario ===
        $tipo_documento    = 1; // ← SIEMPRE 1 como acordamos
        $numero_documento  = trim($_POST['cedula'] ?? '');     // viene del form
        $nombres           = trim($_POST['nombres'] ?? '');
        $apellidos         = trim($_POST['apellidos'] ?? '');
        $telefono          = trim($_POST['telefono'] ?? '');

        // (opcional) ¿ya existe estudiante con ese documento?
        $existeEst = $this->fetchOne("
                SELECT id FROM estudiantes
                WHERE numero_documento = :doc LIMIT 1
            ", [':doc' => $numero_documento]);
        if ($existeEst) {
            $_SESSION['flash_error'] = 'Ya existe un estudiante con ese documento.';
            header('Location: ' . $routes['clientes_nuevos_create']);
            return;
        }

        // ¿ya existe usuario con ese login?
        $existeUser = $this->fetchOne("SELECT id FROM users WHERE username = :u", [':u' => $numero_documento]);
        if ($existeUser) {
            $_SESSION['flash_error'] = 'Ya existe un usuario con ese documento.';
            header('Location: ' . $routes['clientes_nuevos_create']);
            return;
        }

        try {

            $this->conn->beginTransaction();

            $codigo = $this->nextUniqueStudentCode($this->conn);

            $stmt = $this->conn->prepare("
                    INSERT INTO estudiantes (
                        empresa_id, codigo, nombres, apellidos, tipo_documento, numero_documento, celular, estado
                    ) VALUES (
                        :empresa_id, :codigo, :nombres, :apellidos, :tdoc, :ndoc, :cel, '5'
                    )
                ");
            $stmt->execute([
                ':empresa_id' => $empresa_id,
                ':codigo'     => $codigo,
                ':nombres'    => $nombres,
                ':apellidos'  => $apellidos,
                ':tdoc'       => 1,
                ':ndoc'       => $numero_documento,
                ':cel'        => $telefono !== '' ? $telefono : null,
            ]);
            $estudiante_id = (int)$this->conn->lastInsertId();

            // === Crear usuario (rol estudiante) ===
            $username = $numero_documento;

            $hash    = password_hash($numero_documento, PASSWORD_BCRYPT);

            // Campos NOT NULL en tu tabla: email, phone, address, first_name, last_name, status, role_id, empresa_id
            // Si no pides email/dirección en el formulario, genera valores seguros por defecto.
            $email   = $_POST['email'] ?? ('est' . $numero_documento . '@noemail.local');
            $phone   = $telefono;                 // del formulario (requerido)
            $address = $_POST['address'] ?? '';   // si no lo pides aún, inserta cadena vacía (no NULL)

            // role_id del estudiante (según tu BD parece ser 5)
            $roleId  = 5;

            $stmt = $this->conn->prepare("
                INSERT INTO users (
                    username, email, password,
                    first_name, last_name, phone, address,
                    status, role_id, empresa_id, estudiante_id,
                    created_at, updated_at
                ) VALUES (
                    :u, :email, :p,
                    :first, :last, :phone, :addr,
                    1, :role_id, :empresa_id, :eid,
                    NOW(), NOW()
                )
            ");

            $stmt->execute([
                ':u'          => $username,
                ':email'      => $email,
                ':p'          => $hash,
                ':first'      => $nombres,
                ':last'       => $apellidos,
                ':phone'      => $phone,
                ':addr'       => $address,
                ':role_id'    => $roleId,
                ':empresa_id' => $empresa_id,
                ':eid'        => $estudiante_id,
            ]);

            // === Registrar en clientes_nuevos ===
            $stmt = $this->conn->prepare("
                    INSERT INTO clientes_nuevos (
                        empresa_id, estudiante_id, telefono, estado, created_by, created_at
                    ) VALUES (
                        :empresa_id, :eid, :tel, 1, :uid, NOW()
                    )
                ");
            $stmt->execute([
                ':empresa_id' => $empresa_id,
                ':eid'        => $estudiante_id,
                ':tel'        => $telefono !== '' ? $telefono : null,
                ':uid'        => $user_id,
            ]);

            $this->conn->commit();

            $_SESSION['flash_success'] = 'Cliente nuevo creado. Se generó usuario y acceso inicial.';
            header('Location: ' . $routes['clientes_nuevos_index']);
        } catch (Throwable $e) {
            $this->conn->rollBack();
            error_log('[ClientesNuevos@store] ' . $e->getMessage());
            $_SESSION['flash_error'] = 'No fue posible crear el cliente nuevo.';
            header('Location: ' . $routes['clientes_nuevos_create']);
        }
    }

    // Se llama desde el login (después de validar credenciales)
    public function marcarLoginNuevo(int $estudiante_id)
    {
        $stmt = $this->conn->prepare("
            UPDATE clientes_nuevos SET last_login_at = NOW() WHERE estudiante_id = :id
        ");
        $stmt->execute([':id' => $estudiante_id]);
    }

    // Al completar el perfil:
    public function finalizarOnboarding(int $estudiante_id)
    {
        $this->conn->beginTransaction();
        try {
            $stmt = $this->conn->prepare("UPDATE users SET must_complete_profile = 0 WHERE estudiante_id = :id");
            $stmt->execute([':id' => $estudiante_id]);

            $stmt = $this->conn->prepare("
                UPDATE clientes_nuevos
                SET estado = 0, completed_at = NOW()
                WHERE estudiante_id = :id
            ");
            $stmt->execute([':id' => $estudiante_id]);

            $this->conn->commit();
        } catch (Throwable $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    // Helpers
    private function fetchOne($sql, $params = [])
    {
        $st = $this->conn->prepare($sql);
        foreach ($params as $k => $v) $st->bindValue($k, $v);
        $st->execute();
        return $st->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    private function fetchAll($sql, $params = [])
    {
        $st = $this->conn->prepare($sql);
        foreach ($params as $k => $v) $st->bindValue($k, $v);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    // ==== helper ====
    // Ej: EST + 12 hex = 15 chars (p.ej. EST680f6f744547)
    private function generateStudentCode(): string
    {
        return 'EST' . strtoupper(bin2hex(random_bytes(6))); // 12 hex
    }

    private function nextUniqueStudentCode(PDO $pdo): string
    {
        do {
            $codigo = $this->generateStudentCode();
            $st = $pdo->prepare("SELECT 1 FROM estudiantes WHERE codigo = :c LIMIT 1");
            $st->execute([':c' => $codigo]);
            $exists = (bool) $st->fetchColumn();
        } while ($exists);
        return $codigo;
    }

    public function sendWhatsappTemplate()
    {
        $routes = include '../config/Routes.php';

        // El POST 'id' viene con el ESTUDIANTE_ID
        $estudianteId = (int)($_POST['id'] ?? 0);

        if ($estudianteId <= 0) {
            $_SESSION['flash_error'] = 'ID de estudiante inválido.';
            header('Location: ' . $routes['clientes_nuevos_index']);
            exit;
        }

        // Traer el registro de clientes_nuevos más reciente para ese estudiante
        // (y dentro de la empresa, si aplica)
        $params = [':est' => $estudianteId];
        $empresaFilter = '';
        if (!empty($_SESSION['empresa_id'])) {
            $empresaFilter = ' AND cn.empresa_id = :emp ';
            $params[':emp'] = (int) $_SESSION['empresa_id'];
        }


        // === Empresa: nombre y dominio ===
        $empresa_id = (int)($_SESSION['empresa_id'] ?? 0);
        $empresa = $this->fetchOne("
                SELECT nombre, dominio
                FROM empresas
                WHERE id = :id
                LIMIT 1
            ", [':id' => $empresa_id]);

        // Normalizar dominio -> URL base
        $dominio = trim((string)($empresa['dominio'] ?? ''));
        // Puede venir como 'http://docuia.com' o 'docuia.com' o con espacios
        $dominio = preg_replace('/\s+/', '', $dominio);

        $empresaNombre = strtoupper(trim($empresa['nombre'] ?? ''));  // MAYÚSCULAS

        // Si no trae esquema, asumimos https
        if ($dominio !== '') {
            if (!preg_match('~^https?://~i', $dominio)) {
                $dominio = 'https://' . $dominio;
            }
            // login URL
            $loginUrl = rtrim($dominio, '/') . '/login/';
        } else {
            // Fallback al host actual
            $loginUrl = rtrim(
                (isset($_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST'])
                    ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
                    : 'https://tu-dominio'),
                '/'
            ) . '/login/';
        }

        $row = $this->fetchOne("
                SELECT
                    cn.id AS cliente_nuevo_id,
                    COALESCE(NULLIF(TRIM(cn.telefono), ''), NULLIF(TRIM(e.celular), '')) AS telefono,
                    e.id   AS estudiante_id,
                    e.nombres, e.apellidos, e.numero_documento
                FROM clientes_nuevos cn
                INNER JOIN estudiantes e ON e.id = cn.estudiante_id
                WHERE cn.estudiante_id = :est
                $empresaFilter
                ORDER BY cn.created_at DESC
                LIMIT 1
            ", $params);

        if (!$row) {
            $_SESSION['flash_error'] = 'No existe cliente nuevo para este estudiante.';
            header('Location: ' . $routes['clientes_nuevos_index']);
            exit;
        }

        $telefono = (string)($row['telefono'] ?? '');
        if ($telefono === '') {
            $_SESSION['flash_error'] = 'El estudiante no tiene teléfono para WhatsApp.';
            header('Location: ' . $routes['clientes_nuevos_index']);
            exit;
        }

        // Variables para la plantilla de onboarding
        $loginUrl = rtrim(
            (isset($_SERVER['REQUEST_SCHEME'], $_SERVER['HTTP_HOST'])
                ? $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']
                : 'https://tu-dominio'),
            '/'
        ) . '/login/';

        // Nombre completo en MAYÚSCULAS (compatible con tildes)
        $nombreCompleto = trim(($row['nombres'] ?? '') . ' ' . ($row['apellidos'] ?? ''));
        $nombreMayus = function_exists('mb_strtoupper')
            ? mb_strtoupper($nombreCompleto, 'UTF-8')
            : strtoupper($nombreCompleto); // fallback si no está mbstring

        // === Variables para la plantilla onboarding ===
        $vars = [
            'nombre'     => $nombreMayus,            // ← aquí en MAYÚSCULAS
            'empresa'    => $empresaNombre,          // ya estaba en strtoupper
            'documento'  => (string)($row['numero_documento'] ?? ''),
            'login_url'  => $loginUrl,
        ];

        require_once '../shared/utils/WhatsappLink.php';
        $href = WhatsappLink::link($telefono, 'onboarding_cliente', $vars, 'CO');

        if ($href === '') {
            $_SESSION['flash_error'] = 'Teléfono inválido para WhatsApp.';
            header('Location: ' . $routes['clientes_nuevos_index']);
            exit;
        }

        // Redirige a wa.me (abrirá WhatsApp con el mensaje listo)
        header('Location: ' . $href);
        exit;
    }
}
