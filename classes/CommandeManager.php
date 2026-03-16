<?php
require_once "../config/config.php";
require_once "../classes/Commande.php";
require_once "../classes/FactureManager.php";

class CommandeManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Create a new commande with details
     */
    public function createCommande(Commande $commande, array $details): array
    {
        try {
            $this->pdo->beginTransaction();

            // Insert commande
            $req = $this->pdo->prepare("INSERT INTO commandes (date_commande, id_user, etat, id_client, total) 
                                        VALUES (:date_commande, :id_user, 'en_cours', :id_client, :total)");
            $req->execute([
                ":date_commande" => $commande->getDateCommande(),
                ":id_user" => $commande->getIdUser(),
               // ":etat" => $commande->getEtat(),
                ":id_client" => $commande->getIdClient(),
                ":total" => $commande->getTotal()
            ]);

            $commandeId = $this->pdo->lastInsertId();

            // Insert details
            $reqDetail = $this->pdo->prepare("INSERT INTO detailcommande (quantite, prix, sous_total, id_produit, id_commande) 
                                              VALUES (:quantite, :prix, :sous_total, :id_produit, :id_commande)");

            foreach ($details as $detail) {
                $reqDetail->execute([
                    ":quantite" => $detail['quantite'],
                    ":prix" => $detail['prix'],
                    ":sous_total" => $detail['sous_total'],
                    ":id_produit" => $detail['id_produit'],
                    ":id_commande" => $commandeId
                ]);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Commande créée avec succès",
                'id' => $commandeId
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => "Erreur lors de la création de la commande: " . $e->getMessage()
            ];
        }
    }

    /**
     * Load commandes with optional filters
     */
    public function loadCommande(int $limit, int $offset = 0, string $search = "", string $etat = ""): array
    {
        try {
            $sql = "SELECT c.*, cl.nom as client_nom, cl.telephone as client_telephone, cl.adresse as client_adresse,
                           u.nom as user_nom
                    FROM commandes c
                    LEFT JOIN clients cl ON c.id_client = cl.id
                    LEFT JOIN users u ON c.id_user = u.id";
            
            $where = [];
            $params = [];

            if ($search !== "") {
                $where[] = "(c.id LIKE :search OR cl.nom LIKE :search OR cl.telephone LIKE :search)";
                $params[":search"] = "%" . $search . "%";
            }

            if ($etat !== "") {
                $where[] = "c.etat = :etat";
                $params[":etat"] = $etat;
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(" AND ", $where);
            }

            $sql .= " ORDER BY c.date_commande DESC LIMIT :limit OFFSET :offset";
            
            $req = $this->pdo->prepare($sql);

            foreach ($params as $key => $value) {
                $req->bindValue($key, $value, PDO::PARAM_STR);
            }

            $req->bindValue(':limit', $limit, PDO::PARAM_INT);
            $req->bindValue(':offset', $offset, PDO::PARAM_INT);
            $req->execute();

            $result = [];
            while ($datas = $req->fetch(PDO::FETCH_ASSOC)) {
                $commande = new Commande(
                    (int) $datas['id'],
                    $datas['date_commande'],
                    (int) $datas['id_user'],
                    $datas['etat'],
                    (int) $datas['id_client'],
                    (float) $datas['total']
                );
                // Add client info
                $commande->client_nom = $datas['client_nom'] ?? '';
                $commande->client_telephone = $datas['client_telephone'] ?? '';
                $commande->client_adresse = $datas['client_adresse'] ?? '';
                $commande->user_nom = $datas['user_nom'] ?? '';
                $commande->user_prenom = $datas['user_prenom'] ?? '';
                
                $result[] = $commande;
            }

            return [
                'success' => true,
                'commandes' => $result
            ];
        } catch (PDOException $e) {
            return [
                "success" => false,
                "message" => "Erreur lors de la récupération des commandes: " . $e->getMessage()
            ];
        }
    }

    /**
     * Get details of a specific commande
     */
    public function getCommandeDetails(int $id_commande): array
    {
        try {
            $req = $this->pdo->prepare("SELECT dc.*, p.nom as produit_nom, p.code_barre
                                        FROM detailcommande dc
                                        LEFT JOIN poduits p ON dc.id_produit = p.id
                                        WHERE dc.id_commande = :id_commande");
            $req->execute([":id_commande" => $id_commande]);

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
     * Count total commandes
     */
    public function countCommandes(string $search = "", string $etat = ""): int
    {
        try {
            $sql = "SELECT COUNT(*) FROM commandes c
                    LEFT JOIN clients cl ON c.id_client = cl.id";
            
            $where = [];
            $params = [];

            if ($search !== "") {
                $where[] = "(c.id LIKE :search OR cl.nom LIKE :search OR cl.telephone LIKE :search)";
                $params[":search"] = "%" . $search . "%";
            }

            if ($etat !== "") {
                $where[] = "c.etat = :etat";
                $params[":etat"] = $etat;
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
     * Get counts by status
     */
    public function getCountByStatus(): array
    {
        try {
            $req = $this->pdo->query("SELECT etat, COUNT(*) as count FROM commandes GROUP BY etat");
            $result = ['en_cours' => 0, 'cloturee' => 0, 'annulee' => 0];
            
            while ($datas = $req->fetch(PDO::FETCH_ASSOC)) {
                if ($datas['etat'] === 'en_cours') {
                    $result['en_cours'] = (int) $datas['count'];
                } elseif ($datas['etat'] === 'cloturee') {
                    $result['cloturee'] = (int) $datas['count'];
                } elseif ($datas['etat'] === 'annulee') {
                    $result['annulee'] = (int) $datas['count'];
                }
            }

            return $result;
        } catch (PDOException $e) {
            return ['en_cours' => 0, 'cloturee' => 0, 'annulee' => 0];
        }
    }

    /**
     * Get a single commande by ID
     */
    public function getCommande(int $id): ?Commande
    {
        try {
            $req = $this->pdo->prepare("SELECT * FROM commandes WHERE id = :id");
            $req->execute([":id" => $id]);
            $datas = $req->fetch(PDO::FETCH_ASSOC);

            if ($datas) {
                return new Commande(
                    (int) $datas['id'],
                    $datas['date_commande'],
                    (int) $datas['id_user'],
                    $datas['etat'],
                    (int) $datas['id_client'],
                    (float) $datas['total']
                );
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    /**
     * Update commande (only if etat is 'en_cours')
     */
    public function updateCommande(Commande $commande, array $details): array
    {
        try {
            // Check if commande is in 'en_cours' status
            $currentCmd = $this->getCommande($commande->getId());
            if (!$currentCmd || $currentCmd->getEtat() !== 'en_cours') {
                return [
                    'success' => false,
                    'message' => "La commande ne peut être modifiée car elle n'est plus en cours"
                ];
            }

            $this->pdo->beginTransaction();

            // Update commande
            $req = $this->pdo->prepare("UPDATE commandes SET date_commande = :date_commande, id_client = :id_client, 
                                        total = :total WHERE id = :id AND etat = 'en_cours'");
            $req->execute([
                ":date_commande" => $commande->getDateCommande(),
                ":id_client" => $commande->getIdClient(),
                ":total" => $commande->getTotal(),
                ":id" => $commande->getId()
            ]);

            // Delete old details
            $deleteReq = $this->pdo->prepare("DELETE FROM detailcommande WHERE id_commande = :id_commande");
            $deleteReq->execute([":id_commande" => $commande->getId()]);

            // Insert new details
            $reqDetail = $this->pdo->prepare("INSERT INTO detailcommande (quantite, prix, sous_total, id_produit, id_commande) 
                                              VALUES (:quantite, :prix, :sous_total, :id_produit, :id_commande)");

            foreach ($details as $detail) {
                $reqDetail->execute([
                    ":quantite" => $detail['quantite'],
                    ":prix" => $detail['prix'],
                    ":sous_total" => $detail['sous_total'],
                    ":id_produit" => $detail['id_produit'],
                    ":id_commande" => $commande->getId()
                ]);
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Commande mise à jour avec succès"
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => "Erreur lors de la mise à jour: " . $e->getMessage()
            ];
        }
    }

    /**
     * Close commande (cloture) - decreases stock and generates invoice
     */
    public function clotureCommande(int $id_commande): array
    {
        try {
            // Check if commande is in 'en_cours' status
            $currentCmd = $this->getCommande($id_commande);
            if (!$currentCmd || $currentCmd->getEtat() !== 'en_cours') {
                return [
                    'success' => false,
                    'message' => "La commande ne peut être clôturée car elle n'est plus en cours"
                ];
            }

            $this->pdo->beginTransaction();

            // Get details to decrease stock
            $details = $this->getCommandeDetails($id_commande);
            
            // Decrease stock for each product
            $updateStock = $this->pdo->prepare("UPDATE poduits SET quantite = quantite - :quantite WHERE id = :id");
            foreach ($details['details'] as $detail) {
                $updateStock->execute([
                    ":quantite" => $detail['quantite'],
                    ":id" => $detail['id_produit']
                ]);
            }

            // Update commande status to 'cloturee'
            $req = $this->pdo->prepare("UPDATE commandes SET etat = 'cloturee' WHERE id = :id AND etat = 'en_cours'");
            $req->execute([":id" => $id_commande]);

            // Generate invoice
            $factureManager = new FactureManager($this->pdo);
            $factureResult = $factureManager->genererFacture($id_commande, $currentCmd->getTotal(), $details['details']);

            if (!$factureResult['success']) {
                $this->pdo->rollBack();
                return $factureResult;
            }

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Commande clôturée et facture générée avec succès",
                'facture_id' => $factureResult['facture_id']
            ];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            return [
                'success' => false,
                'message' => "Erreur lors de la clôture: " . $e->getMessage()
            ];
        }
    }

    /**
     * Cancel commande (annule)
     */
    public function annuleCommande(int $id_commande): array
    {
        try {
            // Check if commande is in 'en_cours' status
            $currentCmd = $this->getCommande($id_commande);
            if (!$currentCmd || $currentCmd->getEtat() !== 'en_cours') {
                return [
                    'success' => false,
                    'message' => "La commande ne peut être annulée car elle n'est plus en cours"
                ];
            }

            // Update commande status to 'annulee'
            $req = $this->pdo->prepare("UPDATE commandes SET etat = 'annulee' WHERE id = :id AND etat = 'en_cours'");
            $req->execute([":id" => $id_commande]);

            return [
                'success' => true,
                'message' => "Commande annulée avec succès"
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => "Erreur lors de l'annulation: " . $e->getMessage()
            ];
        }
    }

    /**
     * Delete commande
     */
    public function deleteCommande(int $id_commande): array
    {
        try {
            // Only allow deletion if status is 'en_cours' or 'annulee'
            $currentCmd = $this->getCommande($id_commande);
            if ($currentCmd && $currentCmd->getEtat() === 'cloturee') {
                return [
                    'success' => false,
                    'message' => "Impossible de supprimer une commande clôturée"
                ];
            }

            $this->pdo->beginTransaction();

            // Delete details first
            $reqDetail = $this->pdo->prepare("DELETE FROM detailcommande WHERE id_commande = :id_commande");
            $reqDetail->execute([":id_commande" => $id_commande]);

            // Delete commande
            $req = $this->pdo->prepare("DELETE FROM commandes WHERE id = :id");
            $req->execute([":id" => $id_commande]);

            $this->pdo->commit();

            return [
                'success' => true,
                'message' => "Commande supprimée avec succès"
            ];
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            return [
                'success' => false,
                'message' => "Erreur lors de la suppression: " . $e->getMessage()
            ];
        }
    }
    public function getCommandeVendeur(): int{
        try {
            $req = $this->pdo->prepare("SELECT COUNT(*) AS commande_vendeur FROM commandes WHERE id_user = :id");
            $req->execute([
                ":id" => $_SESSION['user']['id']
            ]);
            $nb = $req->fetch(PDO::FETCH_ASSOC)['commande_vendeur'] ?? 0;
            return $nb;
        } catch (PDOException $e) {
            return 0;
        }
    }

}

