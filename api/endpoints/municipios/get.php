<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT m.id, m.nombre, m.departamento_id, d.nombre AS departamento
    FROM municipios m
    LEFT JOIN departamentos d ON m.departamento_id = d.id
    WHERE m.activo = 1
");
$stmt->execute();
$municipios = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($municipios);
