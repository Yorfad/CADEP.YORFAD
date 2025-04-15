<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$nombre = $data['nombre'] ?? null;
$departamento_id = $data['departamento_id'] ?? null;

if (!$nombre || !$departamento_id) {
    Response::error("Los campos 'nombre' y 'departamento_id' son obligatorios", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("INSERT INTO municipios (nombre, departamento_id, activo, sincronizado) VALUES (?, ?, 1, 0)");
    $stmt->execute([$nombre, $departamento_id]);
    Response::json(["mensaje" => "Municipio creado", "id" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    Response::error("Error al crear municipio: " . $e->getMessage(), 500);
}
