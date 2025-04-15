<?php
require_once __DIR__ . '/../../auth/validate.php';      // Valida token
require_once __DIR__ . '/../../config/database.php';    // Conexión DB
require_once __DIR__ . '/../../core/Response.php';      // Respuestas JSON

$usuario_id = $GLOBALS['usuario_actual']['id'];
$data = json_decode(file_get_contents("php://input"), true);

// Validar campos requeridos
$requeridos = ['nombre_completo', 'cui', 'fecha_nacimiento', 'sexo', 'telefono', 'sede_id'];
foreach ($requeridos as $campo) {
    if (empty($data[$campo])) {
        Response::error("El campo '$campo' es obligatorio", 422);
    }
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        INSERT INTO pacientes (
            nombre_completo, cui, fecha_nacimiento, sexo,
            direccion, telefono, correo, estudia,
            nivel_educativo, responsable_id, sede_id,
            activo, sincronizado
        )
        VALUES (
            :nombre, :cui, :fecha, :sexo,
            :direccion, :telefono, :correo, :estudia,
            :nivel, :responsable, :sede,
            1, 0
        )
    ");

    $stmt->execute([
        ':nombre' => $data['nombre_completo'],
        ':cui' => $data['cui'],
        ':fecha' => $data['fecha_nacimiento'],
        ':sexo' => $data['sexo'],
        ':direccion' => $data['direccion'] ?? null,
        ':telefono' => $data['telefono'],
        ':correo' => $data['correo'] ?? null,
        ':estudia' => isset($data['estudia']) ? (int)$data['estudia'] : 0,
        ':nivel' => $data['nivel_educativo'] ?? null,
        ':responsable' => $data['responsable_id'] ?? null,
        ':sede' => $data['sede_id']
    ]);

    $id = $conn->lastInsertId();
    Response::json(["mensaje" => "Paciente creado", "id" => $id], 201);

} catch (PDOException $e) {
    Response::error("Error al insertar paciente: " . $e->getMessage(), 500);
}
