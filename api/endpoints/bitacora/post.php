<?php
require_once __DIR__ . '/../../auth/validate.php';  
require_once __DIR__ . '/../../config/database.php';  
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$usuario_id = $GLOBALS['usuario_actual']['id'] ?? null;
$modulo = $data['modulo'] ?? null;
$accion = $data['accion'] ?? null;
$descripcion = $data['descripcion'] ?? '';

if (!$usuario_id || !$modulo || !$accion) {
    Response::error("Los campos 'modulo', 'accion' y el usuario autenticado son obligatorios", 422);
}

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    INSERT INTO bitacora (usuario_id, modulo, accion, descripcion)
    VALUES (:usuario_id, :modulo, :accion, :descripcion)
");

$stmt->execute([
    ':usuario_id' => $usuario_id,
    ':modulo' => $modulo,
    ':accion' => $accion,
    ':descripcion' => $descripcion
]);

Response::json(["mensaje" => "Registro agregado a la bitácora"]);
