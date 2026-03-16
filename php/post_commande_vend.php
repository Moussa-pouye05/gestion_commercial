<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});
$con = new CommandeManager($pdo);
$nb_vend = $con->getCommandeVendeur();

echo json_encode([
    "nb_commande_vendeur" => $nb_vend
]);