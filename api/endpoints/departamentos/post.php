<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$nombre = $data['nombre'] ?? null;

if (!$nombre) {
    Response::error("El campo 'nombre' es obligatorio", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("INSERT INTO departamentos (nombre, activo, sincronizado) VALUES (?, 1, 0)");
    $stmt->execute([$nombre]);
    Response::json(["mensaje" => "Departamento creado", "id" => $conn->lastInsertId()]);
} catch (PDOException $e) {
    Response::error("Error al crear departamento: " . $e->getMessage(), 500);
}
