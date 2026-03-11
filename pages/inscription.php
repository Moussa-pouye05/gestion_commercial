<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <h1 class="text-2xl font-bold text-center mt-10">Inscription</h1>
    <div class="error-insert text-center"></div>
    <form id="form_inscription" action="" method="post" enctype="multipart/form-data" class="w-[50%] mx-auto mt-10 bg-slate-100 p-6 rounded-lg shadow-lg">
        <div class="mb-4">
            <label for="nom" class="block text-gray-700 font-bold mb-2">Nom d'utilisateur</label>
            <input type="text" id="nom" name="nom" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="email" class="block text-gray-700 font-bold mb-2">Email</label>
            <input type="email" id="email" name="email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="password" class="block text-gray-700 font-bold mb-2">Mot de passe</label>
            <input type="password" id="password" name="password" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="profile_picture" class="block text-gray-700 font-bold mb-2">Photo de profil</label>
            <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
        </div>
        <div class="mb-4">
            <label for="telephone" class="block text-gray-700 font-bold mb-2">Telephone</label>
            <input type="text" id="telephone" name="telephone" accept="image/*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring focus:ring-blue-500">
        </div>
        <select 
                name="role" 
                id="role"
                class="bg-gray-50 border border-gray-200 text-sm mb-2 w-full px-4 py-2 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-400 transition">
                <option value="admin">Admin</option>
                <option value="vendeur">Vendeur</option>
            </select>
        <button type="submit" class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-600 transition-colors">S'inscrire</button>
        <a href="connexion.php" class="my-2">Se connecter</a>
        <div class="error_connect text-sm text-red-500 font-bold"></div>
                <div class="succes_connect text-sm  font-bold"></div>
    </form>
     <script src="../js/connexion.js"></script>
</body>
</html>
