<?php
session_start();
require_once "../config/config.php";

class FournisseurManager{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }
    public function createFournisseur(Fournisseur $fourn): array{
        try {
            $verify = $this->pdo->prepare("SELECT * FROM fournisseurs WHERE telephone = :telephone");
            $verify->execute([
                ":telephone" => $fourn->getTelephone()
            ]);
            if($verify->fetch()){
                return [
                    'success' => false,
                    'message' => "Ce fournisseur existe déjà"
                ];
            }

            $req = $this->pdo->prepare("INSERT INTO fournisseurs (nom,adresse,telephone,id_admin) VALUES (:nom,:adresse,:telephone,:id_admin)");
            $req->execute([
                ":nom" => $fourn->getNom(),
                ":adresse" => $fourn->getAdresse(),
                ":telephone" => $fourn->getTelephone(),
                ":id_admin" => $_SESSION['user']['id'] 
            ]);
            return [
                'success' => true,
                'message' => "Fournisseur créer avec succes",
                'id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $th) {
            return [
                'success' => false,
                'message' => "Erreur lors de la creation du fournisseur"
            ];
        }
    }

    public function loadFournisseur($limit, $offset, string $search = ""): array{
    
    try{
        $sql = "SELECT * FROM fournisseurs";
        $params = [];

        if ($search !== "") {
            $sql .= " WHERE nom LIKE :search OR telephone LIKE :search OR adresse LIKE :search";
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
            $fourns = new Fournisseur((int)$datas['id'],$datas['nom'],$datas['telephone'],$datas['adresse']);
            $result[] = $fourns;
        }
        return [
            "success" => true,
            "fournisseurs" => $result
        ];
    }catch(PDOException $e){
            return [
                "success" => false,
                "message" => "Erreur lors de la récupération des données"
            ];
    }
}
//Compter le nombre fournisseur
public function countFournisseurs(string $search = ""){
    if ($search === "") {
        $sql = "SELECT COUNT(*) FROM fournisseurs ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    $sql = "SELECT COUNT(*) FROM fournisseurs WHERE nom LIKE :search OR telephone LIKE :search OR adresse LIKE :search";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ":search" => "%" . $search . "%"
    ]);
    return $stmt->fetchColumn();
}

public function editFournisseur(Fournisseur $fourn): bool{
    try{
        $req = $this->pdo->prepare("UPDATE fournisseurs SET nom=:nom,adresse=:adresse,telephone=:telephone WHERE id=:id");
        $req->execute([
            ":nom" => $fourn->getNom(),
            ":telephone" => $fourn->getTelephone(),
            ":adresse" => $fourn->getAdresse(),
            ":id" => $fourn->getId()
        ]);
        return true;
    }catch(PDOException $e){
        return false;
    }
}

public function deleteFournisseur($id): bool{
    try{
        $req = $this->pdo->prepare("DELETE FROM fournisseurs WHERE id = :id");
        $req->execute([
            ":id" =>$id
        ]);
        return true;
    }catch(PDOException $e){
        return false;
    }
}
}