<?php

class Database {
    private $driver = "mysql";
    private $host = "127.0.0.1";
    private $port = "3308";
    private $dbname = "hospital";
    private $username = "admin";
    private $password = "admin123";
    private $charset = "utf8mb4";
    private $conn; // ← AQUÍ sí está declarada

    public function connect() {
        try {
            $dsn = "{$this->driver}:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage(); // útil para debug
            return null;
        }
    }
}
?>

