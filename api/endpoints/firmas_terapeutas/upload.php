<?php
require_once __DIR__ . '/../../auth/validate.php';  
require_once __DIR__ . '/../../config/database.php';  
require_once __DIR__ . '/../../core/Response.php';

$terapeuta_id = $_POST['terapeuta_id'] ?? null;
$file = $_FILES['firma'] ?? null;

if (!$terapeuta_id || !$file) {
    Response::error("Faltan datos requeridos: 'terapeuta_id' o archivo de firma", 422);
}

$uploadDir = __DIR__ . '/../../uploads/firmas/' . $terapeuta_id;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = 'firma_' . time() . '.png';
$filepath = $uploadDir . '/' . $filename;
$ruta_bd = 'uploads/firmas/' . $terapeuta_id . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    Response::error("Error al guardar la firma", 500);
}

$db = new Database();
$conn = $db->connect();


// Verifica si ya existe una firma para el terapeuta
$stmt = $conn->prepare("SELECT id FROM firmas_terapeutas WHERE terapeuta_id = ?");
$stmt->execute([$terapeuta_id]);

if ($stmt->rowCount() > 0) {
    $stmt = $conn->prepare("
        UPDATE firmas_terapeutas
        SET ruta_firma = ?, actualizado_en = NOW()
        WHERE terapeuta_id = ?
    ");
    $stmt->execute([$ruta_bd, $terapeuta_id]);
} else {
    $stmt = $conn->prepare("
        INSERT INTO firmas_terapeutas (terapeuta_id, ruta_firma)
        VALUES (?, ?)
    ");
    $stmt->execute([$terapeuta_id, $ruta_bd]);
}

Response::json(["mensaje" => "Firma guardada correctamente"]);
