<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID del municipio", 400);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("UPDATE municipios SET activo = 1, sincronizado = 0, actualizado_en = NOW() WHERE id = ?");
    $stmt->execute([$id]);
    Response::json(["mensaje" => "Municipio activado"]);
} catch (PDOException $e) {
    Response::error("Error al activar municipio: " . $e->getMessage(), 500);
}
