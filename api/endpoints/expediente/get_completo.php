<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$id_paciente = $_GET['id_paciente'] ?? null;
if (!$id_paciente) {
    Response::error("Falta el parámetro 'id_paciente'", 400);
}

$db = new Database();
$conn = $db->connect();


// Paciente
$stmt = $conn->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$id_paciente]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) {
    Response::error("Paciente no encontrado", 404);
}

// Contactos
$stmt = $conn->prepare("SELECT * FROM contactos_paciente WHERE paciente_id = ?");
$stmt->execute([$id_paciente]);
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Terapeuta actual
$stmt = $conn->prepare("
    SELECT t.*
    FROM terapeutas t
    INNER JOIN reportes_clinicos r ON r.terapeuta_id = t.id
    WHERE r.paciente_id = ?
    ORDER BY r.fecha DESC LIMIT 1
");
$stmt->execute([$id_paciente]);
$terapeuta = $stmt->fetch(PDO::FETCH_ASSOC);

// Reportes clínicos
$stmt = $conn->prepare("SELECT * FROM reportes_clinicos WHERE paciente_id = ? AND activo = 1");
$stmt->execute([$id_paciente]);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Archivos adjuntos
$stmt = $conn->prepare("
    SELECT a.*
    FROM archivos_reportes a
    INNER JOIN reportes_clinicos r ON r.id = a.reporte_id
    WHERE r.paciente_id = ?
");
$stmt->execute([$id_paciente]);
$archivos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Citas pasadas y futuras
$stmt = $conn->prepare("
    SELECT * FROM citas WHERE paciente_id = ? AND activo = 1
");
$stmt->execute([$id_paciente]);
$citas_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);

$citas = ["pasadas" => [], "futuras" => []];
$now = date("Y-m-d H:i:s");
foreach ($citas_raw as $cita) {
    if ($cita['fecha'] < $now) {
        $citas["pasadas"][] = $cita;
    } else {
        $citas["futuras"][] = $cita;
    }
}

// Bitácora (si el usuario lo ha editado o lo que sea)
$stmt = $conn->prepare("
    SELECT b.*, u.nombre AS usuario
    FROM bitacora b
    INNER JOIN usuarios u ON u.id = b.usuario_id
    WHERE descripcion LIKE ?
    ORDER BY b.fecha DESC
");
$stmt->execute(["%Paciente ID: $id_paciente%"]);
$bitacora = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Enviar respuesta
Response::json([
    "paciente" => $paciente,
    "contactos" => $contactos,
    "terapeuta_actual" => $terapeuta,
    "reportes" => $reportes,
    "archivos" => $archivos,
    "citas" => $citas,
    "bitacora" => $bitacora
]);
