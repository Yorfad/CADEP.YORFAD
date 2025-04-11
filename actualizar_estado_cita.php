<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);

$id = $input['id'];
$estado = $input['estado'];

$conexion = new mysqli("127.0.0.1", "admin", "admin123", "hospital", 3308);
if ($conexion->connect_error) {
  echo json_encode(['success' => false, 'error' => 'Error de conexión']);
  exit;
}

$stmt = $conexion->prepare("UPDATE citas SET estado = ? WHERE id = ?");
$stmt->bind_param("si", $estado, $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'error' => $stmt->error]);
}

$stmt->close();
$conexion->close();
