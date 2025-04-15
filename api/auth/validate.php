<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Response.php';

// Leer el token
$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? '';

if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
    Response::error('Token no proporcionado', 401);
}

$token = $matches[1];

// Conexión a la base
$db = new Database();
$conn = $db->connect();

// Validar token
$stmt = $conn->prepare("SELECT * FROM tokens WHERE token = :token AND activo = 1 AND expira_en > NOW()");
$stmt->bindParam(':token', $token);
$stmt->execute();

$tokenData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$tokenData) {
    Response::error('Token inválido o expirado', 401);
}

// ✅ Definir variable global del usuario logueado
$GLOBALS['usuario_actual'] = [
    'id' => $tokenData['usuario_id'],
    'token' => $token
];
