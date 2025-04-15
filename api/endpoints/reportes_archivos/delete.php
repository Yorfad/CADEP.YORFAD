<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$id = $data['id'] ?? null;

if (!$id) {
    Response::error("Falta el ID del archivo", 400);
}

$db = new Database();
$conn = $db->connect();


// Obtener ruta para eliminar del sistema de archivos
$stmt = $conn->prepare("SELECT ruta_archivo FROM archivos_reportes WHERE id = ?");
$stmt->execute([$id]);
$archivo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$archivo) {
    Response::error("Archivo no encontrado", 404);
}

$ruta = __DIR__ . '/../../' . $archivo['ruta_archivo'];
if (file_exists($ruta)) {
    unlink($ruta);
}

// Eliminar de la base de datos
$stmt = $conn->prepare("DELETE FROM archivos_reportes WHERE id = ?");
$stmt->execute([$id]);

Response::json(["mensaje" => "Archivo eliminado correctamente"]);
