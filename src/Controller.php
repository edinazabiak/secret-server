<?php

class Controller {
 
    private $db;

    public function __construct($database)
    {
        $this->db = $database;
    }

    /** 
     * URI vizsgálata, ketté bontjuk a folyamatot az azonosító megléte alapján. 
     * 
     * @param string $method A metódus, amit használunk.
     * @param string $hash Lehetséges azonosító az URI végén.
     * 
     * @return void
     */
    public function processRequest($method, $hash = null) 
    {
        if ($hash != null) {
            $this->manageRequestWithID($method, $hash);
        } else {
            $this->manageRequestWithoutID($method);
        }
    }

    /**
     * Ha van azonosító, GET metódus írunk.
     * Ha nincs a megadott azonosítóval titok, vagy lejárt az ideje/lehetséges megtekintéseinek száma, akkor 404-es kóddal térünk vissza.
     * Egyébként pedig csökkentjük a lehetséges megtekintések számát és kiíratjuk a titok adatait.
     * 
     * @param string $method A metódus, amit használunk.
     * @param string $hash Azonosító az URI végén.
     * 
     * @return void
     */
    private function manageRequestWithID($method, $hash) 
    {
        switch($method) {
            case "GET": 
                $result = $this->db->getSecretByHash($hash);
                $secret = new Secret();
                if (empty($result) || $secret->expiredTime($result['expiresAt']) == false || $secret->expiredRemainingView($result['remainingViews']) == false) {
                    http_response_code(404);
                    echo json_encode(["description" => "Secret not found"]);
                }
                else {
                    $this->db->updateRemainingView($hash);
                    echo json_encode($this->db->getSecretByHash($hash));
                }
                break;
        }
    }

    /** 
     * Ha nincs azonosító, GET és POST metódus is megírásra kerül. 
     * Azonosító nélkül lekérjük GET-tel az összes titkot, POST-tal pedig újat adunk hozzá az adatbázishoz. 
     * POST esetében ellenőrizzük, hogy megfelelő adatokat próbálunk-e felvenni. Ha nem, 405-ös hibát dobunk, egyébként 201-est, és sikeresen létrehozzuk az új titkot. 
     * 
     * @param string $method A metódus, amit használunk.
     * 
     * @return void
     */
    public function manageRequestWithoutID($method) 
    {
        switch($method) {
            case "GET": 
                echo json_encode($this->db->getAllSecret());
                break;
            case "POST": 
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $secret = new Secret();
                $secret->setSecret($data["secret"], $data["expireAfter"], $data["expireAfterViews"], $this->db);
                if (!is_int($data["expireAfter"]) || !is_int($data["expireAfterViews"]) || $secret->isValidExpiresAt() == false || $secret->isValidRemainingViews() == false) {
                    http_response_code(405);
                    echo json_encode(["description" => "Invalid input"]);
                    break;
                } else {
                    $id = $this->db->createSecret($secret->generateHash(), $data["secret"], $secret->createdAt, $secret->expiresAt, $data["expireAfterViews"]);
                    http_response_code(201);
                    echo json_encode([
                        "description" => "Successful operation",
                        "id" => $id
                    ]);
                    break;
                }
        }
    }

}