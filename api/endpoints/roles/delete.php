<?php
require_once __DIR__ . '/../../auth/validate.php';      
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID del rol a desactivar", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificar que el rol exista y esté activo
    $stmt = $conn->prepare("SELECT id FROM roles WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        Response::error("Rol no encontrado o ya está inactivo", 404);
    }

    // Desactivar el rol
    $stmt = $conn->prepare("
        UPDATE roles
        SET activo = 0, sincronizado = 0, actualizado_en = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    Response::json(["mensaje" => "Rol desactivado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al desactivar el rol: " . $e->getMessage(), 500);
}
