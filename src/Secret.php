<?php

class Secret {

    private $hash;
    private $secretText;
    public $createdAt;
    public $expiresAt;
    private $remainingViews;
    private $db;

    public function __construct($secretText, $expiresAt, $remainingViews, $database) {
        $this->secretText = $secretText;
        $this->createdAt = date("Y-m-d H:i:s");
        $this->expiresAt = $expiresAt;
        $this->remainingViews = $remainingViews;
        $this->db = $database;
    }

    public function isValidExpiresAt() {
        if ($this->expiresAt < 0) {
            return false;
        } elseif ($this->expiresAt == 0) {
            $this->expiresAt = "";
        } else {
            $this->expiresAt = date("Y-m-d H:i:s", strtotime("+" . $this->expiresAt . " min"));
        }
        return true;
    }

    public function generateHash() {
        $valid = false;
        while(!$valid) {
            $this->hash = hash("md5", random_bytes(10));
            $valid = $this->isValidHash();
        }
        return $this->hash;
    }

    public function isValidHash() {
        foreach ($this->db->getAllSecret() as $row) {
            if ($row['hashCode'] == $this->hash) {
                return false;
            }
        }
        return true;
    }

    public function isValidRemainingViews() {
        if ($this->remainingViews < 0) {
            return false;
        }
        return true;
    }
}