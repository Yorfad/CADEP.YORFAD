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
    SELECT c.*, t.nombre AS terapeuta
    FROM citas c
    INNER JOIN terapeutas t ON c.terapeuta_id = t.id
    WHERE c.paciente_id = ? AND c.activo = 1
");
$stmt->execute([$paciente_id]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($citas);
