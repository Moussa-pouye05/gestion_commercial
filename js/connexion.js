//Connexion
const form_connect = document.getElementById("form_connexion")
if(form_connect){
form_connect.addEventListener("submit", (e) =>{
    e.preventDefault()
    connexion()
})
}

async function connexion(){
    try {
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        const btn = document.querySelector(".btn")
        
        let formData = new FormData();
        formData.append("email", email);
        formData.append("password", password);

        btn.disabled = true;
        btn.innerHTML = "Connexion...";
        const error_connect = document.querySelector(".error_connect")
        const response = await fetch("../php/post_connexion.php",{
            method: 'POST',
            body: formData
        });
        let data = await response.json();
        
        error_connect.textContent = data.message
        
        if(data.success){
            setTimeout(() =>{
                window.location.href = "../view/accueil_view.php"
            },1000)
        }
        btn.disabled = false;
        btn.innerHTML = "Connexion"
    } catch (error) {
        
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
        let response = await fetch("../php/post_inscription.php", {
            method: "POST",
            body: formData
        })
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
