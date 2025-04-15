<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$sede_id = $GLOBALS['usuario_actual']['sede_id'] ?? null;
if (!$sede_id) {
    Response::error("No se encontró sede asignada al usuario.", 403);
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT r.*, p.nombre AS paciente, t.nombre AS terapeuta
    FROM reportes_clinicos r
    JOIN pacientes p ON r.paciente_id = p.id
    JOIN terapeutas t ON r.terapeuta_id = t.id
    WHERE r.activo = 1 AND p.sede_id = ?
    ORDER BY r.fecha DESC
");
$stmt->execute([$sede_id]);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($reportes);
