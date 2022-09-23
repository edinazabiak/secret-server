<?php

class Secret {

    private $hash;
    private $secretText;
    public $createdAt;
    public $expiresAt;
    private $remainingViews;
    private $db;

    public function __construct() 
    {}

    /**
     * A titok objektum adatainak beállítása. 
     * 
     * @param string $secretText A titok szövegét tartalmazza. 
     * @param mixed $expiresAt A titok lejáratának dátuma, ami kezdetben egy szám, később év-hónap-nap óra:perc:másodperc formátumban szerepel.
     * @param int $remainingViews Egy szám, amely azt mondja meg, maximum hány alkalommal tekinthető meg a titok. 
     * @param object $database Az adatbázis objektum, amely az elérhetőségben segít. 
     * 
     * @return void
     */
    public function setSecret($secretText, $expiresAt, $remainingViews, $database)
    {
        $this->secretText = $secretText;
        $this->createdAt = date("Y-m-d H:i:s");
        $this->expiresAt = $expiresAt;
        $this->remainingViews = $remainingViews;
        $this->db = $database;
    }

    /**
     * Megvizsgáljuk, hogy a létrehozásnál megadott lejárati dátumhoz szükséges érték megfelelő-e. 
     * 
     * @return bool
     */
    public function isValidExpiresAt() 
    {
        if ($this->expiresAt < 0) {
            return false;
        } elseif ($this->expiresAt == 0) {
            $this->expiresAt = "";
        } else {
            $this->expiresAt = date("Y-m-d H:i:s", strtotime("+" . $this->expiresAt . " min"));
        }

        return true;
    }

    /**
     * Generálunk egy random hash kódot a létrehozandó titoknak. 
     * 
     * @return mixed
     */
    public function generateHash() 
    {
        $valid = false;
        while(!$valid) {
            $this->hash = hash("md5", random_bytes(10));
            $valid = $this->isValidHash();
        }

        return $this->hash;
    }

    /**
     * Ellenőrizzük, hogy az adott hash kód nem létezik-e már az adatbázisban. 
     * 
     * @return bool
     */
    public function isValidHash() 
    {
        foreach ($this->db->getAllSecret() as $row) {
            if ($row['hashCode'] == $this->hash) {
                return false;
            }
        }

        return true;
    }

    /**
     * Megvizsgáljuk, hogy a létrehozásnál megadott lehetséges megtekintéseknek száma megfelelő érték-e (vagyis legalább 0). 
     * 
     * @return bool
     */
    public function isValidRemainingViews() 
    {
        if ($this->remainingViews < 0) {
            return false;
        }

        return true;
    }

    /**
     * Megvizsgáljuk, hogy a megtekintendő titok lejárati dátuma elmúlt-e már a megtekintés időpontjában.
     * 
     * @param string $expiresAt A titok Datetime formátumban megadott lejárati dátuma.
     * 
     * @return bool 
     */
    public function expiredTime($expiresAt)
    {
        if ($expiresAt == null) {
            return true;
        } elseif (date("Y-m-d H:i:s") > $expiresAt) {
            return false;
        }

        return true;
    }

    /**
     * Megvizsgáljuk, hogy a megtekintendő titok lehetséges megtekintéseinek a száma kevesebb-e már, mint 1. Ha igen, akkor a titok többé nem elérhető. 
     * 
     * @param int $remainingViews A titok lehetséges megtekintéseinek száma.
     * 
     * @param bool
     */
    public function expiredRemainingView($remainingViews)
    {
        if ($remainingViews < 1) {
            return false;
        }

        return true;
    }
}