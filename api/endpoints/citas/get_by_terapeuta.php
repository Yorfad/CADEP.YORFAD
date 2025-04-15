<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$terapeuta_id = $_GET['terapeuta_id'] ?? null;
if (!$terapeuta_id) {
    Response::error("Falta el parámetro 'terapeuta_id'", 400);
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT c.*, p.nombre AS paciente
    FROM citas c
    INNER JOIN pacientes p ON c.paciente_id = p.id
    WHERE c.terapeuta_id = ? AND c.activo = 1
");
$stmt->execute([$terapeuta_id]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($citas);
