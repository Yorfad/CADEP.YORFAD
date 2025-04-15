<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$paciente_id = $_GET['paciente_id'] ?? null;

if (!$paciente_id) {
    Response::error("Falta el parámetro 'paciente_id'", 400);
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT r.*, t.nombre AS terapeuta
    FROM reportes_clinicos r
    INNER JOIN terapeutas t ON r.terapeuta_id = t.id
    WHERE r.paciente_id = ? AND r.activo = 1
");
$stmt->execute([$paciente_id]);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($reportes);
