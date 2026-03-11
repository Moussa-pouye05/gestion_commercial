<?php
session_start();
require_once "../config/config.php";
class UserManager{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    public function inscription(User $user): array{
        try {
            $verify = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
            $verify->execute([
                ":email" => $user->getEmail()
            ]);
            if($verify->fetch()){
                return [
                    'success' => false,
                    'message' => "L'email existe déjà"
                ];
            }

            $req = $this->pdo->prepare("INSERT INTO users (profile,nom,email,mot_de_passe,telephone,role) VALUES (:photo_profil,:nom,:email,:mot_de_passe,:telephone,:role)");
            $req->execute([
                ":photo_profil" => $user->getProfile(),
                ":nom" => $user->getNom(),
                ":email" => $user->getEmail(),
                ":mot_de_passe" => password_hash($user->getPassword(), PASSWORD_DEFAULT),
                
                ":telephone" => $user->getTelephone(),
                ":role" => $user->getRole()
            ]);
            return [
                'success' => true,
                'message' => "Inscription réussie",
                'id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $th) {
            return [
                'success' => false,
                'message' => "Erreur lors de l'inscription"
            ];
        }
    }
    public function connexion(User $user): array
{
    try {

        $req = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $req->execute([
            ":email" => $user->getEmail()
        ]);

        $datas = $req->fetch(PDO::FETCH_ASSOC);

        if (!$datas) {
            return [
                "success" => false,
                "message" => "Email incorrect"
            ];
        }

        if (!password_verify($user->getPassword(), $datas["mot_de_passe"])) {
            return [
                "success" => false,
                "message" => "Mot de passe incorrect"
            ];
        }

        // Session
        $_SESSION["user"] = [
            "id" => $datas["id"],
            "nom" => $datas["nom"],
            "email" => $datas["email"],
            "photo_profil" => $datas["profile"],
            "role" => $datas["role"],
            "telephone" => $datas["telephone"]
        ];
        
            return [
                "role" => $datas['role'],
                "success" => true
            
        ];
        

    } catch (PDOException $th) {
        return [
            "success" => false,
            "message" => "Erreur lors de la récupération des données"
        ];
    }
}
public function loadVendeur($limit, $offset, string $search = ""): array{
    
    try{
        $sql = "SELECT * FROM users WHERE role = 'vendeur'";
        $params = [];

        if ($search !== "") {
            $sql .= " AND (nom LIKE :search OR email LIKE :search OR telephone LIKE :search)";
            $params[":search"] = "%" . $search . "%";
        }

        $sql .= " LIMIT :limit OFFSET :offset";
        $req = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $req->bindValue($key, $value, PDO::PARAM_STR);
        }
        $req->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $req->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $req->execute();
        $result = [];
        while($datas = $req->fetch(PDO::FETCH_ASSOC)){
            $vendeurs = new User((int)$datas['id'],$datas['profile'],$datas['nom'],$datas['email'],$datas['mot_de_passe'],$datas['telephone'],$datas['role']);
            $result[] = $vendeurs;
        }
        return [
            "success" => true,
            "vendeurs" => $result
        ];
    }catch(PDOException $e){
            return [
                "success" => false,
                "message" => "Erreur lors de la récupération des données"
            ];
    }
}
//Compter le nombre utilisateur
public function countVendeurs(string $search = ""){
    if ($search === "") {
        $sql = "SELECT COUNT(*) FROM users WHERE role = 'vendeur'";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    $sql = "SELECT COUNT(*) FROM users WHERE role = 'vendeur' AND (nom LIKE :search OR email LIKE :search OR telephone LIKE :search)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ":search" => "%" . $search . "%"
    ]);
    return $stmt->fetchColumn();
}
//Modifier un user
public function editVendeur(User $user): bool{
    try{
        $password = trim($user->getPassword());

        if ($password === "") {
            $req = $this->pdo->prepare("UPDATE users SET profile=:profile,nom=:nom,email=:email,telephone=:tel WHERE id = :id");
            $req->execute([
                ":profile" =>$user->getProfile(),
                ":nom" => $user->getNom(),
                ":email" => $user->getEmail(),
                ":tel" => $user->getTelephone(),
                ":id" => (int)$user->getId()
            ]);
        } else {
            $req = $this->pdo->prepare("UPDATE users SET profile=:profile,nom=:nom,email=:email,mot_de_passe=:mot,telephone=:tel WHERE id = :id");
            $req->execute([
                ":profile" =>$user->getProfile(),
                ":nom" => $user->getNom(),
                ":email" => $user->getEmail(),
                ":mot" => password_hash($password, PASSWORD_DEFAULT),
                ":tel" => $user->getTelephone(),
                ":id" => (int)$user->getId()
            ]);
        }
    return true;
    }catch(PDOException $e){
    return false;
    } 
}
public function deleteVendeur($id): bool{
    try{
        $req = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $req->execute([
            ":id" => $id
        ]);
        return true;
    }catch(PDOException $e){
        return false;
    }
}
}
