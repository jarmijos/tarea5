<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$accion = $_GET['action'] ?? 'listar';
$metodo = $_SERVER['REQUEST_METHOD'];

// Acciones que requieren POST
if (in_array($accion, ['insertar', 'actualizar', 'eliminar']) && $metodo !== 'POST') {
    http_response_code(403);
    echo json_encode(['error' => 'Metodo no permitido']);
    exit;
}

// Credenciales de la base de datos
$usuario = 'postgres';
$password = '123456789';

if (empty($usuario) || empty($password)) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit;
}

try {
    $pdo = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=clientes_db', $usuario, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($accion === 'listar') {
        $stmt = $pdo->query('SELECT id, razon_social, documento_identificacion, direccion, telefono, correo, created_at FROM clientes ORDER BY id');
        http_response_code(200);
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));

    } elseif ($accion === 'insertar') {
        $data = json_decode(file_get_contents('php://input'), true);
        if (empty($data['razon_social']) || empty($data['documento_identificacion']) || empty($data['direccion']) || empty($data['telefono']) || empty($data['correo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Faltan campos obligatorios']);
            exit;
        }
        $stmt = $pdo->prepare('INSERT INTO clientes (razon_social, documento_identificacion, direccion, telefono, correo) VALUES (:razon_social, :documento_identificacion, :direccion, :telefono, :correo)');
        $stmt->execute([
            ':razon_social' => $data['razon_social'],
            ':documento_identificacion' => $data['documento_identificacion'],
            ':direccion' => $data['direccion'],
            ':telefono' => $data['telefono'],
            ':correo' => $data['correo'],
        ]);
        http_response_code(200);
        echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);

    } elseif ($accion === 'actualizar') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? ($_GET['id'] ?? null);
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Id no valido']);
            exit;
        }
        $stmt = $pdo->prepare('UPDATE clientes SET razon_social = :razon_social, documento_identificacion = :documento_identificacion, direccion = :direccion, telefono = :telefono, correo = :correo WHERE id = :id');
        $stmt->execute([
            ':razon_social' => $data['razon_social'] ?? '',
            ':documento_identificacion' => $data['documento_identificacion'] ?? '',
            ':direccion' => $data['direccion'] ?? '',
            ':telefono' => $data['telefono'] ?? '',
            ':correo' => $data['correo'] ?? '',
            ':id' => $id,
        ]);
        http_response_code(200);
        echo json_encode(['ok' => true, 'id' => $id]);

    } elseif ($accion === 'eliminar') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? ($_GET['id'] ?? null);
        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Id no valido']);
            exit;
        }
        $stmt = $pdo->prepare('DELETE FROM clientes WHERE id = :id');
        $stmt->execute([':id' => $id]);
        http_response_code(200);
        echo json_encode(['ok' => true, 'id' => $id]);

    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Accion no valida']);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
