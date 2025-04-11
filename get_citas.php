<?php
header('Content-Type: application/json');

$conexion = new mysqli("127.0.0.1", "admin", "admin123", "hospital", 3308);
if ($conexion->connect_error) {
  echo json_encode([]);
  exit;
}

$area = isset($_GET['area']) ? $conexion->real_escape_string($_GET['area']) : null;

$query = "
  SELECT 
    c.id,
    c.fecha,
    c.estado,
    p.id AS paciente_id,
    p.nombre_completo AS paciente,
    e.nombre AS especialidad,
    u.id AS usuario_id,
    u.nombre AS terapeuta
  FROM citas c
  JOIN pacientes p ON c.paciente_id = p.id
  JOIN terapeutas t ON c.terapeuta_id = t.id
  JOIN usuarios u ON t.usuario_id = u.id
  JOIN especialidades e ON t.especialidad_id = e.id
";

if ($area) {
  $query .= " WHERE e.nombre = '$area'";
}

$resultado = $conexion->query($query);
$citas = [];

while ($fila = $resultado->fetch_assoc()) {
  $fecha = substr($fila['fecha'], 0, 10);
  $hora_inicio = substr($fila['fecha'], 11, 5);
  $hora_fin = date("H:i", strtotime($fila['fecha'] . " +30 minutes"));

  $citas[] = [
    'id' => $fila['id'],
    'title' => $fila['paciente'],
    'start' => $fila['fecha'],
    'fecha' => $fecha,
    'hora_inicio' => $hora_inicio,
    'hora_fin' => $hora_fin,
    'estado' => $fila['estado'],
    'area' => $fila['especialidad'],
    'paciente' => [
      'id' => $fila['paciente_id'],
      'nombre' => $fila['paciente']
    ],
    'terapeuta' => [
      'id' => $fila['usuario_id'],
      'nombre' => $fila['terapeuta']
    ],
    'color' => $fila['estado'] === 'pendiente' ? '#3788d8' : '#28a745'
  ];
}

echo json_encode($citas);
$conexion->close();
