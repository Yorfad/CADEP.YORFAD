<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
if (!$id) {
    Response::error("Falta el ID del reporte", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        UPDATE reportes_clinicos SET
            fecha = :fecha,
            motivo = :motivo,
            observaciones = :observaciones,
            diagnostico = :diagnostico,
            recomendaciones = :recomendaciones,
            proxima_cita = :proxima_cita,
            actualizado_en = NOW(),
            sincronizado = 0
        WHERE id = :id AND activo = 1
    ");
    $stmt->execute([
        ':id' => $id,
        ':fecha' => $data['fecha'],
        ':motivo' => $data['motivo'],
        ':observaciones' => $data['observaciones'],
        ':diagnostico' => $data['diagnostico'],
        ':recomendaciones' => $data['recomendaciones'],
        ':proxima_cita' => $data['proxima_cita'] ?? null
    ]);

    Response::json(["mensaje" => "Reporte clínico actualizado"]);
} catch (PDOException $e) {
    Response::error("Error al actualizar reporte: " . $e->getMessage(), 500);
}
