<?php
header('Content-Type: application/json');

$conexion = new mysqli("127.0.0.1", "admin", "admin123", "hospital", 3308);
if ($conexion->connect_error) {
  echo json_encode([]);
  exit;
}

$resultado = $conexion->query("SELECT id, nombre FROM sedes ORDER BY nombre");

$sedes = [];
while ($fila = $resultado->fetch_assoc()) {
  $sedes[] = $fila;
}

echo json_encode($sedes);
$conexion->close();
