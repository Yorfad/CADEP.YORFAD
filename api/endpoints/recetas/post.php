<?php
require_once __DIR__ . '/../../auth/validate.php';  
require_once __DIR__ . '/../../config/database.php';  
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$campos = ['paciente_id', 'terapeuta_id', 'fecha', 'descripcion'];
foreach ($campos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    INSERT INTO recetas_medicas (paciente_id, terapeuta_id, fecha, descripcion, instrucciones)
    VALUES (:paciente_id, :terapeuta_id, :fecha, :descripcion, :instrucciones)
");

$stmt->execute([
    ':paciente_id' => $data['paciente_id'],
    ':terapeuta_id' => $data['terapeuta_id'],
    ':fecha' => $data['fecha'],
    ':descripcion' => $data['descripcion'],
    ':instrucciones' => $data['instrucciones'] ?? null
]);

Response::json(["mensaje" => "Receta creada correctamente"]);
