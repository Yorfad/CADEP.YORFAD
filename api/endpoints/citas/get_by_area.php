<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$area = $_GET['area'] ?? null;

if (!$area) {
    Response::error("El parámetro 'area' es obligatorio", 400);
}

$db = new Database();
$conn = $db->connect();

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
  WHERE e.nombre = ?
";

$stmt = $conn->prepare($query);
$stmt->execute([$area]);

$citas = [];

while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
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

file_put_contents("debug_output.txt", json_encode($citas, JSON_PRETTY_PRINT));
Response::json($citas);
