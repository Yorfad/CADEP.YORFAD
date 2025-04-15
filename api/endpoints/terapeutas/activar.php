<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID del terapeuta a activar", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificar si el terapeuta existe y está inactivo
    $stmt = $conn->prepare("SELECT * FROM terapeutas WHERE id = ? AND activo = 0");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        Response::error("Terapeuta no encontrado o ya está activo", 404);
    }

    // Activar
    $update = $conn->prepare("
        UPDATE terapeutas
        SET activo = 1, sincronizado = 0, actualizado_en = NOW()
        WHERE id = ?
    ");
    $update->execute([$id]);

    Response::json(["mensaje" => "Terapeuta activado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al activar terapeuta: " . $e->getMessage(), 500);
}
