<!DOCTYPE html>
<html lang="fr" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
      (function () {
        try {
          var t = localStorage.getItem('theme');
          if (t === 'dark') {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
          } else if (t === 'light') {
            document.documentElement.classList.remove('dark');
            document.documentElement.style.colorScheme = 'light';
          } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
            document.documentElement.style.colorScheme = 'dark';
          }
        } catch (e) {}
      })();
    </script>
    <title>Connexion — G-STOCK</title>
    <link rel="stylesheet" href="../src/output.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <style>
        .img{
            background-image: url(../images/login3.png);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            width:80%;
            border-bottom-right-radius: 20px;
            border-top-right-radius: 20px;
            
        }
        
    </style>
</head>
<body class="min-h-full antialiased transition-colors duration-200">
    <button type="button" id="theme-toggle-login" title="Thème clair / sombre" class="fixed right-4 top-4 z-50 flex h-10 w-10 items-center justify-center rounded-lg border border-slate-200 bg-white shadow-md transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:hover:bg-slate-600">
      <span class="absolute inset-0 flex items-center justify-center dark:hidden" aria-hidden="true"><i class="fa-solid fa-moon text-slate-600"></i></span>
      <span class="absolute inset-0 hidden items-center justify-center dark:flex" aria-hidden="true"><i class="fa-solid fa-sun text-amber-400"></i></span>
    </button>
    <div class="fixed left-0 top-0 flex h-full w-full items-center justify-center bg-slate-100 transition-colors duration-200 dark:bg-slate-800">
        <div class="w-[800px] max-w-[95vw] rounded-lg bg-white p-8 shadow-lg transition-colors duration-200 dark:border dark:border-slate-600 dark:bg-slate-700 dark:shadow-slate-950/40">
            <div class="grid h-full grid-cols-1 gap-6 rounded-lg bg-white shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.19)] transition-colors duration-200 dark:bg-slate-700 sm:grid-cols-2 dark:shadow-none">
                <!-- Image cachée en dessous de 640px, visible au-dessus -->
                <div class="img hidden h-full border text-3xl font-bold text-slate-500 dark:border-slate-600 sm:block"></div>
                <!-- Formulaire prend toute la largeur en dessous de 640px -->
                <form action="" id="form_connexion" method="POST" class="boder mb-4 w-full rounded-lg pb-8 pl-6 pr-6 pt-6">
                    <div class="text-center text-2xl font-bold text-blue-500 dark:text-blue-400"><span class="text-red-500">G</span>-STOCK</div>
                    <div class="mt-4 text-center text-lg text-blue-500 dark:text-blue-400">Bienvenue</div>
                    <div class="mb-6 mt-4 flex items-center gap-2 rounded-md px-3 py-1 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.1)] ring-1 ring-slate-300 dark:bg-slate-800/40 dark:ring-slate-500">
                        <i class="fa-solid fa-user text-slate-300 dark:text-slate-500"></i>
                        <input type="email" id="email" name="email" placeholder="pouye@gmail.com" class="w-full rounded-md bg-transparent px-3 py-1 text-slate-900 placeholder:text-slate-400 focus:outline-none dark:text-slate-100 dark:placeholder:text-slate-500">
                    </div>
                    <div class="mb-6 flex items-center gap-2 rounded-md px-3 py-1 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.1)] ring-1 ring-slate-300 dark:bg-slate-800/40 dark:ring-slate-500">
                        <i class="fa-solid fa-lock text-slate-300 dark:text-slate-500"></i>
                        <input type="password" id="password" name="password" placeholder="......." class="w-full rounded-md bg-transparent px-3 py-1 placeholder:text-4xl placeholder:pb-4 focus:outline-none dark:text-slate-100">
                    </div>
                    <button type="submit" class="btn w-full bg-white-500 text-blue-500 font-bold py-2 px-4 mt-4 rounded-md hover:bg-white-200 transition duration-300 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.1)]">Se connecter</button>
                    <div class="error_connect text-sm text-red-500 text-center mt-2"></div>
                </form>
            </div>
        </div>
    </div>
    <script src="../js/connexion.js"></script>
    <script>
      (function () {
        function setTheme(theme) {
          var isDark = theme === 'dark';
          document.documentElement.classList.toggle('dark', isDark);
          document.documentElement.style.colorScheme = isDark ? 'dark' : 'light';
          try {
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
          } catch (e) {}
        }
        var btn = document.getElementById('theme-toggle-login');
        if (btn) {
          btn.addEventListener('click', function () {
            setTheme(document.documentElement.classList.contains('dark') ? 'light' : 'dark');
          });
        }
        var saved = localStorage.getItem('theme');
        if (saved === 'dark' || saved === 'light') {
          setTheme(saved);
        }
      })();
    </script>
</body>
</html>
