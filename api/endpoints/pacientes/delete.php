<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

// Leer datos del cuerpo JSON
$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID del paciente", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificamos si existe
    $verifica = $conn->prepare("SELECT * FROM pacientes WHERE id = ? AND activo = 1");
    $verifica->execute([$id]);

    if ($verifica->rowCount() === 0) {
        Response::error("Paciente no encontrado o ya desactivado", 404);
    }

    // Eliminación lógica: activo = 0
    $stmt = $conn->prepare("UPDATE pacientes SET activo = 0, sincronizado = 0, actualizado_en = NOW() WHERE id = ?");
    $stmt->execute([$id]);

    Response::json(["mensaje" => "Paciente desactivado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al desactivar paciente: " . $e->getMessage(), 500);
}
