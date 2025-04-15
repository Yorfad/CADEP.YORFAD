<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

$id = $data['id'] ?? null;
if (!$id) {
    Response::error("Falta el ID del usuario", 400);
}

$camposRequeridos = ['nombre', 'rol_id', 'sede_id'];
foreach ($camposRequeridos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

$db = new Database();
$conn = $db->connect();


// Verificar si el usuario existe y está activo
$check = $conn->prepare("SELECT * FROM usuarios WHERE id = ? AND activo = 1");
$check->execute([$id]);
if ($check->rowCount() === 0) {
    Response::error("Usuario no encontrado o desactivado", 404);
}

// Actualizar
try {
    $stmt = $conn->prepare("
        UPDATE usuarios SET
            nombre = :nombre,
            rol_id = :rol,
            departamento_id = :depto,
            municipio_id = :muni,
            sede_id = :sede,
            actualizado_en = NOW(),
            sincronizado = 0
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id,
        ':nombre' => $data['nombre'],
        ':rol' => $data['rol_id'],
        ':depto' => $data['departamento_id'] ?? null,
        ':muni' => $data['municipio_id'] ?? null,
        ':sede' => $data['sede_id']
    ]);

    Response::json(["mensaje" => "Usuario actualizado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al actualizar usuario: " . $e->getMessage(), 500);
}
