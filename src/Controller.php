<?php

class Controller {
 
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    public function processRequest($method, $id = null) {
        if ($id != null) {
            $this->manageRequestWithID($method, $id);
        } else {
            $this->manageRequestWithoutID($method);
        }
    }

    private function manageRequestWithID($method, $id) {

    }

    public function manageRequestWithoutID($method) {
        switch($method) {
            case "GET": 
                echo json_encode($this->db->getAllSecret());
                break;
            case "POST": 
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $secret = new Secret($data["secret"], $data["expireAfter"], $data["expireAfterViews"], $this->db);
                if (!is_int($data["expireAfter"]) || !is_int($data["expireAfterViews"]) || $secret->isValidExpiresAt() == false || $secret->isValidRemainingViews() == false) {
                    http_response_code(405);
                    echo json_encode(["description" => "Invalid input"]);
                    break;
                } else {
                    $id = $this->db->createSecret($secret->generateHash(), $data["secret"], $secret->createdAt, $secret->expiresAt, $data["expireAfterViews"]);
                    echo json_encode([
                        "description" => "Successful operation",
                        "id" => $id
                    ]);
                    break;
                }
        }
    }

}