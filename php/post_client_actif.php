<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$con = new ClientManager($pdo);
$nb_actifs = $con->getTotalClientActif();

echo json_encode([
    "clients_actifs" => $nb_actifs
]);