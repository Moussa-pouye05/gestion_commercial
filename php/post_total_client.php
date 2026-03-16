<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$con = new ClientManager($pdo);
$nb_client = $con->getTotalClient();

echo json_encode([
    "total_cl" => $nb_client
 ]);