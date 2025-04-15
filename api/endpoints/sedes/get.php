<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT s.id, s.nombre, s.direccion, s.telefono, s.departamento_id, s.municipio_id,
           d.nombre AS departamento, m.nombre AS municipio
    FROM sedes s
    LEFT JOIN departamentos d ON s.departamento_id = d.id
    LEFT JOIN municipios m ON s.municipio_id = m.id
    WHERE s.activo = 1
");
$stmt->execute();
$sedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($sedes);
