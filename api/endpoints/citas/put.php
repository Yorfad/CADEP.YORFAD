<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
if (!$id) {
    Response::error("Falta el ID de la cita", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        UPDATE citas SET
            fecha = :fecha,
            motivo = :motivo,
            estado = :estado,
            observaciones = :observaciones,
            actualizado_en = NOW(),
            sincronizado = 0
        WHERE id = :id AND activo = 1
    ");
    $stmt->execute([
        ':id' => $id,
        ':fecha' => $data['fecha'],
        ':motivo' => $data['motivo'] ?? '',
        ':estado' => $data['estado'] ?? 'pendiente',
        ':observaciones' => $data['observaciones'] ?? ''
    ]);

    Response::json(["mensaje" => "Cita actualizada correctamente"]);
} catch (PDOException $e) {
    Response::error("Error al actualizar cita: " . $e->getMessage(), 500);
}
