<?php
require_once __DIR__ . '/../../auth/validate.php';     // Valida token
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

// ID del usuario autenticado (desde validate.php)
$usuario_id = $GLOBALS['usuario_actual']['id'];

$data = json_decode(file_get_contents("php://input"), true);

$actual = $data['password_actual'] ?? null;
$nueva = $data['nueva_password'] ?? null;

if (!$actual || !$nueva) {
    Response::error("Debes proporcionar la contraseña actual y la nueva", 422);
}

if (strlen($nueva) < 6) {
    Response::error("La nueva contraseña debe tener al menos 6 caracteres", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    // Obtener contraseña actual desde la base
    $stmt = $conn->prepare("SELECT password FROM usuarios WHERE id = :id AND activo = 1");
    $stmt->execute([':id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        Response::error("Usuario no encontrado o inactivo", 404);
    }

    // Verificar contraseña actual
    if (!password_verify($actual, $usuario['password'])) {
        Response::error("La contraseña actual no es correcta", 403);
    }

    // Hashear nueva contraseña y actualizar
    $hash = password_hash($nueva, PASSWORD_DEFAULT);
    $update = $conn->prepare("
        UPDATE usuarios SET password = :nueva, actualizado_en = NOW(), sincronizado = 0 WHERE id = :id
    ");
    $update->execute([
        ':nueva' => $hash,
        ':id' => $usuario_id
    ]);

    Response::json(["mensaje" => "Contraseña actualizada correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al actualizar la contraseña: " . $e->getMessage(), 500);
}
