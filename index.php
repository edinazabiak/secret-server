<?php 

declare(strict_types = 1);

// Minden fájl betöltése
spl_autoload_register(function ($class) {
    require __DIR__ . "/src/$class.php";
});

// JSON formátum beállítása
header("Content-type: application/json; charset=UTF-8");

// URI részeinek lekérése
$parts = explode("/", $_SERVER["REQUEST_URI"]);

// 404-es hiba dobása
if ($parts[1] != "secret-server") {
    http_response_code(404);
    exit;
}

// Ha van id megadva az URI-ban, lekérjük
$id = $parts[2] ?? null;

// Adatbázis létrehozása
$db = new Database();

$controller = new Controller($db);
$controller->processRequest($_SERVER["REQUEST_METHOD"], $id);