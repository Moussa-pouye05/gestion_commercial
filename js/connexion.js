function getProjectBaseUrl() {
    const pathname = window.location.pathname;

    if (pathname.includes("/pages/")) {
        return `${window.location.origin}${pathname.split("/pages/")[0]}/`;
    }

    if (pathname.endsWith("/")) {
        return `${window.location.origin}${pathname}`;
    }

    const lastSlashIndex = pathname.lastIndexOf("/");
    const basePath = pathname.slice(0, lastSlashIndex + 1);
    return `${window.location.origin}${basePath}`;
}

const projectBaseUrl = getProjectBaseUrl();

function appUrl(path) {
    return new URL(path.replace(/^\/+/, ""), projectBaseUrl).href;
}

//Connexion
const form_connect = document.getElementById("form_connexion")
if(form_connect){
form_connect.addEventListener("submit", (e) =>{
    e.preventDefault()
    connexion();
    
})
}

async function connexion(){
    
    try {
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const btn = document.querySelector(".btn")
        const error_connect = document.querySelector(".error_connect")
        
        let formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        btn.disabled = true;
        btn.innerHTML = "Connexion...";
        error_connect.textContent = "";
        console.log("nif")
        const response = await fetch(appUrl("php/post_connexion.php"),{
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const data = await response.json();
        
        console.log(data)
        if (!data.success) {
            error_connect.textContent = data.message || "Email ou mot de passe incorrect";
            return;
        }

        if (data.role === "admin") {
            setTimeout(() => {
                window.location.href = appUrl("view/accueil_view.php");
            }, 500);
            return;
        }

        if (data.role === "vendeur") {
            setTimeout(() => {
                window.location.href = appUrl("view/accueil_vendeur_view.php");
            }, 500);
            return;
        }

        error_connect.textContent = "Role utilisateur invalide";
        btn.disabled = false;
        btn.innerHTML = "Connexion"
    } catch (error) {
        const error_connect = document.querySelector(".error_connect")
        if (error_connect) {
            error_connect.textContent = "Erreur lors de la connexion";
        }
    } finally {
        const btn = document.querySelector(".btn");
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = "Connexion";
        }
    }
    
}
const form = document.getElementById("form_inscription");
if(form){
    form.addEventListener("submit", (e) => {
    e.preventDefault();
    inscription();
})
}

async function inscription(){
    try {

        let formData = new FormData(form);

        const error_insert = document.querySelector(".error-insert")
        const succes_connect = document.querySelector(".succes_connect")
        const error_connect = document.querySelector(".error_connect")
       // console.log(error_connect,succes_connect)
        let response = await fetch(appUrl("php/post_inscription.php"), {
            method: "POST",
            credentials: 'same-origin',
            body: formData
        })
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        let datas = await response.json();
        
        if(datas.success){
            succes_connect.textContent = datas.message;
            
            form.reset();
        }else{
            error_connect.textContent = datas.message;
            
        }
        setTimeout(() =>{
            error_connect.textContent = ""
            succes_connect.textContent = ""
        },5000)
        
    } catch (error) {
        console.log("Erreur:" + error);
        
    }
}
