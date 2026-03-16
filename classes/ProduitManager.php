<?php
session_start();
require_once "../config/config.php";

class ProduitManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function createCat(Categorie $cat): array
    {
        try {
            $verify = $this->pdo->prepare("SELECT id FROM categorie WHERE nom = :nom");
            $verify->execute([
                ":nom" => $cat->getNom()
            ]);
            if ($verify->fetch()) {
                return [
                    "success" => false,
                    "message" => "Cette categorie existe deja"
                ];
            }

            $req = $this->pdo->prepare("INSERT INTO categorie (nom) VALUES (:nom)");
            $req->execute([
                ":nom" => $cat->getNom()
            ]);

            return [
                "success" => true,
                "message" => "Categorie ajoutee avec succes",
                "id" => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur lors de la creation de la categorie"
            ];
        }
    }

    public function getCategorie(): array
    {
        try {
            $req = $this->pdo->query("SELECT * FROM categorie");
            $result = [];
            while ($datas = $req->fetch(PDO::FETCH_ASSOC)) {
                $cat = new Categorie((int) $datas['id'], $datas['nom']);
                $result[] = $cat;
            }

            return [
                "success" => true,
                "categorie" => $result
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur"
            ];
        }
    }
    public function getProduit(): array{
        try {
            $req = $this->pdo->query("SELECT * FROM poduits");
            $result = [];
            while($datas = $req->fetch(PDO::FETCH_ASSOC)){
                $produits = new Produit(
                    (int) $datas['id'],
                    $datas['image'],
                    $datas['nom'],
                    (float) $datas['prix_vente'],
                    (float) $datas['prix_achat'],
                    (int) $datas['quantite'],
                    (int) $datas['id_categorie'],
                    $datas['code_barre']
                );
                $result[] = $produits;
            }
            return [
                "success" => true,
                "produits" => $result
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur"
            ];
        }
    }
    public function genererCodeProduit(): string
    {
        $req = $this->pdo->query("SELECT MAX(id) as max_id FROM poduits");
        $data = $req->fetch(PDO::FETCH_ASSOC);
        $next = ((int) ($data['max_id'] ?? 0)) + 1;

        return "PRD" . str_pad((string) $next, 4, "0", STR_PAD_LEFT);
    }

    private function codeBarreExiste(string $codeBarre): bool
    {
        $verify = $this->pdo->prepare("SELECT id FROM poduits WHERE code_barre = :code LIMIT 1");
        $verify->execute([":code" => $codeBarre]);
        return (bool) $verify->fetch(PDO::FETCH_ASSOC);
    }

    public function createProduit(Produit $produit): array
    {
        try {
            $codeBarre = trim($produit->getCodeBarre());
            if ($codeBarre === "") {
                $codeBarre = $this->genererCodeProduit();
            }

            if ($this->codeBarreExiste($codeBarre)) {
                return [
                    "success" => false,
                    "message" => "Ce code barre existe deja"
                ];
            }

            $smtt = $this->pdo->prepare("INSERT INTO poduits (image, nom, prix_vente, prix_achat, quantite, seuilCritique,stock_min, id_categorie, id_admin, code_barre)
                                        VALUES (:img, :nom, :vente, :achat, :quantite, :seuil,:stock_min, :id_cat, :id_admin, :code)");
            $smtt->execute([
                ":img" => $produit->getPhoto(),
                ":nom" => $produit->getNom(),
                ":vente" => $produit->getPrixVente(),
                ":achat" => $produit->getPrixAchat(),
                ":quantite" => $produit->getQuantite(),
                ":seuil" => 5,
                ":stock_min" => 100,
                ":id_cat" => $produit->getCatId(),
                ":id_admin" => $_SESSION['user']['id'],
                ":code" => $codeBarre
            ]);

            return [
                "success" => true,
                "message" => "Produit cree avec succes",
                "id" => $this->pdo->lastInsertId()
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur lors de la creation du produit: " . $e->getMessage(),
                "error" => $e->getMessage()
            ];
        }
    }
    public function loadProduit(int $limit, int $offset = 0, string $search = "", int $categorieId = 0): array
    {
        try {
            $sql = "SELECT * FROM poduits";
            $where = [];
            $params = [];

            if ($search !== "") {
                $where[] = "(nom LIKE :search OR code_barre LIKE :search)";
                $params[":search"] = "%" . $search . "%";
            }

            if ($categorieId > 0) {
                $where[] = "id_categorie = :categorie_id";
                $params[":categorie_id"] = $categorieId;
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }

            $sql .= " ORDER BY quantite DESC LIMIT :limit OFFSET :offset";
            $req = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $req->bindValue($key, $value, PDO::PARAM_STR);
            }

            $req->bindValue(':limit', $limit, PDO::PARAM_INT);
            $req->bindValue(':offset', $offset, PDO::PARAM_INT);
            $req->execute();

            $result = [];
            while ($datas = $req->fetch(PDO::FETCH_ASSOC)) {
                $produits = new Produit(
                    (int) $datas['id'],
                    $datas['image'],
                    $datas['nom'],
                    (float) $datas['prix_vente'],
                    (float) $datas['prix_achat'],
                    (int) $datas['quantite'],
                    (int) $datas['id_categorie'],
                    $datas['code_barre']
                );

                $result[] = $produits;
            }

            return [
                'success' => true,
                'produits' => $result
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur lors de la recuperation des produits" . $e->getMessage()
            ];
        }
    }

    public function countProduits(string $search = "", int $categorieId = 0): int
    {
        $sql = "SELECT COUNT(*) FROM poduits";
        $where = [];
        $params = [];

        if ($search !== "") {
            $where[] = "(nom LIKE :search OR code_barre LIKE :search)";
            $params[":search"] = "%" . $search . "%";
        }

        if ($categorieId > 0) {
            $where[] = "id_categorie = :categorie_id";
            $params[":categorie_id"] = $categorieId;
        }

        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $req = $this->pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $req->bindValue($key, $value, PDO::PARAM_STR);
        }
        $req->execute();

        return (int) $req->fetchColumn();
    }

    public function getTotalProduit():int{
        try {
            $req = $this->pdo->prepare("SELECT COUNT(*) AS total_produit FROM poduits");
            $req->execute();
            $nbr = (int)$req->fetch(PDO::FETCH_ASSOC)["total_produit"] ?? 0;
            return $nbr;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function stockFaible(): int{
        try {
            $req = $this->pdo->prepare("SELECT COUNT(*) as stocFaible FROM poduits WHERE quantite <= seuilCritique");
            $req->execute();
            $nbStockFail = $req->fetch(PDO::FETCH_ASSOC)['stocFaible'] ?? 0;
            return $nbStockFail;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function totalCategorie(){
        try {
            $req = $this->pdo->prepare("SELECT COUNT(*) AS totalCat FROM categorie");
            $req->execute();
            $total = $req->fetch(PDO::FETCH_ASSOC)['totalCat'] ?? 0;
            return $total;
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function totalAmountProduct(): int{
        try {
            $req = $this->pdo->prepare("SELECT SUM(prix_vente*quantite) as somme FROM poduits");
            $req->execute();
            $total = $req->fetch(PDO::FETCH_ASSOC)['somme'] ?? 0;
            return $total;
        } catch (PDOException $e) {
            return 0;
        }
    }
public function updateProduit(Produit $produit): array{
    try {
        $req = $this->pdo->prepare("UPDATE poduits SET image=:img,nom=:nom,prix_achat=:achat,prix_vente=:vente,quantite=:quantite,id_categorie=:id_cat,code_barre=:code WHERE id=:id");
        $req->execute([
            ":id" =>$produit->getId(),
            ":img" => $produit->getPhoto(),
            ":nom" => $produit->getNom(),
            ":achat" => $produit->getPrixAchat(),
            ":vente" => $produit->getPrixVente(),
            ":quantite" => $produit->getQuantite(),
            ":id_cat" => $produit->getCatId(),
            ":code" => $produit->getCodeBarre()
        ]);
        return [
            "success" => true,
            "message" => "Modification reussi"
        ];
    } catch (PDOException $e) {
        return [
                "success" => false,
                "message" => "Erreur lors de la modification" . $e->getMessage()
        ];
    }
}

    public function deleteProduit(int $id): array{
        try {
            $req = $this->pdo->prepare("DELETE FROM poduits WHERE id = :id");
            $req->execute([":id" => $id]);
            
            if ($req->rowCount() > 0) {
                return [
                    "success" => true,
                    "message" => "Produit supprimé avec succès"
                ];
            } else {
                return [
                    "success" => false,
                    "message" => "Produit non trouvé"
                ];
            }
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur lors de la suppression: " . $e->getMessage()
            ];
        }
    }
}
