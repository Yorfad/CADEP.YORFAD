<?php
require_once __DIR__ . '/../../auth/validate.php';      
require_once __DIR__ . '/../../config/database.php';    
require_once __DIR__ . '/../../core/Response.php';      

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID de la especialidad a activar", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificar si la especialidad está inactiva
    $stmt = $conn->prepare("SELECT id FROM especialidades WHERE id = ? AND activo = 0");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        Response::error("Especialidad no encontrada o ya está activa", 404);
    }

    // Activar especialidad
    $stmt = $conn->prepare("
        UPDATE especialidades
        SET activo = 1, sincronizado = 0, actualizado_en = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    Response::json(["mensaje" => "Especialidad activada correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al activar especialidad: " . $e->getMessage(), 500);
}
