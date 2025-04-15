<?php
require_once __DIR__ . '/../../auth/validate.php';      // Protege con token
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validar campos requeridos
$requeridos = ['nombre', 'usuario', 'password', 'rol_id', 'sede_id'];
foreach ($requeridos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

// Validar longitud mínima de contraseña
if (strlen($data['password']) < 6) {
    Response::error("La contraseña debe tener al menos 6 caracteres", 422);
}

// Conexión a DB
$db = new Database();
$conn = $db->connect();


// Verificar si el nombre de usuario ya existe
$stmt = $conn->prepare("SELECT id FROM usuarios WHERE usuario = :usuario");
$stmt->execute([':usuario' => $data['usuario']]);
if ($stmt->rowCount() > 0) {
    Response::error("El nombre de usuario ya está registrado", 409);
}

// Hashear la contraseña
$hash = password_hash($data['password'], PASSWORD_DEFAULT);

// Insertar nuevo usuario
try {
    $stmt = $conn->prepare("
        INSERT INTO usuarios (nombre, usuario, password, rol_id, departamento_id, municipio_id, sede_id, activo, sincronizado)
        VALUES (:nombre, :usuario, :password, :rol_id, :depto, :muni, :sede_id, 1, 0)
    ");
    $stmt->execute([
        ':nombre' => $data['nombre'],
        ':usuario' => $data['usuario'],
        ':password' => $hash,
        ':rol_id' => $data['rol_id'],
        ':depto' => $data['departamento_id'] ?? null,
        ':muni' => $data['municipio_id'] ?? null,
        ':sede_id' => $data['sede_id']
    ]);

    $id = $conn->lastInsertId();
    Response::json(["mensaje" => "Usuario creado correctamente", "id" => $id], 201);

} catch (PDOException $e) {
    Response::error("Error al crear usuario: " . $e->getMessage(), 500);
}
