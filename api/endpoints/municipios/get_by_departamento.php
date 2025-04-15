<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$departamento_id = $_GET['departamento_id'] ?? null;

if (!$departamento_id) {
    Response::error("Falta el parámetro 'departamento_id'", 400);
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT id, nombre
    FROM municipios
    WHERE departamento_id = ? AND activo = 1
");
$stmt->execute([$departamento_id]);

$municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($municipios);
