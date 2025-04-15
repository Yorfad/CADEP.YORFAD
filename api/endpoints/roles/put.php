<?php
require_once __DIR__ . '/../../auth/validate.php';      
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
$nombre = $data['nombre'] ?? null;
$descripcion = $data['descripcion'] ?? null;

if (!$id || !$nombre) {
    Response::error("Los campos 'id' y 'nombre' son obligatorios", 422);
}

$db = new Database();
$conn = $db->connect();


// Verificar que el rol exista y esté activo
$verifica = $conn->prepare("SELECT id FROM roles WHERE id = ? AND activo = 1");
$verifica->execute([$id]);
if ($verifica->rowCount() === 0) {
    Response::error("Rol no encontrado o inactivo", 404);
}

try {
    $stmt = $conn->prepare("
        UPDATE roles SET
            nombre = :nombre,
            descripcion = :descripcion,
            actualizado_en = NOW(),
            sincronizado = 0
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id,
        ':nombre' => $nombre,
        ':descripcion' => $descripcion
    ]);

    Response::json(["mensaje" => "Rol actualizado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al actualizar el rol: " . $e->getMessage(), 500);
}
