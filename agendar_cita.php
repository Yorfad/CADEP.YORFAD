<?php
header('Content-Type: application/json');

$datos = json_decode(file_get_contents("php://input"), true);
$conexion = new mysqli("127.0.0.1", "admin", "admin123", "hospital", 3308);

if ($conexion->connect_error) {
  echo json_encode(["success" => false, "message" => "Error conexión: " . $conexion->connect_error]);
  exit;
}

$stmt = $conexion->prepare("INSERT INTO citas (paciente_id, terapeuta_id, sede_id, fecha, motivo) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("iiiss",
  $datos["paciente_id"],
  $datos["terapeuta_id"],
  $datos["sede_id"],
  $datos["fecha"],
  $datos["motivo"]
);

if ($stmt->execute()) {
  echo json_encode(["success" => true]);
} else {
  echo json_encode(["success" => false, "message" => $stmt->error]);
}

$stmt->close();
$conexion->close();
