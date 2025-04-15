<?php
require_once __DIR__ . '/../../auth/validate.php';
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id_paciente = $_GET['id_paciente'] ?? null;
if (!$id_paciente) {
    die("Falta el parámetro 'id_paciente'");
}

$db = new Database();
$conn = $db->connect();


$stmt = $conn->prepare("SELECT * FROM pacientes WHERE id = ?");
$stmt->execute([$id_paciente]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$paciente) die("Paciente no encontrado");

$stmt = $conn->prepare("SELECT * FROM reportes_clinicos WHERE paciente_id = ?");
$stmt->execute([$id_paciente]);
$reportes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// HTML inicial
$html = "<h1>Expediente Clínico</h1>";
$html .= "<h2>Paciente: {$paciente['nombre']}</h2>";
$html .= "<p>CUI: {$paciente['cui']}</p>";
$html .= "<hr>";
$html .= "<h3>Reportes Clínicos</h3>";

foreach ($reportes as $r) {
    $html .= "<div style='margin-bottom:10px'>";
    $html .= "<strong>Fecha:</strong> {$r['fecha']}<br>";
    $html .= "<strong>Motivo:</strong> {$r['motivo']}<br>";
    $html .= "<strong>Diagnóstico:</strong> {$r['diagnostico']}<br>";
    $html .= "<strong>Recomendaciones:</strong> {$r['recomendaciones']}<br>";
    $html .= "<hr>";
    $html .= "</div>";
}

// Obtener firma del terapeuta
$stmt = $conn->prepare("
    SELECT t.id AS terapeuta_id, f.ruta_firma, t.nombre
    FROM terapeutas t
    LEFT JOIN firmas_terapeutas f ON f.terapeuta_id = t.id
    WHERE t.id = (
        SELECT terapeuta_id FROM reportes_clinicos 
        WHERE paciente_id = ? ORDER BY fecha DESC LIMIT 1
    )
");
$stmt->execute([$id_paciente]);
$firma = $stmt->fetch(PDO::FETCH_ASSOC);

// Añadir firma al HTML si existe
if ($firma && $firma['ruta_firma']) {
    $html .= "<br><br><p><strong>Firma del terapeuta responsable:</strong></p>";
    $html .= "<img src='../../" . $firma['ruta_firma'] . "' width='200' alt='Firma'>";
    $html .= "<p><em>" . htmlspecialchars($firma['nombre']) . "</em></p>";
}

// Generar PDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("expediente_paciente_{$id_paciente}.pdf", ["Attachment" => false]);
exit;
