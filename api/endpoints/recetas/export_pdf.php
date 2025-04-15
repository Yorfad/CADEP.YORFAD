<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = $_GET['id'] ?? null;
if (!$id) die("Falta el ID de la receta");

$db = new Database();
$conn = $db->connect();


// Obtener receta
$stmt = $conn->prepare("
    SELECT r.*, p.nombre AS paciente, t.nombre AS terapeuta, f.ruta_firma
    FROM recetas_medicas r
    JOIN pacientes p ON r.paciente_id = p.id
    JOIN terapeutas t ON r.terapeuta_id = t.id
    LEFT JOIN firmas_terapeutas f ON f.terapeuta_id = t.id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$receta = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$receta) die("Receta no encontrada");

$html = "<h1>Receta Médica</h1>";
$html .= "<p><strong>Paciente:</strong> {$receta['paciente']}</p>";
$html .= "<p><strong>Fecha:</strong> {$receta['fecha']}</p>";
$html .= "<hr>";
$html .= "<p><strong>Descripción:</strong><br>{$receta['descripcion']}</p>";
if ($receta['instrucciones']) {
    $html .= "<p><strong>Instrucciones:</strong><br>{$receta['instrucciones']}</p>";
}
if ($receta['ruta_firma']) {
    $html .= "<br><p><strong>Firma del terapeuta:</strong></p>";
    $html .= "<img src='../../{$receta['ruta_firma']}' width='200'><br>";
    $html .= "<p><em>{$receta['terapeuta']}</em></p>";
}

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("receta_medica_{$id}.pdf", ["Attachment" => false]);
exit;
