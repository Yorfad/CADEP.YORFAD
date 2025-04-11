<?php
header('Content-Type: application/json');

$conexion = new mysqli("127.0.0.1", "admin", "admin123", "hospital", 3308);
if ($conexion->connect_error) {
  echo json_encode([]);
  exit;
}

$query = "
SELECT t.id, CONCAT(u.nombre, ' - ', e.nombre) AS nombre
FROM terapeutas t
JOIN usuarios u ON t.usuario_id = u.id
JOIN especialidades e ON t.especialidad_id = e.id
ORDER BY u.nombre
";

$resultado = $conexion->query($query);
$terapeutas = [];

while ($fila = $resultado->fetch_assoc()) {
  $terapeutas[] = $fila;
}

echo json_encode($terapeutas);
$conexion->close();
