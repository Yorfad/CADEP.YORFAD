<?php
header('Content-Type: application/json');

try {
    $json = file_get_contents("php://input");
    if (!$json) {
        throw new Exception("No se recibió ningún cuerpo JSON.");
    }

    $datos = json_decode($json, true);
    if (!$datos) {
        throw new Exception("JSON inválido.");
    }

    $conexion = new mysqli("127.0.0.1", "admin", "admin123", "hospital", 3308);

    if ($conexion->connect_error) {
        echo json_encode(["success" => false, "message" => "Error de conexión: " . $conexion->connect_error]);
        exit;
    }



    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }

    $stmt = $conexion->prepare("INSERT INTO pacientes 
        (nombre_completo, cui, fecha_nacimiento, sexo, direccion, telefono, correo, estudia, nivel_educativo, sede_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        throw new Exception("Error preparando sentencia: " . $conexion->error);
    }

    $stmt->bind_param("sssssssisi",
        $datos["nombre_completo"],
        $datos["cui"],
        $datos["fecha_nacimiento"],
        $datos["sexo"],
        $datos["direccion"],
        $datos["telefono"],
        $datos["correo"],
        $datos["estudia"],
        $datos["nivel_educativo"],
        $datos["sede_id"]
    );

    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar INSERT: " . $stmt->error);
    }

    echo json_encode(["success" => true]);

    $stmt->close();
    $conexion->close();
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
?>
