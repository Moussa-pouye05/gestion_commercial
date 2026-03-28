<?php

class NotificationManager
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS admin_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            data JSON NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            vendeur_id INT NULL,
            read_status TINYINT(1) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

        $this->pdo->exec($sql);
    }

    public function createAdminNotification(string $type, array $data = [], ?int $sourceUserId = null): bool
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO admin_notifications (type, data, created_at, vendeur_id, read_status)
             VALUES (:type, :data, NOW(), :vendeur_id, 0)"
        );

        return $stmt->execute([
            ':type' => $type,
            ':data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ':vendeur_id' => $sourceUserId
        ]);
    }

    public function getAdminNotifications(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT n.*, u.nom AS vendeur_nom
             FROM admin_notifications n
             LEFT JOIN users u ON u.id = n.vendeur_id
             ORDER BY n.read_status ASC, n.created_at DESC
             LIMIT :limit"
        );
        $stmt->bindValue(':limit', max(1, $limit), PDO::PARAM_INT);
        $stmt->execute();

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(function ($row) {
            $payload = json_decode($row['data'] ?? '{}', true) ?: [];

            return [
                'id' => (int) $row['id'],
                'type' => $row['type'],
                'data' => $payload,
                'created_at' => $row['created_at'],
                'read_status' => (int) $row['read_status'],
                'vendeur_id' => $row['vendeur_id'] ? (int) $row['vendeur_id'] : null,
                'vendeur_nom' => $row['vendeur_nom'] ?? null
            ];
        }, $rows);
    }

    public function countUnread(): int
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM admin_notifications WHERE read_status = 0");
        return (int) $stmt->fetchColumn();
    }

    public function markAllAsRead(): bool
    {
        return (bool) $this->pdo->exec("UPDATE admin_notifications SET read_status = 1 WHERE read_status = 0");
    }
}
