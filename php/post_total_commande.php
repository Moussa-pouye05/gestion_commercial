<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$con = new ClientManager($pdo);
$nb_cmd = $con->getTotalCommande();

echo json_encode([
    "total_commandes" => $nb_cmd
]);