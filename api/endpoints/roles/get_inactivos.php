<?php
require_once __DIR__ . '/../../auth/validate.php';      
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("
    SELECT id, nombre, descripcion
    FROM roles
    WHERE activo = 0
");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($roles);
