<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>G-STOCK — Connexion</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    min-height: 100vh;
    font-family: 'DM Sans', sans-serif;
    background: #0a0f1e;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
  }

  .bg-orb {
    position: fixed;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.18;
    pointer-events: none;
  }
  .orb1 { width: 500px; height: 500px; background: #3b5bdb; top: -100px; left: -100px; animation: drift 12s ease-in-out infinite alternate; }
  .orb2 { width: 400px; height: 400px; background: #0ea5e9; bottom: -80px; right: -60px; animation: drift 15s ease-in-out infinite alternate-reverse; }
  .orb3 { width: 300px; height: 300px; background: #6366f1; top: 40%; left: 40%; animation: drift 10s ease-in-out infinite alternate; }

  @keyframes drift { from { transform: translate(0,0) scale(1); } to { transform: translate(30px, 20px) scale(1.08); } }

  .grid-lines {
    position: fixed; inset: 0; pointer-events: none;
    background-image:
      linear-gradient(rgba(255,255,255,0.025) 1px, transparent 1px),
      linear-gradient(90deg, rgba(255,255,255,0.025) 1px, transparent 1px);
    background-size: 60px 60px;
  }

  .card {
    position: relative; z-index: 10;
    display: flex;
    width: min(900px, 96vw);
    min-height: 520px;
    border-radius: 24px;
    overflow: hidden;
    border: 1px solid rgba(255,255,255,0.08);
    background: rgba(10,15,30,0.6);
    backdrop-filter: blur(24px);
    box-shadow: 0 40px 80px rgba(0,0,0,0.5), 0 0 0 1px rgba(255,255,255,0.04);
    animation: fadeUp 0.7s cubic-bezier(0.22,1,0.36,1) both;
  }

  @keyframes fadeUp { from { opacity: 0; transform: translateY(32px); } to { opacity: 1; transform: translateY(0); } }

  /* LEFT PANEL */
  .panel-left {
    flex: 1;
    padding: 48px 44px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    border-right: 1px solid rgba(255,255,255,0.06);
    background: linear-gradient(145deg, rgba(59,91,219,0.12) 0%, rgba(99,102,241,0.06) 100%);
  }

  .panel-left::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #3b5bdb, #6366f1, #0ea5e9);
    border-radius: 24px 0 0 0;
  }

  .brand {
    display: flex; align-items: center; gap: 12px;
  }

  .brand-icon {
    width: 44px; height: 44px;
    background: linear-gradient(135deg, #3b5bdb 0%, #6366f1 100%);
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    box-shadow: 0 8px 20px rgba(59,91,219,0.4);
  }

  .brand-name {
    font-family: 'Syne', sans-serif;
    font-size: 22px; font-weight: 800;
    color: #fff; letter-spacing: 0.02em;
  }

  .brand-sub {
    font-size: 11px; font-weight: 400;
    color: rgba(255,255,255,0.4);
    letter-spacing: 0.12em; text-transform: uppercase;
  }

  .hero-text {
    flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 32px 0;
  }

  .hero-text h1 {
    font-family: 'Syne', sans-serif;
    font-size: 36px; font-weight: 800; line-height: 1.15;
    color: #fff; margin-bottom: 16px;
    letter-spacing: -0.02em;
  }

  .hero-text h1 span {
    background: linear-gradient(90deg, #60a5fa, #818cf8, #a78bfa);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent;
    background-clip: text;
  }

  .hero-text p {
    font-size: 14px; line-height: 1.7;
    color: rgba(255,255,255,0.45);
    max-width: 280px;
  }

  .features {
    display: flex; flex-direction: column; gap: 14px;
  }

  .feature {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 16px;
    background: rgba(255,255,255,0.04);
    border: 1px solid rgba(255,255,255,0.07);
    border-radius: 12px;
    transition: background 0.2s;
  }

  .feature:hover { background: rgba(255,255,255,0.07); }

  .feature-dot {
    width: 36px; height: 36px; flex-shrink: 0;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
  }

  .dot-blue { background: rgba(59,91,219,0.25); }
  .dot-sky  { background: rgba(14,165,233,0.2); }
  .dot-violet { background: rgba(139,92,246,0.2); }

  .feature-label { font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.85); }
  .feature-desc  { font-size: 11px; color: rgba(255,255,255,0.35); margin-top: 1px; }

  .copy { font-size: 11px; color: rgba(255,255,255,0.2); }

  /* RIGHT PANEL */
  .panel-right {
    width: 380px; flex-shrink: 0;
    padding: 48px 40px;
    display: flex; flex-direction: column; justify-content: center;
    gap: 0;
  }

  .form-header { margin-bottom: 32px; }

  .form-header h2 {
    font-family: 'Syne', sans-serif;
    font-size: 26px; font-weight: 700;
    color: #fff; margin-bottom: 6px;
    letter-spacing: -0.01em;
  }

  .form-header p { font-size: 13px; color: rgba(255,255,255,0.4); }

  .form-group { margin-bottom: 18px; }

  .form-group label {
    display: block; font-size: 12px; font-weight: 500;
    color: rgba(255,255,255,0.5);
    letter-spacing: 0.06em; text-transform: uppercase;
    margin-bottom: 8px;
  }

  .input-wrap { position: relative; }

  .input-wrap svg {
    position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
    width: 16px; height: 16px; stroke: rgba(255,255,255,0.25);
    pointer-events: none; transition: stroke 0.2s;
  }

  .input-wrap input {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(255,255,255,0.08);
    border-radius: 12px;
    padding: 13px 14px 13px 42px;
    font-size: 14px; font-family: 'DM Sans', sans-serif;
    color: #fff;
    outline: none;
    transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
  }

  .input-wrap input::placeholder { color: rgba(255,255,255,0.2); }

  .input-wrap input:focus {
    border-color: rgba(99,102,241,0.6);
    background: rgba(99,102,241,0.08);
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
  }

  .input-wrap:focus-within svg { stroke: rgba(99,102,241,0.8); }

  .form-meta {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px; font-size: 12px;
  }

  .remember { display: flex; align-items: center; gap: 7px; color: rgba(255,255,255,0.4); cursor: pointer; }

  .remember input[type=checkbox] {
    width: 14px; height: 14px;
    accent-color: #6366f1;
    cursor: pointer;
  }

  .forgot { color: #818cf8; font-size: 12px; text-decoration: none; transition: color 0.2s; }
  .forgot:hover { color: #a5b4fc; }

  .btn-submit {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #3b5bdb 0%, #6366f1 100%);
    border: none; border-radius: 12px;
    font-family: 'DM Sans', sans-serif;
    font-size: 14px; font-weight: 500;
    color: #fff; cursor: pointer;
    position: relative; overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
    box-shadow: 0 8px 24px rgba(59,91,219,0.35);
    letter-spacing: 0.02em;
  }

  .btn-submit::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, #4f6ee8 0%, #7577f3 100%);
    opacity: 0; transition: opacity 0.2s;
  }

  .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(59,91,219,0.45); }
  .btn-submit:hover::before { opacity: 1; }
  .btn-submit:active { transform: translateY(0); }

  .btn-submit span { position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; gap: 8px; }

  .divider { margin: 22px 0; display: flex; align-items: center; gap: 12px; }
  .divider-line { flex: 1; height: 1px; background: rgba(255,255,255,0.07); }
  .divider span { font-size: 11px; color: rgba(255,255,255,0.2); white-space: nowrap; }

  .contact-admin {
    text-align: center; font-size: 13px; color: rgba(255,255,255,0.3);
  }

  .contact-admin a { color: #818cf8; text-decoration: none; font-weight: 500; transition: color 0.2s; }
  .contact-admin a:hover { color: #a5b4fc; }

  .error-msg { font-size: 12px; color: #f87171; text-align: center; min-height: 18px; margin-top: 10px; }

  @media (max-width: 640px) {
    .panel-left { display: none; }
    .panel-right { width: 100%; padding: 36px 28px; }
  }
</style>
</head>
<body>

<div class="bg-orb orb1"></div>
<div class="bg-orb orb2"></div>
<div class="bg-orb orb3"></div>
<div class="grid-lines"></div>

<div class="card">

  <!-- Panneau gauche -->
  <div class="panel-left">
    <div class="brand">
      <div class="brand-icon">📦</div>
      <div>
        <div class="brand-name">G-STOCK</div>
        <div class="brand-sub">Gestion de Stock</div>
      </div>
    </div>

    <div class="hero-text">
      <h1>Pilotez vos stocks<br>en <span>temps réel</span></h1>
      <p>Une solution complète pour surveiller, analyser et optimiser vos inventaires depuis un seul endroit.</p>
    </div>

    <div class="features">
      <div class="feature">
        <div class="feature-dot dot-blue">📈</div>
        <div>
          <div class="feature-label">Suivi instantané</div>
          <div class="feature-desc">Mouvements de stock en direct</div>
        </div>
      </div>
      <div class="feature">
        <div class="feature-dot dot-sky">📊</div>
        <div>
          <div class="feature-label">Statistiques avancées</div>
          <div class="feature-desc">Tableaux de bord intelligents</div>
        </div>
      </div>
      <div class="feature">
        <div class="feature-dot dot-violet">🛡</div>
        <div>
          <div class="feature-label">Sécurité maximale</div>
          <div class="feature-desc">Données chiffrées et protégées</div>
        </div>
      </div>
    </div>

    <div class="copy">© 2024 G-STOCK — Tous droits réservés</div>
  </div>

  <!-- Panneau droit : formulaire -->
  <div class="panel-right">
    <div class="form-header">
      <h2>Bon retour 👋</h2>
      <p>Connectez-vous à votre espace de gestion</p>
    </div>

    <form id="form_connexion" method="POST" action="">

      <div class="form-group">
        <label>Adresse email</label>
        <div class="input-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 7 10-7"/>
          </svg>
          <input type="email" id="email" name="email" placeholder="vous@exemple.com" autocomplete="email">
        </div>
      </div>

      <div class="form-group">
        <label>Mot de passe</label>
        <div class="input-wrap">
          <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password">
        </div>
      </div>

      <div class="form-meta">
        <label class="remember">
          <input type="checkbox"> Se souvenir de moi
        </label>
        <a href="#" class="forgot">Mot de passe oublié ?</a>
      </div>

      <button type="submit" class="btn-submit btn">
        <span>
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/>
          </svg>
          Se connecter
        </span>
      </button>

      <div class="error-msg error_connect"></div>

    </form>

    <!-- <div class="divider">
      <div class="divider-line"></div>
      <span>Pas encore de compte ?</span>
      <div class="divider-line"></div>
    </div>

    <div class="contact-admin">
      <a href="#">Contacter l'administrateur</a>
    </div> -->
  </div>

</div>
<script src="../js/connexion.js"></script>
</body>
</html>
