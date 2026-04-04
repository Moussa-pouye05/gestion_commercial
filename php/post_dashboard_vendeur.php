<?php
session_start();
header('Content-Type: application/json');
require_once "../config/config.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Vous devez etre connecte."]);
    exit;
}

$userId = (int) ($_SESSION['user']['id'] ?? 0);

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_stock;charset=utf8", "root", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $startDate = $_GET['start_date'] ?? null;
    $endDate = $_GET['end_date'] ?? null;
    $params = [":user_id" => $userId];

    if ($startDate) {
        $start = DateTime::createFromFormat('Y-m-d', $startDate);
        if ($start) {
            $startDate = $start->format('Y-m-d');
        } else {
            $startDate = null;
        }
    }
    if ($endDate) {
        $end = DateTime::createFromFormat('Y-m-d', $endDate);
        if ($end) {
            $endDate = $end->format('Y-m-d');
        } else {
            $endDate = null;
        }
    }
    if ($startDate && !$endDate) {
        $endDate = $startDate;
    }

    $commandeConditions = ["c.id_user = :user_id"];
    $statusConditions = ["id_user = :user_id"];

    if ($startDate && $endDate) {
        $commandeConditions[] = "DATE(c.date_commande) BETWEEN :start_date AND :end_date";
        $statusConditions[] = "DATE(date_commande) BETWEEN :start_date AND :end_date";
        $params[':start_date'] = $startDate;
        $params[':end_date'] = $endDate;
    }

    $commandeWhereSql = implode(" AND ", $commandeConditions);
    $statusWhereSql = implode(" AND ", $statusConditions);

    $sqlTotals = "SELECT 
        COALESCE(SUM(CASE WHEN c.etat = 'cloturee' THEN c.total ELSE 0 END), 0) AS revenue,
        COUNT(*) AS commands_count,
        COUNT(DISTINCT c.id_client) AS clients_count
    FROM commandes c
    WHERE {$commandeWhereSql}";
    $stmt = $pdo->prepare($sqlTotals);
    $stmt->execute($params);
    $totals = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

    $sqlItems = "SELECT COALESCE(SUM(dc.quantite), 0) AS items_sold
        FROM commandes c
        INNER JOIN detailcommande dc ON dc.id_commande = c.id
        WHERE {$commandeWhereSql} AND c.etat = 'cloturee'";
    $stmt = $pdo->prepare($sqlItems);
    $stmt->execute($params);
    $itemsSold = (int) ($stmt->fetchColumn() ?: 0);

    $sqlStatus = "SELECT etat, COUNT(*) AS count FROM commandes WHERE {$statusWhereSql} GROUP BY etat";
    $stmt = $pdo->prepare($sqlStatus);
    $stmt->execute($params);
    $statusCounts = ["en_cours" => 0, "cloturee" => 0, "annulee" => 0];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $etat = $row['etat'];
        if (array_key_exists($etat, $statusCounts)) {
            $statusCounts[$etat] = (int) $row['count'];
        }
    }

    $sqlRecent = "SELECT c.id, c.date_commande, c.total, c.etat, cl.nom AS client_nom
        FROM commandes c
        LEFT JOIN clients cl ON c.id_client = cl.id
        WHERE {$commandeWhereSql}
        ORDER BY c.date_commande DESC
        LIMIT 10";
    $stmt = $pdo->prepare($sqlRecent);
    $stmt->execute($params);
    $recentCommands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sqlTopProducts = "SELECT p.nom, cat.nom AS categorie, SUM(dc.quantite) AS qte_vendue, COALESCE(SUM(dc.sous_total), 0) AS ca
        FROM detailcommande dc
        INNER JOIN commandes c ON dc.id_commande = c.id
        LEFT JOIN poduits p ON dc.id_produit = p.id
        LEFT JOIN categorie cat ON p.id_categorie = cat.id
        WHERE {$commandeWhereSql} AND c.etat = 'cloturee'
        GROUP BY p.id, p.nom, cat.nom
        ORDER BY qte_vendue DESC, ca DESC
        LIMIT 5";
    $stmt = $pdo->prepare($sqlTopProducts);
    $stmt->execute($params);
    $topProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "seller" => [
            "nom" => $_SESSION['user']['nom'] ?? '',
            "email" => $_SESSION['user']['email'] ?? '',
            "telephone" => $_SESSION['user']['telephone'] ?? '',
            "role" => $_SESSION['user']['role'] ?? ''
        ],
        "totals" => [
            "revenue" => (float) ($totals['revenue'] ?? 0),
            "commands" => (int) ($totals['commands_count'] ?? 0),
            "clients" => (int) ($totals['clients_count'] ?? 0),
            "items" => $itemsSold
        ],
        "status_counts" => $statusCounts,
        "recent_commands" => $recentCommands,
        "top_products" => $topProducts,
        "period" => ["start_date" => $startDate, "end_date" => $endDate]
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur serveur: " . $e->getMessage()]);
}
