<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$success = false;
$message = "";
$id = trim($_POST['id'] ?? "");
if(!is_numeric($id) || empty($id)){
    $message = "Il faut un id";
}else{

$con = new ClientManager($pdo);
$result = $con->deleteclient($id);
if($result === false){
    $message = "Erreur lors de la suppression du client";
}else{
    $success = true;
    $message = "Client supprimé avec succès";
}
}
//retoure json
echo json_encode([
    "success" => $success,
    "message" => $message
]);