let links = document.querySelectorAll("[data-delete]");

//On boucle sur les liens
for(let link of links){
    //On met un écouteur d'évènements
    link.addEventListener("click", function(e){
        //On empeche la navigation
        e.preventDefault();

        //On vérifie la méthode
        if(this.getAttribute("data-method") === "delete") {
            //On demande confirmation
            if(confirm("Voulez-vous supprimer cette image ?")){
                //On envoi la requête ajax DELETE
                fetch(this.getAttribute("href"), {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({"_token": this.dataset.token })
                }).then(response => response.json())
                .then(data => {
                    if(data.success){
                        this.parentElement.remove();
                    }else{
                        alert(data.error);
                    }
                })
            }
        }
    });
}
