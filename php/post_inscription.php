<?php
//header("Content-Type: application/json");
require_once "../config/config.php";
spl_autoload_register(function($class){
    require_once "../classes/".$class.".php";
});

$nom = trim($_POST['nom'] ?? "");
$email = trim($_POST['email'] ?? "");
$password = trim($_POST['password'] ?? "");
$telephone = trim($_POST['telephone'] ?? "");
$role = trim($_POST['role'] ?? "");

$reponse = ["success" => false,"message" => ""];
if( empty($nom)){
    $reponse["message"] = "Le nom est requis";
}elseif(empty($email)){
    $reponse["message"] = "L'email est requis";
}elseif(empty($password)){
    $reponse['message'] = "Le mot de passe est requis";
}elseif(empty($telephone)){
    $reponse['message'] = "Le telephone est requis";
}elseif(!isset($_FILES["profile_picture"]) || $_FILES["profile_picture"]["error"] !== 0){
    $reponse["message"] = "Le profile est requis";
}else{
    $uploadDir = "../uploads/profiles/";

    // créer le dossier s'il n'existe pas
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $tmpName = $_FILES["profile_picture"]["tmp_name"];
    $originalName = $_FILES["profile_picture"]["name"];
    $size = $_FILES["profile_picture"]["size"];

    // extensions autorisées
    $allowed = ["jpg", "jpeg", "png", "webp"];
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        $reponse["message"] = "Format invalide. Formats acceptés : jpg, jpeg, png, webp";
        echo json_encode($reponse);
        exit;
    }

    // taille max 2MB
    if ($size > 2 * 1024 * 1024) {
        $reponse["message"] = "Image trop grande (max 2MB)";
        echo json_encode($reponse);
        exit;
    }

    // générer un nom unique
    $newFileName = uniqid("profile_", true) . "." . $ext;

    // chemin final
    $profile = $uploadDir . $newFileName;

    // déplacer le fichier
    if (!move_uploaded_file($tmpName, $profile)) {
        $reponse["message"] = "Erreur lors de l'envoi de l'image";
        echo json_encode($reponse);
        exit;
    }
    $user = new User(null,$profile, $nom, $email, $password, $telephone,$role);
    $userManager = new UserManager($pdo);
    $reponse = $userManager->inscription($user);
    
}
//return json
echo json_encode($reponse);
