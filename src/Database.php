<?php

class Database {

    private $host = "localhost";
    private $dbname = "secretserver";
    private $user = "root";
    private $password = "";
    private $conn;

    public function __construct()
    {
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->dbname;
            $this->conn = new PDO($dsn, $this->user, $this->password, [PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_STRINGIFY_FETCHES => false]);
        } catch(PDOException $e) {
            http_response_code(500);
            echo json_encode([
                "code" => $e->getCode(),
                "message" => $e->getMessage(), 
                "file" => $e->getFile(),
                "line" => $e->getLine()
            ]);
        }
    }

    public function getAllSecret() {
        $sql = "SELECT * FROM secret";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createSecret($hash, $secretText, $createdAt, $expiresAt, $remainingViews) {
        $sql = "INSERT INTO secret (hashCode, secretText, createdAt, expiresAt, remainingViews)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$hash, $secretText, $createdAt, $expiresAt, $remainingViews]);
        return $this->conn->lastInsertId();
    }
}