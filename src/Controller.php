<?php

class Controller {

    private Database $db;

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
                break;
        }
    }

}