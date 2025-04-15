<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT c.*, p.nombre AS paciente, t.nombre AS terapeuta
    FROM citas c
    INNER JOIN pacientes p ON c.paciente_id = p.id
    INNER JOIN terapeutas t ON c.terapeuta_id = t.id
    WHERE c.activo = 0
");
$stmt->execute();
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($citas);
