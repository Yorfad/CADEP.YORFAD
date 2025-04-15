<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
file_put_contents("debug_login.txt", file_get_contents("php://input"));

$usuario = $data['usuario'] ?? '';
$password = $data['password'] ?? '';

if (!$usuario || !$password) {
    Response::error("Usuario y contraseña son obligatorios", 400);
}

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT u.id, u.usuario, u.nombre, u.rol_id, u.password, u.sede_id,
           r.nombre AS rol, s.nombre AS sede,
           e.id AS especialidad_id, e.nombre AS area
    FROM usuarios u
    JOIN roles r ON u.rol_id = r.id
    LEFT JOIN sedes s ON u.sede_id = s.id
    LEFT JOIN terapeutas t ON t.usuario_id = u.id
    LEFT JOIN especialidades e ON t.especialidad_id = e.id
    WHERE u.usuario = :usuario AND u.activo = 1
");

$stmt->execute([':usuario' => $usuario]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$user || !password_verify($password, $user['password'])) {
    Response::error("Credenciales inválidas", 401);
}

$token = bin2hex(random_bytes(32));
$expira_en = date('Y-m-d H:i:s', strtotime('+24 hours'));

$stmt = $conn->prepare("
    INSERT INTO tokens (usuario_id, token, expira_en)
    VALUES (:usuario_id, :token, :expira_en)
");
$stmt->execute([
    ':usuario_id' => $user['id'],
    ':token' => $token,
    ':expira_en' => $expira_en
]);

Response::json([
    "token" => $token,
    "usuario" => [
        "id" => $user['id'],
        "nombre" => $user['nombre'],
        "usuario" => $user['usuario'],
        "rol" => $user['rol'],
        "rol_id" => $user['rol_id'],
        "especialidad_id" => $user['especialidad_id'],
        "area" => $user['area'],
        "sede_id" => $user['sede_id'],
        "sede" => $user['sede']
    ]
]);
