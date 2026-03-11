<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
<body>
    <div class="fixed top-0 left-0 w-full h-full flex items-center justify-center bg-gray-100">
        <div class="w-[800px] h-[450px] rounded-lg p-8 bg-[#fff] shadow-lg">
            <div class="grid grid-cols-1 sm:grid-cols-2 h-full rounded-lg bg-[#fff] gap-6 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.19)]">
                <!-- Image cachée en dessous de 640px, visible au-dessus -->
                <div class="text-3xl font-bold text-slate-500 border h-full img hidden sm:block"></div>
                <!-- Formulaire prend toute la largeur en dessous de 640px -->
                <form action="" id="form_connexion" method="POST" class="rounded-lg boder pl-6 pr-6 pt-6 pb-8 mb-4 w-full">
                    <div class="text-2xl text-blue-500 text-center font-bold"><span class="text-red-500">G</span>-STOCK</div>
                    <div class="text-lg text-center text-blue-500 mt-4">Bienvenue</div>
                    <div class="mb-6 mt-4 flex items-center gap-2 ring-1 ring-slate-300 rounded-md px-3 py-1 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.1)]">   
                        <i class="fa-solid fa-user text-slate-300"></i>
                        <input type="email" id="email" name="email" placeholder="pouye@gmail.com" class="w-full px-3 py-1 focus:outline-none rounded-md">
                    </div>
                    <div class="mb-6 flex items-center gap-2 ring-1 ring-slate-300 rounded-md px-3 py-1 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.1)]">
                        <i class="fa-solid fa-lock text-slate-300"></i>
                        <input type="password" id="password" name="password" placeholder="......." class="w-full px-3 py-1 placeholder:text-4xl placeholder:pb-4 focus:outline-none rounded-md">
                    </div>
                    <button type="submit" class="btn w-full bg-white-500 text-blue-500 font-bold py-2 px-4 mt-4 rounded-md hover:bg-white-200 transition duration-300 shadow-[0_4px_8px_rgba(0,0,0,0.1),_0_6px_20px_rgba(0,0,0,0.1)]">Se connecter</button>
                    <div class="error_connect text-sm text-red-500 text-center mt-2"></div>
                </form>
            </div>
        </div>
    </div>
    <script src="../js/connexion.js"></script>
</body>
</html>
