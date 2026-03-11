//modal add commande
const modalAddCommande = document.getElementById("modalAddCommande")
const addCommande = document.getElementById("addCommande")
const cancelAddCommande = document.getElementById("cancelAddCommande")

if (modalAddCommande && addCommande && cancelAddCommande) {
    addCommande.addEventListener("click",()=>{
        modalAddCommande.classList.remove("hide")
    })
    cancelAddCommande.addEventListener("click",()=>{
        modalAddCommande.classList.add("hide")
    })
}
