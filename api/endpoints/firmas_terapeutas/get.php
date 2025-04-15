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


$stmt = $conn->prepare("SELECT ruta_firma FROM firmas_terapeutas WHERE terapeuta_id = ?");
$stmt->execute([$terapeuta_id]);

$firma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$firma) {
    Response::error("Firma no encontrada", 404);
}

Response::json($firma);
