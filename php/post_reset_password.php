<?php
session_start();
header("Content-Type: application/json");
require_once "../config/config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

$token = trim($_POST['token'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($token === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Token invalide'
    ]);
    exit;
}

if (strlen($password) < 6) {
    echo json_encode([
        'success' => false,
        'message' => 'Le mot de passe doit contenir au moins 6 caractères'
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, user_id, expires_at, used_at
         FROM password_reset_tokens
         WHERE token = :token
         LIMIT 1"
    );
    $stmt->execute([':token' => $token]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reset) {
        echo json_encode([
            'success' => false,
            'message' => 'Lien de réinitialisation invalide'
        ]);
        exit;
    }

    if (!empty($reset['used_at'])) {
        echo json_encode([
            'success' => false,
            'message' => 'Ce lien a déjà été utilisé'
        ]);
        exit;
    }

    if (strtotime($reset['expires_at']) < time()) {
        echo json_encode([
            'success' => false,
            'message' => 'Ce lien a expiré'
        ]);
        exit;
    }

    $pdo->beginTransaction();

    $updateUser = $pdo->prepare("UPDATE users SET mot_de_passe = :password WHERE id = :user_id");
    $updateUser->execute([
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':user_id' => (int) $reset['user_id']
    ]);

    $markToken = $pdo->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE id = :id");
    $markToken->execute([':id' => (int) $reset['id']]);

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Mot de passe réinitialisé avec succès'
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la réinitialisation du mot de passe'
    ]);
}
