<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$sede_id = $GLOBALS['usuario_actual']['sede_id'] ?? null;
if (!$sede_id) {
    Response::error("No se encontró sede asignada al usuario.", 403);
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("SELECT * FROM pacientes WHERE activo = 1 AND sede_id = ?");
$stmt->execute([$sede_id]);
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

Response::json($pacientes);
