<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? null;

if (!$id || !$nombre) {
    Response::error("Los campos 'id' y 'nombre' son obligatorios", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("UPDATE departamentos SET nombre = ?, sincronizado = 0, actualizado_en = NOW() WHERE id = ? AND activo = 1");
    $stmt->execute([$nombre, $id]);
    Response::json(["mensaje" => "Departamento actualizado"]);
} catch (PDOException $e) {
    Response::error("Error al actualizar departamento: " . $e->getMessage(), 500);
}
