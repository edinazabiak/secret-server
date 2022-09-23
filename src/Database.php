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

    /**
     * Lekérjük a secret tábla összes sorát. 
     * 
     * @return array
     */
    public function getAllSecret() 
    {
        $sql = "SELECT * FROM secret";
        $stmt = $this->conn->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Létrehozunk egy új sort a secret táblában. 
     * 
     * @return string
     */
    public function createSecret($hash, $secretText, $createdAt, $expiresAt, $remainingViews) 
    {
        $sql = "INSERT INTO secret (hashCode, secretText, createdAt, expiresAt, remainingViews)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$hash, $secretText, $createdAt, $expiresAt, $remainingViews]);
        return $hash;
    }

    /**
     * Lekérjük a secret tábla egy bizonyos sorát, amelynek a hash-e megegyezik a paraméterként megadott értékkel. 
     * 
     * @param string $hash Lekérendő titok hash kódja.
     * 
     * @return array
     */
    public function getSecretByHash($hash) 
    {
        $sql = "SELECT * FROM secret WHERE hashcode=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$hash]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Frissítjük a megtekintendő titok lehetséges megtekintéseinek számát. 
     * 
     * @param string $hash Lekérendő titok hash kódja. 
     * 
     * @return void
     */
    public function updateRemainingView($hash)
    {
        $sql = "UPDATE secret SET remainingViews = remainingViews-1 WHERE hashCode=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$hash]);

    }
}