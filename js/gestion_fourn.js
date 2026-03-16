const modalFourn = document.getElementById("modalFourn");
const modalFournEdit = document.getElementById("modalFournEdit");
const closeModalFour = document.getElementById("closeModalFour");
const closeModalFourEdit = document.getElementById("closeModalFourEdit");
const addFourn = document.getElementById("addFourn");
const formFournisseur = document.getElementById("formFournisseur");
const formFournisseurEdit = document.getElementById("formFournisseurEdit");
const searchFourn = document.getElementById("searchFourn");
const paginationFourn = document.getElementById("paginationFourn")
const deleteModalFourn = document.getElementById("deleteModalFourn")
const cancelDeleteFourn = document.getElementById("cancelDeleteFourn")
const confirmDeleteFourn = document.getElementById("confirmDeleteFourn")

const isFournPage = !!(formFournisseur && paginationFourn)
let currentPageFourn = 1;
let currentSearchFourn = "";
let searchDebounceTimerFourn = null;

if(addFourn){
addFourn.addEventListener("click", () => {
    modalFourn.classList.add("flex");
    modalFourn.classList.remove("hidden");
})
}
if(closeModalFour){
closeModalFour.addEventListener("click", () => {
    modalFourn.classList.remove("flex");
    modalFourn.classList.add("hidden");
})
}


if (formFournisseur) {
    formFournisseur.addEventListener("submit", (e) => {
        e.preventDefault();
        createFourn();
        
    });
}

async function createFourn() {
    try {
        const activeFormFourn = formFournisseur;
        if (!activeFormFourn) return;

        const formData = new FormData(activeFormFourn);
        const succes_connect_fourn = document.querySelector(".succes_connect_fourn");
        const error_connect_fourn = document.querySelector(".error_connect_fourn");

        const response = await fetch("../php/post_create_fourn.php", {
            method: "POST",
            body: formData
        });
        const datas = await response.json();

        if (datas.success) {
            if (succes_connect_fourn) succes_connect_fourn.textContent = datas.message;
            if (isFournPage) {
                await loadFournisseur(currentPageFourn);
            }
            activeFormFourn.reset();
        } else if (error_connect_fourn) {
            error_connect_fourn.textContent = datas.message;
        }

        setTimeout(() => {
            if (error_connect_fourn) error_connect_fourn.textContent = "";
            if (succes_connect_fourn) succes_connect_fourn.textContent = "";
        }, 5000);
    } catch (error) {
        console.log("Erreur:" + error);
    }
}

document.addEventListener("DOMContentLoaded", () => {
    if (isFournPage) {
        loadFournisseur(currentPageFourn);
    }

    if (isFournPage && searchFourn) {
        searchFourn.addEventListener("input", (e) => {
            const value = e.target.value || "";
            clearTimeout(searchDebounceTimerFourn);
            searchDebounceTimerFourn = setTimeout(() => {
                loadFournisseur(1, value);
            }, 300);
        }); 
    }
});

async function loadFournisseur(page = 1, search = currentSearchFourn) {
    currentPageFourn = page;
    currentSearchFourn = (search || "").trim();
    try {
        const response = await fetch(`../php/post_read_fournisseur.php?page=${page}&search=${encodeURIComponent(currentSearchFourn)}`);
        const datas = await response.json();
        const tbody = document.querySelector("tbody");
        const noFoundFourn = document.querySelector(".no_found_fourn");
       // console.log(noFoundFourn)
        if (!tbody) return;

        tbody.innerHTML = "";
        if (noFoundFourn) noFoundFourn.textContent = "";

        if (!Array.isArray(datas.fournisseurs) || datas.fournisseurs.length === 0) {
            if (noFoundFourn) noFoundFourn.textContent = "Aucun client trouve.";
            generatePaginationFourn(0, 1);
            return;
        }
        for (const item of datas.fournisseurs) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                
                <td class="flex items-center mt-2">
                    ${item.nom}
                </td>
                <td class="px-6 py-2">${item.adresse}</td>
                <td class="px-6 py-2 font-medium text-gray-900">${item.telephone}</td>
                <td class="px-6 py-2 flex gap-2">
                    <button class="editFourn w-8 h-8 flex items-center justify-center rounded-md bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                        <i class="fa-solid fa-pen text-sm"></i>
                    </button>
                    <button class="deleteFourn w-8 h-8 flex items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);

            tr.querySelector(".editFourn").addEventListener("click", () => {
                if (!modalFournEdit) return;
                modalFournEdit.classList.remove("hidden", "hide");
                modalFournEdit.classList.add("flex");
                recuperationFourn(item);
            });
            closeModalFourEdit.addEventListener("click", () =>{
                modalFournEdit.classList.add("hidden")
            })
            tr.querySelector(".deleteFourn").addEventListener("click", () => {
                deleteFournisseur(item.id);
            });
        }

        generatePaginationFourn(datas.totalPages, datas.currentPage);
    } catch (error) {
        console.log(error);
    }
}
function generatePaginationFourn(totalPages, currentPageValue) {
    const containerFourn = document.getElementById("paginationFourn");
    if (!containerFourn) return;

    containerFourn.innerHTML = "";
    if (!totalPages || totalPages < 0) return;
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded ${i === currentPageValue ? "bg-blue-600 text-white" : "bg-gray-200"}`;
        btn.addEventListener("click", () => loadFournisseur(i, currentSearchFourn));
        containerFourn.appendChild(btn);
    }
}

function recuperationFourn(f){
    const id = document.getElementById("id")
    const nom = document.getElementById("nomEdit")
    const telephone = document.getElementById("telephoneEdit")
    const adresse = document.getElementById("adresseEdit")

    if(!nom || !telephone || !adresse) return;
    id.value = f.id
    nom.value = f.nom
    adresse.value = f.adresse
    telephone.value = f.telephone
}
if(formFournisseurEdit){
    formFournisseurEdit.addEventListener("submit", (e) =>{
        e.preventDefault()
        editFournisseur()
    })
}
async function editFournisseur(){
    try{
        if(!formFournisseurEdit) return;
        const succes_edit_fourn = document.querySelector(".succes_edit_fourn");
        const formData = new FormData(formFournisseurEdit);
        
        if(succes_edit_fourn){
            succes_edit_fourn.textContent = "Modification en cours....";
        }

        const response = await fetch("../php/post_edit_fourn.php",{
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if(succes_edit_fourn){
            succes_edit_fourn.textContent = data.message
            succes_edit_fourn.classList.remove("text-green-500","text-red-500")
            succes_edit_fourn.classList.add(data.success ? "text-green-500" : "text-red-500")
        }
        if(data.success){
            await loadFournisseur(currentPageFourn,currentSearchFourn)
            setTimeout(() =>{
                if(!modalFournEdit) return
                modalFournEdit.classList.add("hidden");
                modalFournEdit.classList.remove("flex","hide");
            },3000)
        }
            setTimeout(() => {
            if (succes_edit_fourn) succes_edit_fourn.textContent = "";
        }, 3000);
    }catch(error){
        console.log("Erreur:" +error)
    }
}
let fournToDelete = null;
function deleteFournisseur(id){
    fournToDelete = id

    if(!deleteModalFourn) return
    deleteModalFourn.classList.remove("hidden")
    deleteModalFourn.classList.add("flex")

    if(deleteModalFourn && confirmDeleteFourn && cancelDeleteFourn){
        cancelDeleteFourn.addEventListener("click", () => {
            deleteModalFourn.classList.add("hidden")
            deleteModalFourn.classList.remove("flex")
            fournToDelete = null
        });
    confirmDeleteFourn.addEventListener("click", async () => {
        if(!fournToDelete) return;
        const formData = new FormData();
        formData.append("id",fournToDelete);
        try{
            const response = await fetch("../php/post_delete_fourn.php",{
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            const msgDeleteFourn = document.querySelector(".delete-fourn")
            if(data.success){
                await loadFournisseur(currentPageFourn,currentSearchFourn)
                if(msgDeleteFourn) msgDeleteFourn.classList.remove("hidden")
            }
            setTimeout(() =>{
                if(msgDeleteFourn) msgDeleteFourn.classList.add("hidden")
            },5000)
        }catch(error){
            console.log("Erreur:" + error)
        }
        deleteModalFourn.classList.add("hidden")
        deleteModalFourn.classList.remove("flex")
        fournToDelete = null
    })  
    }
}