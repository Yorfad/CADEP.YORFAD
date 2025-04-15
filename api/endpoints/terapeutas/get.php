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


// Filtrar terapeutas por sede a través del usuario
$stmt = $conn->prepare("
    SELECT t.*, u.sede_id
    FROM terapeutas t
    JOIN usuarios u ON t.usuario_id = u.id
    WHERE t.activo = 1 AND u.sede_id = ?
");
$stmt->execute([$sede_id]);
$terapeutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($terapeutas);
