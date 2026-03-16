<?php
//session_start();
header('Content-Type: application/json');
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});



if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

//$pdo = new PDO("mysql:host=localhost;dbname=gestion_stock;charset=utf8", "root", "12345");
$clientManager = new ClientManager($pdo);

$result = $clientManager->loadClient(100, 0, "");
echo json_encode($result);

