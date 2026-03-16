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

public function getClient(): array{
    try {
        $req = $this->pdo->query("SELECT * FROM clients");
        $result = [];
        while($datas = $req->fetch(PDO::FETCH_ASSOC)){
            $clients = new Client((int)$datas['id'],$datas['nom'],$datas['telephone'],$datas['adresse']);
            $result[] = $clients;
        }
        return [
                "success" => true,
                "clients" => $result
            ];

    } catch (PDOException $e) {
        return [
            "success" => false,
            "message" => "Erreur" . $e->getMessage()
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

public function getTotalClient(): int{
    try {
        $req = $this->pdo->prepare("SELECT COUNT(*) as totalClient FROM clients ");
        $req->execute();
        $nb_cl = $req->fetch(PDO::FETCH_ASSOC)['totalClient'] ?? 0;
        return $nb_cl;
    } catch (PDOException $e) {
        return 0;
    }
}
public function getTotalClientActif(): int{
    try {
        $req = $this->pdo->prepare("SELECT COUNT(*) as clients_actifs
            FROM clients c
            WHERE EXISTS(
            SELECT 1 
            FROM commandes cmd
            WHERE cmd.id_client = c.id )
            ");
        $req->execute();
        $nb_actif = $req->fetch(PDO::FETCH_ASSOC)['clients_actifs'] ?? 0;
        return $nb_actif;    
    } catch (PDOException $e) {
        return 0;
    }
}

public function getTotalCommande(): int{
    try {
        $req = $this->pdo->prepare("SELECT COUNT(*) AS total_commandes FROM commandes WHERE date_commande >= CURDATE() AND date_commande < CURDATE() + INTERVAL 1 DAY");
        $req->execute();
        $nb_cmd = $req->fetch(PDO::FETCH_ASSOC)['total_commandes'] ?? 0;
        return $nb_cmd;
    } catch (PDOException $e) {
        return 0;
    }
}

public function fidelite(){
    try {
        $req = $this->pdo->prepare("SELECT ROUND(
(
    SELECT COUNT(*) 
    FROM (
        SELECT id_client
        FROM commandes
        GROUP BY id_client
        HAVING COUNT(id) > 1
    ) t
)
/
(
    SELECT COUNT(DISTINCT id_client)
    FROM commandes
)
* 100
, 0) AS taux_fidelite;");
$req->execute();
$taux = $req->fetch(PDO::FETCH_ASSOC)['taux_fidelite'] ?? 0;
return $taux;
    } catch (PDOException $e) {
        return 0;
    }
}
}