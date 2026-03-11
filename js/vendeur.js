const addVendeur = document.getElementById("addVendeur")
const modalAddVendeur = document.getElementById("modalAddVendeur")
const cancelAddVendeur = document.getElementById("cancelAddVendeur")

if (addVendeur && modalAddVendeur && cancelAddVendeur) {
    addVendeur.addEventListener("click",()=>{
        modalAddVendeur.classList.add("flex")
        modalAddVendeur.classList.remove("hidden")
    })
    cancelAddVendeur.addEventListener("click", () =>{
        modalAddVendeur.classList.add("hidden")
        modalAddVendeur.classList.remove("flex")
    })
}
