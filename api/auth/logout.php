<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/Response.php';

$data = json_decode(file_get_contents("php://input"), true);
$token = $data['token'] ?? null;

if (!$token) {
    Response::error("Falta el token para cerrar sesión", 400);
}

$db = new Database();
$conn = $db->connect("mysql", "localhost", "mi_base", "root", "1234");

$stmt = $conn->prepare("DELETE FROM tokens WHERE token = ?");
$stmt->execute([$token]);

Response::json(["mensaje" => "Sesión cerrada correctamente"]);
