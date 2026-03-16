<?php
  if(!$_SESSION['user']){
    header("Location: ../pages/connexion.php");
  }
?>
<header>
  <div class="fixed top-0 z-20 right-0 w-full md:w-[80%] lg:w-[82%] ml-0 md:ml-[20%] lg:ml-[18%] h-16 bg-white shadow-md flex items-center justify-between">
    
    <div class="dashboard text-xl text-slate-600 ml-4 font-bold hidden md:block">
      Dashboard
    </div>
    <div class="block md:hidden"><i class="menu fa-solid fa-bars ml-4 text-lg text-slate-500 cursor-pointer"></i></div>
    

    <div class="flex items-center justify-end">
      <img src="<?= $_SESSION['user']['photo_profil']?>" alt="profil" class="w-10 h-10 rounded-full float-right mt-2 mr-4">
      <span class="text-slate-600 mr-4"><?=$_SESSION['user']['nom']?></span>
    </div>

  </div>
</header>