<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? null;
$direccion = $data['direccion'] ?? '';
$telefono = $data['telefono'] ?? '';
$departamento_id = $data['departamento_id'] ?? null;
$municipio_id = $data['municipio_id'] ?? null;

if (!$id || !$nombre) {
    Response::error("Los campos 'id' y 'nombre' son obligatorios", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        UPDATE sedes
        SET nombre = :nombre, direccion = :direccion, telefono = :telefono,
            departamento_id = :departamento_id, municipio_id = :municipio_id,
            actualizado_en = NOW(), sincronizado = 0
        WHERE id = :id AND activo = 1
    ");
    $stmt->execute([
        ':id' => $id,
        ':nombre' => $nombre,
        ':direccion' => $direccion,
        ':telefono' => $telefono,
        ':departamento_id' => $departamento_id,
        ':municipio_id' => $municipio_id
    ]);

    Response::json(["mensaje" => "Sede actualizada correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al actualizar sede: " . $e->getMessage(), 500);
}
