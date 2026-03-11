<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$con = new ProduitManager($pdo);
$nbr = $con->totalCategorie();

echo json_encode([
    "totalCategorie" => $nbr
]);