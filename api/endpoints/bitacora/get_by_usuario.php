<?php
require_once __DIR__ . '/../../auth/validate.php';  
require_once __DIR__ . '/../../config/database.php';  
require_once __DIR__ . '/../../core/Response.php';

$usuario_id = $_GET['usuario_id'] ?? null;
if (!$usuario_id) {
    Response::error("Falta el parámetro 'usuario_id'", 400);
}

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT b.*, u.nombre AS usuario
    FROM bitacora b
    INNER JOIN usuarios u ON b.usuario_id = u.id
    WHERE b.usuario_id = ?
    ORDER BY b.fecha DESC
");
$stmt->execute([$usuario_id]);
$registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($registros);
