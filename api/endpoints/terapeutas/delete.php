<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID del terapeuta", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificar si existe y está activo
    $stmt = $conn->prepare("SELECT * FROM terapeutas WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        Response::error("Terapeuta no encontrado o ya desactivado", 404);
    }

    // Eliminar lógicamente
    $delete = $conn->prepare("
        UPDATE terapeutas
        SET activo = 0, sincronizado = 0, actualizado_en = NOW()
        WHERE id = ?
    ");
    $delete->execute([$id]);

    Response::json(["mensaje" => "Terapeuta desactivado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al desactivar terapeuta: " . $e->getMessage(), 500);
}
