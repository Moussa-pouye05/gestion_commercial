<?php
session_start();
require_once "../config/config.php";
class ClientManager{
    private PDO $pdo;

    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    public function createClient(Client $client): array{
        try {
            $verify = $this->pdo->prepare("SELECT * FROM clients WHERE telephone = :telephone");
            $verify->execute([
                ":telephone" => $client->getTelephone()
            ]);
            if($verify->fetch()){
                return [
                    'success' => false,
                    'message' => "Ce client existe déjà"
                ];
            }

            $req = $this->pdo->prepare("INSERT INTO clients (nom,telephone,adresse,id_user) VALUES (:nom,:telephone,:adresse,:id_user)");
            $req->execute([
                ":nom" => $client->getNom(),
                ":telephone" => $client->getTelephone(),
                ":adresse" => $client->getAdresse(),
                ":id_user" => $_SESSION['user']['id'] 
            ]);
            return [
                'success' => true,
                'message' => "Client créer avec succes",
                'id' => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $th) {
            return [
                'success' => false,
                'message' => "Erreur lors de l'inscription"
            ];
        }
    }

    public function loadClient($limit, $offset, string $search = ""): array{
    
    try{
        $sql = "SELECT * FROM clients";
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
            $clients = new Client((int)$datas['id'],$datas['nom'],$datas['telephone'],$datas['adresse']);
            $result[] = $clients;
        }
        return [
            "success" => true,
            "clients" => $result
        ];
    }catch(PDOException $e){
            return [
                "success" => false,
                "message" => "Erreur lors de la récupération des données"
            ];
    }
}
//Compter le nombre utilisateur
public function countClients(string $search = ""){
    if ($search === "") {
        $sql = "SELECT COUNT(*) FROM clients ";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchColumn();
    }

    $sql = "SELECT COUNT(*) FROM clients WHERE nom LIKE :search OR telephone LIKE :search OR adresse LIKE :search";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ":search" => "%" . $search . "%"
    ]);
    return $stmt->fetchColumn();
}
public function editClient(Client $client): bool{
    try{
        $req = $this->pdo->prepare("UPDATE clients SET nom=:nom,telephone=:telephone,adresse=:adresse WHERE id=:id");
        $req->execute([
            ":nom" => $client->getNom(),
            ":telephone" => $client->getTelephone(),
            ":adresse" => $client->getAdresse(),
            ":id" => $client->getId()
        ]);
        return true;
    }catch(PDOException $e){
        return false;
    }
}
public function deleteClient($id): bool{
    try{
        $req = $this->pdo->prepare("DELETE FROM clients WHERE id = :id");
        $req->execute([
            ":id" =>$id
        ]);
        return true;
    }catch(PDOException $e){
        return false;
    }
}
}