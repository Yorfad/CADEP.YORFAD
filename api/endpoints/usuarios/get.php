<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();

// Consulta con joins para mostrar detalles del rol y sede
$stmt = $conn->prepare("
    SELECT u.id, u.nombre, u.usuario, u.rol_id, r.nombre AS rol, u.departamento_id,
           u.municipio_id, u.sede_id, s.nombre AS sede, u.creado_en, u.actualizado_en
    FROM usuarios u
    LEFT JOIN roles r ON r.id = u.rol_id
    LEFT JOIN sedes s ON s.id = u.sede_id
    WHERE u.activo = 1
");
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($usuarios);
