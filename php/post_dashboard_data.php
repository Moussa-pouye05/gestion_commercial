<?php
session_start();
header('Content-Type: application/json');
require_once "../config/config.php";

if (!isset($_SESSION['user'])) {
    echo json_encode(["success" => false, "message" => "Vous devez être connecté."]);
    exit;
}

$from = isset($_GET['dateFrom']) && $_GET['dateFrom'] !== '' ? $_GET['dateFrom'] : null;
$to = isset($_GET['dateTo']) && $_GET['dateTo'] !== '' ? $_GET['dateTo'] : null;

$whereClauses = [];
$params = [];
if ($from) {
    $whereClauses[] = "date_commande >= :from";
    $params[':from'] = $from . " 00:00:00";
}
if ($to) {
    $whereClauses[] = "date_commande <= :to";
    $params[':to'] = $to . " 23:59:59";
}
$whereDate = count($whereClauses) > 0 ? "WHERE " . implode(" AND ", $whereClauses) : "";
$whereDateAlias = count($whereClauses) > 0 ? "WHERE " . str_replace("date_commande", "c.date_commande", implode(" AND ", $whereClauses)) : "";
$whereDateAnd = count($whereClauses) > 0 ? "AND " . str_replace("date_commande", "c.date_commande", implode(" AND ", $whereClauses)) : "";

try {
    $pdo = new PDO("mysql:host=localhost;dbname=gestion_stock;charset=utf8", "root", "12345");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Totaux (seulement commandes clôturées)
    $sqlTotal = "SELECT COALESCE(SUM(total),0) AS total_revenue, COUNT(*) AS total_commands FROM commandes " . ($whereDate ? $whereDate . " AND etat = 'cloturee'" : "WHERE etat = 'cloturee'");
    $stmt = $pdo->prepare($sqlTotal);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $totals = $stmt->fetch(PDO::FETCH_ASSOC);

    // 10 dernières commandes
    $sqlRecent = "SELECT c.id, c.date_commande, c.total, c.etat, cl.nom AS client_nom, u.nom AS vendeur_nom
        FROM commandes c
        LEFT JOIN clients cl ON c.id_client = cl.id
        LEFT JOIN users u ON c.id_user = u.id
        " . ($whereDateAlias ? $whereDateAlias . " AND c.etat = 'cloturee'" : "WHERE c.etat = 'cloturee'") . "
        ORDER BY c.date_commande DESC LIMIT 10";
    $stmt = $pdo->prepare($sqlRecent);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $recent = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top 10 meilleures commandes clôturées
    $sqlTopCmd = "SELECT c.id, c.date_commande, c.total, c.etat, cl.nom AS client_nom
        FROM commandes c
        LEFT JOIN clients cl ON c.id_client = cl.id
        " . ($whereDateAlias ? $whereDateAlias . " AND c.etat = 'cloturee'" : "WHERE c.etat = 'cloturee'") . "
        ORDER BY c.total DESC LIMIT 10";
    $stmt = $pdo->prepare($sqlTopCmd);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $top_commands = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // CA par jour (clôturées)
    $sqlCaByDay = "SELECT DATE(c.date_commande) AS jour, COALESCE(SUM(c.total),0) AS total_ca
        FROM commandes c
        " . ($whereDateAlias ? $whereDateAlias . " AND c.etat = 'cloturee'" : "WHERE c.etat = 'cloturee'") . "
        GROUP BY DATE(c.date_commande) ORDER BY DATE(c.date_commande) ASC";
    $stmt = $pdo->prepare($sqlCaByDay);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $ca_by_day = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top 10 meilleurs vendeurs (role vendeur uniquement)
    $sqlTopSellers = "SELECT u.id, u.nom, u.email, COUNT(c.id) AS commandes, COALESCE(SUM(c.total),0) AS montant_total, 
        COALESCE(SUM(c.total)/NULLIF(COUNT(c.id),0),0) AS performance
        FROM commandes c
        LEFT JOIN users u ON c.id_user = u.id
        WHERE u.role = 'vendeur' " . ($whereDateAnd ? " " . $whereDateAnd : "") . "
        GROUP BY u.id
        ORDER BY montant_total DESC
        LIMIT 10";
    $stmt = $pdo->prepare($sqlTopSellers);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $top_sellers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // CA par catégorie
    $sqlCategory = "SELECT cat.id AS category_id, cat.nom AS category_name, COALESCE(SUM(dc.sous_total),0) AS ca
        FROM detailcommande dc
        LEFT JOIN poduits p ON dc.id_produit = p.id
        LEFT JOIN categorie cat ON p.id_categorie = cat.id
        LEFT JOIN commandes c ON dc.id_commande = c.id
        " . ($whereDateAlias ? " " . $whereDateAlias : "") . "
        GROUP BY cat.id
        ORDER BY ca DESC
        LIMIT 10";
    $stmt = $pdo->prepare($sqlCategory);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $category_ca = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top 10 clients
    $sqlTopClients = "SELECT cl.id, cl.nom, cl.telephone, COUNT(c.id) AS commandes, COALESCE(SUM(c.total),0) AS montant_total
        FROM commandes c
        LEFT JOIN clients cl ON c.id_client = cl.id
        " . ($whereDateAlias ? $whereDateAlias : "") . "
        GROUP BY cl.id
        ORDER BY montant_total DESC
        LIMIT 10";
    $stmt = $pdo->prepare($sqlTopClients);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $top_clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top 10 produits les plus vendus
    $sqlTopProducts = "SELECT p.id, p.nom, p.code_barre, cat.nom AS categorie, SUM(dc.quantite) AS qte_vendue, COALESCE(SUM(dc.sous_total),0) AS ca
        FROM detailcommande dc
        LEFT JOIN poduits p ON dc.id_produit = p.id
        LEFT JOIN categorie cat ON p.id_categorie = cat.id
        LEFT JOIN commandes c ON dc.id_commande = c.id
        " . ($whereDateAlias ? " " . $whereDateAlias : "") . "
        GROUP BY p.id
        ORDER BY qte_vendue DESC
        LIMIT 8";
    $stmt = $pdo->prepare($sqlTopProducts);
    foreach ($params as $k => $v) { $stmt->bindValue($k, $v); }
    $stmt->execute();
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Top 10 produits en rupture (stock <= seuilCritique)
    $sqlRupture = "SELECT id, nom, quantite, seuilCritique, stock_min FROM poduits WHERE quantite <= seuilCritique ORDER BY quantite ASC LIMIT 10";
    $stmt = $pdo->prepare($sqlRupture);
    $stmt->execute();
$stock_alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Clients count
    $clients_count = $pdo->query("SELECT COUNT(*) FROM clients")->fetchColumn();

    // Products count
    $products_count = $pdo->query("SELECT COUNT(*) FROM poduits")->fetchColumn();

    echo json_encode([
        "success" => true,
        "totals" => [
            "revenue" => (float) ($totals['total_revenue'] ?? 0),
            "commands" => (int) ($totals['total_commands'] ?? 0)
        ],
        "recent_commands" => $recent,
        "top_commands" => $top_commands,
        "top_sellers" => $top_sellers,
        "top_clients" => $top_clients,
        "top_products" => $top_products,
        "stock_alerts" => $stock_alerts,
        "ca_by_day" => $ca_by_day,
        "category_ca" => $category_ca,
        "clients_count" => (int) $clients_count,
        "products_count" => (int) $products_count
    ]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Erreur serveur: " . $e->getMessage()]);
}
