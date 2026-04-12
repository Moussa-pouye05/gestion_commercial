<?php
session_start();
header("Content-Type: application/json");
require_once "../config/config.php";

function sendResetPasswordEmail(string $recipientEmail, string $resetLink): bool
{
    $subject = APP_NAME . " - Réinitialisation du mot de passe";
    $message = "Bonjour,\n\n";
    $message .= "Vous avez demandé la réinitialisation de votre mot de passe.\n";
    $message .= "Cliquez sur ce lien pour choisir un nouveau mot de passe :\n";
    $message .= $resetLink . "\n\n";
    $message .= "Ce lien expire dans 1 heure.\n";
    $message .= "Si vous n'êtes pas à l'origine de cette demande, ignorez simplement cet email.\n\n";
    $message .= "Cordialement,\n";
    $message .= APP_NAME;

    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'From: ' . APP_NAME . ' <' . APP_MAIL_FROM . '>',
        'Reply-To: ' . APP_MAIL_FROM,
        'X-Mailer: PHP/' . phpversion()
    ];

    return @mail($recipientEmail, $subject, $message, implode("\r\n", $headers));
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Entrer votre email'
    ]);
    exit;
}

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS password_reset_tokens (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        token VARCHAR(64) NOT NULL UNIQUE,
        expires_at DATETIME NOT NULL,
        used_at DATETIME NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_token (token)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $userStmt = $pdo->prepare("SELECT id, email FROM users WHERE email = :email LIMIT 1");
    $userStmt->execute([':email' => $email]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'success' => true,
            'message' => 'Si cet email existe, un lien de réinitialisation a été généré.'
        ]);
        exit;
    }

    $pdo->prepare("UPDATE password_reset_tokens SET used_at = NOW() WHERE user_id = :user_id AND used_at IS NULL")
        ->execute([':user_id' => (int) $user['id']]);

    $token = bin2hex(random_bytes(32));
    $expiresAt = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

    $insertStmt = $pdo->prepare(
        "INSERT INTO password_reset_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)"
    );
    $insertStmt->execute([
        ':user_id' => (int) $user['id'],
        ':token' => $token,
        ':expires_at' => $expiresAt
    ]);

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $projectPath = preg_replace('#/php$#', '', $basePath);
    $resetLink = $scheme . '://' . $host . $projectPath . '/reset_password.php?token=' . urlencode($token);

    $emailSent = sendResetPasswordEmail($user['email'], $resetLink);

    echo json_encode([
        'success' => true,
        'message' => $emailSent
            ? 'Email de réinitialisation envoyé avec succès.'
            : 'Le serveur mail n’est pas configuré. Utilisez le lien ci-dessous pour continuer.',
        'delivery' => $emailSent ? 'email' : 'manual',
        'reset_link' => $emailSent ? null : $resetLink
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la génération du lien de réinitialisation'
    ]);
}
