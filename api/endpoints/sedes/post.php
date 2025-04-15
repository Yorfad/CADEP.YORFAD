<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'] ?? null;
$direccion = $data['direccion'] ?? '';
$telefono = $data['telefono'] ?? '';
$departamento_id = $data['departamento_id'] ?? null;
$municipio_id = $data['municipio_id'] ?? null;

if (!$nombre) {
    Response::error("El campo 'nombre' es obligatorio", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        INSERT INTO sedes (nombre, direccion, telefono, departamento_id, municipio_id, activo, sincronizado)
        VALUES (:nombre, :direccion, :telefono, :departamento_id, :municipio_id, 1, 0)
    ");
    $stmt->execute([
        ':nombre' => $nombre,
        ':direccion' => $direccion,
        ':telefono' => $telefono,
        ':departamento_id' => $departamento_id,
        ':municipio_id' => $municipio_id
    ]);

    $id = $conn->lastInsertId();
    Response::json(["mensaje" => "Sede creada exitosamente", "id" => $id], 201);

} catch (PDOException $e) {
    Response::error("Error al crear sede: " . $e->getMessage(), 500);
}
