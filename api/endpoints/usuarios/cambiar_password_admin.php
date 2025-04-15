<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

// Validamos que el usuario autenticado tenga permisos (opcional)
$usuario_id = $GLOBALS['usuario_actual']['id'];

$data = json_decode(file_get_contents("php://input"), true);

$id_target = $data['id'] ?? null;
$nueva = $data['nueva_password'] ?? null;

if (!$id_target || !$nueva) {
    Response::error("Debes proporcionar el ID del usuario y la nueva contraseña", 422);
}

if (strlen($nueva) < 6) {
    Response::error("La nueva contraseña debe tener al menos 6 caracteres", 422);
}

$db = new Database();
$conn = $db->connect();


// (Opcional) Verifica si el usuario que cambia la clave es administrador
$verifica = $conn->prepare("SELECT rol_id FROM usuarios WHERE id = ? AND activo = 1");
$verifica->execute([$usuario_id]);
$rol = $verifica->fetchColumn();

if (!$rol || $rol != 1) {
    Response::error("No tienes permiso para cambiar contraseñas de otros usuarios", 403);
}

// Verifica si el usuario destino existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND activo = 1");
$stmt->execute([$id_target]);
if ($stmt->rowCount() === 0) {
    Response::error("Usuario destino no encontrado o está desactivado", 404);
}

// Hashea la nueva contraseña
$hash = password_hash($nueva, PASSWORD_DEFAULT);

// Actualiza la contraseña
$update = $conn->prepare("
    UPDATE usuarios
    SET password = :pass, actualizado_en = NOW(), sincronizado = 0
    WHERE id = :id
");
$update->execute([
    ':pass' => $hash,
    ':id' => $id_target
]);

Response::json(["mensaje" => "Contraseña actualizada correctamente"]);
