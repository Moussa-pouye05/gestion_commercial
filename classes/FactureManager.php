<?php
require_once "../config/config.php";
require_once "Facture.php";

class FactureManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Generate a new invoice for a closed commande
     */
    public function genererFacture(int $id_commande, float $total, array $details): array
    {
        try {
           // $this->pdo->beginTransaction();

            // Generate invoice number
            $numero_facture = $this->genererNumeroFacture();
            $date_facture = date('Y-m-d H:i:s');

            // Get id_client from commande
            $reqClient = $this->pdo->prepare("SELECT id_client FROM commandes WHERE id = :id_commande");
            $reqClient->execute([":id_commande" => $id_commande]);
            $id_client = $reqClient->fetchColumn();

            if (!$id_client) {
                return [
                    'success' => false,
                    'message' => "Commande introuvable ou client manquant"
                ];
            }

            // Insert invoice
            $req = $this->pdo->prepare("INSERT INTO facture (numero_facture, date_facture, id_commande, id_client, total, status) 
                                        VALUES (:numero_facture, :date_facture, :id_commande, :id_client, :total, :statut)");
            $req->execute([
                ":numero_facture" => $numero_facture,
                ":date_facture" => $date_facture,
                ":id_commande" => $id_commande,
                ":id_client" => $id_client,
                ":total" => $total,
                ":statut" => "payee"
            ]);

            $factureId = $this->pdo->lastInsertId();

            // Insert invoice details
            $reqDetail = $this->pdo->prepare("INSERT INTO detailfacture (montant, id_produit, id_facture, quantite, sous_total) 
                                              VALUES (:montant, :id_produit, :id_facture, :quantite, :sous_total)");

            foreach ($details as $detail) {
                $reqDetail->execute([
                    ":montant" => $detail['prix'],
                    ":id_produit" => $detail['id_produit'],
                    ":id_facture" => $factureId,
                    ":quantite" => $detail['quantite'],
                    ":sous_total" => $detail['sous_total']
                ]);
            }

           // $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Facture générée avec succès",
                'facture_id' => $factureId,
                'numero_facture' => $numero_facture
            ];
        } catch (PDOException $e) {
           // $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => "Erreur lors de la génération de la facture: " . $e->getMessage()
            ];
        }
    }

    /**
     * Generate unique invoice number
     */
    private function genererNumeroFacture(): string
    {
        $prefix = "FAC";
        $date = date("Ym");
        $req = $this->pdo->query("SELECT COUNT(*) as count FROM facture WHERE numero_facture LIKE '$prefix-$date%'");
        $result = $req->fetch(PDO::FETCH_ASSOC);
        $numero = ($result['count'] ?? 0) + 1;
        
        return $prefix . "-" . $date . "-" . str_pad((string) $numero, 4, "0", STR_PAD_LEFT);
    }

    /**
     * Load invoices with optional filters
     */
    public function loadFacture(int $limit, int $offset = 0, string $search = "", string $statut = ""): array
    {
        try {
            $sql = "SELECT f.*, c.date_commande as commande_date, cl.nom as client_nom, cl.telephone as client_telephone, cl.adresse as client_adresse
                    FROM facture f
                    LEFT JOIN commandes c ON f.id_commande = c.id
                    LEFT JOIN clients cl ON c.id_client = cl.id";
            
            $where = [];
            $params = [];

            if ($search !== "") {
                $where[] = "(f.numero_facture LIKE :search OR cl.nom LIKE :search OR cl.telephone LIKE :search)";
                $params[":search"] = "%" . $search . "%";
            }

            if ($statut !== "") {
                $where[] = "f.statut = :statut";
                $params[":statut"] = $statut;
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }

            $sql .= " ORDER BY f.date_facture DESC LIMIT :limit OFFSET :offset";
            
            $req = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $req->bindValue($key, $value, PDO::PARAM_STR);
            }

            $req->bindValue(':limit', $limit, PDO::PARAM_INT);
            $req->bindValue(':offset', $offset, PDO::PARAM_INT);
            $req->execute();

            $result = [];
            while ($datas = $req->fetch(PDO::FETCH_ASSOC)) {
                $facture = new Facture(
                    (int) $datas['id'],
                    $datas['numero_facture'],
                    $datas['date_facture'],
                    (int) $datas['id_commande'],
                    (float) $datas['total'],
                    $datas['statut']
                );
                // Add client info
                $facture->client_nom = $datas['client_nom'] ?? '';
                $facture->client_telephone = $datas['client_telephone'] ?? '';
                $facture->client_adresse = $datas['client_adresse'] ?? '';
                $facture->commande_date = $datas['commande_date'] ?? '';
                
                $result[] = $facture;
            }

            return [
                'success' => true,
                'factures' => $result
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur lors de la récupération des factures: " . $e->getMessage()
            ];
        }
    }

    /**
     * Get invoice details
     */
    public function getFactureDetails(int $id_facture): array
    {
        try {
            $req = $this->pdo->prepare("SELECT df.*, p.nom as produit_nom, p.code_barre
                                        FROM detailfacture df
                                        LEFT JOIN poduits p ON df.id_produit = p.id
                                        WHERE df.id_facture = :id_facture");
            $req->execute([":id_facture" => $id_facture]);

            $result = [];
            while ($datas = $req->fetch(PDO::FETCH_ASSOC)) {
                $result[] = $datas;
            }

            return [
                'success' => true,
                'details' => $result
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur lors de la récupération des détails: " . $e->getMessage()
            ];
        }
    }

    /**
     * Get a single invoice by ID
     */
    public function getFacture(int $id): ?Facture
    {
        try {
            $req = $this->pdo->prepare("SELECT f.*, c.date_commande as commande_date, cl.nom as client_nom, 
                                        cl.telephone as client_telephone, cl.adresse as client_adresse
                                        FROM facture f
                                        LEFT JOIN commandes c ON f.id_commande = c.id
                                        LEFT JOIN clients cl ON c.id_client = cl.id
                                        WHERE f.id = :id");
            $req->execute([":id" => $id]);
            $datas = $req->fetch(PDO::FETCH_ASSOC);

            if ($datas) {
                $facture = new Facture(
                    (int) $datas['id'],
                    $datas['numero_facture'],
                    $datas['date_facture'],
                    (int) $datas['id_commande'],
                    (float) $datas['total'],
                    $datas['statut']
                );
                $facture->client_nom = $datas['client_nom'] ?? '';
                $facture->client_telephone = $datas['client_telephone'] ?? '';
                $facture->client_adresse = $datas['client_adresse'] ?? '';
                $facture->commande_date = $datas['commande_date'] ?? '';
                return $facture;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Get invoice by commande ID
     */
    public function getFactureByCommande(int $id_commande): ?Facture
    {
        try {
            $req = $this->pdo->prepare("SELECT * FROM facture WHERE id_commande = :id_commande");
            $req->execute([":id_commande" => $id_commande]);
            $datas = $req->fetch(PDO::FETCH_ASSOC);

            if ($datas) {
                return new Facture(
                    (int) $datas['id'],
                    $datas['numero_facture'],
                    $datas['date_facture'],
                    (int) $datas['id_client'],
                    (int) $datas['id_commande'],
                    (float) $datas['total'],
                    $datas['status']
                );
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Count total invoices
     */
    public function countFactures(string $search = "", string $statut = ""): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM facture f
                    LEFT JOIN commandes c ON f.id_commande = c.id
                    LEFT JOIN clients cl ON c.id_client = cl.id";
            
            $where = [];
            $params = [];

            if ($search !== "") {
                $where[] = "(f.numero_facture LIKE :search OR cl.nom LIKE :search OR cl.telephone LIKE :search)";
                $params[":search"] = "%" . $search . "%";
            }

            if ($statut !== "") {
                $where[] = "f.statut = :statut";
                $params[":statut"] = $statut;
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
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * Update invoice status
     */
    public function updateStatut(int $id, string $statut): array
    {
        try {
            $req = $this->pdo->prepare("UPDATE facture SET statut = :statut WHERE id = :id");
            $req->execute([
                ":statut" => $statut,
                ":id" => $id
            ]);

            return [
                'success' => true,
                'message' => "Statut de la facture mis à jour"
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => "Erreur lors de la mise à jour: " . $e->getMessage()
            ];
        }
    }
}

