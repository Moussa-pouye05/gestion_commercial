<?php
$token = trim($_GET['token'] ?? '');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Réinitialiser le mot de passe</title>
<style>
  * { box-sizing: border-box; }
  body {
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0f172a 0%, #172554 45%, #1e293b 100%);
    font-family: Arial, sans-serif;
    color: #fff;
    padding: 24px;
  }
  .card {
    width: min(460px, 100%);
    background: rgba(15, 23, 42, 0.92);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 20px;
    padding: 28px;
    box-shadow: 0 24px 60px rgba(0,0,0,0.35);
  }
  h1 { margin: 0 0 10px; font-size: 28px; }
  p { color: rgba(255,255,255,0.7); font-size: 14px; line-height: 1.6; }
  label { display: block; margin: 16px 0 8px; font-size: 13px; color: rgba(255,255,255,0.8); }
  input {
    width: 100%;
    padding: 13px 14px;
    border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.14);
    background: rgba(255,255,255,0.06);
    color: #fff;
  }
  button {
    width: 100%;
    margin-top: 18px;
    padding: 14px;
    border: none;
    border-radius: 12px;
    background: linear-gradient(135deg, #2563eb, #4f46e5);
    color: #fff;
    cursor: pointer;
  }
  .message { min-height: 20px; margin-top: 14px; font-size: 13px; }
  .error { color: #fca5a5; }
  .success { color: #86efac; }
</style>
</head>
<body>
  <div class="card">
    <?php if ($token): ?>
      <h1>Nouveau mot de passe</h1>
      <p>Choisissez un nouveau mot de passe pour votre compte.</p>
      <form id="reset-password-form">
        <input type="hidden" id="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
        <label for="password">Nouveau mot de passe</label>
        <input type="password" id="password" minlength="6" required>
        <label for="password_confirm">Confirmer le mot de passe</label>
        <input type="password" id="password_confirm" minlength="6" required>
        <button type="submit" id="reset-password-submit">Réinitialiser</button>
        <div class="message" id="reset-password-message"></div>
      </form>
    <?php else: ?>
      <h1>Mot de passe oublié</h1>
      <p>Entrez votre adresse email pour recevoir un lien de réinitialisation.</p>
      <form id="forgot-password-page-form">
        <label for="forgot_email">Adresse email</label>
        <input type="email" id="forgot_email" name="forgot_email" placeholder="vous@exemple.com" autocomplete="email" required>
        <button type="submit" id="forgot-password-page-submit">Envoyer le lien</button>
        <div class="message" id="forgot-password-page-message"></div>
      </form>
    <?php endif; ?>
  </div>
  <script>
    const token = <?= json_encode($token, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    if (token) {
      const form = document.getElementById('reset-password-form');
      form?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirm').value;
        const message = document.getElementById('reset-password-message');
        const submitBtn = document.getElementById('reset-password-submit');

        if (password !== passwordConfirm) {
          message.className = 'message error';
          message.textContent = 'Les mots de passe ne correspondent pas.';
          return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Réinitialisation...';
        message.className = 'message';
        message.textContent = '';

        try {
          const formData = new FormData();
          formData.append('token', token);
          formData.append('password', password);

          const response = await fetch('php/post_reset_password.php', {
            method: 'POST',
            body: formData
          });
          const result = await response.json();

          message.className = result.success ? 'message success' : 'message error';
          message.textContent = result.message || 'Erreur';

          if (result.success) {
            setTimeout(() => {
              window.location.href = 'index.php';
            }, 1800);
          }
        } catch (error) {
          message.className = 'message error';
          message.textContent = 'Erreur lors de la réinitialisation.';
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Réinitialiser';
        }
      });
    } else {
      const form = document.getElementById('forgot-password-page-form');
      form?.addEventListener('submit', async function (e) {
        e.preventDefault();
        const email = document.getElementById('forgot_email').value.trim();
        const message = document.getElementById('forgot-password-page-message');
        const submitBtn = document.getElementById('forgot-password-page-submit');

        if (!email) {
          message.className = 'message error';
          message.textContent = 'Veuillez entrer votre adresse email.';
          return;
        }

        submitBtn.disabled = true;
        submitBtn.textContent = 'Envoi...';
        message.className = 'message';
        message.textContent = '';

        try {
          const formData = new FormData();
          formData.append('email', email);

          const response = await fetch('php/post_forgot_password.php', {
            method: 'POST',
            body: formData
          });
          const result = await response.json();

          if (!result.success) {
            message.className = 'message error';
            message.textContent = result.message || 'Erreur lors de l’envoi.';
            return;
          }

          message.className = 'message success';
          message.innerHTML = result.delivery === 'manual' && result.reset_link
            ? `${result.message}<br><a href="${result.reset_link}" style="color:#86efac;word-break:break-all;">${result.reset_link}</a>`
            : (result.message || 'Lien envoyé avec succès.');
        } catch (error) {
          message.className = 'message error';
          message.textContent = 'Erreur lors de l’envoi du lien.';
        } finally {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Envoyer le lien';
        }
      });
    }
  </script>
</body>
</html>
