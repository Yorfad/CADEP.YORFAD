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
    SELECT c.*, p.nombre AS paciente, t.nombre AS terapeuta
    FROM citas c
    JOIN pacientes p ON c.paciente_id = p.id
    JOIN terapeutas t ON c.terapeuta_id = t.id
    WHERE c.activo = 1 AND p.sede_id = ?
    ORDER BY c.fecha DESC
");
$stmt->execute([$sede_id]);
$citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($citas);
