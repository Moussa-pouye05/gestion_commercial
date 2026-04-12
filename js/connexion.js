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

function setLoginButtonState(isLoading) {
    const btn = document.querySelector(".btn");
    if (!btn) return;
    btn.disabled = isLoading;
    btn.innerHTML = isLoading ? "Connexion..." : "Connexion";
}

function loadRememberedLogin() {
    const rememberedEmail = localStorage.getItem("remembered_email") || "";
    const rememberMe = localStorage.getItem("remember_me") === "1";
    const emailInput = document.getElementById("email");
    const rememberCheckbox = document.getElementById("remember_me");

    if (emailInput && rememberedEmail) {
        emailInput.value = rememberedEmail;
    }

    if (rememberCheckbox) {
        rememberCheckbox.checked = rememberMe;
    }
}

function persistRememberedLogin(email) {
    const rememberCheckbox = document.getElementById("remember_me");
    if (rememberCheckbox?.checked) {
        localStorage.setItem("remembered_email", email);
        localStorage.setItem("remember_me", "1");
        return;
    }

    localStorage.removeItem("remembered_email");
    localStorage.removeItem("remember_me");
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
        const error_connect = document.querySelector(".error_connect")
        const success_connect = document.querySelector(".success_connect")
        
        let formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        setLoginButtonState(true);
        error_connect.textContent = "";
        if (success_connect) success_connect.textContent = "";
        const response = await fetch(appUrl("php/post_connexion.php"),{
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        const data = await response.json();

        if (!data.success) {
            error_connect.textContent = data.message || "Email ou mot de passe incorrect";
            return;
        }

        persistRememberedLogin(email);
        if (success_connect) success_connect.textContent = "Connexion réussie...";

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
        setLoginButtonState(false);
    } catch (error) {
        const error_connect = document.querySelector(".error_connect")
        if (error_connect) {
            error_connect.textContent = "Erreur lors de la connexion";
        }
    } finally {
        setLoginButtonState(false);
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

const forgotPasswordLink = document.getElementById("forgot-password-link");
const forgotPasswordModal = document.getElementById("forgot-password-modal");
const closeForgotPasswordBtn = document.getElementById("close-forgot-password");
const forgotPasswordForm = document.getElementById("forgot-password-form");

function closeForgotPasswordModal() {
    if (!forgotPasswordModal) return;
    forgotPasswordModal.classList.remove("open");
}

if (forgotPasswordLink && forgotPasswordModal) {
    forgotPasswordLink.addEventListener("click", (e) => {
        e.preventDefault();
        const email = document.getElementById("email")?.value || "";
        const forgotEmail = document.getElementById("forgot_email");
        if (forgotEmail && email) {
            forgotEmail.value = email;
        }
        forgotPasswordModal.classList.add("open");
    });
}

if (closeForgotPasswordBtn) {
    closeForgotPasswordBtn.addEventListener("click", closeForgotPasswordModal);
}

if (forgotPasswordModal) {
    forgotPasswordModal.addEventListener("click", (e) => {
        if (e.target === forgotPasswordModal) {
            closeForgotPasswordModal();
        }
    });
}

if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const forgotError = document.querySelector(".forgot_error");
        const forgotSuccess = document.querySelector(".forgot_success");
        const forgotSubmit = document.getElementById("forgot-password-submit");
        const email = document.getElementById("forgot_email")?.value?.trim() || "";

        if (forgotError) forgotError.textContent = "";
        if (forgotSuccess) forgotSuccess.textContent = "";

        forgotSubmit.disabled = true;
        forgotSubmit.querySelector("span").textContent = "Envoi...";

        try {
            const formData = new FormData();
            formData.append("email", email);

            const response = await fetch(appUrl("php/post_forgot_password.php"), {
                method: "POST",
                body: formData
            });
            const result = await response.json();

            if (!result.success) {
                if (forgotError) forgotError.textContent = result.message || "Erreur lors de l'envoi";
                return;
            }

            if (forgotSuccess) {
                forgotSuccess.innerHTML = result.delivery === "manual" && result.reset_link
                    ? `${result.message}<br><a href="${result.reset_link}" style="color:#a5b4fc;word-break:break-all;">${result.reset_link}</a>`
                    : (result.message || "Lien généré avec succès");
            }
        } catch (error) {
            if (forgotError) forgotError.textContent = "Erreur lors de l'envoi du lien";
        } finally {
            forgotSubmit.disabled = false;
            forgotSubmit.querySelector("span").textContent = "Envoyer le lien";
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    loadRememberedLogin();
});
