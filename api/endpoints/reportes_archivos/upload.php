<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../core/Response.php';

$reporte_id = $_POST['reporte_id'] ?? null;
$categoria = $_POST['categoria'] ?? null;
$file = $_FILES['archivo'] ?? null;

if (!$reporte_id || !$categoria || !$file) {
    Response::error("Faltan datos requeridos: 'reporte_id', 'categoria' o archivo", 422);
}

$allowed = ['examen', 'receta', 'foto', 'otro'];
if (!in_array($categoria, $allowed)) {
    Response::error("Categoría no válida", 422);
}

$uploadDir = __DIR__ . '/../../uploads/reportes/' . $reporte_id;
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$filename = basename($file['name']);
$filepath = $uploadDir . '/' . $filename;
$ruta_bd = 'uploads/reportes/' . $reporte_id . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    Response::error("Error al mover el archivo", 500);
}

$db = new Database();
$conn = $db->connect();


try {
    $stmt = $conn->prepare("
        INSERT INTO archivos_reportes (reporte_id, categoria, nombre_archivo, ruta_archivo, tipo_mime)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $reporte_id,
        $categoria,
        $filename,
        $ruta_bd,
        $file['type']
    ]);

    Response::json(["mensaje" => "Archivo subido correctamente"]);

} catch (PDOException $e) {
    Response::error("Error al guardar en la base de datos: " . $e->getMessage(), 500);
}
