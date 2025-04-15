<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("SELECT id, nombre FROM departamentos WHERE activo = 0");
$stmt->execute();
$departamentos = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($departamentos);
