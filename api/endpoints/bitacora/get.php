<?php
require_once __DIR__ . '/../../auth/validate.php';  
require_once __DIR__ . '/../../config/database.php';  
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("
    SELECT b.*, u.nombre AS usuario
    FROM bitacora b
    INNER JOIN usuarios u ON b.usuario_id = u.id
    ORDER BY b.fecha DESC
");
$stmt->execute();
$bitacora = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($bitacora);
