<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);

// Validar ID
$id = $data['id'] ?? null;
if (!$id) {
    Response::error("Falta el ID del terapeuta", 400);
}

// Validar campos requeridos
$requeridos = ['especialidad_id', 'sede_id', 'cui'];
foreach ($requeridos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

// Validar formato CUI
if (!preg_match('/^[0-9]{4} [0-9]{5} [0-9]{4}$/', $data['cui'])) {
    Response::error("Formato de CUI inválido. Debe ser #### ##### ####", 422);
}

$db = new Database();
$conn = $db->connect();


// Verificar que el terapeuta exista y esté activo
$stmt = $conn->prepare("SELECT * FROM terapeutas WHERE id = ? AND activo = 1");
$stmt->execute([$id]);

if ($stmt->rowCount() === 0) {
    Response::error("Terapeuta no encontrado o desactivado", 404);
}

// Actualizar
try {
    $stmt = $conn->prepare("
        UPDATE terapeutas SET
            especialidad_id = :especialidad,
            sede_id = :sede,
            cui = :cui,
            actualizado_en = NOW(),
            sincronizado = 0
        WHERE id = :id
    ");

    $stmt->execute([
        ':id' => $id,
        ':especialidad' => $data['especialidad_id'],
        ':sede' => $data['sede_id'],
        ':cui' => $data['cui']
    ]);

    Response::json(["mensaje" => "Terapeuta actualizado correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al actualizar terapeuta: " . $e->getMessage(), 500);
}
