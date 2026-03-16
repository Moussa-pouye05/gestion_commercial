const formClient = document.getElementById("formClient");
const searchClient = document.getElementById("searchClient");
const paginationClient = document.getElementById("paginationClient")
const modalEditClient = document.getElementById("modalEditClient")
const closeModalClient = document.getElementById("closeModalClient")
const formClientEdit = document.getElementById("formClientEdit")
const deleteModalClient = document.getElementById("deleteModalClient")
const cancelDeleteClient  = document.getElementById("cancelDeleteClient")
const confirmDeleteClient  = document.getElementById("confirmDeleteClient")

const isClientPage = !!(formClient && paginationClient && modalEditClient);
let currentPageClient = 1;
let currentSearchClient = "";
let searchDebounceTimerClient = null;


if (formClient) {
    formClient.addEventListener("submit", (e) => {
        e.preventDefault();
        createClient();
    });
}

async function createClient() {
    try {
        const activeForm = formClient;
        if (!activeForm) return;

        const formData = new FormData(activeForm);
        const succes_connect = document.querySelector(".succes_connect");
        const error_connect = document.querySelector(".error_connect");

        const response = await fetch("../php/post_create_client.php", {
            method: "POST",
            body: formData
        });
        const datas = await response.json();

        if (datas.success) {
            await totalClient();
            if (succes_connect) succes_connect.textContent = datas.message;
            if (isClientPage) {
                await loadClient(currentPageClient);
            }
            activeForm.reset();
        } else if (error_connect) {
            error_connect.textContent = datas.message;
        }

        setTimeout(() => {
            if (error_connect) error_connect.textContent = "";
            if (succes_connect) succes_connect.textContent = "";
        }, 5000);
    } catch (error) {
        console.log("Erreur:" + error);
    }
}
document.addEventListener("DOMContentLoaded", () => {
    if (isClientPage) {
        loadClient(currentPageClient);
    }

    if (isClientPage && searchClient) {
        searchClient.addEventListener("input", (e) => {
            const value = e.target.value || "";
            clearTimeout(searchDebounceTimerClient);
            searchDebounceTimerClient = setTimeout(() => {
                loadClient(1, value);
            }, 300);
        }); 
    }
    totalClient()
    clientActif()
    totalCommande()
    fidelite()
});
async function loadClient(page = 1, search = currentSearchClient) {
    currentPageClient = page;
    currentSearchClient = (search || "").trim();
    try {
        const response = await fetch(`../php/post_read_client.php?page=${page}&search=${encodeURIComponent(currentSearchClient)}`);
        const datas = await response.json();
        const tbody = document.querySelector("tbody");
        const noFound = document.querySelector(".no-found-client");
        if (!tbody) return;

        tbody.innerHTML = "";
        if (noFound) noFound.textContent = "";

        if (!Array.isArray(datas.clients) || datas.clients.length === 0) {
            if (noFound) noFound.textContent = "Aucun client trouve.";
            generatePaginationClient(0, 1);
            return;
        }
        for (const item of datas.clients) {
            const tr = document.createElement("tr");
            tr.innerHTML = `
                
                <td class="flex items-center mt-2">
                    ${item.nom}
                </td>
                <td class="px-6 py-2 font-medium text-gray-900">${item.telephone}</td>
                <td class="px-6 py-2">${item.adresse}</td>
                
                <td class="px-6 py-2 flex gap-2">
                    <button class="editClient w-8 h-8 flex items-center justify-center rounded-md bg-yellow-50 text-yellow-600 hover:bg-yellow-100 transition">
                        <i class="fa-solid fa-pen text-sm"></i>
                    </button>
                    <button class="deleteClient w-8 h-8 flex items-center justify-center rounded-md bg-red-50 text-red-600 hover:bg-red-100 transition">
                        <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                </td>
            `;

            tbody.appendChild(tr);

            tr.querySelector(".editClient").addEventListener("click", () => {
                if (!modalEditClient) return;
                modalEditClient.classList.remove("hidden", "hide");
                modalEditClient.classList.add("flex");
                recuperationClient(item);
            });
            closeModalClient.addEventListener("click", () =>{
                modalEditClient.classList.add("hidden")
            })
            tr.querySelector(".deleteClient").addEventListener("click", () => {
                deleteClient(item.id);
            });
        }

        generatePaginationClient(datas.totalPages, datas.currentPage);
    } catch (error) {
        console.log(error);
    }
}
function generatePaginationClient(totalPages, currentPageValue) {
    const container = document.getElementById("paginationClient");
    if (!container) return;

    container.innerHTML = "";
    if (!totalPages || totalPages < 0) return;
    for (let i = 1; i <= totalPages; i++) {
        const btn = document.createElement("button");
        btn.textContent = i;
        btn.className = `px-3 py-1 rounded ${i === currentPageValue ? "bg-blue-600 text-white" : "bg-gray-200"}`;
        btn.addEventListener("click", () => loadClient(i, currentSearchClient));
        container.appendChild(btn);
    }
}
function recuperationClient(c){
    const id = document.getElementById("id")
    const nom = document.getElementById("nomEdit")
    const telephone = document.getElementById("telephoneEdit")
    const adresse = document.getElementById("adresseEdit")

    if(!id || !nom || !telephone || !adresse) return
    id.value = c.id;
    nom.value = c.nom;
    telephone.value = c.telephone;
    adresse.value = c.adresse
}
if(formClientEdit){
    formClientEdit.addEventListener("submit", (e) =>{
        e.preventDefault()
        editClient()
    })
}
async function editClient(){
    try{
        if(!formClientEdit) return;
        const succes_edit_client = document.querySelector(".succes_edit_client");
        const formData = new FormData(formClientEdit);
        
        if(succes_edit_client){
            succes_edit_client.textContent = "Modification en cours....";
        }

        const response = await fetch("../php/post_edit_client.php",{
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        if(succes_edit_client){
            succes_edit_client.textContent = data.message
            succes_edit_client.classList.remove("text-green-500","text-red-500")
            succes_edit_client.classList.add(data.success ? "text-green-500" : "text-red-500")
        }
        if(data.success){
            await loadClient(currentPageClient,currentSearchClient)
            setTimeout(() =>{
                if(!modalEditClient) return
                modalEditClient.classList.add("hidden");
                modalEditClient.classList.remove("flex","hide");
            },3000)
        }
            setTimeout(() => {
            if (succes_edit_client) succes_edit_client.textContent = "";
        }, 3000);
    }catch(error){
        console.log("Erreur:" +error)
    }
}
let clientToDelete = null
async function deleteClient(id){
    clientToDelete = id

    if(!deleteModalClient) return;

    deleteModalClient.classList.remove("hidden")
    deleteModalClient.classList.add("flex")
    
    if(deleteModalClient && confirmDeleteClient && cancelDeleteClient){
        cancelDeleteClient.addEventListener("click",() =>{
            deleteModalClient.classList.add("hidden")
            deleteModalClient.classList.remove("flex")
            clientToDelete = null
        })
    }
    confirmDeleteClient.addEventListener("click",async () =>{
        if(!clientToDelete) return
        const formData = new FormData();
        formData.append("id",clientToDelete);
        try{
            const response = await fetch("../php/post_delete_client.php",{
                method: "POST",
                body: formData
            });
            const data = await response.json();
            const msgDeleteClient = document.querySelector(".delete-client")
            if(data.success){
                await loadClient(currentPageClient,currentSearchClient)
                await totalClient();
                if(msgDeleteClient) msgDeleteClient.classList.remove("hidden")
            }
        setTimeout(() =>{
            if(msgDeleteClient) msgDeleteClient.classList.add("hidden")
        },5000)
        
        }catch(error){
            consoloe.log(Error)
        }
        deleteModalClient.classList.add("hidden")
        deleteModalClient.classList.remove("flex")
        clientToDelete = null
    })
}
let total_client = document.querySelector(".total-client")
async function totalClient(){
    try {
        const response = await fetch("../php/post_total_client.php");
        const data = await response.json();
        if(total_client){
            total_client.textContent = data.total_cl
        }
        
    } catch (error) {
        console.log("Erreur:" + error)
    }
}
let clientsActif = document.querySelector(".clients-actifs");
async function clientActif(){
    try {
        const response = await fetch("../php/post_client_actif.php");
        const data = await response.json();
        if(clientsActif){
            clientsActif.textContent = data.clients_actifs
        }
        
    } catch (error) {
        console.log("Erreur:" + error)
    }
}
let total_cmd = document.querySelector(".total-commande")
async function totalCommande(){
    try {
        const response = await fetch("../php/post_total_commande.php");
        const data = await response.json();
        if(total_cmd){
            total_cmd.textContent = data.total_commandes
        }
        
    } catch (error) {
        console.log("Erreur:" +error)
    }
}
const taux_fidelite = document.querySelector(".fidelite");

async function fidelite(){
    try {
        const response = await fetch("../php/post_taux_fidel.php");
        const data = await response.json();
        
        if(taux_fidelite){
            taux_fidelite.textContent = data.taux_fidelite + "%"
        }
    } catch (error) {
        console.log("Erreur:" + error);
    }
}