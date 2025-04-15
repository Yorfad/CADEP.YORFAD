<?php
class Response {
    /**
     * Devuelve una respuesta JSON con código de estado HTTP
     * @param array $data
     * @param int $status
     */
    public static function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Atajo para errores
     */
    public static function error($message, $status = 400) {
        self::json(['error' => $message], $status);
    }
}
