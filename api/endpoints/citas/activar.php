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


$stmt = $conn->prepare("UPDATE citas SET activo = 1, sincronizado = 0, actualizado_en = NOW() WHERE id = ?");
$stmt->execute([$id]);

Response::json(["mensaje" => "Cita reactivada correctamente"]);
