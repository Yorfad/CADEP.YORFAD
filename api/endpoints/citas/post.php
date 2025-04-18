<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$requeridos = ['paciente_id', 'terapeuta_id', 'fecha'];
foreach ($requeridos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        INSERT INTO citas (paciente_id, terapeuta_id, fecha, motivo, estado, observaciones, activo, sincronizado)
        VALUES (:paciente_id, :terapeuta_id, :fecha, :motivo, 'pendiente', :observaciones, 1, 0)
    ");
    $stmt->execute([
        ':paciente_id' => $data['paciente_id'],
        ':terapeuta_id' => $data['terapeuta_id'],
        ':fecha' => $data['fecha'],
        ':motivo' => $data['motivo'] ?? '',
        ':observaciones' => $data['observaciones'] ?? ''
    ]);

    Response::json(["mensaje" => "Cita creada", "id" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    Response::error("Error al crear la cita: " . $e->getMessage(), 500);
}


$stmt = $conn->prepare("UPDATE citas SET activo = 1, sincronizado = 0, actualizado_en = NOW() WHERE id = ?");
$stmt->execute([$id]);

Response::json(["mensaje" => "Cita reactivada correctamente"]);
