<?php
require_once 'config/database.php';

$sql = "CREATE TABLE IF NOT EXISTS admin_notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50) NOT NULL,
    data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vendeur_id INT,
    read_status TINYINT DEFAULT 0,
    FOREIGN KEY (vendeur_id) REFERENCES vendeurs(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($pdo->exec($sql)) {
    echo "Table admin_notifications créée avec succès.";
} else {
    echo "Erreur création table.";
}
?>
