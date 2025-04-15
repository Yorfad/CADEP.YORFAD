<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID del usuario", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificar si el usuario existe y está activo
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        Response::error("Usuario no encontrado o ya está desactivado", 404);
    }

    // Desactivar el usuario
    $update = $conn->prepare("
        UPDATE usuarios
        SET activo = 0, sincronizado = 0, actualizado_en = NOW()
        WHERE id = ?
    ");
    $update->execute([$id]);

    Response::json(["mensaje" => "Usuario desactivado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al desactivar usuario: " . $e->getMessage(), 500);
}
