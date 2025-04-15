<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID de la sede", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        UPDATE sedes
        SET activo = 0, sincronizado = 0, actualizado_en = NOW()
        WHERE id = :id AND activo = 1
    ");
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        Response::error("Sede no encontrada o ya está inactiva", 404);
    }

    Response::json(["mensaje" => "Sede desactivada correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al desactivar sede: " . $e->getMessage(), 500);
}
