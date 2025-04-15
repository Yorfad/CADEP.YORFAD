<?php
require_once __DIR__ . '/../../auth/validate.php';      // Valida token
require_once __DIR__ . '/../../config/database.php';    // Conexión DB
require_once __DIR__ . '/../../core/Response.php';      // Formato de respuesta

$db = new Database();
$conn = $db->connect();


// Consulta de especialidades activas
$stmt = $conn->prepare("
    SELECT id, nombre, descripcion
    FROM especialidades
    WHERE activo = 1
");
$stmt->execute();

$especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($especialidades);
