<?php
require_once "../config/config.php";

require_once "../vendor/autoload.php";
require_once "../config/config.php";
require_once "../classes/FactureManager.php";
require_once "../classes/CommandeManager.php";
require_once "../classes/ClientManager.php";

use Spipu\Html2Pdf\Html2Pdf;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id_commande = $data['id_commande'] ?? null;

    if (!$id_commande) {
        http_response_code(400);
        echo json_encode(['error' => 'ID commande manquant']);
        exit;
    }

    try {
        $pdo = getConnection();
        $factureManager = new FactureManager($pdo);
        $commandeManager = new CommandeManager($pdo);
        $clientManager = new ClientManager($pdo);

        // Get facture by commande
        $facture = $factureManager->getFactureByCommande($id_commande);
        if (!$facture) {
            http_response_code(404);
            echo json_encode(['error' => 'Facture non trouvée']);
            exit;
        }

        // Get client
        $clientReq = $pdo->prepare("SELECT * FROM clients c JOIN commandes cmd ON c.id = cmd.id_client WHERE cmd.id = :id");
        $clientReq->execute([':id' => $id_commande]);
        $clientData = $clientReq->fetch(PDO::FETCH_ASSOC);
        $client = $clientData ? (object)$clientData : null;

        // Get commande
        $commande = $commandeManager->getCommande($id_commande);

        // Get facture details
        $detailsResult = $factureManager->getFactureDetails($facture->id);
        $facture->details = $detailsResult['details'] ?? [];

        // Générer le HTML de la facture
        $html = generateFactureHTML($facture, $commande, $client);

        // Créer le PDF
        $html2pdf = new Html2Pdf('P', 'A4', 'fr');
        $html2pdf->writeHTML($html);
        
        // Sortie du PDF
        $html2pdf->output('facture_' . $facture->numero_facture . '.pdf', 'D');

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erreur lors de la génération du PDF: ' . $e->getMessage()]);
    }
}
</xai:function_call

<xai:function_call name="edit_file">
<parameter name="path">php/post_generate_pdf.php

function generateFactureHTML($facture, $commande, $client) {
    $html = '
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        .header { background: linear-gradient(90deg, #2563eb, #9333ea); color: white; padding: 20px; border-radius: 10px 10px 0 0; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; }
        .content { padding: 20px; background: white; border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 10px 10px; }
        .info-section { background: #f9fafb; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .info-section h3 { margin: 0 0 10px 0; color: #374151; }
        .info-section p { margin: 5px 0; font-size: 14px; color: #6b7280; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background: #f3f4f6; font-weight: bold; }
        .total { text-align: right; background: linear-gradient(90deg, #10b981, #059669); color: white; padding: 15px; border-radius: 8px; font-size: 18px; font-weight: bold; }
        .status { display: inline-block; padding: 5px 10px; background: #dcfce7; color: #166534; border-radius: 20px; font-size: 12px; }
    </style>
    
    <div class="header">
        <h1>FACTURE</h1>
        <p>N° ' . $facture->numero_facture . '</p>
        <p>Date: ' . date('d/m/Y H:i', strtotime($facture->date_facture)) . '</p>
        <span class="status">' . ($facture->statut === 'payee' ? 'Payée' : $facture->statut) . '</span>
    </div>
    
    <div class="content">
        <div style="display: flex; gap: 20px; margin-bottom: 20px;">
            <div class="info-section" style="flex: 1;">
                <h3>Informations Client</h3>
                <p><strong>Nom:</strong> ' . ($client->nom ?? 'N/A') . '</p>
                <p><strong>Téléphone:</strong> ' . ($client->telephone ?? 'N/A') . '</p>
                <p><strong>Adresse:</strong> ' . ($client->adresse ?? 'N/A') . '</p>
            </div>
            
            <div class="info-section" style="flex: 1;">
                <h3>Détails Commande</h3>
                <p><strong>Commande:</strong> CMD-' . str_pad($commande->id, 3, '0', STR_PAD_LEFT) . '</p>
                <p><strong>Date:</strong> ' . date('d/m/Y H:i', strtotime($commande->date_commande)) . '</p>
            </div>
        </div>
        
        <h3 style="color: #374151; margin-bottom: 15px;">Produits commandés</h3>
        <table>
            <thead>
                <tr>
                    <th>Produit</th>
                    <th style="text-align: center;">Qté</th>
                    <th style="text-align: center;">Prix unitaire</th>
                    <th style="text-align: center;">Sous-total</th>
                </tr>
            </thead>
            <tbody>';

    if ($facture->details && count($facture->details) > 0) {
        foreach ($facture->details as $detail) {
            $html .= '
                <tr>
                    <td>' . ($detail['produit_nom'] ?? 'N/A') . '</td>
                    <td style="text-align: center;">' . $detail['quantite'] . '</td>
                    <td style="text-align: center;">' . number_format($detail['montant'], 0, ',', ' ') . ' FCFA</td>
                    <td style="text-align: center;">' . number_format($detail['sous_total'], 0, ',', ' ') . ' FCFA</td>
                </tr>';
        }
    }

    $html .= '
            </tbody>
        </table>
        
        <div class="total">
            TOTAL: ' . number_format($facture->total, 0, ',', ' ') . ' FCFA
        </div>
    </div>';

    return $html;
}
?>