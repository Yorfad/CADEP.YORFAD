<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT t.*, u.nombre AS nombre_usuario, e.nombre AS especialidad, s.nombre AS sede
    FROM terapeutas t
    INNER JOIN usuarios u ON u.id = t.usuario_id
    INNER JOIN especialidades e ON e.id = t.especialidad_id
    INNER JOIN sedes s ON s.id = t.sede_id
    WHERE t.activo = 0
");
$stmt->execute();
$terapeutas = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($terapeutas);
