<?php
require_once __DIR__ . '/../../auth/validate.php';  
require_once __DIR__ . '/../../config/database.php';  
require_once __DIR__ . '/../../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$terapeuta_id = $data['terapeuta_id'] ?? null;

if (!$terapeuta_id) {
    Response::error("Falta el ID del terapeuta", 400);
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("SELECT ruta_firma FROM firmas_terapeutas WHERE terapeuta_id = ?");
$stmt->execute([$terapeuta_id]);
$firma = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$firma) {
    Response::error("No existe una firma para este terapeuta", 404);
}

$ruta = __DIR__ . '/../../' . $firma['ruta_firma'];
if (file_exists($ruta)) {
    unlink($ruta);
}

$stmt = $conn->prepare("DELETE FROM firmas_terapeutas WHERE terapeuta_id = ?");
$stmt->execute([$terapeuta_id]);

Response::json(["mensaje" => "Firma eliminada correctamente"]);
