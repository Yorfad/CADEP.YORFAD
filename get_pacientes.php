<?php
header('Content-Type: application/json');

$conexion = new mysqli("127.0.0.1", "admin", "admin123", "hospital", 3308);
if ($conexion->connect_error) {
  echo json_encode([]);
  exit;
}

$resultado = $conexion->query("SELECT id, nombre_completo FROM pacientes WHERE activo = 1 ORDER BY nombre_completo");

$pacientes = [];
while ($fila = $resultado->fetch_assoc()) {
  $pacientes[] = $fila;
}

echo json_encode($pacientes);
$conexion->close();
