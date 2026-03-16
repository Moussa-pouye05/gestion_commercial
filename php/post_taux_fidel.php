<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$con = new ClientManager($pdo);
$taux = $con->fidelite();

echo json_encode([
    "taux_fidelite" => $taux
]);