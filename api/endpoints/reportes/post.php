<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$campos = ['paciente_id', 'terapeuta_id', 'fecha', 'motivo', 'observaciones', 'diagnostico', 'recomendaciones'];
foreach ($campos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

$proxima_cita = $data['proxima_cita'] ?? null;

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        INSERT INTO reportes_clinicos (
            paciente_id, terapeuta_id, fecha, motivo, observaciones,
            diagnostico, recomendaciones, proxima_cita, activo, sincronizado
        ) VALUES (
            :paciente_id, :terapeuta_id, :fecha, :motivo, :observaciones,
            :diagnostico, :recomendaciones, :proxima_cita, 1, 0
        )
    ");
    $stmt->execute([
        ':paciente_id' => $data['paciente_id'],
        ':terapeuta_id' => $data['terapeuta_id'],
        ':fecha' => $data['fecha'],
        ':motivo' => $data['motivo'],
        ':observaciones' => $data['observaciones'],
        ':diagnostico' => $data['diagnostico'],
        ':recomendaciones' => $data['recomendaciones'],
        ':proxima_cita' => $proxima_cita
    ]);

    Response::json(["mensaje" => "Reporte clínico creado", "id" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    Response::error("Error al crear reporte: " . $e->getMessage(), 500);
}
