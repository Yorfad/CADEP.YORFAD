<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT r.*, p.nombre AS paciente, t.nombre AS terapeuta
    FROM reportes_clinicos r
    INNER JOIN pacientes p ON r.paciente_id = p.id
    INNER JOIN terapeutas t ON r.terapeuta_id = t.id
    WHERE r.activo = 0
");
$stmt->execute();
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($reportes);
