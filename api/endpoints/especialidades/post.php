<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$nombre = $data['nombre'] ?? null;
$descripcion = $data['descripcion'] ?? '';

if (!$nombre) {
    Response::error("El campo 'nombre' es obligatorio", 422);
}

$db = new Database();
$conn = $db->connect();


try {
    // Verificar si ya existe una especialidad activa con ese nombre
    $stmt = $conn->prepare("SELECT id FROM especialidades WHERE nombre = ? AND activo = 1");
    $stmt->execute([$nombre]);

    if ($stmt->rowCount() > 0) {
        Response::error("Ya existe una especialidad con ese nombre", 409);
    }

    // Insertar nueva especialidad
    $stmt = $conn->prepare("
        INSERT INTO especialidades (nombre, descripcion, activo, sincronizado)
        VALUES (:nombre, :descripcion, 1, 0)
    ");
    $stmt->execute([
        ':nombre' => $nombre,
        ':descripcion' => $descripcion
    ]);

    $id = $conn->lastInsertId();
    Response::json(["mensaje" => "Especialidad creada exitosamente", "id" => $id], 201);

} catch (PDOException $e) {
    Response::error("Error al crear la especialidad: " . $e->getMessage(), 500);
}
