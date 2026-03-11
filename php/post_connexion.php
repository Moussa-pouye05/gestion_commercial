<?php
header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$pass = trim($_POST['password'] ?? "");
$email = trim($_POST['email'] ?? "");

$reponse = [
    'success' => false,
    'message' => "",
    'role' => ""
];

if( empty($pass)){
    $reponse['message'] = "Entrer votre mot de passe";
}elseif( empty($email) ){
    $reponse['message'] = "Entrer votre email";
}else{
        
        $user = new User(null, "", "", $email, $pass, "", "");
        $um = new UserManager($pdo);
        $reponse = $um->connexion($user);
}

echo json_encode($reponse);
