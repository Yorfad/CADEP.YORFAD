<?php
require_once __DIR__ . '/../../auth/validate.php';      
require_once __DIR__ . '/../../config/database.php';    
require_once __DIR__ . '/../../core/Response.php';      

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID de la especialidad a desactivar", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificar si la especialidad existe y está activa
    $stmt = $conn->prepare("SELECT id FROM especialidades WHERE id = ? AND activo = 1");
    $stmt->execute([$id]);

    if ($stmt->rowCount() === 0) {
        Response::error("Especialidad no encontrada o ya está inactiva", 404);
    }

    // Desactivar especialidad (eliminación lógica)
    $stmt = $conn->prepare("
        UPDATE especialidades
        SET activo = 0, sincronizado = 0, actualizado_en = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    Response::json(["mensaje" => "Especialidad desactivada correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al desactivar especialidad: " . $e->getMessage(), 500);
}
