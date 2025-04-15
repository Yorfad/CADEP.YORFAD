<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$reporte_id = $_GET['reporte_id'] ?? null;
if (!$reporte_id) {
    Response::error("Falta el parámetro 'reporte_id'", 400);
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("SELECT * FROM archivos_reportes WHERE reporte_id = ?");
$stmt->execute([$reporte_id]);

$archivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($archivos);
