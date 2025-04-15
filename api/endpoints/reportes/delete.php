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


$stmt = $conn->prepare("UPDATE reportes_clinicos SET activo = 0, sincronizado = 0, actualizado_en = NOW() WHERE id = ?");
$stmt->execute([$id]);

Response::json(["mensaje" => "Reporte clínico desactivado"]);
