<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? null;
$departamento_id = $data['departamento_id'] ?? null;

if (!$id || !$nombre || !$departamento_id) {
    Response::error("Los campos 'id', 'nombre' y 'departamento_id' son obligatorios", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("UPDATE municipios SET nombre = ?, departamento_id = ?, sincronizado = 0, actualizado_en = NOW() WHERE id = ? AND activo = 1");
    $stmt->execute([$nombre, $departamento_id, $id]);
    Response::json(["mensaje" => "Municipio actualizado"]);
} catch (PDOException $e) {
    Response::error("Error al actualizar municipio: " . $e->getMessage(), 500);
}
