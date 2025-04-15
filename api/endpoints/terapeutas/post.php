<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validar campos requeridos
$requeridos = ['usuario_id', 'especialidad_id', 'sede_id', 'cui'];
foreach ($requeridos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

// Validar formato de CUI
if (!preg_match('/^[0-9]{4} [0-9]{5} [0-9]{4}$/', $data['cui'])) {
    Response::error("Formato de CUI inválido. Debe ser #### ##### ####", 422);
}

// Conexión
$db = new Database();
$conn = $db->connect();


// Verificar que usuario_id no esté ya asignado a otro terapeuta
$stmt = $conn->prepare("SELECT id FROM terapeutas WHERE usuario_id = ?");
$stmt->execute([$data['usuario_id']]);
if ($stmt->rowCount() > 0) {
    Response::error("Este usuario ya está asignado como terapeuta", 409);
}

// Insertar terapeuta
try {
    $stmt = $conn->prepare("
        INSERT INTO terapeutas (usuario_id, especialidad_id, sede_id, cui, activo, sincronizado)
        VALUES (:usuario_id, :especialidad_id, :sede_id, :cui, 1, 0)
    ");

    $stmt->execute([
        ':usuario_id' => $data['usuario_id'],
        ':especialidad_id' => $data['especialidad_id'],
        ':sede_id' => $data['sede_id'],
        ':cui' => $data['cui']
    ]);

    $id = $conn->lastInsertId();
    Response::json(["mensaje" => "Terapeuta creado exitosamente", "id" => $id], 201);

} catch (PDOException $e) {
    Response::error("Error al crear terapeuta: " . $e->getMessage(), 500);
}
