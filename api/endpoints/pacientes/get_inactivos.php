<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("SELECT * FROM pacientes WHERE activo = 0");
$stmt->execute();
$pacientes_inactivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($pacientes_inactivos);
